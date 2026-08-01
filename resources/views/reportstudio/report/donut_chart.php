<?php
/** @var array $config block config */
$series = $config['series'] ?? [];
$chartId = 'rs-donut-' . uniqid();
$values = [];
$labels = [];
foreach ($series as $s) {
    $values[] = (int)($s['value'] ?? 0);
    $labels[] = $s['label'] ?? '';
}
?>
<div class="rs-report-block rs-block-donut-chart">
    <div id="<?= e($chartId) ?>"></div>
    <script>
    (function() {
        if (!window.ApexCharts) return;
        var el = document.getElementById('<?= e($chartId) ?>');
        if (!el || el.__apexChart) return;
        var chart = new ApexCharts(el, {
            chart: { type: 'donut', height: 280, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($values) ?>,
            labels: <?= json_encode($labels) ?>,
            legend: { show: <?= json_encode($config['legend'] !== false) ?>, position: 'bottom' },
            colors: ['#0d47a1', '#00897b', '#546e7a', '#d97706', '#dc2626', '#7c3aed'],
            stroke: { width: 2 }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
