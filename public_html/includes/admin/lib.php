<?php
if (!defined('PERF')) { http_response_code(403); exit; }
/* Admin panel helpers: login, saving settings, uploads, and the form builder used by admin.php. */

define('STORE', dirname(__DIR__, 2) . '/storage');
define('ADMIN_FILE', STORE . '/admin.json');
define('SETUP_CODE_FILE', STORE . '/admin-setup-code.txt');
define('ATTEMPTS_FILE', STORE . '/admin-attempts.json');
define('HISTORY_DIR', STORE . '/history');
define('HISTORY_KEEP', 30);
define('IDLE_TIMEOUT', 4 * 3600);
define('ROOT', dirname(__DIR__, 2));

/* ---------- Files ---------- */

function read_json($path) {
    if (!is_file($path)) return [];
    $v = json_decode((string)file_get_contents($path), true);
    return is_array($v) ? $v : [];
}

/* Write via a temporary file + rename so a half-written file is never read by the website. */
function write_json($path, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    $tmp = $path . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) return false;
    if (!@rename($tmp, $path)) { @unlink($tmp); return false; }
    return true;
}

function store_writable() { return is_dir(STORE) && is_writable(STORE); }

/* Load, change and save settings.json under a lock, keeping a copy of the previous version. */
function settings_update(callable $change) {
    if (!store_writable()) return false;
    $lock = fopen(STORE . '/settings.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX)) return false;
    $cur = read_json(SETTINGS_FILE);
    $new = $change($cur);
    $new['updated'] = date('Y-m-d H:i:s');
    if ($cur) history_add($cur);
    $ok = write_json(SETTINGS_FILE, $new);
    flock($lock, LOCK_UN);
    fclose($lock);
    return $ok;
}

function history_add($settings) {
    if (!is_dir(HISTORY_DIR)) @mkdir(HISTORY_DIR, 0755);
    write_json(HISTORY_DIR . '/settings-' . date('Ymd-His') . '-' . bin2hex(random_bytes(2)) . '.json', $settings);
    $files = history_list();
    foreach (array_slice($files, HISTORY_KEEP) as $f) @unlink(HISTORY_DIR . '/' . $f);
}

function history_list() {
    $files = array_map('basename', glob(HISTORY_DIR . '/settings-*.json') ?: []);
    rsort($files);
    return $files;
}

/* ---------- Login ---------- */

function admin_session_start() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    ini_set('session.use_strict_mode', '1');
    session_name('perf_admin');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
    if (!empty($_SESSION['admin']) && time() - ($_SESSION['seen'] ?? 0) > IDLE_TIMEOUT) {
        $_SESSION = [];
        flash('You were logged out after a period of inactivity.', 'info');
    }
    if (!empty($_SESSION['admin'])) $_SESSION['seen'] = time();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function admin_exists() { return !empty(read_json(ADMIN_FILE)['hash']); }
function logged_in() { return !empty($_SESSION['admin']); }

function csrf_field() { return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function csrf_ok() { return is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['csrf'], $_POST['csrf']); }

/* One-time code that proves the person creating the first password can open the hosting file manager. */
function setup_code() {
    if (!is_file(SETUP_CODE_FILE) && store_writable()) {
        $code = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        @file_put_contents(SETUP_CODE_FILE, $code . "\n\nEnter this code on the admin setup page to create the admin password.\nThis file is deleted automatically once the password is set.\n");
    }
    return is_file(SETUP_CODE_FILE) ? trim(strtok((string)file_get_contents(SETUP_CODE_FILE), "\n")) : '';
}

function password_problem($pw, $pw2) {
    if (mb_strlen($pw) < 10) return 'The password must be at least 10 characters long.';
    if ($pw !== $pw2) return 'The two passwords do not match.';
    return '';
}

function set_password($pw) {
    return write_json(ADMIN_FILE, ['hash' => password_hash($pw, PASSWORD_DEFAULT), 'changed' => date('Y-m-d H:i:s')]);
}

/* Brute-force protection: 5 wrong passwords from one IP address lock it out for 15 minutes. */
function login_locked() {
    $a = read_json(ATTEMPTS_FILE)[md5($_SERVER['REMOTE_ADDR'] ?? '')] ?? null;
    return $a && $a['fails'] >= 5 && time() - $a['since'] < 900;
}
function login_failed($reset = false) {
    $all = read_json(ATTEMPTS_FILE);
    $ip = md5($_SERVER['REMOTE_ADDR'] ?? '');
    foreach ($all as $k => $a) if (time() - $a['since'] >= 900) unset($all[$k]);
    if ($reset) unset($all[$ip]);
    else $all[$ip] = ['fails' => ($all[$ip]['fails'] ?? 0) + 1, 'since' => $all[$ip]['since'] ?? time()];
    write_json(ATTEMPTS_FILE, $all);
}

function login_as_admin() {
    session_regenerate_id(true);
    $_SESSION['admin'] = true;
    $_SESSION['seen'] = time();
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* ---------- Messages & redirects ---------- */

function flash($msg, $type = 'ok') { $_SESSION['flash'][] = [$type, $msg]; }
function go($section = '', $extra = '') {
    header('Location: ' . url('admin') . ($section !== '' ? '?s=' . rawurlencode($section) . $extra : ''), true, 303);
    exit;
}

/* ---------- Cleaning input ---------- */

function slugify($s) {
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string)$s) ?: (string)$s), '-'));
    return substr($s, 0, 60);
}

