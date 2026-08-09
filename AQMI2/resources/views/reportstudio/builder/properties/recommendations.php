<?php
/** @var array $config */
$items = $config['items'] ?? [];
$title = $config['title'] ?? 'Recommandations';
?>
<label class="form-label small fw-bold">Titre de la section</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="title" value="<?= e($title) ?>">

<label class="form-label small fw-bold">Recommandations</label>
<small class="d-block text-muted mb-1">Une recommandation par ligne</small>
<textarea class="form-control form-control-sm rs-prop" data-prop="items_raw" rows="6"><?= e(
    implode("\n", array_map(static fn ($i) => $i['text'] ?? $i ?? '', $items))
) ?></textarea>
