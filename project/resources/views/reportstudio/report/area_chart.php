<?php
/** @var array $config block config */
$series = $config['series'] ?? [];
$chartId = 'rs-area-' . uniqid();
$categories = [];
foreach (($series[0]['data'] ?? []) as $d) {
    $categories[] = $d['label'] ?? '';
}
$apexSeries = [];
foreach ($series as $s) {
    $apexSeries[] = ['name' => $s['label'] ?? '', 'data' => array_map(fn($d) => (int)($d['value'] ?? 0), $s['data'] ?? [])];
}
?>
<div class="rs-report-block rs-block-area-chart">
    <div id="<?= e($chartId) ?>"></div>
    <script>
    (function() {
        if (!window.ApexCharts) return;
        var el = document.getElementById('<?= e($chartId) ?>');
        if (!el || el.__apexChart) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($apexSeries) ?>,
            xaxis: { categories: <?= json_encode($categories) ?> },
            stroke: { curve: <?= json_encode($config['smooth'] !== false ? 'smooth' : 'straight') ?>, width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
            legend: { show: <?= json_encode($config['legend'] !== false) ?> },
            colors: ['#0d47a1', '#00897b'],
            markers: { size: 3 }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
