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
?>
<div class="rs-block <?= $enabled ? '' : 'rs-block-disabled' ?>"
     data-block-key="<?= e($key) ?>"
     data-block-id="<?= e($block['id'] ?? '') ?>"
     data-config='<?= e(json_encode($config)) ?>'
     data-title="<?= e($title) ?>"
     data-visibility="<?= e($visibility) ?>">
    <div class="rs-block-toolbar">
        <span class="rs-block-handle" title="Déplacer"><i class="bi bi-grip-vertical"></i></span>
        <span class="rs-block-type"><i class="bi <?= e($blockIcon ?? 'bi-box') ?>"></i> <?= e($title ?: $key) ?></span>
        <span class="rs-vis-badge badge bg-info text-dark" title="Visibilité">
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