/* File name from a caption, keeping capitals so the gallery caption reads as typed: "NEET topper 2026" -> NEET-topper-2026 */
function file_name($s) { return substr(trim(preg_replace('/[^A-Za-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string)$s) ?: (string)$s), '-'), 0, 80); }

function one_line($v, $max = 300) { return mb_substr(trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string)$v)), 0, $max); }
function multi_line($v, $max = 6000) {
    $v = str_replace(["\r\n", "\r"], "\n", (string)$v);
    return mb_substr(trim(preg_replace('/[\x00-\x08\x0B-\x1F\x7F]+/u', '', $v)), 0, $max);
}

/* Links must be web addresses (or a page of this site); anything else such as "javascript:" is refused. */
function clean_url($v, &$err) {
    $v = one_line($v, 500);
    if ($v === '') return '';
    if (preg_match('#^/[^/\\\\]#', $v) || $v === '/') return $v;
    if (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $v) && preg_match('#^[\w-]+(\.[\w-]+)+#', $v)) $v = 'https://' . $v;
    if (!preg_match('#^https?://#i', $v) || !filter_var($v, FILTER_VALIDATE_URL)) { $err = 'is not a valid web address (it should start with https://).'; return $v; }
    return $v;
}

/* Answers may keep a few simple tags; all attributes except safe link targets are removed. */
function safe_html($s) {
    $s = strip_tags(multi_line($s), '<b><strong><i><em><u><a><br><p><ul><ol><li>');
    return preg_replace_callback('#<(/?)([a-z]+)([^>]*)>#i', function ($m) {
        $tag = strtolower($m[2]);
        if ($m[1] !== '') return "</$tag>";
        if ($tag === 'a') {
            $href = preg_match('#href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))#i', $m[3], $h) ? html_entity_decode($h[1] . ($h[2] ?? '') . ($h[3] ?? '')) : '';
            if (preg_match('#^(https?://|mailto:|tel:|/)#i', trim($href))) return '<a href="' . e(trim($href)) . '"' . (preg_match('#^https?://#i', trim($href)) ? ' target="_blank" rel="noopener"' : '') . '>';
            return '<a>';
        }
        return "<$tag>";
    }, $s);
}

