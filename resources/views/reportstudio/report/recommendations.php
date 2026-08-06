<?php
/** Recommendations — numbered list with priority indicators.
 * @var array $config
 * @var string $title
 */
$items = $config['items'] ?? [];
$title = $config['title'] ?? ($title ?: 'Recommandations');
?>
<div class="rs-block-recommendations py-2">
    <h5 class="rs-block-title"><i class="bi bi-list-check" style="color: var(--rs-primary)"></i> <?= e($title) ?></h5>
    <ol class="rs-reco-list">
        <?php foreach ($items as $item): ?>
            <li><?= e(is_array($item) ? ($item['text'] ?? '') : $item) ?></li>
        <?php endforeach; ?>
        <?php if (empty($items)): ?><li class="text-muted">Aucune recommandation</li><?php endif; ?>
    </ol>
</div>
