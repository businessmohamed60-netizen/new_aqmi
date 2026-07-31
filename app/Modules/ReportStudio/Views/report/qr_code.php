<?php
/** QR Code. @var array $config @var string $title */
$value = $config['value'] ?? '';
$size  = (int) ($config['size']  ?? 120);
$label = $config['label'] ?? '';
?>
<div class="rs-block-qr text-center py-2">
    <?php if ($value): ?>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=<?= $size ?>x<?= $size ?>&data=<?= urlencode($value) ?>"
             width="<?= $size ?>" height="<?= $size ?>" alt="QR Code" class="rs-qr-img">
    <?php else: ?>
        <div class="rs-qr-placeholder" style="width:<?= $size ?>px;height:<?= $size ?>px;">
            <i class="bi bi-qr-code fs-1"></i>
        </div>
    <?php endif; ?>
    <?php if ($label): ?><p class="small text-muted mt-1 mb-0"><?= e($label) ?></p><?php endif; ?>
</div>