/* Cleans one submitted field. Problems are added to $errors (label => message); the typed value is kept so it can be corrected. */
function clean_field($f, $raw, $label, &$errors, $path) {
    $err = '';
    switch ($f['type']) {
        case 'textarea': $v = multi_line($raw); break;
        case 'html':     $v = safe_html($raw); break;
        case 'email':
            $v = one_line($raw, 120);
            if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) $err = 'is not a valid email address.';
            break;
        case 'url': $v = clean_url($raw, $err); break;
        case 'map':
            $v = one_line($raw, 2000);
            if (preg_match('#src\s*=\s*["\']([^"\']+)#i', $v, $m)) $v = html_entity_decode($m[1]);
            if ($v !== '' && !preg_match('#^https://(www\.)?google\.[a-z.]+/maps#i', $v)) $err = 'should be the Google Maps "Embed a map" code.';
            break;
        case 'digits': $v = preg_replace('/\D/', '', (string)$raw); break;
        case 'date':
            $v = one_line($raw, 10);
            if ($v !== '' && !(preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v))) $err = 'is not a valid date.';
            break;
        case 'checkbox': $v = !empty($raw); break;
        case 'select':   $v = one_line($raw, 60); if (!array_key_exists($v, field_options($f))) $v = (string)array_key_first(field_options($f)); break;
        case 'icon':     $v = one_line($raw, 30); if (!isset(icon_paths()[$v])) $v = 'check'; break;
        case 'lines':    $v = array_values(array_filter(array_map('one_line', explode("\n", (string)$raw)), 'strlen')); break;
        case 'services':
            global $DATA;
            $v = array_values(array_unique(array_filter(array_map('strval', (array)$raw), function ($s) { global $DATA; return isset($DATA['services'][$s]); })));
            break;
        default:
            $v = one_line($raw, $f['maxlength'] ?? 300);
            if ($v !== '' && !empty($f['pattern']) && !preg_match($f['pattern'], $v)) $err = 'is not in the expected format' . (!empty($f['placeholder']) ? ' (' . $f['placeholder'] . ').' : '.');
    }
    if ($err === '' && !empty($f['required']) && ($v === '' || $v === [])) $err = 'is required.';
    if ($err !== '') $errors[$path] = $label . ' ' . $err;
    return $v;
}

function field_options($f) {
    global $DATA;
    return is_array($f['options']) ? $f['options'] : ($DATA[$f['options']] ?? []);
}

/* ---------- Converting between stored values and form rows ---------- */

/* Stored list/map -> plain rows for the form. */
function to_rows($f, $value) {
    $rows = [];
    $value = is_array($value) ? $value : [];
    if (!empty($f['group'])) {
        $sub = array_keys(array_diff_key($f['fields'], [$f['group'] => 1]));
        foreach ($value as $g => $items) foreach ((array)$items as $it) {
            $r = [$f['group'] => $g];
            foreach ($sub as $i => $k) $r[$k] = $it[$i] ?? $it[$k] ?? '';
            $rows[] = $r;
        }
    } elseif (!empty($f['key'])) {
        foreach ($value as $k => $it) $rows[] = (!empty($f['flat']) ? [$f['flat'] => $it] : (array)$it) + [$f['key'] => $k];
    } else {
        $rows = array_values(array_filter($value, 'is_array'));
    }
    return $rows;
}

/* Submitted rows -> stored list/map. $formRows receives the cleaned rows, to show the form again if there are errors. */
function from_rows($f, $rows, &$errors, $path, &$formRows = null) {
    $out = [];
    $formRows = [];
    $n = 0;
    foreach ((array)$rows as $raw) {
        if (!is_array($raw)) continue;
        $filled = false;   // a row where only drop-downs / tick boxes are set counts as empty and is ignored
        foreach ($f['fields'] as $k => $sf) {
            if (in_array($sf['type'], ['checkbox', 'select', 'icon'], true)) continue;
            $v = $raw[$k] ?? '';
            if (is_array($v) ? $v !== [] : trim((string)$v) !== '') $filled = true;
        }
        if (!$filled) continue;
        $n++;
        $title = trim((string)($raw[$f['title_field'] ?? ''] ?? '')) ?: '#' . $n;
        $row = [];
        foreach ($f['fields'] as $k => $sf) $row[$k] = clean_field($sf, $raw[$k] ?? null, "\"$title\": " . $sf['label'], $errors, "$path.$n.$k");
        $formRows[] = $row;
        if (!empty($f['key'])) {
            $key = slugify($row[$f['key']] !== '' ? $row[$f['key']] : $row[$f['title_field']]);
            if ($key === '') { $errors["$path.$n"] = "\"$title\": a name is required."; continue; }
            if (isset($out[$key])) { $errors["$path.$n"] = "\"$title\": the code \"$key\" is used twice."; continue; }
            unset($row[$f['key']]);
            $out[$key] = !empty($f['flat']) ? $row[$f['flat']] : $row;
        } elseif (!empty($f['group'])) {
            $g = $row[$f['group']];
            unset($row[$f['group']]);
            $out[$g][] = array_values($row);
        } else {
            $out[] = $row;
        }
    }
    return $out;
}

