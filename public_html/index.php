<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = '';
$title = 'PERF | Poudyal Education & Research Foundation';
$desc  = 'PERF offers career counseling, MBBS abroad, medical admissions, Ausbildung in Germany, abroad studies, online education and skill development programs.';
require __DIR__ . '/includes/header.php';
$stats = cfg('stats', []);
$testimonials = cfg('testimonials', []);
// Until real figures are added (admin panel or config.php), show facts that come straight from the site content.
if (!$stats) $stats = [
    ['value' => (string)count($DATA['services']), 'label' => 'Programs & services', 'text' => 'Medical, abroad, online and skill programs under one roof.'],
    ['value' => (string)count($DATA['destinations']), 'label' => 'Study destinations', 'text' => 'Options in India and around the world, matched to your goals.'],
    ['value' => (string)count($DATA['process']), 'label' => 'Simple steps to admission', 'text' => 'A clear plan from first conversation to your first day.'],
    ['value' => '100%', 'label' => 'Free first counseling', 'text' => 'Honest advice before you spend a single rupee.'],
];
$events = array_values(array_filter(cfg('events', []), function ($ev) { return empty($ev['date']) || strtotime($ev['date']) >= strtotime('today'); }));
?>
<section class="hero">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap hero-in">
    <div class="hero-text">
      <span class="pill"><?= icon('spark', 16) ?> <?= e(cfg('tagline')) ?></span>
      <h1><?= e(cfg('hero_title')) ?><?php if (cfg('hero_highlight') !== ''): ?> <span class="hl"><?= e(cfg('hero_highlight')) ?></span><?php endif; ?></h1>
      <?php if (cfg('hero_text') !== ''): ?><p><?= e(cfg('hero_text')) ?></p><?php endif; ?>
      <div class="hero-btns">
        <a class="btn btn-gold btn-lg" href="<?= url('contact') ?>#enquiry">Book Free Counseling <?= icon('arrow', 18) ?></a>
        <a class="btn btn-ghost btn-lg" href="#find-path">Find my path</a>
      </div>
      <?php if (cfg('hero_points')): ?><ul class="hero-points">
        <?php foreach (cfg('hero_points') as $pt): ?><li><?= icon('check', 18) ?> <?= e($pt) ?></li><?php endforeach; ?>
      </ul><?php endif; ?>
    </div>

    <div class="hero-art" aria-hidden="true">
      <svg class="globe" viewBox="0 0 400 400">
        <defs>
          <radialGradient id="gg" cx="35%" cy="30%" r="75%"><stop offset="0" stop-color="#3d55c4"/><stop offset=".6" stop-color="#1c2b6b"/><stop offset="1" stop-color="#0b1440"/></radialGradient>
          <linearGradient id="gold" x1="0" x2="1"><stop offset="0" stop-color="#ffd76a"/><stop offset="1" stop-color="#f2b61f"/></linearGradient>
        </defs>
        <circle cx="200" cy="200" r="190" class="ring r1"/>
        <circle cx="200" cy="200" r="150" class="ring r2"/>
        <circle cx="200" cy="200" r="112" fill="url(#gg)"/>
        <g class="meridians" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="1.2">
          <ellipse cx="200" cy="200" rx="112" ry="40"/><ellipse cx="200" cy="200" rx="112" ry="80"/>
          <ellipse cx="200" cy="200" rx="40" ry="112"/><ellipse cx="200" cy="200" rx="80" ry="112"/>
          <path d="M88 200h224M200 88v224"/>
        </g>
        <path d="M110 250 C150 120 260 110 300 160" fill="none" stroke="url(#gold)" stroke-width="3" stroke-dasharray="6 8" class="route"/>
        <circle cx="110" cy="250" r="7" fill="#f2b61f"/><circle cx="300" cy="160" r="7" fill="#f2b61f"/>
        <circle cx="300" cy="160" r="7" fill="none" stroke="#f2b61f" class="ping"/>
        <g class="plane"><path d="M0-9 3-1 11 3 11 5 3 3 2 9 5 11 5 12 0 11-5 12-5 11-2 9-3 3-11 5-11 3-3-1z" fill="#fff"/></g>
      </svg>
      <div class="chip-float c1"><span class="ci"><?= icon('stethoscope', 18) ?></span><b>MBBS Abroad</b></div>
      <div class="chip-float c2"><span class="ci"><?= icon('briefcase', 18) ?></span><b>Ausbildung, Germany</b></div>
      <div class="chip-float c3"><span class="ci"><?= icon('laptop', 18) ?></span><b>Online Degrees</b></div>
      <div class="chip-float c4"><span class="ci"><?= icon('compass', 18) ?></span><b>Career Counseling</b></div>
    </div>
  </div>
  <svg class="wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60V30C240 0 480 0 720 22s480 38 720 8v30z"/></svg>
