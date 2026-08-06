<?php
/** Gauge — single value radial indicator.
 * @var array $config
 * @var string $title
 */
$value = (int) ($config['value'] ?? 0);
$min   = (int) ($config['min']   ?? 0);
$max   = (int) ($config['max']   ?? 100);
$label = $config['label'] ?? ($title ?: 'Indicateur');
$unit  = $config['unit']  ?? '%';
$range = max(1, $max - $min);
$pct   = min(100, max(0, round((($value - $min) / $range) * 100)));
$chartId = 'rs-gauge-' . uniqid();
$gaugeColor = $pct >= 75 ? '#2EC4B6' : ($pct >= 50 ? '#1F6FEB' : ($pct >= 25 ? '#C9A227' : '#E5484D'));
?>
<div class="rs-block-gauge text-center py-2">
    <h5 class="rs-block-title"><?= e($label) ?></h5>
    <div id="<?= $chartId ?>" style="display:inline-block"></div>
    <div class="rs-gauge-readout fs-4 fw-bold" style="color:<?= $gaugeColor ?>"><?= $value ?><small class="text-muted"><?= e($unit) ?></small></div>
    <script>
    (function(){
        var el = document.getElementById('<?= $chartId ?>');
        if (!el || !window.ApexCharts) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'radialBar', height: 200, sparkline: { enabled: true }, fontFamily: 'Inter, Arial, sans-serif' },
            series: [<?= $pct ?>],
            plotOptions: {
                radialBar: {
                    startAngle: -135, endAngle: 135,
                    hollow: { size: '64%' },
                    dataLabels: { name: { show: false }, value: { show: false } },
                    track: { background: '#EEF2F7', strokeWidth: '100%' }
                }
            },
            fill: { colors: ['<?= $gaugeColor ?>'] },
            stroke: { lineCap: 'round' }
        });
        chart.render();
    })();
    </script>
</div>