/* Reads a whole section from the submitted form. Returns [values to store, errors, values to show in the form]. */
function section_from_post($sec) {
    $errors = [];
    $vals = [];
    $form = [];
    $post = $_POST['f'] ?? [];
    foreach ($sec['fields'] as $key => $f) {
        $raw = $post[$key] ?? null;
        if ($f['type'] === 'rows') {
            $vals[$key] = from_rows($f, $raw, $errors, $key, $form[$key]);
            continue;
        } elseif ($f['type'] === 'object') {
            $obj = [];
            foreach ($f['fields'] as $k => $sf) {
                $obj[$k] = $sf['type'] === 'image'
                    ? image_from_post($sf, "$key.$k", $raw[$k] ?? '', $errors)
                    : clean_field($sf, $raw[$k] ?? null, ($f['label'] !== '' ? $f['label'] . ': ' : '') . $sf['label'], $errors, "$key.$k");
            }
            $vals[$key] = $obj;
        } elseif ($f['type'] === 'image') {
            $vals[$key] = image_from_post($f, $key, $raw, $errors);
        } else {
            $vals[$key] = clean_field($f, $raw, $f['label'], $errors, $key);
        }
        $form[$key] = $vals[$key];
    }
    return [$vals, $errors, $form];
}

/* Runs stored values (e.g. from a backup file) through the same checks as the edit form. Returns [values, errors]. */
function clean_stored($sec, $values) {
    $join = function ($f, $v) { return $f['type'] === 'lines' ? implode("\n", (array)$v) : $v; };
    $raw = [];
    foreach ($sec['fields'] as $key => $f) {
        $v = $values[$key] ?? null;
        if ($f['type'] === 'rows') {
            $raw[$key] = [];
            foreach (to_rows($f, $v) as $row) {
                foreach ($f['fields'] as $k => $sf) $row[$k] = $join($sf, $row[$k] ?? '');
                $raw[$key][] = $row;
            }
        } elseif ($f['type'] === 'object') {
            foreach ($f['fields'] as $k => $sf) $raw[$key][$k] = $join($sf, $v[$k] ?? '');
        } else {
            $raw[$key] = $join($f, $v);
        }
    }
    [$post, $files] = [$_POST, $_FILES];
    $_POST = ['f' => $raw];
    $_FILES = [];
    [$vals, $errors] = section_from_post($sec);
    [$_POST, $_FILES] = [$post, $files];
    return [$vals, $errors];
}

/* The section's current values, shaped for the form. */
function section_values($sec) {
    global $CFG, $DATA;
    $src = $sec['store'] === 'data' ? $DATA : $CFG;
    $vals = [];
    foreach ($sec['fields'] as $key => $f) $vals[$key] = $src[$key] ?? ($f['type'] === 'object' ? [] : '');
    return $vals;
}

/* ---------- Uploads ---------- */

function upload_error_text($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE: return 'the file is too large.';
        case UPLOAD_ERR_PARTIAL: return 'the upload was interrupted, please try again.';
        case UPLOAD_ERR_NO_FILE: return 'no file was chosen.';
        default: return 'the upload failed (error ' . (int)$code . ').';
    }
}