</section>

<section class="section finder" id="find-path">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Find your path</span><h2>What would you like to do?</h2><p>Tap the option that fits you best. We'll show the programs that can get you there.</p></div>
    <div class="goals" role="group" aria-label="Choose your goal">
      <?php foreach ($DATA['goals'] as $i => $g): ?>
        <a class="goal" href="<?= url('services') ?>#<?= e($g['services'][0] ?? '') ?>" data-goal="<?= $i ?>">
          <span class="goal-ico"><?= icon($g['icon'], 28) ?></span>
          <b><?= e($g['title']) ?></b><small><?= e($g['text']) ?></small>
          <span class="goal-tick"><?= icon('check', 16) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php foreach ($DATA['goals'] as $i => $g): ?>
      <div class="goal-result" data-result="<?= $i ?>" hidden>
        <p class="gr-title"><?= icon('spark', 20) ?> Great choice! These programs are a good fit for you:</p>
        <div class="gr-list">
          <?php foreach ($g['services'] as $slug): $s = $DATA['services'][$slug] ?? null; if (!$s) continue; ?>
            <a class="gr-item" href="<?= url('services') ?>#<?= e($slug) ?>"><span class="svc-ico"><?= icon($s['icon'], 22) ?></span><span><b><?= e($s['title']) ?></b><small><?= e($s['text']) ?></small></span></a>
          <?php endforeach; ?>
        </div>
        <div class="hero-btns center-btns">
          <a class="btn btn-gold" href="<?= url('contact') ?>?interest=<?= e($g['services'][0] ?? '') ?>#enquiry">Get free advice on this <?= icon('arrow', 18) ?></a>
          <?php if (wa_link()): ?><a class="btn btn-wa" href="<?= e(wa_link('Hello PERF, I would like to ' . strtolower($g['title']) . '. Please guide me.')) ?>" target="_blank" rel="noopener"><?= wa_svg(18) ?> Ask on WhatsApp</a><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section stats-band">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap">
    <div class="sec-head light"><span class="eyebrow">Our core strengths</span><h2>Guidance you can count on</h2></div>
    <div class="grid g4 stats">
      <?php foreach ($stats as $s): ?><div class="stat"><b data-count="<?= e($s['value']) ?>"><?= e($s['value']) ?></b><span><?= e($s['label']) ?></span><?php if (!empty($s['text'])): ?><p><?= e($s['text']) ?></p><?php endif; ?></div><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">What we do</span><h2>Key services</h2><p>From choosing the right course to settling in abroad, we support you at every stage.</p></div>
    <div class="grid g3">
      <?php foreach ($DATA['services'] as $slug => $s) if (!empty($s['home'])) echo service_card($slug, $s); ?>
    </div>
    <p class="center"><a class="btn btn-navy" href="<?= url('services') ?>">View all services <?= icon('arrow', 18) ?></a></p>
  </div>
</section>

