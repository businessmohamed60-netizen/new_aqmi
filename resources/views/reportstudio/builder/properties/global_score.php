<?php
/** @var array $config */
$score       = $config['score']       ?? 0;
$max         = $config['max']         ?? 100;
$showRating  = $config['show_rating'] ?? true;
$label       = $config['label']       ?? 'Score global';
?>
<label class="form-label small fw-bold">Libellé</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="<?= e($label) ?>">

<label class="form-label small fw-bold">Score</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="score"
       min="0" value="<?= (int) $score ?>">

<label class="form-label small fw-bold">Score maximum</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="max"
       min="1" value="<?= (int) $max ?>">

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_rating"
           id="gs-show-rating" <?= $showRating ? 'checked' : '' ?>>
    <label class="form-check-label small" for="gs-show-rating">Afficher la note</label>
</div>

<?php include __DIR__ . '/data_source.php'; ?>
