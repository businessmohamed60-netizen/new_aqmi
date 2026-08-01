<?php
/** Radar Chart — multi-axis. @var array $config @var string $title */
$axes  = $config['axes']  ?? [];
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
            chart: { type: 'radar', height: 320, toolbar: { show: false } },
            series: [{ name: <?= json_encode($title ?: 'Radar') ?>, data: <?= json_encode($values) ?> }],
            xaxis: { categories: <?= json_encode($labels) ?> },
            yaxis: { min: 0, max: 100 },
            legend: { show: <?= $legend ? 'true' : 'false' ?> },
            colors: ['#0d47a1'],
            fill: { opacity: 0.2 },
            markers: { size: 4, colors: ['#0d47a1'] },
            tooltip: { theme: 'light' }
        });
        chart.render();
    })();
    </script>
</div>
