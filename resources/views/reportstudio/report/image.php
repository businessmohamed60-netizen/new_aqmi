<?php
/** Image. @var array $config @var string $title */
$url   = $config['url']   ?? '';
$alt   = $config['alt']   ?? '';
$width = $config['width'] ?? '100%';
$align = $config['align'] ?? 'center';
$maxHeight = $config['max_height'] ?? '';
$objectFit = $config['object_fit'] ?? 'contain';
$borderRadius = $config['border_radius'] ?? '0';
$styleStr = 'width: ' . e($width) . '; border-radius: ' . e($borderRadius) . ';';
if ($maxHeight) $styleStr .= ' max-height: ' . e($maxHeight) . ';';
if ($objectFit) $styleStr .= ' object-fit: ' . e($objectFit) . ';';
?>
<div class="rs-block-image py-2 text-<?= e($align) ?>">
    <?php if ($url): ?>
        <img src="<?= e($url) ?>" alt="<?= e($alt) ?>" style="<?= $styleStr ?>" class="rs-img">
    <?php else: ?>
        <div class="rs-img-placeholder d-inline-block">
            <i class="bi bi-card-image fs-1 text-muted"></i>
        </div>
    <?php endif; ?>
</div>
