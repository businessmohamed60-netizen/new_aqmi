<?php
/** QR Code. @var array $config @var string $title */
$mode  = $config['mode']  ?? 'manual';
$value = $config['value'] ?? '';
$size  = (int) ($config['size']  ?? 120);
$label = $config['label'] ?? '';
?>
<div class="rs-block-qr text-center py-2">
    <?php if ($mode === 'verify'): ?>
        <div class="rs-qr-placeholder" style="width:<?= $size ?>px;height:<?= $size ?>px;display:flex;align-items:center;justify-content:center;border:2px dashed #6366f1;border-radius:8px;background:#eef2ff;color:#4338ca;flex-direction:column;gap:6px;">
            <i class="bi bi-shield-check" style="font-size:2rem;"></i>
            <span style="font-size:0.65rem;font-weight:600;">Vérification certificat</span>
        </div>
    <?php elseif ($value): ?>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=<?= $size ?>x<?= $size ?>&data=<?= urlencode($value) ?>"
             width="<?= $size ?>" height="<?= $size ?>" alt="QR Code" class="rs-qr-img">
    <?php else: ?>
        <div class="rs-qr-placeholder" style="width:<?= $size ?>px;height:<?= $size ?>px;">
            <i class="bi bi-qr-code fs-1"></i>
        </div>
    <?php endif; ?>
    <?php if ($label): ?><p class="small text-muted mt-1 mb-0"><?= e($label) ?></p><?php endif; ?>
</div>
