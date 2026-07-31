<?php
/** @var array $config */
$axes  = $config['axes']  ?? [];
$legend = $config['legend'] ?? true;
?>
<label class="form-label small fw-bold">Axes du radar</label>
<small class="d-block text-muted mb-1">Un axe par ligne : Libellé, Valeur (0-100)</small>
<textarea class="form-control form-control-sm mb-2 rs-prop" data-prop="axes_raw" rows="5"><?= e(
    implode("\n", array_map(
        static fn ($a) => ($a['label'] ?? '') . ',' . ($a['value'] ?? 0),
        $axes
    ))
) ?></textarea>

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="legend"
           id="rc-legend" <?= $legend ? 'checked' : '' ?>>
    <label class="form-check-label small" for="rc-legend">Afficher la légende</label>
</div>
