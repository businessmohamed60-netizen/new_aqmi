<?php
/** @var array $config block config */
$series = $config['series'] ?? [];
$horizontal = !empty($config['horizontal']);
$chartId = 'rs-bar-' . uniqid();
$categories = [];
$values = [];
foreach (($series[0]['data'] ?? []) as $d) {
    $categories[] = $d['label'] ?? '';
    $values[] = (int)($d['value'] ?? 0);
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
            chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($apexSeries) ?>,
            xaxis: { categories: <?= json_encode($categories) ?> },
            plotOptions: { bar: { horizontal: <?= json_encode($horizontal) ?>, borderRadius: 4 } },
            legend: { show: <?= json_encode($config['legend'] !== false) ?> },
            colors: ['#0d47a1', '#00897b', '#546e7a', '#d97706'],
            fill: { opacity: 0.9 }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
