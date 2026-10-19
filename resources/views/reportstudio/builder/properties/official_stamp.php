<?php
/** Official Stamp properties. @var array $config */
$style    = $config['style']    ?? 'circular';
$text     = $config['text']     ?? 'CERTIFIÉ';
$subtext  = $config['subtext']  ?? 'AQMI';
$color    = $config['color']    ?? '#102A43';
$size     = (int) ($config['size'] ?? 100);
$align    = $config['align']    ?? 'right';
?>
<label class="form-label small fw-bold">Style du tampon</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="style">
    <option value="circular" <?= $style === 'circular' ? 'selected' : '' ?>>Circulaire</option>
    <option value="rectangular" <?= $style === 'rectangular' ? 'selected' : '' ?>>Rectangulaire</option>
    <option value="badge" <?= $style === 'badge' ? 'selected' : '' ?>>Badge</option>
</select>

<label class="form-label small fw-bold">Texte principal</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="text" value="<?= e($text) ?>">

<label class="form-label small fw-bold">Sous-texte</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="subtext" value="<?= e($subtext) ?>">

<label class="form-label small fw-bold">Couleur</label>
<input type="color" name="stamp_color" class="form-control form-control-color mb-2 rs-prop" data-prop="color" value="<?= e($color) ?>">

<label class="form-label small fw-bold">Taille (px)</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="size" min="60" max="300" value="<?= $size ?>">

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>
