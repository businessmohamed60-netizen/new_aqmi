<?php
/** Global Score — premium circular indicator with rating badge.
 * @var array $config
 * @var string $title
 */
$score      = (int) ($config['score'] ?? 0);
$max        = (int) ($config['max']   ?? 100);
$label      = $config['label'] ?? ($title ?: 'Score global');
$showRating = $config['show_rating'] ?? true;
$pct   = $max > 0 ? min(100, round(($score / $max) * 100)) : 0;
$rating = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'D'));
$ratingLabel = $pct >= 80 ? 'Excellent' : ($pct >= 60 ? 'Bon' : ($pct >= 40 ? 'Moyen' : 'Faible'));
?>
<div class="rs-block-global-score text-center py-3">
    <div class="rs-score-ring" style="--rs-pct: <?= $pct ?>">
        <div class="rs-score-ring-inner">
            <span class="rs-score-value"><?= $score ?><small>/<?= $max ?></small></span>
        </div>
    </div>
    <h4 class="mt-3 rs-score-label"><?= e($label) ?></h4>
    <?php if ($showRating): ?>
        <span class="rs-score-rating badge" data-rating="<?= $rating ?>">Note <?= $rating ?> · <?= e($ratingLabel) ?></span>
    <?php endif; ?>
</div>
