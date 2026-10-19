<?php
/** @var array $config */
$horizontal = $config['horizontal'] ?? false;
$legend     = $config['legend'] ?? true;
?>
<label class="form-label small fw-bold">Orientation</label>
<select class="form-select form-select-sm rs-prop mb-2" data-prop="horizontal">
    <option value="0" <?= empty($horizontal) ? 'selected' : '' ?>>Vertical</option>
    <option value="1" <?= !empty($horizontal) ? 'selected' : '' ?>>Horizontal</option>
</select>

<div class="form-check form-switch mb-2">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="legend" id="bar-legend" <?= $legend !== false ? 'checked' : '' ?>>
    <label class="form-check-label small" for="bar-legend">Afficher la légende</label>
</div>

<?php include __DIR__ . '/data_source.php'; ?>
