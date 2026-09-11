<?php
/** Recommendations — premium numbered list with gold counter badges.
 * @var array $config
 * @var string $title
 */
$items = $config['items'] ?? [];
$title = $config['title'] ?? ($title ?: 'Recommandations');
$icon  = $config['icon'] ?? 'bi-list-check';
$numbered = $config['numbered'] ?? true;
$color = $config['color'] ?? '#0f2845';
$fontSize = $config['font_size'] ?? '0.88rem';
?>
<div class="rs-block-recommendations py-2">
    <h5 class="rs-block-title"><i class="bi <?= e($icon) ?>" style="color: var(--cert-gold, #c5a455)"></i> <?= e($title) ?></h5>
    <?php if ($numbered): ?>
    <ol class="rs-reco-list" style="color: <?= e($color) ?>; font-size: <?= e($fontSize) ?>;">
        <?php foreach ($items as $item): ?>
            <li><?= e(is_array($item) ? ($item['text'] ?? '') : $item) ?></li>
        <?php endforeach; ?>
    </ol>
    <?php else: ?>
    <ul style="list-style: none; padding-left: 0;">
        <?php foreach ($items as $item): ?>
            <li style="padding: 8px 0; border-bottom: 1px solid #f0e8d8; font-size: <?= e($fontSize) ?>; color: <?= e($color) ?>;">
                <i class="bi <?= e($icon) ?>" style="color: var(--cert-gold, #c5a455); margin-right: 8px;"></i>
                <?= e(is_array($item) ? ($item['text'] ?? '') : $item) ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?php if (empty($items)): ?><p class="text-muted" style="font-size: <?= e($fontSize) ?>;">Aucune recommandation</p><?php endif; ?>
</div>
