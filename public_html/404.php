<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
http_response_code(404);
$page = '404';
$title = 'Page not found';
$desc  = 'The page you are looking for could not be found.';
require __DIR__ . '/includes/header.php';
?>
<section class="section notfound">
  <div class="wrap narrow center">
    <span class="eyebrow">Error 404</span>
    <h1>Page not found</h1>
    <p>The page you are looking for may have moved. Try one of these instead.</p>
    <div class="hero-btns center-btns"><a class="btn btn-gold" href="<?= url() ?>">Go to Home</a><a class="btn btn-outline" href="<?= url('services') ?>">Our Services</a><a class="btn btn-outline" href="<?= url('contact') ?>">Contact Us</a></div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
