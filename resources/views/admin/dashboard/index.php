<?php
$title = 'Dashboard';
ob_start();
?>

<!-- Welcome Card -->
<div class="nova-welcome-card mb-4">
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:1;">
        <div>
            <div class="nova-welcome-title">Tableau de Bord</div>
            <div class="nova-welcome-sub"><?= $stats['total_assessments'] ?> évaluations sur la plateforme</div>
            <div class="d-flex align-items-center gap-4 mt-2">
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value"><?= $stats['completed_assessments'] ?></span>
                    <span class="nova-welcome-stat-label">Complétées</span>
                </div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value"><?= number_format($stats['average_score'], 1) ?>%</span>
                    <span class="nova-welcome-stat-label">Score moyen</span>
                </div>
            </div>
        </div>
        <div class="d-none d-md-block">
            <i class="fas fa-microchip" style="font-size:3.5rem;opacity:0.1;color:var(--vx-primary);"></i>
        </div>
    </div>
</div>

<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <?php
    $metrics = [
        ['label' => 'Évaluations', 'value' => $stats['total_assessments'], 'icon' => 'fa-clipboard-check', 'color' => 'var(--vx-primary)'],
        ['label' => 'Complétées', 'value' => $stats['completed_assessments'], 'icon' => 'fa-check-circle', 'color' => 'var(--vx-success)'],
        ['label' => 'Prospects', 'value' => $stats['total_leads'], 'icon' => 'fa-users', 'color' => 'var(--vx-info)'],
        ['label' => 'Score Moyen', 'value' => number_format($stats['average_score'], 1) . '%', 'icon' => 'fa-chart-line', 'color' => 'var(--vx-warning)'],
        ['label' => 'Modèles', 'value' => $stats['total_models'], 'icon' => 'fa-layer-group', 'color' => 'var(--vx-primary)'],
        ['label' => 'Questions', 'value' => $stats['total_questions'], 'icon' => 'fa-question-circle', 'color' => 'var(--vx-danger)'],
    ];
    foreach ($metrics as $m):
    ?>
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="nova-metric-card">
                <div class="nova-metric-icon" style="background:<?= $m['color'] ?>12;color:<?= $m['color'] ?>;border:1px solid <?= $m['color'] ?>25;">
                    <i class="fas <?= $m['icon'] ?>"></i>
                </div>
                <div>
                    <div class="nova-metric-value"><?= e($m['value']) ?></div>
                    <div class="nova-metric-label"><?= $m['label'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Models Overview -->
<?php if (!empty($stats['models_stats'])): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-layer-group me-2"></i>Modèles d'Évaluation</span>
        <a href="/admin/evaluation-models" class="btn btn-outline-secondary btn-sm">Gérer</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($stats['models_stats'] as $ms): ?>
                <div class="col-md-4">
                    <div class="nova-metric-card" style="min-height:60px;padding:0.85rem;">
                        <div class="nova-metric-icon" style="width:36px;height:36px;background:<?= $ms['color'] ?>15;color:<?= $ms['color'] ?>;border:1px solid <?= $ms['color'] ?>25;font-size:0.85rem;">
                            <i class="fas <?= $ms['icon'] ?>"></i>
                        </div>
                        <div>
                            <div style="color:var(--vx-text-primary);font-weight:600;font-size:0.8rem;"><?= e($ms['name']) ?></div>
                            <div style="color:var(--vx-text-muted);font-size:0.65rem;"><?= $ms['domains_count'] ?> domaines · <?= $ms['questions_count'] ?> questions</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Monthly Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 style="color:var(--vx-text-primary);font-weight:600;margin-bottom:0;font-size:0.85rem;">Évolution Mensuelle</h6>
                    <span class="badge" style="background:var(--vx-primary-light);color:var(--vx-primary);border:1px solid rgba(115,103,240,0.15);">12 mois</span>
                </div>
                <div id="monthlyChart"></div>
            </div>
        </div>
    </div>

    <!-- Score Distribution -->
    <div class="col-lg-4">
        <div class="card" style="height:100%;">
            <div class="card-body">
                <h6 style="color:var(--vx-text-primary);font-weight:600;margin-bottom:1rem;font-size:0.85rem;">Répartition des Scores</h6>
                <canvas id="scoreDistChart" height="240"></canvas>
            </div>
        </div>
    </div>

    <!-- Domain Averages -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 style="color:var(--vx-text-primary);font-weight:600;margin-bottom:1rem;font-size:0.85rem;">Moyennes par Domaine</h6>
                <canvas id="domainChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="col-lg-6">
        <div class="card" style="height:100%;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-2"></i>Prospects Récents</span>
                <a href="/admin/leads" class="btn btn-outline-secondary btn-sm">Voir tout</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead><tr><th>Nom</th><th>Entreprise</th><th>Secteur</th><th>Score</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (!empty($stats['recent_leads'])): ?>
                            <?php foreach ($stats['recent_leads'] as $l): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--vx-text-primary);">
                                        <a href="/admin/leads/detail/<?= $l['id'] ?>" style="color:var(--vx-text-primary);text-decoration:none;"><?= e($l['firstname'] . ' ' . $l['lastname']) ?></a>
                                    </td>
                                    <td><?= e($l['company']) ?></td>
                                    <td><span class="badge" style="background:var(--vx-primary-light);color:var(--vx-primary);"><?= e($l['sector'] ?? '-') ?></span></td>
                                    <td>
                                        <?php if ($l['total_score']): ?>
                                            <span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);"><?= e($l['total_score']) ?>%</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:var(--vx-input-bg);color:var(--vx-text-muted);">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color:var(--vx-text-muted);font-size:0.7rem;"><?= formatDate($l['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;color:var(--vx-text-muted);padding:2rem;">Aucun prospect</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="/admin/evaluation-models/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau modèle</a>
                    <a href="/admin/questions/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle question</a>
                    <a href="/admin/leads" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye me-1"></i>Voir les prospects</a>
                    <a href="/admin/lead-fields" class="btn btn-outline-secondary btn-sm"><i class="fas fa-id-card me-1"></i>Champs personnalisés</a>
                    <a href="/admin/reports" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-alt me-1"></i>Rapports PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$monthlyLabels = json_encode(array_column($chartData['monthly_assessments'], 'month'));
