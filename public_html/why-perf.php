<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'why-perf';
$title = 'Why Choose PERF';
$desc  = 'Global opportunities, holistic student development, experienced mentors, industry and university connections and lifelong learning support.';
require __DIR__ . '/includes/header.php';
?>
<?php page_hero('Why Choose PERF', 'Guidance that looks at the whole student, not just the admission.'); ?>

<section class="section">
  <div class="wrap grid g3">
    <?php foreach ($DATA['why'] as $w): ?>
      <div class="card ic"><span class="svc-ico"><?= icon($w['icon'], 26) ?></span><h3><?= e($w['title']) ?></h3><p><?= e($w['text']) ?></p></div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Our approach</span><h2>How we guide you</h2></div>
    <ol class="steps">
      <?php foreach ($DATA['process'] as $i => $st): ?>
        <li><span class="num"><?= $i + 1 ?></span><h3><?= e($st['title']) ?></h3><p><?= e($st['text']) ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="section">
  <div class="wrap two">
    <div>
      <span class="eyebrow">For parents</span>
      <h2>A partner you can trust with your child's future</h2>
      <p>Choosing a course or country is a big family decision. We explain every option clearly, including eligibility, costs, safety and long-term outcomes, so that parents and students can decide together with confidence.</p>
    </div>
    <ul class="card ticks big-ticks">
      <li><?= icon('check', 18) ?>Clear information on fees and timelines</li>
      <li><?= icon('check', 18) ?>Only recognised institutions recommended</li>
      <li><?= icon('check', 18) ?>Regular updates through the process</li>
      <li><?= icon('check', 18) ?>Support even after admission</li>
    </ul>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
