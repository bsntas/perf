<?php
if (!defined('PERF')) { http_response_code(403); exit; }

date_default_timezone_set('Asia/Kolkata');

$CFG  = require __DIR__ . '/config.php';
$DATA = require __DIR__ . '/data.php';

function cfg($key, $default = '') { global $CFG; return $CFG[$key] ?? $default; }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Static build (GitHub Pages preview): PERF_STATIC=1 renders plain HTML, PERF_BASE is the
   path the site is served from, e.g. '/perf/'. On Hostinger neither is set. */
define('STATIC_BUILD', getenv('PERF_STATIC') === '1');
define('BASE', '/' . trim((string)getenv('PERF_BASE'), '/') . (trim((string)getenv('PERF_BASE'), '/') === '' ? '' : '/'));

/* Clean URL for a page: url('about') -> /about, url('') -> / */
function url($page = '') { return BASE . ltrim($page, '/'); }

/* Asset URL with cache-busting version so browsers pick up edits immediately. */
function asset($path) {
    $file = dirname(__DIR__) . '/' . $path;
    return BASE . $path . (is_file($file) ? '?v=' . filemtime($file) : '');
}

function wa_link($text = null) {
    $n = preg_replace('/\D/', '', cfg('whatsapp_intl'));
    if ($n === '') return '';
    return 'https://wa.me/' . $n . '?text=' . rawurlencode($text ?? cfg('whatsapp_text'));
}
function tel_link() { $n = preg_replace('/\D/', '', cfg('phone_intl')); return $n === '' ? '' : 'tel:+' . $n; }

function map_url() {
    if (cfg('map_embed_url') !== '') return cfg('map_embed_url');
    if (cfg('address') === '') return '';
    return 'https://www.google.com/maps?q=' . rawurlencode(cfg('address')) . '&output=embed';
}

/* Responsive logo using WebP with PNG fallback. */
function logo($which, $alt, $attrs = '') {
    return '<picture><source srcset="' . asset("assets/img/logo-$which.webp") . '" type="image/webp">'
         . '<img src="' . asset("assets/img/logo-$which.png") . '" alt="' . e($alt) . '" ' . $attrs . '></picture>';
}

/* Minimal inline icon set (stroke icons), so no icon font or extra requests are needed. */
function icon($name, $size = 24) {
    static $p = [
        'compass'     => '<circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36z"/>',
        'stethoscope' => '<path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/>',
        'globe'       => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'heart'       => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/>',
        'briefcase'   => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'laptop'      => '<path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/>',
        'chart'       => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>',
        'plane'       => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
        'tools'       => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'grad'        => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
        'users'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'link'        => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
        'book'        => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>',
        'shield'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'phone'       => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'mail'        => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'pin'         => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'clock'       => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'check'       => '<path d="M20 6 9 17l-5-5"/>',
        'download'    => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/>',
        'arrow'       => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    ];
    return '<svg class="i" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

function wa_svg($size = 28) {
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35M12.05 21.5h-.01a9.4 9.4 0 0 1-4.8-1.31l-.34-.2-3.56.93.95-3.47-.22-.36a9.4 9.4 0 0 1-1.44-5.01c0-5.2 4.23-9.43 9.44-9.43 2.52 0 4.89.98 6.67 2.77a9.37 9.37 0 0 1 2.76 6.67c0 5.2-4.24 9.43-9.44 9.43m8.03-17.46A11.27 11.27 0 0 0 12.05.72C5.8.72.7 5.81.7 12.07c0 2 .52 3.95 1.52 5.67L.6 23.62l6.02-1.58a11.3 11.3 0 0 0 5.42 1.38h.01c6.25 0 11.35-5.09 11.35-11.35 0-3.03-1.18-5.88-3.32-8.02"/></svg>';
}

/* Gallery: reads photos straight from assets/img/gallery/<category>/ so PERF only needs to upload images.
   The caption is taken from the file name: "neet-topper-2025_asha.jpg" -> "Neet topper 2025 asha". */
function gallery_items() {
    $cats = ['achievements' => 'Student achievements', 'events' => 'Events & workshops', 'awards' => 'Awards & recognitions', 'international' => 'International exposure'];
    $items = [];
    foreach ($cats as $slug => $label) {
        $files = glob(dirname(__DIR__) . "/assets/img/gallery/$slug/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}", GLOB_BRACE) ?: [];
        foreach ($files as $f) {
            $cap = ucfirst(trim(preg_replace('/[-_]+/', ' ', pathinfo($f, PATHINFO_FILENAME))));
            $items[] = ['cat' => $slug, 'src' => "assets/img/gallery/$slug/" . basename($f), 'caption' => $cap, 'time' => filemtime($f)];
        }
    }
    usort($items, function ($a, $b) { return $b['time'] <=> $a['time']; });
    return [$cats, $items];
}

function brochure_url() { return is_file(dirname(__DIR__) . '/assets/brochure/PERF-Brochure.pdf') ? asset('assets/brochure/PERF-Brochure.pdf') : ''; }

/* Page hero used by inner pages. */
function page_hero($title, $sub) {
    echo '<section class="page-hero"><div class="wrap"><h1>' . $title . '</h1><p>' . $sub . '</p></div></section>';
}

/* Service card. $full adds the bullet list (services page). */
function service_card($slug, $s, $full = false) {
    global $DATA;
    $h = '<article class="card svc" id="' . e($slug) . '" data-cat="' . e($s['cat']) . '">'
       . '<div class="svc-top"><span class="svc-ico">' . icon($s['icon'], 26) . '</span><span class="tag">' . e($DATA['categories'][$s['cat']]) . '</span></div>'
       . '<h3>' . e($s['title']) . '</h3><p>' . e($s['text']) . '</p>';
    if ($full) {
        $h .= '<ul class="ticks">';
        foreach ($s['points'] as $pt) $h .= '<li>' . icon('check', 16) . e($pt) . '</li>';
        $h .= '</ul>';
    }
    $h .= '<a class="more" href="' . url('contact') . '?interest=' . e($slug) . '#enquiry">Enquire now ' . icon('arrow', 16) . '</a></article>';
    return $h;
}
