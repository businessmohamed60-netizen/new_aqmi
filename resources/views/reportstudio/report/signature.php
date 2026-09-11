<?php
/** Signature — elegant with optional official stamp. @var array $config @var string $title */
$label    = $config['label'] ?? '';
$role     = $config['role']  ?? '';
$showDate = $config['show_date'] ?? true;
$showStamp = $config['show_stamp'] ?? false;
$lineColor = $config['line_color'] ?? '#0f2845';
$fontSize = $config['font_size'] ?? '0.9rem';
$stampColor = $lineColor;
$dateFormat = $config['date_format'] ?? 'fr-FR';
$dateStr = !empty($config['date']) ? $config['date'] : date('d/m/Y');
?>
<div class="rs-block-signature py-3 d-flex align-items-end gap-3">
    <div class="flex-grow-1">
        <div class="rs-sig-line" style="border-color: <?= e($lineColor) ?>"></div>
        <?php if ($label): ?><p class="mb-0 fw-semibold" style="font-size: <?= e($fontSize) ?>; color: <?= e($lineColor) ?>"><?= e($label) ?></p><?php endif; ?>
        <?php if ($role): ?><p class="text-muted small mb-0" style="font-size: <?= e($fontSize) ?>"><?= e($role) ?></p><?php endif; ?>
        <?php if ($showDate): ?><p class="text-muted small mb-0" style="font-size: 0.78rem; color: #8298b5;">Date : <?= e($dateStr) ?></p><?php endif; ?>
    </div>
    <?php if ($showStamp): ?>
        <div class="rs-sig-stamp" style="opacity: 0.85;">
            <div class="rs-stamp rs-stamp-circular" style="width:80px;height:80px;border-color:<?= e($stampColor) ?>;color:<?= e($stampColor) ?>">
                <div class="rs-stamp-inner" style="position: relative; z-index: 1;">
                    <span class="rs-stamp-text" style="font-size: 0.62rem;">CERTIFIÉ</span>
                    <span class="rs-stamp-subtext" style="font-size: 0.52rem;">AQMI</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
