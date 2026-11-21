<?php
if (!defined('PERF')) { http_response_code(403); exit; }
/* Expects $page (slug), $title and $desc to be set by the page. */
$nav = ['' => 'Home', 'about' => 'About', 'services' => 'Services', 'gallery' => 'Gallery', 'why-perf' => 'Why PERF', 'faqs' => 'FAQs', 'contact' => 'Contact'];
$canonical = cfg('site_url') . '/' . $page;
$fullTitle = $page === '' ? $title : $title . ' | PERF';
$org = [
    '@context' => 'https://schema.org', '@type' => 'EducationalOrganization',
    'name' => cfg('name'), 'alternateName' => cfg('short'), 'url' => cfg('site_url'),
    'logo' => cfg('site_url') . '/assets/img/logo-full.png', 'slogan' => cfg('tagline'),
    'email' => cfg('email'),
];
if (cfg('phone_intl') !== '') $org['telephone'] = '+' . preg_replace('/\D/', '', cfg('phone_intl'));
if (cfg('address') !== '') $org['address'] = cfg('address');
$same = array_values(array_filter(cfg('social', [])));
if ($same) $org['sameAs'] = $same;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if (STATIC_BUILD): ?><meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta name="theme-color" content="#1c2b6b">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(cfg('name')) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(cfg('site_url')) ?>/assets/img/og-image.jpg">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/png" sizes="32x32" href="<?= asset('assets/img/favicon-32.png') ?>">
<link rel="icon" type="image/png" sizes="128x128" href="<?= asset('assets/img/favicon.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('assets/img/apple-touch-icon.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<script type="application/ld+json"><?= json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php if (cfg('ga_id') !== ''): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(cfg('ga_id')) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e(cfg('ga_id')) ?>');</script>
<?php endif; ?>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<?php if (cfg('phone_display') !== '' || cfg('email') !== ''): ?>
<div class="topbar"><div class="wrap topbar-in">
  <span class="tb-tag"><?= e(cfg('tagline')) ?></span>
  <span class="tb-links">
    <?php if (tel_link()): ?><a href="<?= e(tel_link()) ?>"><?= icon('phone', 14) ?> <?= e(cfg('phone_display')) ?></a><?php endif; ?>
    <?php if (cfg('email') !== ''): ?><a href="mailto:<?= e(cfg('email')) ?>"><?= icon('mail', 14) ?> <?= e(cfg('email')) ?></a><?php endif; ?>
  </span>
</div></div>
<?php endif; ?>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand" href="<?= url() ?>" aria-label="PERF home"><?= logo('mark', 'PERF - Poudyal Education and Research Foundation', 'width="360" height="164"') ?></a>
    <button class="menu-btn" aria-label="Open menu" aria-expanded="false" aria-controls="nav"><span></span><span></span><span></span></button>
    <nav id="nav" class="nav" aria-label="Main">
      <?php foreach ($nav as $slug => $label): ?>
        <a href="<?= url($slug) ?>"<?= $slug === $page ? ' class="active" aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a class="btn btn-gold btn-sm" href="<?= url('contact') ?>#enquiry">Free Counseling</a>
    </nav>
  </div>
</header>
<main id="main">
