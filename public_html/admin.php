<?php
/* Admin panel: https://perf.org.in/admin
   Edits contact details, events, home page content, services, FAQs, gallery photos and more.
   Changes are saved to storage/settings.json (see includes/admin/schema.php for what can be edited).
   Forgot the password? Delete storage/admin.json in hPanel File Manager and open /admin again. */
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
if (STATIC_BUILD) exit;
require __DIR__ . '/includes/admin/lib.php';
$SECTIONS = require __DIR__ . '/includes/admin/schema.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'none'");
admin_session_start();

$s = (string)($_GET['s'] ?? '');
$sec = $SECTIONS[$s] ?? null;
$post = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
$action = (string)($_POST['action'] ?? '');
$error = '';        // message shown on the login / setup screens
$errors = [];       // field problems on an edit screen
$formVals = null;   // values to show again when a save had problems

if ($post && !csrf_ok()) {
    flash('Your session expired, so nothing was saved. Please try again.', 'err');
    go($s);
}

/* ---------- First-time setup ---------- */
if (!admin_exists()) {
    $code = setup_code();
    if ($post && $action === 'setup') {
        $pw = (string)($_POST['password'] ?? '');
        if (login_locked()) $error = 'Too many wrong attempts. Please wait 15 minutes.';
        elseif ($code === '' || !hash_equals($code, strtoupper(trim((string)($_POST['code'] ?? ''))))) { login_failed(); $error = 'The setup code is not correct.'; }
        elseif ($p = password_problem($pw, (string)($_POST['password2'] ?? ''))) $error = $p;
        elseif (!set_password($pw)) $error = 'The password could not be saved. Make sure the storage folder is writable.';
        else {
            @unlink(SETUP_CODE_FILE);
            login_failed(true);
            login_as_admin();
            flash('Welcome! Your admin password is set. Everything below already shows the current website content.');
            go();
        }
    }
    auth_page('Set up the admin panel', function () use ($code, $error) { ?>
        <p>Create the password for the PERF admin panel.</p>
        <?php if ($code === ''): ?>
            <p class="alert err">The <code>storage</code> folder is not writable. In hPanel File Manager, set the permissions of <code>public_html/storage</code> to 755 and reload this page.</p>
        <?php else: ?>
            <p class="alert info">To prove you manage this website, open <b>hPanel → Files → File Manager</b>, go to <code>public_html/storage/</code> and open <code>admin-setup-code.txt</code>. Enter the code from that file below.</p>
            <?php if ($error): ?><p class="alert err"><?= e($error) ?></p><?php endif; ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="setup">
                <div class="field"><label for="code">Setup code</label><input id="code" name="code" required autocomplete="off" autocapitalize="characters"></div>
                <div class="field"><label for="pw">New password (at least 10 characters)</label><input id="pw" type="password" name="password" required minlength="10" autocomplete="new-password"></div>
                <div class="field"><label for="pw2">Repeat password</label><input id="pw2" type="password" name="password2" required minlength="10" autocomplete="new-password"></div>
                <button class="btn btn-gold wide" type="submit">Create password</button>
            </form>
        <?php endif;
    });
}

/* ---------- Login ---------- */
if (!logged_in()) {
    if ($post && $action === 'login') {
        if (login_locked()) $error = 'Too many wrong attempts. Please wait 15 minutes and try again.';
        elseif (password_verify((string)($_POST['password'] ?? ''), read_json(ADMIN_FILE)['hash'])) {
            login_failed(true);
            login_as_admin();
            go($s);
        } else { login_failed(); $error = 'Wrong password.'; }
    }
    auth_page('Admin login', function () use ($error) { ?>
        <?php foreach ($_SESSION['flash'] ?? [] as [$t, $m]): ?><p class="alert <?= e($t) ?>"><?= e($m) ?></p><?php endforeach; unset($_SESSION['flash']); ?>
        <?php if ($error): ?><p class="alert err"><?= e($error) ?></p><?php endif; ?>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="login">
            <div class="field"><label for="pw">Password</label><input id="pw" type="password" name="password" required autofocus autocomplete="current-password"></div>
            <button class="btn btn-gold wide" type="submit">Log in</button>
        </form>
        <p class="help">Forgot the password? Delete <code>public_html/storage/admin.json</code> in hPanel File Manager and open this page again to set a new one. Website content is not affected.</p>
    <?php });
}

