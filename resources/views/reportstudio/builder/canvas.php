<?php
declare(strict_types=1);

/**
 * Builder chrome — React-based 3-pane layout:
 *   left  : block library palette (click to add)
 *   center: drop canvas (current template blocks, grid layout, reorderable)
 *   right : property panel (edits the selected block's config)
 *
 * Variables expected:
 * @var array  $template      assembled template ['template'=>..., 'blocks'=>...]
 * @var array  $palette       BlockRegistry::grouped() — [category => [{block_key,label,icon,partial}, ...]]
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQMI Report Studio — Builder</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="/assets/modules/reportstudio/css/report_studio.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>:root { --rs-primary: #0d9488; --rs-heading: #0b1f4d; --rs-body: #102A43; --rs-font: 'DejaVu Sans', sans-serif; --rs-background: #ffffff; --rs-accent: #b8860b; }</style>
</head>
<body class="rs-builder-body">

<div id="rs-builder-root"></div>

<script>
window.RS_TEMPLATE_ID = <?= (int) ($template['template']['id'] ?? 0) ?>;
window.RS_TEMPLATE_DATA = <?= json_encode([
    'template' => $template['template'] ?? [],
    'blocks'   => $template['blocks'] ?? [],
    'settings' => [
        'orientation'          => $template['template']['orientation'] ?? 'portrait',
        'watermark_text'       => $template['template']['watermark_text'] ?? '',
        'watermark_opacity'    => (float) ($template['template']['watermark_opacity'] ?? 0.08),
        'report_number_prefix' => $template['template']['report_number_prefix'] ?? 'AQMI-RPT-',
        'certification_date'   => $template['template']['certification_date'] ?? null,
        'expiration_date'      => $template['template']['expiration_date'] ?? null,
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.RS_PALETTE = <?= json_encode($palette, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.RS_PREVIEW_URL = '<?= e(route('reportstudio.preview.show', ['id' => $template['template']['id'] ?? 0])) ?>';
window.RS_DASHBOARD_URL = '<?= e(route('reportstudio.index')) ?>';
</script>

<script src="https://cdn.jsdelivr.net/npm/react@18.3.1/umd/react.production.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/react-dom@18.3.1/umd/react-dom.production.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<script src="/assets/modules/reportstudio/js/react_builder.js"></script>
</body>
</html>
