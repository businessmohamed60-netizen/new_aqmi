<?php
declare(strict_types=1);

/**
 * Builder chrome — 3-pane layout:
 *   left  : block library palette (draggable source)
 *   center: drop canvas (current template blocks, sortable)
 *   right : property panel (edits the selected block's config)
 *
 * Variables expected:
 * @var array  $template      assembled template ['template'=>..., 'blocks'=>...]
 * @var array  $palette       BlockRegistry::grouped() — [category => [{block_key,label,icon,partial}, ...]]
 * @var string $themeCss      compiled CSS custom properties for the active theme
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQMI Report Studio — Builder</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <link href="/assets/modules/reportstudio/css/report_studio.css" rel="stylesheet">
    <style><?= $themeCss ?? '' ?></style>
</head>
<body class="rs-builder-body">

<div class="rs-topbar d-flex align-items-center justify-content-between px-3">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route('reportstudio.index') ?>" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
        <span class="text-white-50 small">|</span>
        <span class="text-white fw-semibold"><?= e($template['template']['name'] ?? 'Template') ?></span>
        <span class="badge bg-light text-dark ms-2"><?= e($template['template']['status'] ?? 'draft') ?></span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-light" id="rs-btn-undo" title="Annuler">
            <i class="bi bi-arrow-counterclockwise"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-light" id="rs-btn-redo" title="Refaire">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <a href="<?= route('reportstudio.preview.show', ['id' => $template['template']['id'] ?? 0]) ?>"
           target="_blank" class="btn btn-sm btn-light">
            <i class="bi bi-eye"></i> Aperçu
        </a>
        <button type="button" class="btn btn-sm btn-success" id="rs-btn-save">
            <i class="bi bi-check-lg"></i> Enregistrer
        </button>
    </div>
</div>

<div class="rs-settings-bar d-flex align-items-center gap-2 px-3 py-1">
    <span class="small text-muted fw-bold"><i class="bi bi-gear"></i> Paramètres:</span>
    <select class="form-select form-select-sm rs-setting" id="rs-set-orientation" style="width:auto" data-setting="orientation">
        <option value="portrait">A4 Portrait</option>
        <option value="landscape">A4 Paysage</option>
    </select>
    <div class="vr"></div>
    <span class="small text-muted">N° rapport:</span>
    <input type="text" class="form-control form-control-sm rs-setting" id="rs-set-report-number" style="width:120px" data-setting="report_number_prefix" placeholder="AQMI-RPT-">
    <div class="vr"></div>
    <span class="small text-muted">Date cert.:</span>
    <input type="date" class="form-control form-control-sm rs-setting" id="rs-set-cert-date" style="width:140px" data-setting="certification_date">
    <span class="small text-muted">Expiration:</span>
    <input type="date" class="form-control form-control-sm rs-setting" id="rs-set-exp-date" style="width:140px" data-setting="expiration_date">
    <div class="vr"></div>
    <span class="small text-muted">Filigrane:</span>
    <input type="text" class="form-control form-control-sm rs-setting" id="rs-set-watermark" style="width:120px" data-setting="watermark_text" placeholder="Texte filigrane">
</div>

<div class="rs-panes">
    <!-- Left: Block library palette -->
    <aside class="rs-pane rs-palette" id="rs-palette-pane">
        <div class="rs-pane-header">
            <i class="bi bi-blocks"></i>
            <span>Bibliothèque de blocs</span>
        </div>
        <div class="rs-pane-body">
            <?php foreach ($palette as $category => $items): ?>
                <div class="rs-palette-group">
                    <div class="rs-palette-cat">
                        <i class="bi bi-chevron-down rs-toggle"></i>
                        <?= e(ucfirst($category)) ?>
                        <span class="rs-count"><?= count($items) ?></span>
                    </div>
                    <div class="rs-palette-items">
                        <?php foreach ($items as $block): ?>
                            <div class="rs-palette-item"
                                 data-block-key="<?= e($block['block_key']) ?>"
                                 data-block-label="<?= e($block['label']) ?>"
                                 data-block-icon="<?= e($block['icon']) ?>">
                                <i class="bi <?= e($block['icon']) ?>"></i>
                                <span><?= e($block['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- Center: Drop canvas -->
    <main class="rs-pane rs-canvas-pane">
        <div class="rs-pane-header rs-canvas-header">
            <i class="bi bi-file-earmark-text"></i>
            <span>Canvas du rapport</span>
            <span class="rs-canvas-count ms-auto badge bg-secondary" id="rs-canvas-count">0 bloc(s)</span>
        </div>
        <div class="rs-canvas-scroll">
            <div class="rs-canvas" id="rs-canvas"
                 data-template-id="<?= e($template['template']['id'] ?? 0) ?>">
                <?php if (!empty($template['blocks'])): ?>
                    <?php foreach ($template['blocks'] as $block): ?>
                        <?= view_partial('reportstudio/builder/block_card', ['block' => $block], true) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rs-canvas-empty" id="rs-canvas-empty">
                        <i class="bi bi-arrows-move"></i>
                        <p>Glissez des blocs ici pour construire votre rapport</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Right: Property panel -->
    <aside class="rs-pane rs-properties" id="rs-properties-pane">
        <div class="rs-pane-header">
            <i class="bi bi-sliders"></i>
            <span>Propriétés</span>
        </div>
        <div class="rs-pane-body" id="rs-properties-body">
            <div class="rs-properties-empty">
                <i class="bi bi-hand-index"></i>
                <p>Sélectionnez un bloc pour éditer ses propriétés</p>
            </div>
        </div>
    </aside>
</div>

<div class="rs-statusbar">
    <span id="rs-status-msg">Prêt</span>
    <span class="ms-auto small text-muted" id="rs-status-save"></span>
</div>

<!-- Toast for save feedback -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="rs-toast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="rs-toast-body">Modifications enregistrées</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>window.RS_TEMPLATE_ID = <?= (int) ($template['template']['id'] ?? 0) ?>;</script>
<script>window.RS_TEMPLATE_SETTINGS = <?= json_encode([
    'orientation'        => $template['template']['orientation'] ?? 'portrait',
    'watermark_text'     => $template['template']['watermark_text'] ?? '',
    'watermark_opacity'  => (float) ($template['template']['watermark_opacity'] ?? 0.08),
    'report_number_prefix' => $template['template']['report_number_prefix'] ?? 'AQMI-RPT-',
    'certification_date' => $template['template']['certification_date'] ?? null,
    'expiration_date'    => $template['template']['expiration_date'] ?? null,
]) ?>;</script>
<script src="/assets/modules/reportstudio/js/block_renderers.js"></script>
<script src="/assets/modules/reportstudio/js/builder.js"></script>
<script src="/assets/modules/reportstudio/js/property_panel.js"></script>
</body>
</html>