/* ---------- Downloads ---------- */
if (isset($_GET['download'])) {
    if ($_GET['download'] === 'leads' && is_file(STORE . '/leads.csv')) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="perf-enquiries-' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF";   // so Excel reads special characters correctly
        readfile(STORE . '/leads.csv');
        exit;
    }
    if ($_GET['download'] === 'backup') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="perf-settings-' . date('Y-m-d') . '.json"');
        echo json_encode(read_json(SETTINGS_FILE) ?: new stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    go($s);
}

/* ---------- Actions ---------- */
if ($post) {
    if (!store_writable()) { flash('The storage folder is not writable, so changes cannot be saved. Set its permissions to 755 in hPanel File Manager.', 'err'); go($s); }

    switch ($action) {
    case 'logout':
        $_SESSION = [];
        session_regenerate_id(true);
        flash('You have been logged out.', 'info');
        go();

    case 'save':
        if (!$sec || empty($sec['fields'])) go();
        [$vals, $errors, $formVals] = section_from_post($sec);
        if ($errors) break;   // show the form again with the problems marked
        $ok = settings_update(function ($set) use ($sec, $vals) {
            foreach ($vals as $k => $v) $set[$sec['store']][$k] = $v;
            return $set;
        });
        flash($ok ? $sec['title'] . ' saved. The website is updated.' : 'Sorry, the changes could not be saved.', $ok ? 'ok' : 'err');
        if ($ok && $s === 'categories') {
            $orphans = array_filter($DATA['services'], function ($sv) use ($vals) { return !isset($vals['categories'][$sv['cat'] ?? '']); });
            if ($orphans) flash('These services are in a category that no longer exists, please choose a new one under Services: ' . implode(', ', array_column($orphans, 'title')), 'info');
        }
        go($s);

    case 'reset':
        if (!$sec || empty($sec['fields'])) go();
        settings_update(function ($set) use ($sec) {
            foreach (array_keys($sec['fields']) as $k) unset($set[$sec['store']][$k]);
            return $set;
        });
        flash($sec['title'] . ' restored to the original content.');
        go($s);

    case 'gallery_upload':
        $cat = (string)($_POST['cat'] ?? '');
        if (!isset(gallery_cats()[$cat])) { flash('Please choose a gallery category.', 'err'); go('gallery'); }
        $files = $_FILES['photos'] ?? null;
        $caption = one_line($_POST['caption'] ?? '', 80);
        $done = 0;
        $fails = [];
        foreach ((array)($files['name'] ?? []) as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
            $file = ['name' => $name, 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]];
            $err = '';
            if (save_image($file, "assets/img/gallery/$cat", $caption !== '' ? $caption : pathinfo($name, PATHINFO_FILENAME), $err) !== '') $done++;
            else $fails[] = "$name: $err";
        }
        if ($done) flash("$done photo" . ($done > 1 ? 's' : '') . ' added to the gallery.');
        foreach ($fails as $f) flash($f, 'err');
        if (!$done && !$fails) flash('Please choose one or more photos to upload.', 'err');
        go('gallery');

    case 'gallery_rename':
    case 'gallery_delete':
        $src = (string)($_POST['src'] ?? '');
        $ok = false;
        if (preg_match('#^assets/img/gallery/([a-z]+)/([\w.-]+\.(jpe?g|png|webp))$#i', $src, $m) && isset(gallery_cats()[$m[1]]) && is_file(__DIR__ . "/$src")) {
            if ($action === 'gallery_delete') {
                $ok = @unlink(__DIR__ . "/$src");
                flash($ok ? 'Photo deleted.' : 'The photo could not be deleted.', $ok ? 'ok' : 'err');
            } else {
                $base = file_name(one_line($_POST['caption'] ?? '', 80)) ?: 'photo';
                $ext = strtolower($m[3]);
                $dir = __DIR__ . "/assets/img/gallery/$m[1]";
                $target = "$dir/$base.$ext";
                for ($i = 2; file_exists($target) && realpath($target) !== realpath(__DIR__ . "/$src"); $i++) $target = "$dir/$base-$i.$ext";
                $mtime = filemtime(__DIR__ . "/$src");
                $ok = @rename(__DIR__ . "/$src", $target);
                if ($ok) @touch($target, $mtime);   // keep its place in the newest-first order
                flash($ok ? 'Caption updated.' : 'The caption could not be changed.', $ok ? 'ok' : 'err');
            }
        }
        go('gallery');

    case 'brochure_upload':
        $f = $_FILES['brochure'] ?? null;
        $dir = __DIR__ . '/assets/brochure';
        if (!$f || $f['error'] !== UPLOAD_ERR_OK) flash('Upload failed: ' . upload_error_text($f['error'] ?? UPLOAD_ERR_NO_FILE), 'err');
        elseif ($f['size'] > 25 * 1024 * 1024) flash('The brochure must be smaller than 25 MB.', 'err');
        elseif (file_get_contents($f['tmp_name'], false, null, 0, 5) !== '%PDF-') flash('Please upload a PDF file.', 'err');
        elseif ((!is_dir($dir) && !@mkdir($dir, 0755, true)) || !move_uploaded_file($f['tmp_name'], "$dir/PERF-Brochure.pdf")) flash('The brochure could not be saved.', 'err');
        else { @chmod("$dir/PERF-Brochure.pdf", 0644); flash('Brochure uploaded. The download button is now on the services page.'); }
        go('brochure');

    case 'brochure_delete':
        @unlink(__DIR__ . '/assets/brochure/PERF-Brochure.pdf');
        flash('Brochure removed.');
        go('brochure');

    case 'password':
        if (!password_verify((string)($_POST['current'] ?? ''), read_json(ADMIN_FILE)['hash'])) flash('The current password is wrong.', 'err');
        elseif ($p = password_problem((string)($_POST['password'] ?? ''), (string)($_POST['password2'] ?? ''))) flash($p, 'err');
        elseif (!set_password((string)$_POST['password'])) flash('The password could not be saved.', 'err');
        else { login_as_admin(); flash('Password changed.'); }
        go('account');

    case 'restore':
        $file = basename((string)($_POST['file'] ?? ''));
        if (in_array($file, history_list(), true)) {
            $old = read_json(HISTORY_DIR . "/$file");
            settings_update(function () use ($old) { return $old; });
            flash('The earlier version has been restored.');
        }
        go('account');

    case 'import':
        $f = $_FILES['backup'] ?? null;
        $data = $f && $f['error'] === UPLOAD_ERR_OK && $f['size'] < 2 * 1024 * 1024 ? json_decode((string)file_get_contents($f['tmp_name']), true) : null;
        if (!is_array($data) || array_diff(array_keys($data), ['config', 'data', 'updated'])) { flash('That file is not a PERF settings backup.', 'err'); go('account'); }
        // Check the backup with the same rules as the edit pages; unknown settings are dropped.
        $clean = [];
        $problems = [];
        // Categories and services first: other sections are checked against them.
        $order = array_merge(['categories' => 0, 'services' => 0], $SECTIONS);
        foreach ($order as $id => $_) {
            $x = $SECTIONS[$id];
            if (empty($x['fields'])) continue;
            $part = array_intersect_key((array)($data[$x['store']] ?? []), $x['fields']);
            if (!$part) continue;
            [$vals, $errs] = clean_stored($x, $part);
            $problems = array_merge($problems, array_values($errs));
            foreach (array_keys($part) as $k) {
                $clean[$x['store']][$k] = $vals[$k];
                if ($x['store'] === 'data') $DATA[$k] = $vals[$k];
            }
        }
        if ($problems) { flash('The backup was not restored because of these problems: ' . implode(' ', array_slice($problems, 0, 5)), 'err'); go('account'); }
        settings_update(function () use ($clean) { return $clean; });
        flash('Backup restored.');
        go('account');

    case 'reset_all':
        settings_update(function () { return []; });
        flash('All content has been restored to the original. Earlier versions are still listed below.');
        go('account');
    }
}

