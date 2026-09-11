<?php
/** Cover Page — Premium certificate title page with editorial-grade design.
 * Features: double gold borders, ornamental corner flourishes, guilloche pattern,
 *   elegant typography hierarchy, central seal, refined decorative elements.
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
$goldColor    = $config['gold_color']   ?? '#c5a455';
$rptNumber    = $reportNumber ?? 'AQMI-RPT-000';
$certDate     = !empty($template['certification_date']) ? date('d/m/Y', strtotime($template['certification_date'])) : date('d/m/Y');
$expDate      = !empty($template['expiration_date']) ? date('d/m/Y', strtotime($template['expiration_date'])) : '';
$bgImageUrl     = $config['bg_image_url'] ?? '';
$bgImageOpacity = $config['bg_image_opacity'] ?? 1;
$bgImageSize    = $config['bg_image_size'] ?? 'cover';
$bgImagePosition= $config['bg_image_position'] ?? 'center';
$bgImageRepeat  = $config['bg_image_repeat'] ?? 'no-repeat';
$bgColor        = $config['bg_color'] ?? '#faf8f3';
$showExpiry     = $config['show_expiry'] ?? false;
$certBodyText   = $config['cert_body_text'] ?? 'Ce document atteste que l\'entreprise mentionnée ci-dessous a fait l\'objet d\'une évaluation complète de sa maturité qualité selon le référentiel AQMI.';
$signatureLabel = $config['signature_label'] ?? 'Direction Qualité AQMI';
$signatureRole  = $config['signature_role'] ?? 'Auditeur Principal';

$coverStyle = 'min-height: 320px; position: relative; background-color: ' . e($bgColor) . ';';
if ($bgImageUrl) {
    $coverStyle .= ' overflow: hidden;';
}
?>
<div class="rs-block-cover-page cert-premium" style="<?= $coverStyle ?>">
    <?php if ($bgImageUrl): ?>
        <div style="position:absolute;inset:0;background-image:url(<?= e($bgImageUrl) ?>);background-size:<?= e($bgImageSize) ?>;background-position:<?= e($bgImagePosition) ?>;background-repeat:<?= e($bgImageRepeat) ?>;opacity:<?= e((string)$bgImageOpacity) ?>;z-index:0;"></div>
    <?php endif; ?>

    <!-- Outer decorative border (gold) -->
    <div class="cert-border-outer" style="border-color: <?= e($goldColor) ?>;"></div>
    <!-- Inner decorative border (thinner, gold-light) -->
    <div class="cert-border-inner" style="border-color: <?= e($goldColor) ?>;"></div>
    <!-- Guilloche pattern overlay -->
    <div class="cert-guilloche" style="--cg-color: <?= e($goldColor) ?>;"></div>

    <!-- Ornamental corners -->
    <div class="cert-flourish cert-flourish-tl" style="--cf-color: <?= e($goldColor) ?>"></div>
    <div class="cert-flourish cert-flourish-tr" style="--cf-color: <?= e($goldColor) ?>"></div>
    <div class="cert-flourish cert-flourish-bl" style="--cf-color: <?= e($goldColor) ?>"></div>
    <div class="cert-flourish cert-flourish-br" style="--cf-color: <?= e($goldColor) ?>"></div>

    <!-- Top accent bar -->
    <div class="cert-accent-bar" style="background: linear-gradient(90deg, transparent, <?= e($goldColor) ?>, transparent);"></div>

    <div class="cert-content" style="position: relative; z-index: 5; min-height: 300px; display: flex; flex-direction: column; justify-content: space-between;">
        <!-- Header: brand + reference -->
        <div class="cert-header-row d-flex justify-content-between align-items-start">
            <div>
                <?php if ($showLogo): ?>
                    <div class="cert-brand" style="color: <?= e($accentColor) ?>">
                        <span class="cert-brand-text">AQMI</span>
                    </div>
                    <div class="cert-brand-tagline">Automotive Quality Maturity Index</div>
                <?php endif; ?>
            </div>
            <div class="text-end cert-ref-block">
                <?php if ($showNumber): ?>
                    <div class="cert-ref-label">Référence</div>
                    <div class="cert-ref-number"><?= e($rptNumber) ?></div>
                <?php endif; ?>
                <?php if ($showDate): ?>
                    <div class="cert-ref-date mt-1">
                        <i class="bi bi-calendar3" style="font-size:0.65rem;"></i> <?= e($certDate) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Center: title + company + body + stamp -->
        <div class="cert-center-section text-center">
            <div class="cert-ornament-top" style="--co-color: <?= e($goldColor) ?>">
                <span class="cert-ornament-line"></span>
                <span class="cert-ornament-diamond"></span>
                <span class="cert-ornament-line"></span>
            </div>

            <h1 class="cert-title" style="color: <?= e($accentColor) ?>"><?= e($reportTitle) ?></h1>
            <p class="cert-subtitle"><?= e($subtitle) ?></p>

            <div class="cert-ornament-bottom" style="--co-color: <?= e($goldColor) ?>">
                <span class="cert-ornament-line"></span>
                <span class="cert-ornament-diamond"></span>
                <span class="cert-ornament-line"></span>
            </div>

            <div class="cert-company-block">
                <p class="cert-prepared-label mb-1">Rapport préparé pour</p>
                <h2 class="cert-company-name" style="color: <?= e($accentColor) ?>"><?= e($companyName) ?></h2>
            </div>

            <?php if (!empty($certBodyText)): ?>
                <p class="cert-body-text"><?= e($certBodyText) ?></p>
            <?php endif; ?>

            <?php if ($showStamp): ?>
                <div class="cert-seal-area">
                    <div class="cert-seal" style="border-color: <?= e($goldColor) ?>; color: <?= e($accentColor) ?>">
                        <div class="cert-seal-inner-ring" style="border-color: <?= e($goldColor) ?>"></div>
                        <div class="cert-seal-content">
                            <i class="bi bi-patch-check-fill cert-seal-icon" style="color: <?= e($goldColor) ?>"></i>
                            <span class="cert-seal-text">CERTIFIÉ</span>
                            <span class="cert-seal-subtext">AQMI</span>
                        </div>
                        <div class="cert-seal-stars">
                            <span style="color: <?= e($goldColor) ?>">★</span>
                            <span style="color: <?= e($goldColor) ?>">★</span>
                            <span style="color: <?= e($goldColor) ?>">★</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer: signature + meta -->
        <div class="cert-footer-section">
            <div class="cert-footer-divider" style="background: linear-gradient(90deg, transparent, <?= e($goldColor) ?>, transparent);"></div>
            <div class="cert-footer-row d-flex justify-content-between align-items-end">
                <div class="cert-signature-block">
                    <div class="cert-sig-line" style="border-color: <?= e($accentColor) ?>"></div>
                    <p class="cert-sig-name"><?= e($signatureLabel) ?></p>
                    <p class="cert-sig-role"><?= e($signatureRole) ?></p>
                </div>
                <div class="text-center cert-validity-block">
                    <?php if ($showExpiry && $expDate): ?>
                        <span class="cert-validity-label">Valide jusqu'au</span>
                        <span class="cert-validity-date"><?= e($expDate) ?></span>
                    <?php endif; ?>
                </div>
                <div class="text-end cert-meta-block">
                    <span class="cert-meta-text">AQMI · NOVAQYS</span>
                    <span class="cert-meta-confidential">Document confidentiel</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom accent bar -->
    <div class="cert-accent-bar cert-accent-bar-bottom" style="background: linear-gradient(90deg, transparent, <?= e($goldColor) ?>, transparent);"></div>
</div>
