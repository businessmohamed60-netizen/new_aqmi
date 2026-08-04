<?php
/** KPI Card — single metric with icon and trend.
 * @var array $config
 * @var string $title
 */
$label   = $config['label']   ?? ($title ?: 'Indicateur');
$value   = $config['value']   ?? 0;
$unit    = $config['unit']    ?? '';
$icon    = $config['icon']    ?? 'bi-check-circle';
$color   = $config['color']   ?? '#102A43';
$trend   = $config['trend']   ?? '';
$trendUp = ($config['trend_direction'] ?? 'up') === 'up';
?>
<div class="rs-block-kpi-card" style="border: 1px solid #EEF2F7; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px;">
    <div style="width:48px;height:48px;border-radius:10px;background:<?= e($color) ?>15;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi <?= e($icon) ?>" style="font-size:1.4rem;color:<?= e($color) ?>"></i>
    </div>
    <div class="flex-grow-1">
        <p class="small text-muted mb-0" style="font-weight:600;letter-spacing:0.03em;text-transform:uppercase;"><?= e($label) ?></p>
        <div class="d-flex align-items-baseline gap-2">
            <span style="font-size:1.6rem;font-weight:800;color:#0D1B3E;"><?= e($value) ?></span>
            <?php if ($unit): ?><small class="text-muted"><?= e($unit) ?></small><?php endif; ?>
            <?php if ($trend): ?>
                <small style="color: <?= $trendUp ? '#2EC4B6' : '#E5484D' ?>;font-weight:600;">
                    <i class="bi bi-arrow-<?= $trendUp ? 'up' : 'down' ?>-short"></i><?= e($trend) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
</div>
