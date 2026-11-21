<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'about';
$title = 'About Us';
$desc  = 'About Poudyal Education & Research Foundation (PERF): our mission, vision, values, leadership and international partnerships.';
require __DIR__ . '/includes/header.php';
$f = cfg('founder', []);
?>
<?php page_hero('About PERF', 'A trusted name in education, career counseling and global opportunities.'); ?>

<section class="section">
  <div class="wrap two">
    <div>
      <span class="eyebrow">Who we are</span>
      <h2>Poudyal Education &amp; Research Foundation</h2>
      <?php foreach (preg_split('/\n\s*\n/', trim(cfg('about_intro'))) as $para) if ($para !== '') echo '<p>' . e($para) . '</p>'; ?>
      <?php if (cfg('about_extra') !== ''): ?><p><?= e(cfg('about_extra')) ?></p><?php endif; ?>
    </div>
    <div class="about-logo card"><?= logo('full', 'PERF logo', 'width="720" height="480" loading="lazy"') ?></div>
  </div>
</section>

<section class="section alt">
  <div class="wrap grid g2">
    <div class="card mv"><span class="svc-ico"><?= icon('compass', 26) ?></span><h3>Our mission</h3><p><?= e(cfg('mission')) ?></p></div>
    <div class="card mv"><span class="svc-ico"><?= icon('globe', 26) ?></span><h3>Our vision</h3><p><?= e(cfg('vision')) ?></p></div>
  </div>
</section>

<?php if (!empty($f['message'])): ?>
<section class="section">
  <div class="wrap two founder-row">
    <div class="founder card">
      <?php if (!empty($f['photo'])): ?><img src="<?= e(asset($f['photo'])) ?>" alt="<?= e($f['name']) ?>" loading="lazy"><?php else: ?><div class="avatar"><?= icon('users', 56) ?></div><?php endif; ?>
    </div>
    <div>
      <span class="eyebrow">Leadership</span>
      <h2>Message from the founder</h2>
      <?php foreach (preg_split('/\n\s*\n/', trim($f['message'])) as $para): ?><p><?= e($para) ?></p><?php endforeach; ?>
      <?php if (!empty($f['name'])): ?><p class="sign"><b><?= e($f['name']) ?></b><br><span><?= e($f['title']) ?></span></p><?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section<?= empty($f['message']) ? '' : ' alt' ?>">
  <div class="wrap grid g2">
    <div class="card">
      <h3>Our values<?= cfg('certifications') ? ' &amp; certifications' : '' ?></h3>
      <ul class="ticks"><?php foreach (cfg('values', []) as $v): ?><li><?= icon('check', 16) ?><?= e($v) ?></li><?php endforeach; ?></ul>
      <?php if (cfg('certifications')): ?>
        <h4 class="sub">Registrations &amp; certifications</h4>
        <ul class="ticks"><?php foreach (cfg('certifications') as $v): ?><li><?= icon('shield', 16) ?><?= e($v) ?></li><?php endforeach; ?></ul>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>International collaborations &amp; partnerships</h3>
      <?php if (cfg('partners')): ?>
        <ul class="ticks"><?php foreach (cfg('partners') as $v): ?><li><?= icon('globe', 16) ?><?= e($v) ?></li><?php endforeach; ?></ul>
      <?php else: ?>
        <p>PERF works with universities, training institutions and employers in India and abroad to give students access to recognised programs and real career opportunities.</p>
        <p><a class="more" href="<?= url('contact') ?>">Ask us about our partner institutions <?= icon('arrow', 16) ?></a></p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
