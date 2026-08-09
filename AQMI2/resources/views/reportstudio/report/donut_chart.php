<?php
/** Donut Chart — proportional data.
 * @var array $config
 */
$series  = $config['series'] ?? [];
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
            chart: { type: 'donut', height: 300, toolbar: { show: false }, fontFamily: 'Inter, Arial, sans-serif' },
            series: <?= json_encode($values) ?>,
            labels: <?= json_encode($labels) ?>,
            legend: { show: <?= json_encode($config['legend'] !== false) ?>, position: 'bottom', fontSize: '12px', fontWeight: 600 },
            colors: ['#102A43', '#2EC4B6', '#455A64', '#C9A227', '#E5484D', '#1F6FEB'],
            stroke: { width: 2, colors: ['#fff'] },
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, fontSize: '1.1rem', fontWeight: 700, color: '#0D1B3E' } } } } },
            tooltip: { theme: 'light' }
        });
        chart.render();
        el.__apexChart = chart;
    })();
    </script>
</div>
