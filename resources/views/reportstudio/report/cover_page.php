<?php
/** Cover Page — premium certificate title page with decorative borders.
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
$accentColor  = $config['accent_color'] ?? '#0f2845';
$rptNumber    = $reportNumber ?? 'AQMI-RPT-000';
$certDate     = !empty($template['certification_date']) ? date('d/m/Y', strtotime($template['certification_date'])) : date('d/m/Y');
$bgImageUrl     = $config['bg_image_url'] ?? '';
$bgImageOpacity = $config['bg_image_opacity'] ?? 1;
$bgImageSize    = $config['bg_image_size'] ?? 'cover';
$bgImagePosition= $config['bg_image_position'] ?? 'center';
$bgImageRepeat  = $config['bg_image_repeat'] ?? 'no-repeat';
$bgColor        = $config['bg_color'] ?? '#faf8f3';

$coverStyle = 'min-height: 280px; position: relative; background-color: ' . e($bgColor) . ';';
if ($bgImageUrl) {
    $coverStyle .= ' overflow: hidden;';
}
?>
<div class="rs-block-cover-page" style="<?= $coverStyle ?>">
    <?php if ($bgImageUrl): ?>
        <div style="position:absolute;inset:0;background-image:url(<?= e($bgImageUrl) ?>);background-size:<?= e($bgImageSize) ?>;background-position:<?= e($bgImagePosition) ?>;background-repeat:<?= e($bgImageRepeat) ?>;opacity:<?= e((string)$bgImageOpacity) ?>;z-index:0;"></div>
    <?php endif; ?>

    <div class="cert-decorative-border" style="border-color: <?= e($accentColor) ?>33;"></div>
    <div class="cert-corner cert-corner-tl" style="border-color: <?= e($accentColor) ?>;"></div>
    <div class="cert-corner cert-corner-tr" style="border-color: <?= e($accentColor) ?>;"></div>
    <div class="cert-corner cert-corner-bl" style="border-color: <?= e($accentColor) ?>;"></div>
    <div class="cert-corner cert-corner-br" style="border-color: <?= e($accentColor) ?>;"></div>

    <div class="cert-content" style="position: relative; z-index: 3; min-height: 260px; display: flex; flex-direction: column; justify-content: space-between;">
        <!-- Header: brand + reference -->
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <?php if ($showLogo): ?>
                    <div class="cert-brand" style="color: <?= e($accentColor) ?>">AQMI</div>
                    <div class="cert-brand-tagline">Automotive Quality Maturity Index</div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <?php if ($showNumber): ?>
                    <div class="cert-ref-label">Référence</div>
                    <div class="cert-ref-number"><?= e($rptNumber) ?></div>
                <?php endif; ?>
                <?php if ($showDate): ?>
                    <div class="cert-ref-date mt-1"><i class="bi bi-calendar3" style="font-size:0.68rem;"></i> <?= e($certDate) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Center: title + company + stamp -->
        <div class="text-center" style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; padding: 20px 0;">
            <hr class="cert-divider">
            <h1 class="cert-title" style="color: <?= e($accentColor) ?>"><?= e($reportTitle) ?></h1>
            <p class="cert-subtitle"><?= e($subtitle) ?></p>
            <hr class="cert-divider">

            <div style="margin-top: 16px;">
                <p class="cert-prepared-label mb-1">Rapport préparé pour</p>
                <h2 class="cert-company-name"><?= e($companyName) ?></h2>
            </div>

            <?php if ($showStamp): ?>
                <div class="cert-stamp-container" style="margin-top: 20px;">
                    <div class="rs-stamp rs-stamp-circular" style="width:100px;height:100px;border-color:<?= e($accentColor) ?>;color:<?= e($accentColor) ?>">
                        <div class="rs-stamp-inner">
                            <span class="rs-stamp-text" style="font-size: 0.72rem;">CERTIFIÉ</span>
                            <span class="rs-stamp-subtext" style="font-size: 0.58rem;">AQMI</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer bar -->
        <div class="cert-footer-bar d-flex justify-content-between align-items-end">
            <div>
                <span style="font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: #8298b5; font-weight: 600;">AQMI · NOVAQYS</span>
            </div>
            <div class="text-end">
                <span style="font-size: 0.65rem; color: #8298b5;">Document confidentiel</span>
            </div>
        </div>
    </div>
</div>
