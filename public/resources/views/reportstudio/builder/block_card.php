<?php
declare(strict_types=1);

/**
 * A single block instance rendered on the canvas.
 * @var array $block  one row from template['blocks']:
 *                      [id, block_key, title, block_config, sort_order, is_enabled]
 */
$config  = $block['block_config'] ?? [];
$key     = $block['block_key'] ?? '';
$title   = $block['title'] ?? '';
$enabled = $block['is_enabled'] ?? true;
$visibility = $block['visibility'] ?? 'web_pdf';
$visBadge = match ($visibility) {
    'web_only' => ['bi-globe', 'Web'],
    'pdf_only' => ['bi-file-pdf', 'PDF'],
    default    => ['bi-eye', 'Web+PDF'],
};

// Category color mapping (Canva-style)
$catColors = [
    'global_score'    => 'metrics',
    'radar_chart'     => 'charts',
    'bar_chart'       => 'charts',
    'line_chart'      => 'charts',
    'donut_chart'     => 'charts',
    'area_chart'      => 'charts',
    'gauge'           => 'metrics',
    'recommendations' => 'content',
    'company_info'    => 'content',
    'aqmi_logo'       => 'branding',
    'company_logo'    => 'branding',
    'official_stamp'  => 'branding',
    'qr_code'         => 'utility',
    'signature'       => 'utility',
    'header'          => 'structure',
    'footer'          => 'structure',
    'rich_text'       => 'content',
    'image'           => 'media',
    'background'      => 'media',
    'cover_page'      => 'structure',
    'kpi_card'        => 'metrics',
    'domain_scores'   => 'metrics',
    'page_break'      => 'structure',
];
$catClass = $catColors[$key] ?? 'utility';
$catColorVar = 'var(--rs-cat-' . $catClass . ')';
?>
<div class="rs-block <?= $enabled ? '' : 'rs-block-disabled' ?>"
     data-block-key="<?= e($key) ?>"
     data-block-id="<?= e($block['id'] ?? '') ?>"
     data-config='<?= e(json_encode($config)) ?>'
     data-title="<?= e($title) ?>"
     data-visibility="<?= e($visibility) ?>">
    <div class="rs-block-toolbar">
        <span class="rs-block-handle" title="Déplacer"><i class="bi bi-grip-vertical"></i></span>
        <span class="rs-block-type"><i class="bi <?= e($blockIcon ?? 'bi-box') ?>" style="color: <?= $catColorVar ?>"></i> <?= e($title ?: $key) ?></span>
        <span class="rs-vis-badge badge" style="background: <?= $catColorVar ?>1a; color: <?= $catColorVar ?>" title="Visibilité">
            <i class="bi <?= e($visBadge[0]) ?>"></i> <?= e($visBadge[1]) ?>
        </span>
        <div class="rs-block-actions ms-auto">
            <button type="button" class="btn btn-sm rs-block-toggle" title="Activer/Désactiver">
                <i class="bi <?= $enabled ? 'bi-eye' : 'bi-eye-slash' ?>"></i>
            </button>
            <button type="button" class="btn btn-sm rs-block-edit" title="Propriétés">
                <i class="bi bi-sliders"></i>
            </button>
            <button type="button" class="btn btn-sm rs-block-duplicate" title="Dupliquer">
                <i class="bi bi-files"></i>
            </button>
            <button type="button" class="btn btn-sm rs-block-delete" title="Supprimer">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    <div class="rs-block-preview" data-block-key="<?= e($key) ?>">
        <div class="rs-live-render"></div>
    </div>
</div>