/* Saves an uploaded photo into $dir with a readable file name. Large photos are resized to $maxW
   pixels wide and phone photos are turned the right way up (when the server has the GD extension). */
function save_image($file, $dir, $name, &$err, $maxW = 1600) {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) { $err = upload_error_text($file['error'] ?? UPLOAD_ERR_NO_FILE); return ''; }
    if ($file['size'] > 15 * 1024 * 1024) { $err = 'the file is larger than 15 MB.'; return ''; }
    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]])) { $err = 'only JPG, PNG and WebP photos can be uploaded.'; return ''; }
    $abs = ROOT . '/' . $dir;
    if (!is_dir($abs) && !@mkdir($abs, 0755, true)) { $err = "the folder $dir could not be created."; return ''; }
    $ext = $types[$info[2]];
    $base = file_name($name) ?: 'photo';
    $target = "$abs/$base.$ext";
    for ($i = 2; file_exists($target); $i++) $target = "$abs/$base-$i.$ext";
    if (!resize_image($file['tmp_name'], $info, $target, $maxW) && !move_uploaded_file($file['tmp_name'], $target)) { $err = 'the file could not be saved (check folder permissions).'; return ''; }
    @chmod($target, 0644);
    return $dir . '/' . basename($target);
}

function resize_image($src, $info, $target, $maxW) {
    if (!function_exists('imagecreatetruecolor')) return false;
    [$w, $h, $type] = $info;
    $orient = ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) ? (int)(@exif_read_data($src)['Orientation'] ?? 1) : 1;
    // Re-save JPEGs always (fixes rotation, removes hidden location data); other types only when too wide.
    if ($type !== IMAGETYPE_JPEG && $w <= $maxW) return false;
    @ini_set('memory_limit', '512M');
    $load = [IMAGETYPE_JPEG => 'imagecreatefromjpeg', IMAGETYPE_PNG => 'imagecreatefrompng', IMAGETYPE_WEBP => 'imagecreatefromwebp'][$type];
    if (!function_exists($load) || !($img = @$load($src))) return false;
    if (in_array($orient, [3, 6, 8], true)) {
        $img = imagerotate($img, [3 => 180, 6 => -90, 8 => 90][$orient], 0);
        if ($orient !== 3) [$w, $h] = [$h, $w];
    }
    if ($w > $maxW) {
        $nh = (int)round($h * $maxW / $w);
        $dst = imagecreatetruecolor($maxW, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $maxW, $nh, $w, $h);
        imagedestroy($img);
        $img = $dst;
    }
    $ok = $type === IMAGETYPE_JPEG ? imagejpeg($img, $target, 85) : ($type === IMAGETYPE_PNG ? imagepng($img, $target, 8) : imagewebp($img, $target, 85));
    imagedestroy($img);
    return $ok;
}

/* $_FILES entry for a field path like "founder.photo" (input name up[founder][photo]). */
function uploaded_file($path) {
    $parts = explode('.', $path);
    $f = $_FILES['up'] ?? null;
    if (!$f) return null;
    $out = [];
    foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $prop) {
        $v = $f[$prop];
        foreach ($parts as $p) $v = $v[$p] ?? null;
        $out[$prop] = $v;
    }
    return $out['error'] === null || $out['error'] === UPLOAD_ERR_NO_FILE ? null : $out;
}

function image_from_post($f, $path, $current, &$errors) {
    $current = (string)$current;
    if (!preg_match('#^' . preg_quote($f['dir'], '#') . '/[\w.-]+$#', $current) || !is_file(ROOT . '/' . $current)) $current = '';
    $rm = $_POST['rm'] ?? [];
    foreach (explode('.', $path) as $p) $rm = $rm[$p] ?? null;
    if ($rm) $current = '';
    if ($file = uploaded_file($path)) {
        $err = '';
        $saved = save_image($file, $f['dir'], pathinfo($file['name'], PATHINFO_FILENAME), $err, 1200);
        if ($saved !== '') return $saved;
        $errors[$path] = $f['label'] . ': ' . $err;
    }
    return $current;
}

