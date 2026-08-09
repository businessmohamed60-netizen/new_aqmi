<?php
/** KPI Card properties. @var array $config */
$label   = $config['label']   ?? '';
$value   = $config['value']   ?? 0;
$unit    = $config['unit']    ?? '';
$icon    = $config['icon']    ?? 'bi-check-circle';
$color   = $config['color']   ?? '#102A43';
$trend   = $config['trend']   ?? '';
$trendUp = ($config['trend_direction'] ?? 'up') === 'up';
$icons = ['bi-check-circle','bi-clock','bi-trophy','bi-graph-up','bi-shield-check','bi-people','bi-gear','bi-star'];
?>
<label class="form-label small fw-bold">Libellé</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="<?= e($label) ?>">

<label class="form-label small fw-bold">Valeur</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="value" value="<?= e($value) ?>">

<label class="form-label small fw-bold">Unité</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="unit" value="<?= e($unit) ?>">

<label class="form-label small fw-bold">Icône</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="icon">
    <?php foreach ($icons as $ic): ?>
        <option value="<?= e($ic) ?>" <?= $icon === $ic ? 'selected' : '' ?>><?= e($ic) ?></option>
    <?php endforeach; ?>
</select>

<label class="form-label small fw-bold">Couleur</label>
<input type="color" class="form-control form-control-color mb-2 rs-prop" data-prop="color" value="<?= e($color) ?>">

<label class="form-label small fw-bold">Tendance</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="trend" value="<?= e($trend) ?>" placeholder="ex: +12%">

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="trend_direction" id="kpi-up" <?= $trendUp ? 'checked' : '' ?>>
    <label class="form-check-label small" for="kpi-up">Tendance à la hausse</label>
</div>
