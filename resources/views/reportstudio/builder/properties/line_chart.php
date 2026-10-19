<?php
/** @var array $config */
$smooth = $config['smooth'] ?? true;
$legend = $config['legend'] ?? true;
?>
<div class="form-check form-switch mb-2">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="smooth" id="line-smooth" <?= $smooth !== false ? 'checked' : '' ?>>
    <label class="form-check-label small" for="line-smooth">Courbe lissée</label>
</div>

<div class="form-check form-switch mb-2">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="legend" id="line-legend" <?= $legend !== false ? 'checked' : '' ?>>
    <label class="form-check-label small" for="line-legend">Afficher la légende</label>
</div>

<?php include __DIR__ . '/data_source.php'; ?>
