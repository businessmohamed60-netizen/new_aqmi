<?php
/** KPI Card — premium metric with icon and trend.
 * @var array $config
 * @var string $title
 */
$label   = $config['label']   ?? ($title ?: 'Indicateur');
$value   = $config['value']   ?? 0;
$unit    = $config['unit']    ?? '';
$icon    = $config['icon']    ?? 'bi-check-circle';
$color   = $config['color']   ?? '#0f2845';
$trend   = $config['trend']   ?? '';
$trendUp = ($config['trend_direction'] ?? 'up') === 'up';
$showTrend = $config['show_trend'] ?? true;
$bgColor = $config['bg_color'] ?? '#ffffff';
$borderColor = $config['border_color'] ?? '#e8e0d0';
$iconBg = $config['icon_bg'] ?? true;
$fontSize = $config['font_size'] ?? '1.1rem';
?>
<div class="rs-block-kpi-card" style="border: 1px solid <?= e($borderColor) ?>; background: <?= e($bgColor) ?>;">
    <div style="width:48px;height:48px;border-radius:10px;background:<?= e($color) ?>15;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi <?= e($icon) ?>" style="font-size:1.4rem;color:<?= e($color) ?>"></i>
    </div>
    <div class="flex-grow-1">
        <p class="small mb-0" style="font-weight:600;letter-spacing:0.04em;text-transform:uppercase;color:#8298b5;font-size:0.68rem;"><?= e($label) ?></p>
        <div class="d-flex align-items-baseline gap-2">
            <span style="font-size:<?= e($fontSize) ?>;font-weight:800;color:#0f2845;"><?= e($value) ?></span>
            <?php if ($unit): ?><small style="color:#8298b5;font-size:0.78rem;"><?= e($unit) ?></small><?php endif; ?>
            <?php if ($trend && $showTrend): ?>
                <small style="color: <?= $trendUp ? '#2EC4B6' : '#E5484D' ?>;font-weight:600;font-size:0.72rem;">
                    <i class="bi bi-arrow-<?= $trendUp ? 'up' : 'down' ?>-short"></i><?= e($trend) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
</div>
