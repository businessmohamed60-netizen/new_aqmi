<?php
/** Cover Page — certification report title page.
 * @var array $config
 * @var string $title
 * @var array  $template
 * @var string $reportNumber
 */
$companyName  = $config['company_name']  ?? 'Nom de l\'entreprise';
$reportTitle  = $config['report_title']  ?? 'Rapport d\'Audit Qualité';
$subtitle     = $config['subtitle']     ?? 'Automotive Quality Maturity Index';
$showLogo     = $config['show_logo']     ?? true;
$showStamp    = $config['show_stamp']    ?? true;
$showDate     = $config['show_date']     ?? true;
$showNumber   = $config['show_number']  ?? true;
$accentColor  = $config['accent_color'] ?? '#102A43';
$rptNumber    = $reportNumber ?? 'AQMI-RPT-000';
$certDate     = !empty($template['certification_date']) ? date('d/m/Y', strtotime($template['certification_date'])) : date('d/m/Y');
?>
<div class="rs-block-cover-page" style="min-height: 240px; display: flex; flex-direction: column; justify-content: space-between; padding: 20px 0;">
    <div class="text-center" style="border-bottom: 3px solid <?= e($accentColor) ?>; padding-bottom: 20px; margin-bottom: 20px;">
        <?php if ($showLogo): ?>
            <div class="mb-3">
                <span class="rs-aqmi-mark" style="font-size: 2.2rem; color: <?= e($accentColor) ?>">AQMI</span>
            </div>
        <?php endif; ?>
        <h1 style="font-size: 1.9rem; font-weight: 800; color: <?= e($accentColor) ?>; letter-spacing: -0.01em; margin-bottom: 6px;"><?= e($reportTitle) ?></h1>
        <p style="font-size: 1rem; color: #486581; font-weight: 500; margin: 0;"><?= e($subtitle) ?></p>
    </div>

    <div class="text-center" style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
        <p class="text-muted small mb-1" style="letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Rapport préparé pour</p>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #0D1B3E; margin-bottom: 16px;"><?= e($companyName) ?></h2>
        <?php if ($showStamp): ?>
            <div style="margin: 20px auto;">
                <div class="rs-stamp rs-stamp-circular" style="width:100px;height:100px;border-color:<?= e($accentColor) ?>;color:<?= e($accentColor) ?>">
                    <div class="rs-stamp-inner">
                        <span class="rs-stamp-text">CERTIFIÉ</span>
                        <span class="rs-stamp-subtext">AQMI</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-end" style="border-top: 1px solid #EEF2F7; padding-top: 16px; margin-top: 20px;">
        <div>
            <?php if ($showNumber): ?><p class="small text-muted mb-0"><i class="bi bi-hash"></i> <?= e($rptNumber) ?></p><?php endif; ?>
        </div>
        <div class="text-end">
            <?php if ($showDate): ?><p class="small text-muted mb-0"><i class="bi bi-calendar3"></i> <?= e($certDate) ?></p><?php endif; ?>
        </div>
    </div>
</div>
