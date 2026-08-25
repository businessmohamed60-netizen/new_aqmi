<?php
/** Background — decorative background image or color container.
 * @var array $config
 * @var string $title
 */
$imageUrl  = $config['image_url']  ?? '';
$bgColor   = $config['bg_color']   ?? '#ffffff';
$opacity   = $config['opacity']    ?? 1;
$size      = $config['size']       ?? 'cover';
$position  = $config['position']   ?? 'center';
$repeat    = $config['repeat']     ?? 'no-repeat';
$minHeight = $config['min_height'] ?? '300px';
$padding   = $config['padding']    ?? '24px';
?>
<div class="rs-block-background" style="position: relative; min-height: <?= e($minHeight) ?>; padding: <?= e($padding) ?>; background-color: <?= e($bgColor) ?>;">
    <?php if ($imageUrl): ?>
        <div style="position: absolute; inset: 0; background-image: url('<?= e($imageUrl) ?>'); background-size: <?= e($size) ?>; background-position: <?= e($position) ?>; background-repeat: <?= e($repeat) ?>; opacity: <?= e((string) $opacity) ?>; z-index: 0;"></div>
    <?php endif; ?>
    <div style="position: relative; z-index: 1;">
        <?php if (!$imageUrl): ?>
            <span class="text-muted small"><i class="bi bi-image-alt"></i> Arrière-plan</span>
        <?php endif; ?>
    </div>
</div>
