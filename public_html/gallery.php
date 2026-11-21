<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'gallery';
$title = 'Gallery & Success Stories';
$desc  = 'Student achievements, events and workshops, awards and international exposure at PERF.';
require __DIR__ . '/includes/header.php';
[$cats, $items] = gallery_items();
?>
<?php page_hero('Gallery &amp; Success Stories', 'Student achievements, events, awards and international exposure.'); ?>

<section class="section">
  <div class="wrap">
    <?php if ($items): ?>
      <div class="filters" id="gfilters" role="group" aria-label="Filter photos">
        <button type="button" class="chip on" data-f="all" aria-pressed="true">All</button>
        <?php foreach ($cats as $k => $label): if (!array_filter($items, fn($i) => $i['cat'] === $k)) continue; ?>
          <button type="button" class="chip" data-f="<?= e($k) ?>" aria-pressed="false"><?= e($label) ?></button>
        <?php endforeach; ?>
      </div>
      <div class="gallery" id="gallery">
        <?php foreach ($items as $it): ?>
          <figure class="tile" data-cat="<?= e($it['cat']) ?>">
            <a href="<?= e(asset($it['src'])) ?>" data-caption="<?= e($it['caption']) ?>"><img src="<?= e(asset($it['src'])) ?>" alt="<?= e($it['caption']) ?>" loading="lazy"></a>
            <figcaption><?= e($it['caption']) ?></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
      <div class="lightbox" id="lightbox" hidden><button type="button" class="lb-close" aria-label="Close">&times;</button><figure><img alt=""><figcaption></figcaption></figure></div>
    <?php else: ?>
      <div class="grid g4 empty-cats">
        <?php foreach ($cats as $k => $label): ?>
          <div class="card ic center"><span class="svc-ico"><?= icon(['achievements' => 'grad', 'events' => 'users', 'awards' => 'shield', 'international' => 'globe'][$k], 26) ?></span><h3><?= e($label) ?></h3><p>Photos coming soon.</p></div>
        <?php endforeach; ?>
      </div>
      <p class="center">Follow our journey and see our students' success stories. Photos will be added here soon.</p>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
