<?php
/** Bar Chart — vertical / horizontal.
 * @var array $config
 */
$series      = $config['series'] ?? [];
$horizontal  = !empty($config['horizontal']);
$chartId     = 'rs-bar-' . uniqid();
$categories  = [];
foreach (($series[0]['data'] ?? []) as $d) {
    $categories[] = $d['label'] ?? '';
}
$apexSeries = [];
foreach ($series as $s) {
    $apexSeries[] = ['name' => $s['label'] ?? '', 'data' => array_map(fn($d) => (int)($d['value'] ?? 0), $s['data'] ?? [])];
}
?>
<div class="rs-report-block rs-block-bar-chart">
    <div id="<?= e($chartId) ?>"></div>
    <script>
    (function() {
        if (!window.ApexCharts) return;
        var el = document.getElementById('<?= e($chartId) ?>');
        if (!el || el.__apexChart) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($apexSeries) ?>,
            xaxis: { categories: <?= json_encode($categories) ?>, labels: { style: { fontSize: '12px', fontWeight: 600 } } },
            plotOptions: { bar: { horizontal: <?= json_encode($horizontal) ?>, borderRadius: 6, columnWidth: '55%' } },
            legend: { show: <?= json_encode($config['legend'] !== false) ?>, position: 'bottom' },
            colors: ['#102A43', '#2EC4B6', '#455A64', '#C9A227'],
            fill: { opacity: 0.9 },
            grid: { borderColor: '#EEF2F7', strokeDashArray: 3 },
            tooltip: { theme: 'light' }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
