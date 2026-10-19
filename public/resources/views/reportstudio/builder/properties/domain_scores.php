<?php
/** Domain Scores properties. @var array $config */
$domains = $config['domains'] ?? [];
$title   = $config['title']   ?? '';
?>
<label class="form-label small fw-bold">Titre du tableau</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="title" value="<?= e($title) ?>">

<label class="form-label small fw-bold">Domaines <small class="text-muted">(un par ligne: Libellé, Score, Max)</small></label>
<textarea class="form-control form-control-sm rs-prop" data-prop="domains_raw" rows="8"><?= e(
    implode("\n", array_map(static fn($d) => ($d['label'] ?? '') . ',' . ($d['score'] ?? 0) . ',' . ($d['max'] ?? 100), $domains))
) ?></textarea>
