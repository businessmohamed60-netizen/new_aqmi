<?php
/** @var array $config */
$url   = $config['url']   ?? '';
$size  = $config['size']  ?? 'md';
$align = $config['align'] ?? 'left';
?>
<label class="form-label small fw-bold">URL du logo</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="url" value="<?= e($url) ?>" placeholder="https://...">

<label class="form-label small fw-bold">Taille</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="size">
    <option value="sm" <?= $size === 'sm' ? 'selected' : '' ?>>Petite</option>
    <option value="md" <?= $size === 'md' ? 'selected' : '' ?>>Moyenne</option>
    <option value="lg" <?= $size === 'lg' ? 'selected' : '' ?>>Grande</option>
</select>

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>
