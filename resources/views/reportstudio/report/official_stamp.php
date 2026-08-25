<?php
/** Official Stamp — AQMI certification seal (circular, rectangular, or badge).
 * @var array $config
 * @var string $title
 */
$style   = $config['style']   ?? 'circular';
$text    = $config['text']    ?? 'CERTIFIÉ';
$subtext = $config['subtext'] ?? 'AQMI';
$color   = $config['color']   ?? '#102A43';
$size    = (int) ($config['size'] ?? 110);
$align   = $config['align']   ?? 'right';
$useCustomImage = $config['use_custom_image'] ?? false;
$imageUrl = $config['image_url'] ?? '';
$imageHeight = $config['image_height'] ?? '100px';
?>
<div class="rs-block-stamp text-<?= e($align) ?> py-2">
    <?php if ($useCustomImage && $imageUrl): ?>
        <img src="<?= e($imageUrl) ?>" alt="Official Stamp"
             style="height: <?= e($imageHeight) ?>;">
    <?php elseif ($style === 'circular'): ?>
        <div class="rs-stamp rs-stamp-circular"
             style="width:<?= $size ?>px;height:<?= $size ?>px;border-color:<?= e($color) ?>;color:<?= e($color) ?>">
            <div class="rs-stamp-inner">
                <span class="rs-stamp-text" style="font-size:<?= max(9, (int)($size * 0.12)) ?>px"><?= e($text) ?></span>
                <span class="rs-stamp-subtext" style="font-size:<?= max(8, (int)($size * 0.09)) ?>px"><?= e($subtext) ?></span>
            </div>
        </div>
    <?php elseif ($style === 'rectangular'): ?>
        <div class="rs-stamp rs-stamp-rectangular"
             style="border-color:<?= e($color) ?>;color:<?= e($color) ?>">
            <div class="rs-stamp-inner rs-stamp-rect-inner">
                <span class="rs-stamp-text"><?= e($text) ?></span>
                <span class="rs-stamp-subtext"><?= e($subtext) ?></span>
            </div>
        </div>
    <?php else: ?>
        <div class="rs-stamp rs-stamp-badge" style="color:<?= e($color) ?>">
            <i class="bi bi-patch-check-fill" style="font-size:<?= (int)($size * 0.5) ?>px"></i>
            <span class="rs-stamp-text small d-block mt-1"><?= e($text) ?></span>
        </div>
    <?php endif; ?>
</div>
