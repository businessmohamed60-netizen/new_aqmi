<?php
/** Background properties. @var array $config */
$imageUrl  = $config['image_url']  ?? '';
$bgColor   = $config['bg_color']   ?? '#ffffff';
$opacity   = $config['opacity']    ?? 1;
$size      = $config['size']       ?? 'cover';
$position  = $config['position']   ?? 'center';
$repeat    = $config['repeat']     ?? 'no-repeat';
$minHeight = $config['min_height'] ?? '300px';
$padding   = $config['padding']    ?? '24px';
?>
<label class="form-label small fw-bold">URL de l'image de fond</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="image_url" value="<?= e($imageUrl) ?>" placeholder="https://... ou téléchargez ci-dessus">

<label class="form-label small fw-bold">Couleur de fond</label>
<input type="color" class="form-control form-control-color mb-2 rs-prop" data-prop="bg_color" value="<?= e($bgColor) ?>">

<label class="form-label small fw-bold">Opacité</label>
<input type="number" step="0.1" min="0" max="1" class="form-control form-control-sm mb-2 rs-prop" data-prop="opacity" value="<?= e((string) $opacity) ?>">

<label class="form-label small fw-bold">Taille</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="size">
    <option value="cover" <?= $size === 'cover' ? 'selected' : '' ?>>Couvrir</option>
    <option value="contain" <?= $size === 'contain' ? 'selected' : '' ?>>Contenir</option>
    <option value="100% 100%" <?= $size === '100% 100%' ? 'selected' : '' ?>>Étirer</option>
    <option value="auto" <?= $size === 'auto' ? 'selected' : '' ?>>Auto</option>
</select>

<label class="form-label small fw-bold">Position</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="position">
    <option value="center" <?= $position === 'center' ? 'selected' : '' ?>>Centré</option>
    <option value="top" <?= $position === 'top' ? 'selected' : '' ?>>Haut</option>
    <option value="bottom" <?= $position === 'bottom' ? 'selected' : '' ?>>Bas</option>
    <option value="left" <?= $position === 'left' ? 'selected' : '' ?>>Gauche</option>
    <option value="right" <?= $position === 'right' ? 'selected' : '' ?>>Droite</option>
</select>

<label class="form-label small fw-bold">Répétition</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="repeat">
    <option value="no-repeat" <?= $repeat === 'no-repeat' ? 'selected' : '' ?>>Aucune</option>
    <option value="repeat" <?= $repeat === 'repeat' ? 'selected' : '' ?>>Répéter</option>
    <option value="repeat-x" <?= $repeat === 'repeat-x' ? 'selected' : '' ?>>Horizontale</option>
    <option value="repeat-y" <?= $repeat === 'repeat-y' ? 'selected' : '' ?>>Verticale</option>
</select>

<label class="form-label small fw-bold">Hauteur minimale</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="min_height" value="<?= e($minHeight) ?>" placeholder="300px">

<label class="form-label small fw-bold">Padding</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="padding" value="<?= e($padding) ?>" placeholder="24px">
