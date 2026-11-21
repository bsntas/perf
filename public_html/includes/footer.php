<?php if (!defined('PERF')) { http_response_code(403); exit; } ?>
</main>
<?php if (empty($noCta)): ?>
<section class="cta-band">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap cta-in">
    <div><span class="eyebrow">Free first session</span><h2>Ready to plan your future?</h2><p>Talk to a PERF counselor and get clear guidance on courses, countries and careers.</p></div>
    <div class="cta-btns">
      <a class="btn btn-gold" href="<?= url('contact') ?>#enquiry">Book Free Counseling</a>
      <?php if (wa_link()): ?><a class="btn btn-ghost" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">WhatsApp Us</a>
      <?php elseif (tel_link()): ?><a class="btn btn-ghost" href="<?= e(tel_link()) ?>">Call Us</a><?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<footer class="site-footer">
  <div class="wrap fgrid">
    <div>
      <a href="<?= url() ?>" class="flogo"><?= logo('full', 'Poudyal Education and Research Foundation logo', 'width="720" height="480" loading="lazy"') ?></a>
      <p class="muted">PERF guides students and families through education, careers and global opportunities.</p>
      <?php $social = array_filter(cfg('social', [])); if ($social): ?>
      <p class="social"><?php foreach ($social as $k => $u): ?><a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="PERF on <?= e(ucfirst($k)) ?>"><?= social_svg($k) ?></a><?php endforeach; ?></p>
      <?php endif; ?>
    </div>
    <div>
      <h4>Explore</h4>
      <?php foreach ($nav as $slug => $label): ?><a href="<?= url($slug) ?>"><?= e($label) ?></a><?php endforeach; ?>
    </div>
    <div>
      <h4>Programs</h4>
      <?php foreach (array_slice($DATA['services'], 0, 8, true) as $slug => $s): ?><a href="<?= url('services') ?>#<?= e($slug) ?>"><?= e($s['title']) ?></a><?php endforeach; ?>
    </div>
    <div>
      <h4>Study destinations</h4>
      <?php foreach ($DATA['destinations'] as $d): ?><a href="<?= url('contact') ?>?interest=<?= e($d['services'][0]) ?>#enquiry"><?= e($d['name']) ?></a><?php endforeach; ?>
    </div>
    <div class="fcontact">
      <h4>Contact</h4>
      <a class="btn btn-gold btn-sm fbtn" href="<?= url('contact') ?>#enquiry">Book Free Counseling</a>
      <?php if (cfg('address') !== ''): ?><p><?= icon('pin', 16) ?> <span><?= e(cfg('address')) ?></span></p><?php endif; ?>
      <?php if (tel_link()): ?><p><?= icon('phone', 16) ?> <a href="<?= e(tel_link()) ?>"><?= e(cfg('phone_display')) ?></a></p><?php endif; ?>
      <?php if (cfg('email') !== ''): ?><p><?= icon('mail', 16) ?> <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a></p><?php endif; ?>
      <?php if (cfg('hours') !== ''): ?><p><?= icon('clock', 16) ?> <span><?= e(cfg('hours')) ?></span></p><?php endif; ?>
    </div>
  </div>
  <div class="copy">&copy; <?= date('Y') ?> Poudyal Education &amp; Research Foundation (PERF). All rights reserved.</div>
</footer>
<?php if (wa_link()): ?>
<a class="wa-float" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" aria-label="Chat with PERF on WhatsApp"><?= wa_svg() ?></a>
<?php endif; ?>
<button type="button" class="to-top" id="to-top" aria-label="Back to top"><?= icon('up', 22) ?></button>
<script src="<?= asset('assets/js/main.js') ?>" defer></script>
</body>
</html>
