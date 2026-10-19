<?php
/** @var array $config */
$size  = $config['size']  ?? 'md';
$align = $config['align'] ?? 'left';
$useCustomImage = $config['use_custom_image'] ?? false;
$imageUrl = $config['image_url'] ?? '';
$imageHeight = $config['image_height'] ?? '60px';
$imageBorderRadius = $config['image_border_radius'] ?? '0';
?>
<div class="form-check form-switch mb-2">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="use_custom_image" id="aqmi-custom-img" <?= $useCustomImage ? 'checked' : '' ?>>
    <label class="form-check-label small" for="aqmi-custom-img">Utiliser une image personnalisée</label>
</div>

<?php if ($useCustomImage): ?>
<label class="form-label small fw-bold">URL du logo</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="image_url" value="<?= e($imageUrl) ?>" placeholder="Téléchargez ou collez une URL">

<label class="form-label small fw-bold">Hauteur de l'image</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="image_height" value="<?= e($imageHeight) ?>" placeholder="60px">

<label class="form-label small fw-bold">Arrondi des coins</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="image_border_radius" value="<?= e($imageBorderRadius) ?>" placeholder="0">
<?php endif; ?>

<?php if (!$useCustomImage): ?>
<label class="form-label small fw-bold">Taille</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="size">
    <option value="sm" <?= $size === 'sm' ? 'selected' : '' ?>>Petite</option>
    <option value="md" <?= $size === 'md' ? 'selected' : '' ?>>Moyenne</option>
    <option value="lg" <?= $size === 'lg' ? 'selected' : '' ?>>Grande</option>
</select>
<?php endif; ?>

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>
