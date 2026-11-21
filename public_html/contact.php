<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'contact';
$title = 'Contact Us | Book Free Counseling';
$desc  = 'Contact PERF by phone, email or WhatsApp, or send an enquiry to book a free counseling session.';
require __DIR__ . '/includes/header.php';
$pre = $_GET['interest'] ?? '';
$sent = $_GET['sent'] ?? '';
$map = map_url();
?>
<?php page_hero('Contact Us', 'Send an enquiry or book a free counseling session. We usually reply within one working day.'); ?>

<section class="section">
  <div class="wrap two top">
    <?php if (STATIC_BUILD): /* no server on GitHub Pages: main.js sends the enquiry via WhatsApp or email */ ?>
    <form class="card form" id="enquiry" data-static="1" data-wa="<?= e(preg_replace('/\D/', '', cfg('whatsapp_intl'))) ?>" data-email="<?= e(cfg('form_to')) ?>">
    <?php else: ?>
    <form class="card form" id="enquiry" action="<?= url('enquiry.php') ?>" method="post">
    <?php endif; ?>
      <h2 class="h3">Book your free counseling</h2>
      <p class="note">Fields marked * are required.</p>
      <div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <input type="hidden" name="t" value="<?= time() ?>">
      <label>Full name *<input type="text" name="name" required maxlength="100" autocomplete="name"></label>
      <div class="row2">
        <label>Phone / WhatsApp *<input type="tel" name="phone" required maxlength="20" pattern="[0-9+()\-\s]{7,20}" autocomplete="tel" inputmode="tel"></label>
        <label>Email<input type="email" name="email" maxlength="120" autocomplete="email"></label>
      </div>
      <fieldset class="pills"><legend>I am a</legend>
        <?php foreach (['Student', 'Parent', 'Working professional', 'Other'] as $i => $r): ?><label><input type="radio" name="role" value="<?= e($r) ?>"<?= $i ? '' : ' checked' ?>><span><?= e($r) ?></span></label><?php endforeach; ?>
      </fieldset>
      <div class="row2">
        <label>Interested in
          <select name="interest">
            <?php foreach ($DATA['services'] as $slug => $s): ?><option value="<?= e($slug) ?>"<?= $pre === $slug ? ' selected' : '' ?>><?= e($s['title']) ?></option><?php endforeach; ?>
            <option value="other"<?= $pre === 'other' ? ' selected' : '' ?>>Something else</option>
          </select>
        </label>
        <label>City<input type="text" name="city" maxlength="60" autocomplete="address-level2"></label>
      </div>
      <label>Message<textarea name="message" rows="4" maxlength="1500" placeholder="Tell us about your qualification, goals or questions"></textarea></label>
      <button class="btn btn-gold btn-lg" type="submit">Send enquiry <?= icon('arrow', 18) ?></button>
      <p class="status<?= $sent === '1' ? ' ok' : ($sent === '0' ? ' err' : '') ?>" id="status" role="status" aria-live="polite"><?php
        if ($sent === '1') echo 'Thank you! Our team will contact you shortly.';
        elseif ($sent === '0') echo 'Sorry, your enquiry could not be sent. Please check the details or call us.';
      ?></p>
    </form>

    <div class="info">
      <div class="card contact-card">
        <h2 class="h3">Get in touch</h2>
        <?php if (tel_link()): ?><p><?= icon('phone', 20) ?><span><b>Phone</b><a href="<?= e(tel_link()) ?>"><?= e(cfg('phone_display')) ?></a></span></p><?php endif; ?>
        <?php if (cfg('email') !== ''): ?><p><?= icon('mail', 20) ?><span><b>Email</b><a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a></span></p><?php endif; ?>
        <?php if (cfg('address') !== ''): ?><p><?= icon('pin', 20) ?><span><b>Address</b><?= e(cfg('address')) ?></span></p><?php endif; ?>
        <?php if (cfg('hours') !== ''): ?><p><?= icon('clock', 20) ?><span><b>Office hours</b><?= e(cfg('hours')) ?></span></p><?php endif; ?>
        <div class="hero-btns">
          <?php if (wa_link()): ?><a class="btn btn-wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= wa_svg(18) ?> Chat on WhatsApp</a><?php endif; ?>
          <?php if (tel_link()): ?><a class="btn btn-outline" href="<?= e(tel_link()) ?>"><?= icon('phone', 18) ?> Call now</a><?php endif; ?>
        </div>
      </div>
      <?php if ($map): ?>
      <div class="card map"><iframe src="<?= e($map) ?>" title="PERF office location on Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php $noCta = true; require __DIR__ . '/includes/footer.php'; ?>
