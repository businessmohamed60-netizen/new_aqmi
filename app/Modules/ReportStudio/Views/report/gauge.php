<?php
/** Gauge — single value indicator. @var array $config @var string $title */
$value = (int) ($config['value'] ?? 0);
$min   = (int) ($config['min']   ?? 0);
$max   = (int) ($config['max']   ?? 100);
$label = $config['label'] ?? ($title ?: 'Indicateur');
$unit  = $config['unit']  ?? '%';
$range = max(1, $max - $min);
$pct   = min(100, max(0, round((($value - $min) / $range) * 100)));
$canvasId = 'rs-gauge-' . uniqid();
?>
<div class="rs-block-gauge text-center py-2">
    <h5 class="rs-block-title"><?= e($label) ?></h5>
    <canvas id="<?= $canvasId ?>" width="240" height="140"></canvas>
    <div class="rs-gauge-readout fs-4 fw-bold"><?= $value ?><small class="text-muted"><?= e($unit) ?></small></div>
    <script>
    (function(){
        var ctx = document.getElementById('<?= $canvasId ?>');
        if (!ctx || !window.Chart) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [<?= $pct ?>, <?= 100 - $pct ?>],
                    backgroundColor: ['rgb(0,137,123)', 'rgba(84,110,122,0.15)'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: { responsive: true, cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    })();
    </script>
</div>
