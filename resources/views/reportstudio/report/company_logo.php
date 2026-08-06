<?php
/** Company Logo. @var array $config @var string $title */
$url   = $config['url']   ?? '';
$size  = $config['size']  ?? 'md';
$align = $config['align'] ?? 'left';
$sizePx = ['sm' => 60, 'md' => 100, 'lg' => 160][$size] ?? 100;
?>
<div class="rs-block-company-logo text-<?= e($align) ?> py-2">
    <?php if ($url): ?>
        <img src="<?= e($url) ?>" alt="Logo" style="max-width: <?= $sizePx ?>px; max-height: <?= $sizePx ?>px;">
    <?php else: ?>
        <div class="d-inline-block rs-logo-placeholder" style="width:<?= $sizePx ?>px;height:<?= $sizePx ?>px;">
            <i class="bi bi-image"></i>
        </div>
    <?php endif; ?>
</div>
