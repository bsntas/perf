<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'services';
$title = 'Services & Courses';
$desc  = 'Career counseling, medical education, MBBS abroad, Ausbildung Germany, online education, management & IT courses, abroad studies and skill development.';
require __DIR__ . '/includes/header.php';
$brochure = brochure_url();
?>
<?php page_hero('Services &amp; Courses', 'Programs and guidance for every stage of your education and career.'); ?>

<section class="section">
  <div class="wrap">
    <div class="filters" id="filters" role="group" aria-label="Filter services">
      <button type="button" class="chip on" data-f="all" aria-pressed="true">All</button>
      <?php foreach ($DATA['categories'] as $k => $label): ?><button type="button" class="chip" data-f="<?= e($k) ?>" aria-pressed="false"><?= e($label) ?></button><?php endforeach; ?>
    </div>
    <div class="grid g3" id="svc-grid">
      <?php foreach ($DATA['services'] as $slug => $s) echo service_card($slug, $s, true); ?>
    </div>

    <div class="card brochure">
      <div><h3>Not sure which program fits you?</h3><p>Book a free counseling session and we will help you compare options<?= $brochure ? ', or download our brochure for course details and the admission process' : '' ?>.</p></div>
      <div class="hero-btns">
        <?php if ($brochure): ?><a class="btn btn-outline" href="<?= e($brochure) ?>" download><?= icon('download', 18) ?> Download Brochure</a><?php endif; ?>
        <a class="btn btn-navy" href="<?= url('contact') ?>#enquiry">Get Free Counseling</a>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
