<?php
/** @var array $config */
$legend = $config['legend'] ?? true;
?>
<div class="form-check form-switch mb-2">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="legend" id="donut-legend" <?= $legend !== false ? 'checked' : '' ?>>
    <label class="form-check-label small" for="donut-legend">Afficher la légende</label>
</div>

<?php include __DIR__ . '/data_source.php'; ?>
