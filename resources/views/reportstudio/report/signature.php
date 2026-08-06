<?php
/** Signature — with optional official stamp. @var array $config @var string $title */
$label    = $config['label'] ?? '';
$role     = $config['role']  ?? '';
$showDate = $config['show_date'] ?? true;
$showStamp = $config['show_stamp'] ?? false;
$stampColor = '#102A43';
?>
<div class="rs-block-signature py-3 d-flex align-items-end gap-3">
    <div class="flex-grow-1">
        <div class="rs-sig-line"></div>
        <?php if ($label): ?><p class="mb-0 fw-semibold"><?= e($label) ?></p><?php endif; ?>
        <?php if ($role): ?><p class="text-muted small mb-0"><?= e($role) ?></p><?php endif; ?>
        <?php if ($showDate): ?><p class="text-muted small">Date : ________________</p><?php endif; ?>
    </div>
    <?php if ($showStamp): ?>
        <div class="rs-sig-stamp">
            <div class="rs-stamp rs-stamp-circular" style="width:80px;height:80px;border-color:<?= e($stampColor) ?>;color:<?= e($stampColor) ?>">
                <div class="rs-stamp-inner">
                    <span class="rs-stamp-text">CERTIFIÉ</span>
                    <span class="rs-stamp-subtext">AQMI</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