/* ---------- Screens ---------- */

function auth_page($title, $body) { ?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | PERF</title><meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" href="<?= asset('assets/img/favicon-32.png') ?>"><link rel="stylesheet" href="<?= asset('assets/admin/admin.css') ?>"></head>
<body class="auth"><main class="auth-box"><img class="auth-logo" src="<?= asset('assets/img/logo-full.png') ?>" alt="PERF" width="180"><h1><?= e($title) ?></h1><?php $body(); ?>
<p class="back"><a href="<?= url() ?>">&larr; Back to the website</a></p></main></body></html>
<?php exit; }

$flashes = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$menu = [];
foreach ($SECTIONS as $id => $x) $menu[$x['menu']][$id] = $x;
$saved = read_json(SETTINGS_FILE);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($sec['title'] ?? 'Dashboard') ?> | PERF admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" href="<?= asset('assets/img/favicon-32.png') ?>">
<link rel="stylesheet" href="<?= asset('assets/admin/admin.css') ?>">
</head>
<body>
<header class="top">
  <button class="menu-btn" type="button" aria-label="Menu" aria-controls="side" aria-expanded="false" data-menu>&#9776;</button>
  <a class="brand" href="<?= url('admin') ?>"><img src="<?= asset('assets/img/logo-mark.png') ?>" alt="" height="34"> <span>Admin</span></a>
  <span class="top-r">
    <a class="btn btn-light btn-sm" href="<?= url() ?>" target="_blank" rel="noopener">View website &#8599;</a>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button class="btn btn-light btn-sm" type="submit">Log out</button></form>
  </span>
