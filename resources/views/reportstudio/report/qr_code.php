<?php
/** QR Code — framed with subtle gold border. @var array $config @var string $title */
$mode  = $config['mode']  ?? 'manual';
$value = $config['value'] ?? '';
$size  = (int) ($config['size']  ?? 120);
$label = $config['label'] ?? '';
$align = $config['align'] ?? 'center';
?>
<div class="rs-block-qr text-<?= e($align) ?> py-2" style="display: flex; flex-direction: column; align-items: <?= $align === 'left' ? 'flex-start' : ($align === 'right' ? 'flex-end' : 'center') ?>;">
    <?php if ($mode === 'verify'): ?>
        <div class="rs-qr-placeholder" style="width:<?= $size ?>px;height:<?= $size ?>px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;">
            <i class="bi bi-shield-check" style="font-size:2rem;"></i>
            <span style="font-size:0.62rem;font-weight:600;">Vérification certificat</span>
        </div>
    <?php elseif ($value): ?>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=<?= $size ?>x<?= $size ?>&data=<?= urlencode($value) ?>"
             width="<?= $size ?>" height="<?= $size ?>" alt="QR Code" class="rs-qr-img">
    <?php else: ?>
        <div class="rs-qr-placeholder" style="width:<?= $size ?>px;height:<?= $size ?>px;">
            <i class="bi bi-qr-code" style="font-size:2.5rem;"></i>
        </div>
    <?php endif; ?>
    <?php if ($label): ?><p class="small mt-1 mb-0" style="color: #8298b5; font-size: 0.72rem;"><?= e($label) ?></p><?php endif; ?>
</div>
