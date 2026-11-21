<?php
define('PERF', 1);
require __DIR__ . '/includes/functions.php';
$page = 'faqs';
$title = 'Frequently Asked Questions';
$desc  = 'Answers to common questions from students and parents about admission process, eligibility, fees and studying abroad with PERF.';
require __DIR__ . '/includes/header.php';
$ld = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
?>
<?php page_hero('Frequently Asked Questions', 'Admission process, eligibility, fees and study-abroad concerns.'); ?>

<section class="section">
  <div class="wrap narrow">
    <label class="faq-search"><?= icon('search', 22) ?><span class="sr">Search the FAQs</span>
      <input type="search" id="faq-q" placeholder="Type a word, e.g. fees, visa, NEET" autocomplete="off"></label>
    <p class="faq-none" id="faq-none" hidden>No matching questions. <a href="<?= url('contact') ?>#enquiry"><b>Ask a counselor instead</b></a></p>
    <?php foreach ($DATA['faqs'] as $group => $list): ?>
      <h2 class="faq-group"><?= e($group) ?></h2>
      <?php foreach ($list as [$q, $a]):
        $ld['mainEntity'][] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($a)]]; ?>
        <details class="faq"><summary><?= e($q) ?></summary><p><?= $a ?></p></details>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <p class="center faq-more">Still have questions? <a href="<?= url('contact') ?>#enquiry"><b>Ask a counselor</b></a></p>
  </div>
</section>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
