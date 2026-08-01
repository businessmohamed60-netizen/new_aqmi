<?php
/** Gauge — single value indicator. @var array $config @var string $title */
$value = (int) ($config['value'] ?? 0);
$min   = (int) ($config['min']   ?? 0);
$max   = (int) ($config['max']   ?? 100);
$label = $config['label'] ?? ($title ?: 'Indicateur');
$unit  = $config['unit']  ?? '%';
$range = max(1, $max - $min);
$pct   = min(100, max(0, round((($value - $min) / $range) * 100)));
$chartId = 'rs-gauge-' . uniqid();
?>
<div class="rs-block-gauge text-center py-2">
    <h5 class="rs-block-title"><?= e($label) ?></h5>
    <div id="<?= $chartId ?>" style="display:inline-block"></div>
    <div class="rs-gauge-readout fs-4 fw-bold"><?= $value ?><small class="text-muted"><?= e($unit) ?></small></div>
    <script>
    (function(){
        var el = document.getElementById('<?= $chartId ?>');
        if (!el || !window.ApexCharts) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'radialBar', height: 180, sparkline: { enabled: true } },
            series: [<?= $pct ?>],
            plotOptions: {
                radialBar: {
                    startAngle: -135, endAngle: 135,
                    hollow: { size: '62%' },
                    dataLabels: {
                        name: { show: false },
                        value: { show: false }
                    },
                    track: { background: '#e2e8f0' }
                }
            },
            fill: { colors: ['#00897b'] },
            stroke: { lineCap: 'round' }
        });
        chart.render();
    })();
    </script>
</div>