<section class="section" id="destinations">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Study destinations</span><h2>Where would you like to go?</h2><p>Explore countries our students choose for medicine, vocational training and higher studies.</p></div>
    <div class="grid g4 dests">
      <?php foreach ($DATA['destinations'] as $d): ?>
        <a class="card dest" href="<?= url('contact') ?>?interest=<?= e($d['services'][0] ?? '') ?>#enquiry">
          <span class="dest-code"><?= e($d['code']) ?></span>
          <span><b><?= e($d['name']) ?></b><small><?= e($d['text']) ?></small></span>
          <?= icon('arrow', 18) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Services for students</span><h2>End-to-end support, all in one place</h2><p>Everything you need between &ldquo;I want to study&rdquo; and your first day of class.</p></div>
    <div class="grid g4 support">
      <?php foreach ($DATA['support'] as $sp): ?>
        <div class="card ic sup"><span class="svc-ico"><?= icon($sp['icon'], 24) ?></span><h3><?= e($sp['title']) ?></h3><p><?= e($sp['text']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Why PERF</span><h2>Why students and parents choose us</h2></div>
    <div class="grid g3">
      <?php foreach ($DATA['why'] as $w): ?>
        <div class="card ic"><span class="svc-ico"><?= icon($w['icon'], 26) ?></span><h3><?= e($w['title']) ?></h3><p><?= e($w['text']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">How it works</span><h2>Your journey in <?= count($DATA['process']) ?> simple steps</h2></div>
    <ol class="steps journey">
      <?php foreach ($DATA['process'] as $i => $st): ?>
        <li><span class="num"><?= $i + 1 ?></span><h3><?= e($st['title']) ?></h3><p><?= e($st['text']) ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<?php if ($events): ?>
<section class="section">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Upcoming events</span><h2>Seminars, webinars and fairs</h2><p>Meet our counselors and get your questions answered in person or online.</p></div>
    <div class="grid g3">
      <?php foreach ($events as $ev): $ts = !empty($ev['date']) ? strtotime($ev['date']) : 0; ?>
        <article class="card ic event">
          <?php if ($ts): ?><span class="ev-date"><b><?= date('d', $ts) ?></b><?= date('M Y', $ts) ?></span><?php endif; ?>
          <span class="badge">New</span>
          <h3><?= e($ev['title']) ?></h3>
          <?php if (!empty($ev['place'])): ?><p class="ev-place"><?= icon('pin', 16) ?> <?= e($ev['place']) ?></p><?php endif; ?>
          <?php if (!empty($ev['text'])): ?><p><?= e($ev['text']) ?></p><?php endif; ?>
          <a class="more" href="<?= e(!empty($ev['link']) ? $ev['link'] : url('contact') . '#enquiry') ?>"<?= !empty($ev['link']) ? ' target="_blank" rel="noopener"' : '' ?>>Register <?= icon('arrow', 16) ?></a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($testimonials): ?>
<section class="section<?= $events ? ' alt' : '' ?>">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Testimonials</span><h2>What students and parents say</h2></div>
    <div class="slider" id="slider" aria-roledescription="carousel">
      <div class="slides">
        <?php foreach ($testimonials as $t): ?>
          <blockquote class="slide"><span class="stars"><?= str_repeat(icon('star', 18), 5) ?></span><p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p><cite><?= e($t['name']) ?><?php if (!empty($t['detail'])): ?><span><?= e($t['detail']) ?></span><?php endif; ?></cite></blockquote>
        <?php endforeach; ?>
      </div>
      <div class="dots" id="dots"></div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section<?= ($testimonials xor $events) ? ' alt' : '' ?>">
  <div class="wrap two">
    <div>
      <span class="eyebrow">Have questions?</span>
      <h2>Answers for students and parents</h2>
      <p>Admission process, eligibility, fees, safety abroad and more. Read our FAQs or speak to a counselor directly.</p>
      <div class="hero-btns"><a class="btn btn-navy" href="<?= url('faqs') ?>">Read FAQs</a><a class="btn btn-outline" href="<?= url('contact') ?>">Contact us</a></div>
    </div>
    <?php form_open('callback', 'card form callback'); ?>
      <h2 class="h3">Request a call back</h2>
      <p class="note">Leave your number and a counselor will call you, free of charge.</p>
      <label>Full name *<input type="text" name="name" required maxlength="100" autocomplete="name"></label>
      <label>Phone / WhatsApp *<input type="tel" name="phone" required maxlength="20" pattern="[0-9+()\-\s]{7,20}" autocomplete="tel" inputmode="tel"></label>
      <div class="row2">
        <label>I am a
          <select name="role"><?php foreach (['Student', 'Parent', 'Working professional', 'Institute', 'Partner'] as $r): ?><option><?= e($r) ?></option><?php endforeach; ?></select>
        </label>
        <label>Interested in
          <select name="interest"><?php foreach ($DATA['services'] as $slug => $sv): ?><option value="<?= e($slug) ?>"><?= e($sv['title']) ?></option><?php endforeach; ?><option value="other">Something else</option></select>
        </label>
      </div>
      <button class="btn btn-gold" type="submit">Call me back <?= icon('phone', 18) ?></button>
      <p class="status" role="status" aria-live="polite"></p>
    </form>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
