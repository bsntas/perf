<?php
/* Local preview only (Hostinger uses public_html/.htaccess instead).
   Run from the repo root:  php -S localhost:8000 -t public_html dev/router.php */
$root = realpath(__DIR__ . '/../public_html');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/(includes|storage)(/|$)|/\.#', $path)) { http_response_code(403); exit('Forbidden'); }
if ($path !== '/' && is_file($root . $path)) {
    if (substr($path, -4) === '.php') { chdir($root); require $root . $path; return true; }
    return false;                                   // static file
}
$slug = trim($path, '/');
$file = $root . '/' . ($slug === '' ? 'index' : $slug) . '.php';
if (preg_match('/^[a-z0-9-]*$/', $slug) && is_file($file)) { chdir($root); require $file; return true; }
require $root . '/404.php';
