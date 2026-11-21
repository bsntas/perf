<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = '';
$title = 'PERF | Poudyal Education & Research Foundation';
$desc  = 'PERF offers career counseling, MBBS abroad, medical admissions, Ausbildung in Germany, abroad studies, online education and skill development programs.';
require __DIR__ . '/includes/header.php';
$stats = cfg('stats', []);
$testimonials = cfg('testimonials', []);
?>
<section class="hero">
  <div class="wrap hero-in">
    <div class="hero-text">
      <span class="eyebrow"><?= e(cfg('tagline')) ?></span>
      <h1>Your global education and career journey <span class="hl">starts here</span></h1>
      <p>PERF guides students and parents through career counseling, medical admissions, studies abroad and skill development, with honest advice at every step.</p>
      <div class="hero-btns">
        <a class="btn btn-gold" href="<?= url('contact') ?>#enquiry">Book Free Counseling</a>
        <a class="btn btn-outline" href="<?= url('services') ?>">Explore Services</a>
      </div>
      <ul class="hero-points">
        <li><?= icon('check', 18) ?> Free first counseling session</li>
        <li><?= icon('check', 18) ?> India &amp; abroad programs</li>
        <li><?= icon('check', 18) ?> End-to-end admission support</li>
      </ul>
    </div>
    <div class="hero-art"><?= logo('full', 'Poudyal Education and Research Foundation - Education, Employment, Empowerment', 'width="720" height="480" fetchpriority="high"') ?></div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">What we do</span><h2>Key services</h2><p>From choosing the right course to settling in abroad, we support you at every stage.</p></div>
    <div class="grid g3">
      <?php foreach ($DATA['services'] as $slug => $s) if (!empty($s['home'])) echo service_card($slug, $s); ?>
    </div>
    <p class="center"><a class="btn btn-navy" href="<?= url('services') ?>">View all services</a></p>
  </div>
</section>

<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Why PERF</span><h2>Why students and parents choose us</h2></div>
    <div class="grid g3">
      <?php foreach ($DATA['why'] as $w): ?>
        <div class="card ic"><span class="svc-ico"><?= icon($w['icon'], 26) ?></span><h3><?= e($w['title']) ?></h3><p><?= e($w['text']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">How it works</span><h2>Four simple steps</h2></div>
    <ol class="steps">
      <?php foreach ($DATA['process'] as $i => $st): ?>
        <li><span class="num"><?= $i + 1 ?></span><h3><?= e($st['title']) ?></h3><p><?= e($st['text']) ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<?php if ($stats): ?>
<section class="section stats-band">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Success highlights</span><h2>Our impact</h2></div>
    <div class="grid g4 stats">
      <?php foreach ($stats as $s): ?><div><b><?= e($s['value']) ?></b><span><?= e($s['label']) ?></span></div><?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($testimonials): ?>
<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Testimonials</span><h2>What students and parents say</h2></div>
    <div class="slider" id="slider" aria-roledescription="carousel">
      <div class="slides">
        <?php foreach ($testimonials as $t): ?>
          <blockquote class="slide"><p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p><cite><?= e($t['name']) ?><?php if (!empty($t['detail'])): ?><span><?= e($t['detail']) ?></span><?php endif; ?></cite></blockquote>
        <?php endforeach; ?>
      </div>
      <div class="dots" id="dots"></div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section<?= $testimonials ? '' : ' alt' ?>">
  <div class="wrap two">
    <div>
      <span class="eyebrow">Have questions?</span>
      <h2>Answers for students and parents</h2>
      <p>Admission process, eligibility, fees, safety abroad and more. Read our FAQs or speak to a counselor directly.</p>
      <div class="hero-btns"><a class="btn btn-navy" href="<?= url('faqs') ?>">Read FAQs</a><a class="btn btn-outline" href="<?= url('contact') ?>">Contact us</a></div>
    </div>
    <div class="card quote-card"><p class="big"><?= e(cfg('tagline')) ?></p><p>Our promise to every student who walks through our door.</p></div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
