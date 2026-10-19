<?php
/** AQMI Logo. @var array $config @var string $title */
$size  = $config['size']  ?? 'md';
$align = $config['align'] ?? 'left';
$sizeClass = ['sm' => 'rs-logo-sm', 'md' => 'rs-logo-md', 'lg' => 'rs-logo-lg'][$size] ?? 'rs-logo-md';
$useCustomImage = $config['use_custom_image'] ?? false;
$imageUrl = $config['image_url'] ?? '';
$imageHeight = $config['image_height'] ?? '60px';
$imageBorderRadius = $config['image_border_radius'] ?? '0';
?>
<div class="rs-block-aqmi-logo text-<?= e($align) ?> py-2">
    <?php if ($useCustomImage && $imageUrl): ?>
        <img src="<?= e($imageUrl) ?>" alt="AQMI Logo"
             style="height: <?= e($imageHeight) ?>; border-radius: <?= e($imageBorderRadius) ?>;">
    <?php else: ?>
        <div class="rs-logo <?= $sizeClass ?> d-inline-block">
            <span class="rs-aqmi-mark">AQMI</span>
        </div>
    <?php endif; ?>
</div>
