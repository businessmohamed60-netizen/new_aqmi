<?php
declare(strict_types=1);
/**
 * Live preview of an assembled report template.
 *
 * Variables from PreviewService::loadForPreview():
 * @var array  $template      template row
 * @var array  $blocks        enabled block instances (arrays)
 * @var array  $theme         theme row (may be empty)
 * @var string $themeCss      compiled :root CSS
 * @var string $themeStyle    inline style string
 * @var array  $pageSettings  orientation, watermark, metadata
 * @var string $reportNumber  resolved report number
 */
use App\Modules\ReportStudio\Services\TemplateRenderer;

$orientation  = $pageSettings['orientation'] ?? 'portrait';
$watermark    = $pageSettings['watermark_text'] ?? '';
$wmOpacity    = $pageSettings['watermark_opacity'] ?? 0.08;
$renderer     = new TemplateRenderer();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu — <?= e($template['name'] ?? 'Rapport') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
    <link href="/assets/modules/reportstudio/css/report_studio.css" rel="stylesheet">
    <style><?= $themeCss ?></style>
</head>
<body class="rs-preview-body <?= e($orientation) ?>">
<div class="rs-preview-toolbar d-flex align-items-center justify-content-between px-3 py-2 sticky-top">
    <span class="fw-semibold"><i class="bi bi-eye"></i> Aperçu — <?= e($template['name'] ?? '') ?>
        <span class="badge bg-light text-dark ms-1"><?= e(strtoupper($orientation)) ?></span>
    </span>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" id="rs-preview-orient" style="width:auto">
            <option value="portrait" <?= $orientation === 'portrait' ? 'selected' : '' ?>>A4 Portrait</option>
            <option value="landscape" <?= $orientation === 'landscape' ? 'selected' : '' ?>>A4 Paysage</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer / PDF
        </button>
        <a href="<?= route('reportstudio.builder.edit', ['id' => $template['id'] ?? 0]) ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-gear"></i> Modifier
        </a>
    </div>
</div>

<div class="rs-report-container rs-<?= e($orientation) ?>" data-report-number="<?= e($reportNumber) ?>">
    <?php if ($watermark): ?>
        <div class="rs-watermark" style="opacity:<?= e((string)$wmOpacity) ?>"><?= e($watermark) ?></div>
    <?php endif; ?>

    <div class="rs-report" style="<?= e($themeStyle) ?>">
        <?php if (empty($blocks)): ?>
            <div class="rs-report-empty text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted">Ce rapport ne contient aucun bloc actif.</p>
            </div>
        <?php else: ?>
            <?php
            // Group blocks into rows: consecutive blocks fill a 12-col grid.
            // When the row is full (sum >= 12) or the block is full-width (span=12), start a new row.
            $currentSpan = 0;
            $rowOpen = false;
            foreach ($blocks as $block):
                $span = (int)($block['column_span'] ?? 12);
                $span = max(1, min(12, $span === 0 ? 12 : $span));
                if ($rowOpen && ($currentSpan + $span > 12 || $span === 12 || $currentSpan === 12)) {
                    echo '</div>';
                    $rowOpen = false;
                    $currentSpan = 0;
                }
                if (!$rowOpen) {
                    echo '<div class="row g-2 rs-report-row">';
                    $rowOpen = true;
                    $currentSpan = 0;
                }
                $currentSpan += $span;
            ?>
                <div class="col-<?= $span ?> rs-report-block-col">
                    <section class="rs-report-block rs-vis-<?= e($block['visibility'] ?? 'web_pdf') ?>"
                             data-block-key="<?= e($block['block_key'] ?? '') ?>">
                        <?= $renderer->renderBlock(
                            $block['block_key'] ?? '',
                            $block['block_config'] ?? [],
                            $block['title'] ?? '',
                            $template,
                            $reportNumber
                        ) ?>
                    </section>
                </div>
            <?php endforeach;
            if ($rowOpen) echo '</div>';
            ?>
        <?php endif; ?>
    </div>

    <div class="rs-page-footer">
        <span class="rs-page-num"></span>
    </div>
</div>

<script>
window.RS_PREVIEW = {
    reportNumber: <?= json_encode($reportNumber) ?>,
    certDate: <?= json_encode($pageSettings['certification_date'] ?? null) ?>,
    expDate: <?= json_encode($pageSettings['expiration_date'] ?? null) ?>,
    orientation: <?= json_encode($orientation) ?>,
    templateName: <?= json_encode($template['name'] ?? '') ?>,
};
</script>
<script src="/assets/modules/reportstudio/js/preview.js"></script>
</body>
</html>
