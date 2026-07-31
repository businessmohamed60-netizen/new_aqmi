<?php
/** Radar Chart — multi-axis. @var array $config @var string $title */
$axes  = $config['axes']  ?? [];
$legend = $config['legend'] ?? true;
$labels = array_map(static fn ($a) => $a['label'] ?? '', $axes);
$values = array_map(static fn ($a) => (int) ($a['value'] ?? 0), $axes);
$canvasId = 'rs-radar-' . uniqid();
?>
<div class="rs-block-radar py-2">
    <?php if ($title): ?><h5 class="rs-block-title"><?= e($title) ?></h5><?php endif; ?>
    <canvas id="<?= $canvasId ?>" height="320"></canvas>
    <script>
    (function(){
        var ctx = document.getElementById('<?= $canvasId ?>');
        if (!ctx || !window.Chart) return;
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: <?= json_encode($title ?: 'Radar') ?>,
                    data: <?= json_encode($values) ?>,
                    fill: true,
                    backgroundColor: 'rgba(13,71,161,0.2)',
                    borderColor: 'rgb(13,71,161)',
                    pointBackgroundColor: 'rgb(13,71,161)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: <?= $legend ? 'true' : 'false' ?> } },
                scales: { r: { beginAtZero: true, max: 100 } }
            }
        });
    })();
    </script>
</div>
