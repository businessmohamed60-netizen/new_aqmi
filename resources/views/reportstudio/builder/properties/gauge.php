<?php
/** @var array $config */
$value = $config['value'] ?? 0;
$min   = $config['min']   ?? 0;
$max   = $config['max']   ?? 100;
$label = $config['label'] ?? 'Indicateur';
$unit  = $config['unit']  ?? '%';
?>
<label class="form-label small fw-bold">Libellé</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="<?= e($label) ?>">

<label class="form-label small fw-bold">Valeur</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="value"
       min="<?= (int) $min ?>" max="<?= (int) $max ?>" value="<?= (int) $value ?>">

<div class="row g-2 mb-2">
    <div class="col-6">
        <label class="form-label small fw-bold">Min</label>
        <input type="number" class="form-control form-control-sm rs-prop" data-prop="min" value="<?= (int) $min ?>">
    </div>
    <div class="col-6">
        <label class="form-label small fw-bold">Max</label>
        <input type="number" class="form-control form-control-sm rs-prop" data-prop="max" value="<?= (int) $max ?>">
    </div>
</div>

<label class="form-label small fw-bold">Unité</label>
<input type="text" class="form-control form-control-sm rs-prop" data-prop="unit" value="<?= e($unit) ?>">