</header>
<div class="layout">
<nav class="side" id="side" aria-label="Admin">
  <a href="<?= url('admin') ?>"<?= $s === '' ? ' class="on"' : '' ?>><?= icon('compass', 18) ?> Dashboard</a>
  <?php foreach ($menu as $group => $items): ?>
    <h4><?= e($group) ?></h4>
    <?php foreach ($items as $id => $x): ?><a href="<?= url('admin') ?>?s=<?= e($id) ?>"<?= $s === $id ? ' class="on"' : '' ?>><?= icon($x['icon'], 18) ?> <?= e($x['title']) ?></a><?php endforeach; ?>
  <?php endforeach; ?>
</nav>
<main class="main">
<?php foreach ($flashes as [$t, $m]): ?><p class="alert <?= e($t) ?>"><?= e($m) ?></p><?php endforeach; ?>
<?php if (!store_writable()): ?><p class="alert err">The <code>storage</code> folder is not writable, so changes cannot be saved. In hPanel File Manager set the permissions of <code>public_html/storage</code> to 755.</p><?php endif; ?>

<?php if (!$sec): /* ---------- Dashboard ---------- */
    $upcoming = array_filter(cfg('events', []), function ($ev) { return empty($ev['date']) || strtotime($ev['date']) >= strtotime('today'); });
    $leadCount = is_file(STORE . '/leads.csv') ? max(0, count(file(STORE . '/leads.csv', FILE_SKIP_EMPTY_LINES)) - 1) : 0;
    $photos = count(gallery_items()[1]);
    $missing = [];
    foreach (['phone_display' => 'phone number', 'whatsapp_intl' => 'WhatsApp number', 'address' => 'office address'] as $k => $l) if (cfg($k) === '') $missing[] = $l;