$monthlyData = json_encode(array_column($chartData['monthly_assessments'], 'total'));
$scoreDistData = json_encode(array_map(fn($d) => $d['count'], $chartData['score_distribution']));
$scoreDistLabels = json_encode(array_map(fn($d) => $d['level'], $chartData['score_distribution']));
$scoreDistColors = json_encode(array_map(fn($d) => $d['color'], $chartData['score_distribution']));
$domainNames = json_encode(array_map(fn($d) => $d['name_fr'] ?: $d['name'], $domainAverages));
$domainAvgs = json_encode(array_map(fn($d) => round((float)$d['avg_percent'], 1), $domainAverages));

$extraScripts = <<<SCRIPTS
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart - ApexCharts
    var monthlyOptions = {
        chart: { type: 'area', height: 280, toolbar: { show: false }, background: 'transparent', foreColor: 'var(--vx-text-muted)' },
        series: [{ name: 'Évaluations', data: {$monthlyData} }],
        xaxis: { categories: {$monthlyLabels}, labels: { style: { fontSize: '11px', colors: 'var(--vx-text-muted)' } } },
        yaxis: { labels: { style: { colors: 'var(--vx-text-muted)' } } },
        colors: ['var(--vx-primary)'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, colorStops: [{ offset: 0, color: '#7367f0', opacity: 0.3 }, { offset: 100, color: '#7367f0', opacity: 0.05 }] } },
        stroke: { curve: 'smooth', width: 2, colors: ['#7367f0'] },
        dataLabels: { enabled: false },
        grid: { borderColor: 'rgba(115,103,240,0.06)' },
        tooltip: { theme: 'dark' }
    };
    if (document.getElementById('monthlyChart')) {
        new ApexCharts(document.getElementById('monthlyChart'), monthlyOptions).render();
    }

    // Score Distribution Pie
    var scoreCtx = document.getElementById('scoreDistChart');
    if (scoreCtx) {
        new Chart(scoreCtx, {
            type: 'doughnut',
            data: {
                labels: {$scoreDistLabels},
                datasets: [{ data: {$scoreDistData}, backgroundColor: {$scoreDistColors}, borderWidth: 0 }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, color: 'var(--vx-text-secondary)' } }
                }
            }
        });
    }

    // Domain Averages Bar
    var domainCtx = document.getElementById('domainChart');
    if (domainCtx) {
        new Chart(domainCtx, {
            type: 'bar',
            data: {
                labels: {$domainNames},
                datasets: [{
                    label: 'Moyenne %', data: {$domainAvgs},
                    backgroundColor: 'rgba(115,103,240,0.6)', borderRadius: 4, borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, max: 100, grid: { color: 'rgba(115,103,240,0.04)' }, ticks: { color: 'var(--vx-text-muted)' } },
                    y: { grid: { display: false }, ticks: { color: 'var(--vx-text-secondary)', font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
SCRIPTS;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>