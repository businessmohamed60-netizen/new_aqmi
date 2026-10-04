<?php
$title = __('cgu.title') . ' - NOVAQYS';
$showFooter = true;
ob_start();
?>
<div style="max-width:860px;margin:0 auto;padding:3rem 1.5rem 5rem;font-family:'Manrope','Inter',system-ui,sans-serif;color:#1a1a2e;line-height:1.7;">
  <h1 style="font-size:2rem;font-weight:800;margin-bottom:0.5rem;letter-spacing:-0.5px;"><?= e(__('cgu.title')) ?></h1>
  <p style="color:#7d8794;font-size:0.85rem;margin-bottom:2.5rem;"><?= e(__('cgu.updated')) ?></p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section1_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section1_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section2_title')) ?></h2>
  <ul style="font-size:0.9rem;color:#475569;padding-left:1.5rem;">
    <li><strong><?= e(__('cgu.def_platform')) ?></strong> : <?= e(__('cgu.def_platform_desc')) ?></li>
    <li><strong><?= e(__('cgu.def_user')) ?></strong> : <?= e(__('cgu.def_user_desc')) ?></li>
    <li><strong><?= e(__('cgu.def_assessment')) ?></strong> : <?= e(__('cgu.def_assessment_desc')) ?></li>
    <li><strong><?= e(__('cgu.def_report')) ?></strong> : <?= e(__('cgu.def_report_desc')) ?></li>
  </ul>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section3_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section3_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section4_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section4_body')) ?>
  </p>
  <ul style="font-size:0.9rem;color:#475569;padding-left:1.5rem;">
    <li><?= e(__('cgu.prohibited1')) ?></li>
    <li><?= e(__('cgu.prohibited2')) ?></li>
    <li><?= e(__('cgu.prohibited3')) ?></li>
    <li><?= e(__('cgu.prohibited4')) ?></li>
  </ul>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section5_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section5_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section6_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section6_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section7_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section7_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section8_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section8_body')) ?>
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;"><?= e(__('cgu.section9_title')) ?></h2>
  <p style="font-size:0.9rem;color:#475569;">
    <?= e(__('cgu.section9_body')) ?> <a href="mailto:contact@novaqys.com" style="color:#1F6FEB;">contact@novaqys.com</a>
  </p>

  <div style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid #e7e1d7;">
    <a href="/" style="color:#1F6FEB;text-decoration:none;font-size:0.85rem;font-weight:600;">
      <i class="fas fa-arrow-left" style="margin-right:0.3rem;"></i><?= e(__('cgu.back_home')) ?>
    </a>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