?>
  <h1>Welcome to the PERF admin panel</h1>
  <p class="lead">Choose what to update from the menu. Changes appear on the website as soon as you save.</p>
  <?php if ($missing): ?><p class="alert info">Not filled in yet: <b><?= e(implode(', ', $missing)) ?></b>. <a href="<?= url('admin') ?>?s=contact">Add contact details &rarr;</a></p><?php endif; ?>
  <div class="tiles">
    <a class="tile" href="<?= url('admin') ?>?s=events"><?= icon('clock', 26) ?><b><?= count($upcoming) ?></b><span>Upcoming events</span></a>
    <a class="tile" href="<?= url('admin') ?>?s=leads"><?= icon('mail', 26) ?><b><?= $leadCount ?></b><span>Enquiries received</span></a>
    <a class="tile" href="<?= url('admin') ?>?s=gallery"><?= icon('star', 26) ?><b><?= $photos ?></b><span>Gallery photos</span></a>
    <a class="tile" href="<?= url('admin') ?>?s=testimonials"><?= icon('users', 26) ?><b><?= count(cfg('testimonials', [])) ?></b><span>Testimonials</span></a>
  </div>
  <h2>Quick links</h2>
  <div class="quick">
    <?php foreach (['events' => 'Add or change an event', 'announcement' => 'Show an announcement', 'contact' => 'Edit phone, email & address', 'gallery' => 'Upload photos', 'faqs' => 'Edit FAQs', 'services' => 'Edit services'] as $id => $label): ?>
      <a class="card-link" href="<?= url('admin') ?>?s=<?= e($id) ?>"><?= icon($SECTIONS[$id]['icon'], 20) ?> <?= e($label) ?> <span>&rarr;</span></a>
    <?php endforeach; ?>
  </div>
  <p class="help"><?= !empty($saved['updated']) ? 'Last change saved ' . e(date('j M Y, g:i a', strtotime($saved['updated']))) . '.' : 'No changes saved yet: the website shows its original content.' ?></p>

<?php elseif (!empty($sec['fields'])): /* ---------- Edit a section ---------- */
    $customised = array_intersect_key($saved[$sec['store']] ?? [], $sec['fields']);
?>
  <h1><?= e($sec['title']) ?></h1>
  <?php if (!empty($sec['intro'])): ?><p class="lead"><?= e($sec['intro']) ?></p><?php endif; ?>
  <?php if ($errors): ?><div class="alert err"><b>Nothing was saved yet. Please correct the following:</b><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <?= render_section_form($s, $sec, $formVals ?? section_values($sec), $errors, $formVals !== null) ?>
  <?php if ($customised): ?>
    <form method="post" class="reset" data-confirm="Undo all changes on this page and go back to the original content?"><?= csrf_field() ?><input type="hidden" name="action" value="reset"><input type="hidden" name="section" value="<?= e($s) ?>">
      <button class="link danger" type="submit">Restore the original content for this page</button>
    </form>
  <?php endif; ?>

<?php elseif ($sec['page'] === 'gallery'): /* ---------- Gallery ---------- */
    [$cats, $items] = gallery_items();
