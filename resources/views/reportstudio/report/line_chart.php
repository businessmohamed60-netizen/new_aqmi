<?php
/** Line Chart — trend line with multiple series.
 * @var array $config
 */
$series  = $config['series'] ?? [];
$chartId = 'rs-line-' . uniqid();
$categories = [];
foreach (($series[0]['data'] ?? []) as $d) {
    $categories[] = $d['label'] ?? '';
}
$apexSeries = [];
foreach ($series as $s) {
    $apexSeries[] = ['name' => $s['label'] ?? '', 'data' => array_map(fn($d) => (int)($d['value'] ?? 0), $s['data'] ?? [])];
}
?>
<div class="rs-report-block rs-block-line-chart">
    <div id="<?= e($chartId) ?>"></div>
    <script>
    (function() {
        if (!window.ApexCharts) return;
        var el = document.getElementById('<?= e($chartId) ?>');
        if (!el || el.__apexChart) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'line', height: 300, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($apexSeries) ?>,
            xaxis: { categories: <?= json_encode($categories) ?>, labels: { style: { fontSize: '12px', fontWeight: 600 } } },
            stroke: { curve: <?= json_encode($config['smooth'] !== false ? 'smooth' : 'straight') ?>, width: 3 },
            legend: { show: <?= json_encode($config['legend'] !== false) ?>, position: 'bottom' },
            colors: ['#102A43', '#2EC4B6', '#C9A227', '#E5484D'],
            markers: { size: 5, strokeWidth: 2, strokeColors: '#fff' },
            grid: { borderColor: '#EEF2F7', strokeDashArray: 3 },
            tooltip: { theme: 'light' }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
