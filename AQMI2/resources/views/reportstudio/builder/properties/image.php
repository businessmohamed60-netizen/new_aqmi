<?php
/** @var array $config */
$url   = $config['url']   ?? '';
$alt   = $config['alt']   ?? '';
$width = $config['width'] ?? '100%';
$align = $config['align'] ?? 'center';
?>
<label class="form-label small fw-bold">URL de l'image</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="url" value="<?= e($url) ?>" placeholder="https://...">

<label class="form-label small fw-bold">Texte alternatif</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="alt" value="<?= e($alt) ?>">

<label class="form-label small fw-bold">Largeur</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="width" value="<?= e($width) ?>" placeholder="100% ou 300px">

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>
