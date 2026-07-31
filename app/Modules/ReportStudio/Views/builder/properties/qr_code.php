<?php
/** @var array $config */
$value = $config['value'] ?? '';
$size  = $config['size']  ?? 120;
$label = $config['label'] ?? '';
?>
<label class="form-label small fw-bold">Donnée encodée (URL ou texte)</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="value"
       value="<?= e($value) ?>" placeholder="https://aqmi.example">

<label class="form-label small fw-bold">Taille (px)</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="size"
       min="60" max="400" value="<?= (int) $size ?>">

<label class="form-label small fw-bold">Libellé sous le QR</label>
<input type="text" class="form-control form-control-sm rs-prop" data-prop="label" value="<?= e($label) ?>">
