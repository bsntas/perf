<?php
/* Enquiry form handler: emails the enquiry and always saves a copy to storage/leads.csv. */
define('PERF', 1);
require __DIR__ . '/includes/functions.php';

$ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
function done($ok, $msg, $code = 200) {
    global $ajax;
    if ($ajax) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $msg]);
    } else {
        header('Location: ' . url('contact') . '?sent=' . ($ok ? '1' : '0') . '#enquiry', true, 303);
    }
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { header('Location: ' . url('contact'), true, 303); exit; }

// Spam checks: hidden honeypot field, and forms submitted faster than a human could type.
if (!empty($_POST['website'])) done(true, 'Thank you!');
$t = (int)($_POST['t'] ?? 0);
if ($t > 0 && time() - $t < 3) done(true, 'Thank you!');

// Rate limit: one enquiry per 30 seconds per IP.
$store = __DIR__ . '/storage';
$rl = $store . '/rl_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
if (is_file($rl) && time() - filemtime($rl) < 30) done(false, 'Please wait a moment before sending again.', 429);

function clean($v, $max) { return mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags((string)$v))), 0, $max); }
$name  = clean($_POST['name'] ?? '', 100);
$phone = clean($_POST['phone'] ?? '', 20);
$email = clean($_POST['email'] ?? '', 120);
$role  = clean($_POST['role'] ?? '', 40);
$city  = clean($_POST['city'] ?? '', 60);
$slug  = clean($_POST['interest'] ?? '', 60);
$int   = $DATA['services'][$slug]['title'] ?? 'Something else';
$msg   = mb_substr(trim(strip_tags((string)($_POST['message'] ?? ''))), 0, 1500);

if ($name === '') done(false, 'Please enter your name.', 422);
if (!preg_match('/^[0-9+()\-\s]{7,20}$/', $phone) || strlen(preg_replace('/\D/', '', $phone)) < 7) done(false, 'Please enter a valid phone number.', 422);
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) done(false, 'Please enter a valid email address.', 422);

$when = date('Y-m-d H:i:s');

// 1) Save to CSV (prefix formula characters so the file is safe to open in Excel).
$saved = false;
if (is_dir($store) && is_writable($store)) {
    $csv = $store . '/leads.csv';
    $new = !is_file($csv);
    if ($fh = @fopen($csv, 'a')) {
        flock($fh, LOCK_EX);
        if ($new) fputcsv($fh, ['Date', 'Name', 'Phone', 'Email', 'Role', 'City', 'Interested in', 'Message', 'IP']);
        $row = array_map(function ($v) { return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v; },
            [$when, $name, $phone, $email, $role, $city, $int, $msg, $_SERVER['REMOTE_ADDR'] ?? '']);
        $saved = fputcsv($fh, $row) !== false;
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}

// 2) Email the enquiry.
$to   = cfg('form_to');
$from = cfg('form_from');
$body = "New enquiry from the PERF website\n\n"
      . "Name:          $name\nPhone:         $phone\nEmail:         $email\nI am a:        $role\nCity:          $city\nInterested in: $int\n"
      . "Received:      $when\n\nMessage:\n" . ($msg !== '' ? $msg : '-') . "\n";
$headers = "From: PERF Website <$from>\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nMIME-Version: 1.0\r\n";
if ($email !== '') $headers .= "Reply-To: $email\r\n";
$subject = mb_encode_mimeheader("New enquiry: $name - $int", 'UTF-8');
$mailed = $to !== '' && function_exists('mail') && @mail($to, $subject, $body, $headers, '-f' . $from);

if ($mailed || $saved) {
    @touch($rl);
    done(true, 'Thank you! Our team will contact you shortly.');
}
done(false, 'Sorry, we could not send your enquiry. Please call or WhatsApp us.', 500);