/* Gallery folders and their labels (same as on the gallery page). */
function gallery_cats() { return gallery_items()[0]; }

/* ---------- Form builder ---------- */

function fid($name) { return 'f_' . trim(preg_replace('/[^a-z0-9]+/i', '_', $name), '_'); }

function render_field($name, $f, $value, $errPath, $errors) {
    $id = fid($name);
    $err = $errors[$errPath] ?? '';
    $type = $f['type'];
    $req = !empty($f['required']) ? ' <span class="req" title="Required">*</span>' : '';
    $ph = !empty($f['placeholder']) ? ' placeholder="' . e($f['placeholder']) . '"' : '';
    $cls = 'field f-' . $type . ($err ? ' has-err' : '');
    $help = (!empty($f['help']) ? '<small class="help">' . e($f['help']) . '</small>' : '') . ($err ? '<small class="err">' . e($err) . '</small>' : '');

    if ($type === 'checkbox') {
        return '<div class="' . $cls . '"><label class="check"><input type="hidden" name="' . e($name) . '" value=""><input type="checkbox" name="' . e($name) . '" value="1"' . ($value ? ' checked' : '') . '> ' . e($f['label']) . '</label>' . $help . '</div>';
    }
    $h = '<div class="' . $cls . '"><label for="' . $id . '">' . e($f['label']) . $req . '</label>';
    switch ($type) {
        case 'textarea': case 'html':
            $h .= '<textarea id="' . $id . '" name="' . e($name) . '" rows="' . ($f['rows'] ?? 3) . '"' . $ph . '>' . e($value) . '</textarea>';
            break;
        case 'lines':
            $h .= '<textarea id="' . $id . '" name="' . e($name) . '" rows="' . max(3, min(10, count((array)$value) + 1)) . '"' . $ph . '>' . e(implode("\n", (array)$value)) . '</textarea>';
            break;
        case 'select':
            $h .= '<select id="' . $id . '" name="' . e($name) . '">';
            foreach (field_options($f) as $k => $l) $h .= '<option value="' . e($k) . '"' . ((string)$k === (string)$value ? ' selected' : '') . '>' . e($l) . '</option>';
            $h .= '</select>';
            break;
        case 'icon':
            $h .= '<span class="icon-pick"><span class="icon-prev">' . icon(isset(icon_paths()[$value]) ? $value : 'check', 22) . '</span><select id="' . $id . '" name="' . e($name) . '" data-icon>';
            foreach (array_keys(icon_paths()) as $k) $h .= '<option' . ($k === $value ? ' selected' : '') . '>' . e($k) . '</option>';
            $h .= '</select></span>';
            break;
        case 'services':
            global $DATA;
            $sel = array_values(array_filter((array)$value, function ($s) use ($DATA) { return isset($DATA['services'][$s]); }));
            $order = array_merge($sel, array_diff(array_keys($DATA['services']), $sel));
            $h .= '<span class="checks">';
            foreach ($order as $s) $h .= '<label class="check"><input type="checkbox" name="' . e($name) . '[]" value="' . e($s) . '"' . (in_array($s, $sel, true) ? ' checked' : '') . '> ' . e($DATA['services'][$s]['title']) . '</label>';
            $h .= '</span>';
            break;
        case 'image':
            $up = 'up[' . str_replace('.', '][', $errPath) . ']';
            $rm = 'rm[' . str_replace('.', '][', $errPath) . ']';
            $h .= '<span class="img-field">';
            if ($value !== '' && is_file(ROOT . '/' . $value)) $h .= '<img src="' . e(asset($value)) . '" alt=""><label class="check"><input type="checkbox" name="' . e($rm) . '" value="1"> Remove photo</label>';
            $h .= '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '"><input id="' . $id . '" type="file" name="' . e($up) . '" accept="image/jpeg,image/png,image/webp"></span>';
            break;
        default:
            $t = ['email' => 'email', 'url' => 'text', 'date' => 'date', 'digits' => 'text'][$type] ?? 'text';
            $extra = $type === 'digits' ? ' inputmode="numeric"' : '';
            if (!empty($f['maxlength'])) $extra .= ' maxlength="' . (int)$f['maxlength'] . '"';
            $h .= '<input id="' . $id . '" type="' . $t . '" name="' . e($name) . '" value="' . e($value) . '"' . $ph . $extra . '>';
    }
    return $h . $help . '</div>';
}

