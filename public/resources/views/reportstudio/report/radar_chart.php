<?php
/** Radar Chart — multi-axis spider chart.
 * @var array $config
 * @var string $title
 */
$axes   = $config['axes']  ?? [];
$legend = $config['legend'] ?? true;
$labels = array_map(static fn ($a) => $a['label'] ?? '', $axes);
$values = array_map(static fn ($a) => (int) ($a['value'] ?? 0), $axes);
$chartId = 'rs-radar-' . uniqid();
?>
<div class="rs-block-radar py-2">
    <?php if ($title): ?><h5 class="rs-block-title"><?= e($title) ?></h5><?php endif; ?>
    <div id="<?= $chartId ?>"></div>
    <script>
    (function(){
        var el = document.getElementById('<?= $chartId ?>');
        if (!el || !window.ApexCharts) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'radar', height: 340, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: [{ name: <?= json_encode($title ?: 'Score') ?>, data: <?= json_encode($values) ?> }],
            xaxis: { categories: <?= json_encode($labels) ?>, labels: { style: { fontSize: '12px', fontWeight: 600 } } },
            yaxis: { min: 0, max: 100, show: false },
            legend: { show: <?= $legend ? 'true' : 'false' ?> },
            colors: ['#102A43'],
            fill: { opacity: 0.15, colors: ['#1F6FEB'] },
            stroke: { width: 2, colors: ['#102A43'] },
            markers: { size: 5, colors: ['#102A43'], strokeColor: '#fff', strokeWidth: 2 },
            tooltip: { theme: 'light', y: { formatter: function(v){ return v + ' / 100'; } } },
            plotOptions: { radar: { polygons: { strokeColors: '#EEF2F7', connectorColors: '#EEF2F7' } } }
        });
        chart.render();
    })();
    </script>
</div>
