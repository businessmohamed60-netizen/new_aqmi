<?php
/** Official Stamp — AQMI certification seal. @var array $config @var string $title */
$style    = $config['style']    ?? 'circular';
$text     = $config['text']     ?? 'CERTIFIÉ';
$subtext  = $config['subtext']  ?? 'AQMI';
$color    = $config['color']    ?? '#0d47a1';
$size     = (int) ($config['size'] ?? 100);
$align    = $config['align']    ?? 'right';
?>
<div class="rs-block-stamp text-<?= e($align) ?> py-2">
    <div class="rs-stamp rs-stamp-<?= e($style) ?>"
         style="width:<?= $size ?>px;height:<?= $size ?>px;border-color:<?= e($color) ?>;color:<?= e($color) ?>">
        <?php if ($style === 'circular'): ?>
            <div class="rs-stamp-inner">
                <span class="rs-stamp-text"><?= e($text) ?></span>
                <span class="rs-stamp-subtext"><?= e($subtext) ?></span>
            </div>
        <?php elseif ($style === 'rectangular'): ?>
            <div class="rs-stamp-inner rs-stamp-rect-inner">
                <span class="rs-stamp-text"><?= e($text) ?></span>
                <span class="rs-stamp-subtext"><?= e($subtext) ?></span>
            </div>
        <?php else: ?>
            <div class="rs-stamp-inner rs-stamp-star">
                <i class="bi bi-patch-check-fill" style="font-size:<?= (int)($size * 0.5) ?>px;color:<?= e($color) ?>"></i>
                <span class="rs-stamp-text small"><?= e($text) ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