function render_row($key, $f, $i, $row, $errors, $n) {
    $title = trim((string)($row[$f['title_field'] ?? ''] ?? ''));
    $hasErr = (bool)array_filter(array_keys($errors), function ($k) use ($key, $n) { return strpos($k, "$key.$n.") === 0 || $k === "$key.$n"; });
    $h = '<details class="row' . ($hasErr ? ' has-err' : '') . '" data-title-field="' . e($f['title_field'] ?? '') . '"' . ($hasErr || $title === '' ? ' open' : '') . '><summary><span class="row-title">' . ($title !== '' ? e($title) : '<i>New item</i>') . '</span>'
       . '<span class="row-tools"><button type="button" class="ib" data-up title="Move up" aria-label="Move up">&#8593;</button><button type="button" class="ib" data-down title="Move down" aria-label="Move down">&#8595;</button><button type="button" class="ib del" data-del title="Delete" aria-label="Delete">&#10005;</button></span></summary><div class="row-body">';
    foreach ($f['fields'] as $k => $sf) {
        $h .= render_field("f[$key][$i][$k]", $sf, $row[$k] ?? ($sf['type'] === 'services' || $sf['type'] === 'lines' ? [] : ''), "$key.$n.$k", $errors);
    }
    return $h . '</div></details>';
}

/* $asRows: $vals holds form rows already (re-showing a form with errors) instead of stored values. */
function render_section_form($id, $sec, $vals, $errors, $asRows = false) {
    $multipart = strpos(json_encode($sec['fields']), '"image"') !== false ? ' enctype="multipart/form-data"' : '';
    $h = '<form method="post" class="edit" data-dirty' . $multipart . '>' . csrf_field() . '<input type="hidden" name="action" value="save"><input type="hidden" name="section" value="' . e($id) . '">';
    foreach ($sec['fields'] as $key => $f) {
        if ($f['type'] === 'rows') {
            $rows = $asRows ? (array)$vals[$key] : to_rows($f, $vals[$key]);
            $h .= '<fieldset class="rows" data-rows="' . e($key) . '"><legend>' . e($f['label']) . ' <span class="count">(' . count($rows) . ')</span></legend>'
                . (!empty($f['help']) ? '<p class="help">' . e($f['help']) . '</p>' : '')
                . (count($rows) > 3 ? '<p class="row-actions"><button type="button" class="link" data-expand>Expand all</button> · <button type="button" class="link" data-collapse>Collapse all</button></p>' : '')
                . '<div class="row-list">';
            foreach ($rows as $n => $row) $h .= render_row($key, $f, $n, $row, $errors, $n + 1);
            $h .= '</div><template>' . render_row($key, $f, '__i__', [], [], 0) . '</template>'
                . '<button type="button" class="btn btn-light" data-add>+ ' . e($f['add'] ?? 'Add') . '</button></fieldset>';
        } elseif ($f['type'] === 'object') {
            $h .= '<fieldset class="group">' . ($f['label'] !== '' ? '<legend>' . e($f['label']) . '</legend>' : '') . (!empty($f['help']) ? '<p class="help">' . e($f['help']) . '</p>' : '');
            foreach ($f['fields'] as $k => $sf) $h .= render_field("f[$key][$k]", $sf, $vals[$key][$k] ?? '', "$key.$k", $errors);
            $h .= '</fieldset>';
        } else {
            $h .= render_field("f[$key]", $f, $vals[$key], $key, $errors);
        }
    }
    return $h . '<div class="save-bar"><button class="btn btn-gold" type="submit">Save changes</button><span class="dirty-note">You have unsaved changes</span></div></form>';
}