?>
  <h1>Gallery photos</h1>
  <p class="lead">Photos appear on the gallery page, newest first. The caption is shown under each photo.</p>
  <form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="action" value="gallery_upload">
    <h2 class="h3">Upload photos</h2>
    <div class="grid2">
      <div class="field"><label for="g-cat">Category</label><select id="g-cat" name="cat"><?php foreach ($cats as $k => $l): ?><option value="<?= e($k) ?>"><?= e($l) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label for="g-cap">Caption (optional)</label><input id="g-cap" name="caption" maxlength="80" placeholder="e.g. NEET topper 2026 - Asha"><small class="help">Leave empty to use each file's name.</small></div>
    </div>
    <div class="field"><label for="g-files">Photos (JPG, PNG or WebP; you can choose several)</label><input id="g-files" type="file" name="photos[]" multiple required accept="image/jpeg,image/png,image/webp"><small class="help">Large photos are resized automatically.</small></div>
    <button class="btn btn-gold" type="submit">Upload</button>
  </form>
  <?php foreach ($cats as $k => $l): $list = array_filter($items, function ($i) use ($k) { return $i['cat'] === $k; }); ?>
    <h2><?= e($l) ?> <span class="count">(<?= count($list) ?>)</span></h2>
    <?php if (!$list): ?><p class="help">No photos yet.</p><?php else: ?>
    <div class="photos">
      <?php foreach ($list as $it): ?>
        <figure>
          <img src="<?= e(asset($it['src'])) ?>" alt="" loading="lazy">
          <form method="post" class="cap"><?= csrf_field() ?><input type="hidden" name="action" value="gallery_rename"><input type="hidden" name="src" value="<?= e($it['src']) ?>">
            <input name="caption" value="<?= e($it['caption']) ?>" aria-label="Caption" maxlength="80"><button class="ib" type="submit" title="Save caption" aria-label="Save caption">&#10003;</button>
          </form>
          <form method="post" data-confirm="Delete this photo?"><?= csrf_field() ?><input type="hidden" name="action" value="gallery_delete"><input type="hidden" name="src" value="<?= e($it['src']) ?>"><button class="link danger" type="submit">Delete</button></form>
        </figure>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  <?php endforeach; ?>

<?php elseif ($sec['page'] === 'brochure'): /* ---------- Brochure ---------- */
    $b = __DIR__ . '/assets/brochure/PERF-Brochure.pdf';
?>
  <h1>Brochure (PDF)</h1>
  <p class="lead">When a brochure is uploaded, a "Download brochure" button appears on the services page.</p>
  <div class="panel">
    <?php if (is_file($b)): ?>
      <p><?= icon('check', 18) ?> Current brochure: <a href="<?= e(asset('assets/brochure/PERF-Brochure.pdf')) ?>" target="_blank" rel="noopener">PERF-Brochure.pdf</a> (<?= round(filesize($b) / 1048576, 1) ?> MB, uploaded <?= e(date('j M Y', filemtime($b))) ?>)</p>
    <?php else: ?><p>No brochure uploaded yet.</p><?php endif; ?>
    <form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="brochure_upload">
      <div class="field"><label for="br"><?= is_file($b) ? 'Replace with a new PDF' : 'Choose a PDF' ?> (max 25 MB)</label><input id="br" type="file" name="brochure" accept="application/pdf" required></div>
      <button class="btn btn-gold" type="submit">Upload</button>
    </form>
    <?php if (is_file($b)): ?>
      <form method="post" class="reset" data-confirm="Remove the brochure from the website?"><?= csrf_field() ?><input type="hidden" name="action" value="brochure_delete"><button class="link danger" type="submit">Remove brochure</button></form>
    <?php endif; ?>
  </div>

<?php elseif ($sec['page'] === 'leads'): /* ---------- Enquiries ---------- */
    $rows = [];
    if (is_file(STORE . '/leads.csv') && ($fh = fopen(STORE . '/leads.csv', 'r'))) {
        $head = fgetcsv($fh);
        while (($r = fgetcsv($fh)) !== false) if (count($r) > 1) $rows[] = $r;
        fclose($fh);
    }
    $rows = array_reverse($rows);
?>
  <h1>Enquiries</h1>
  <p class="lead">Every enquiry sent through the website forms, newest first. They are also emailed to <b><?= e(cfg('form_to')) ?></b>.</p>
  <?php if (!$rows): ?><p class="panel">No enquiries yet.</p><?php else: ?>
    <p class="toolbar"><a class="btn btn-gold btn-sm" href="<?= url('admin') ?>?s=leads&amp;download=leads"><?= icon('download', 16) ?> Download all for Excel (<?= count($rows) ?>)</a>
      <input type="search" placeholder="Search enquiries" data-filter="#leads" aria-label="Search enquiries"></p>
    <div class="table-wrap"><table id="leads">
      <thead><tr><?php foreach (array_slice($head, 0, 8) as $h): ?><th><?= e($h) ?></th><?php endforeach; ?></tr></thead>
      <tbody><?php foreach (array_slice($rows, 0, 500) as $r): ?><tr><?php foreach (array_slice($r, 0, 8) as $i => $c): $c = ltrim($c, "'"); ?><td><?php
        if ($i === 2 && $c !== '') echo '<a href="tel:' . e(preg_replace('/[^\d+]/', '', $c)) . '">' . e($c) . '</a>';
        elseif ($i === 3 && $c !== '') echo '<a href="mailto:' . e($c) . '">' . e($c) . '</a>';
        else echo e($c); ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
    </table></div>
    <?php if (count($rows) > 500): ?><p class="help">Showing the latest 500. Download the file to see all.</p><?php endif; ?>
  <?php endif; ?>

