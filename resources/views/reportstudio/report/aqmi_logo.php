<?php
/** AQMI Logo. @var array $config @var string $title */
$size  = $config['size']  ?? 'md';
$align = $config['align'] ?? 'left';
$sizeClass = ['sm' => 'rs-logo-sm', 'md' => 'rs-logo-md', 'lg' => 'rs-logo-lg'][$size] ?? 'rs-logo-md';
?>
<div class="rs-block-aqmi-logo text-<?= e($align) ?> py-2">
    <div class="rs-logo <?= $sizeClass ?> d-inline-block">
        <span class="rs-aqmi-mark">AQMI</span>
    </div>
</div>