<?php elseif ($sec['page'] === 'account'): /* ---------- Password & backups ---------- */ ?>
  <h1>Password &amp; backups</h1>
  <div class="panel">
    <h2 class="h3">Change password</h2>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="password">
      <div class="field"><label for="cur">Current password</label><input id="cur" type="password" name="current" required autocomplete="current-password"></div>
      <div class="grid2">
        <div class="field"><label for="np">New password (at least 10 characters)</label><input id="np" type="password" name="password" required minlength="10" autocomplete="new-password"></div>
        <div class="field"><label for="np2">Repeat new password</label><input id="np2" type="password" name="password2" required minlength="10" autocomplete="new-password"></div>
      </div>
      <button class="btn btn-gold" type="submit">Change password</button>
    </form>
  </div>
  <div class="panel">
    <h2 class="h3">Earlier versions</h2>
    <p class="help">A copy is kept each time something is saved (the last <?= HISTORY_KEEP ?> changes). Restoring a version brings back all content as it was at that time.</p>
    <?php $hist = history_list(); if (!$hist): ?><p>No earlier versions yet.</p><?php else: ?>
      <ul class="hist"><?php foreach ($hist as $f): $t = DateTime::createFromFormat('Ymd-His', substr($f, 9, 15)); ?>
        <li><span>Before the change at <?= $t ? e($t->format('j M Y, g:i:s a')) : e($f) ?></span>
          <form method="post" data-confirm="Restore all content to this version?"><?= csrf_field() ?><input type="hidden" name="action" value="restore"><input type="hidden" name="file" value="<?= e($f) ?>"><button class="btn btn-light btn-sm" type="submit">Restore</button></form></li>
      <?php endforeach; ?></ul>
    <?php endif; ?>
  </div>
  <div class="panel">
    <h2 class="h3">Backup file</h2>
    <p class="help">Download all content you changed in the admin panel as one file, to keep a copy or move it to another server.</p>
    <p><a class="btn btn-light btn-sm" href="<?= url('admin') ?>?s=account&amp;download=backup"><?= icon('download', 16) ?> Download backup</a></p>
    <form method="post" enctype="multipart/form-data" data-confirm="Replace all current content with this backup?"><?= csrf_field() ?><input type="hidden" name="action" value="import">
      <div class="field"><label for="bk">Restore from a backup file</label><input id="bk" type="file" name="backup" accept="application/json,.json" required></div>
      <button class="btn btn-light btn-sm" type="submit">Restore backup</button>
    </form>
  </div>
  <div class="panel">
    <h2 class="h3">Start over</h2>
    <p class="help">Undo every change made in the admin panel and show the original content again. Photos and the brochure are not affected.</p>
    <form method="post" data-confirm="Undo ALL changes made in the admin panel?"><?= csrf_field() ?><input type="hidden" name="action" value="reset_all"><button class="btn btn-danger btn-sm" type="submit">Restore all original content</button></form>
  </div>
<?php endif; ?>
</main>
</div>
<template id="icons"><?php foreach (array_keys(icon_paths()) as $k): ?><span data-name="<?= e($k) ?>"><?= icon($k, 22) ?></span><?php endforeach; ?></template>
<script src="<?= asset('assets/admin/admin.js') ?>"></script>
</body>
</html>
