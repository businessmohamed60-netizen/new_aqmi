<?php
$title = 'Dashboard';
ob_start();
?>

<!-- Welcome Hero -->
<div class="nova-welcome-card mb-4">
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:1;">
        <div>
            <div class="nova-welcome-title">Tableau de Bord</div>
            <div class="nova-welcome-sub"><?= $stats['total_assessments'] ?> évaluations sur la plateforme</div>
            <div class="d-flex align-items-center gap-4 mt-3">
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-success-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $stats['completed_assessments'] ?></span>
                    <span class="nova-welcome-stat-label">Complétées</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= number_format($stats['average_score'], 1) ?>%</span>
                    <span class="nova-welcome-stat-label">Score moyen</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-info-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= number_format($stats['completion_rate'], 0) ?>%</span>
                    <span class="nova-welcome-stat-label">Taux complétion</span>
                </div>
            </div>
        </div>
        <div class="d-none d-md-block" style="position:relative;">
            <div style="width:72px;height:72px;border-radius:var(--vx-radius-xl);background:var(--vx-primary-gradient);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;box-shadow:0 8px 32px var(--vx-primary-glow);">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards with Sparklines -->
<div class="row g-3 mb-4">
    <?php
    $metrics = [
        ['label' => 'Évaluations', 'value' => $stats['total_assessments'], 'icon' => 'fa-clipboard-check', 'color' => '#6366f1', 'gradient' => 'var(--vx-primary-gradient)', 'trend' => '+12%', 'trendUp' => true],
        ['label' => 'Complétées', 'value' => $stats['completed_assessments'], 'icon' => 'fa-check-circle', 'color' => '#10b981', 'gradient' => 'var(--vx-success-gradient)', 'trend' => '+8%', 'trendUp' => true],
        ['label' => 'Prospects', 'value' => $stats['total_leads'], 'icon' => 'fa-users', 'color' => '#06b6d4', 'gradient' => 'var(--vx-info-gradient)', 'trend' => '+24%', 'trendUp' => true],
        ['label' => 'Score Moyen', 'value' => number_format($stats['average_score'], 1) . '%', 'icon' => 'fa-chart-line', 'color' => '#f59e0b', 'gradient' => 'var(--vx-warning-gradient)', 'trend' => '+3.2%', 'trendUp' => true],
        ['label' => 'Modèles', 'value' => $stats['total_models'], 'icon' => 'fa-layer-group', 'color' => '#8b5cf6', 'gradient' => 'linear-gradient(135deg,#8b5cf6,#a78bfa)', 'trend' => 'Stable', 'trendUp' => null],
        ['label' => 'Questions', 'value' => $stats['total_questions'], 'icon' => 'fa-question-circle', 'color' => '#ef4444', 'gradient' => 'var(--vx-danger-gradient)', 'trend' => '+5', 'trendUp' => true],
    ];
    foreach ($metrics as $i => $m):
    ?>
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="nova-kpi-card" style="--kpi-color:<?= $m['color'] ?>;">
                <div class="nova-kpi-top">
                    <div class="nova-kpi-icon" style="background:<?= $m['color'] ?>18;color:<?= $m['color'] ?>;border:1px solid <?= $m['color'] ?>28;">
                        <i class="fas <?= $m['icon'] ?>"></i>
                    </div>
                    <div class="nova-kpi-trend <?= $m['trendUp'] === true ? 'up' : ($m['trendUp'] === false ? 'down' : 'neutral') ?>">
                        <i class="fas fa-arrow-<?= $m['trendUp'] === true ? 'up' : ($m['trendUp'] === false ? 'down' : 'right') ?>"></i>
                        <?= e($m['trend']) ?>
                    </div>
                </div>
                <div class="nova-kpi-value" style="background:<?= $m['gradient'] ?>;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= e($m['value']) ?></div>
                <div class="nova-kpi-label"><?= $m['label'] ?></div>
                <div class="nova-kpi-spark" id="spark-<?= $i ?>"></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Main Grid: Gauge + Radar + Donut -->
<div class="row g-4 mb-4">
    <!-- Radial Gauge - Average Score -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="nova-card-title"><i class="fas fa-gauge-high me-2" style="color:var(--vx-primary);"></i>Score Global</h6>
                    <span class="nova-badge-primary">AQMI</span>
                </div>
                <div id="scoreGauge" class="flex-grow-1 d-flex align-items-center justify-content-center"></div>
                <div class="nova-gauge-footer">
                    <div class="nova-gauge-stat">
                        <span class="nova-gauge-stat-dot" style="background:var(--vx-success);"></span>
                        <span>Niveau: <strong id="gaugeLevel"><?= $stats['average_score'] >= 86 ? 'Excellence' : ($stats['average_score'] >= 71 ? 'Performant' : ($stats['average_score'] >= 51 ? 'Structuré' : ($stats['average_score'] >= 31 ? 'En Développement' : 'Débutant'))) ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Radar Chart - Domain Performance -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="nova-card-title"><i class="fas fa-bullseye me-2" style="color:var(--vx-info);"></i>Performance par Domaine</h6>
                    <span class="nova-badge-info">10 domaines</span>
                </div>
                <div id="radarChart" class="flex-grow-1 d-flex align-items-center justify-content-center"></div>
            </div>
        </div>
    </div>

    <!-- Donut Chart - Score Distribution -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="nova-card-title"><i class="fas fa-chart-pie me-2" style="color:var(--vx-warning);"></i>Répartition des Scores</h6>
                    <span class="nova-badge-warning">Niveaux</span>
                </div>
                <div id="scoreDonut" class="flex-grow-1 d-flex align-items-center justify-content-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row: Area Chart + Domain Bars -->
<div class="row g-4 mb-4">
    <!-- Monthly Area Chart -->
    <div class="col-lg-8">
        <div class="card nova-glass-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="nova-card-title"><i class="fas fa-chart-area me-2" style="color:var(--vx-primary);"></i>Évolution Mensuelle</h6>
                    <div class="d-flex gap-2">
                        <span class="nova-legend-dot" style="--dot:var(--vx-primary);">Évaluations</span>
                        <span class="nova-legend-dot" style="--dot:var(--vx-info);">Prospects</span>
                    </div>
                </div>
                <div id="monthlyChart"></div>
            </div>
        </div>
    </div>

    <!-- Domain Averages Bar -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-body">
                <h6 class="nova-card-title mb-3"><i class="fas fa-list-ol me-2" style="color:var(--vx-success);"></i>Moyennes par Domaine</h6>
                <div id="domainBars"></div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Certification Requests -->
<?php if (!empty($stats['pending_certifications'])): ?>
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card nova-glass-card" style="border-color:rgba(245,158,11,0.35);box-shadow:0 0 24px rgba(245,158,11,0.08);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:rgba(245,158,11,0.04);border-bottom:1px solid rgba(245,158,11,0.18);">
                <span class="nova-card-title" style="margin:0;color:var(--vx-warning);">
                    <i class="fas fa-certificate me-2"></i>Demandes de Certification en attente
                </span>
                <span class="nova-badge-warning"><?= $stats['pending_certifications_count'] ?> à traiter</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="table nova-table">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Score</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['pending_certifications'] as $pc):
                            $pcStatus = $pc['status'] === 'certification_requested'
                                ? '<span class="nova-badge-warning"><i class="fas fa-hourglass me-1"></i>En attente</span>'
                                : '<span class="nova-badge-info"><i class="fas fa-magnifying-glass me-1"></i>En examen</span>';
                        ?>
                            <tr>
                                <td style="font-weight:600;color:var(--vx-text-primary);"><?= e($pc['company'] ?? 'N/A') ?></td>
                                <td><?= e(trim(($pc['firstname'] ?? '') . ' ' . ($pc['lastname'] ?? ''))) ?></td>
                                <td style="font-size:0.72rem;color:var(--vx-text-muted);"><?= e($pc['email'] ?? '-') ?></td>
                                <td>
                                    <?php if (isset($pc['total_score']) && $pc['total_score'] !== null): ?>
                                        <span style="font-weight:700;color:var(--vx-success);"><?= round((float)$pc['total_score']) ?>%</span>
                                    <?php else: ?>
                                        <span style="color:var(--vx-text-muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $pcStatus ?></td>
                                <td style="color:var(--vx-text-muted);font-size:0.7rem;"><?= formatDate($pc['certification_requested_at']) ?></td>
                                <td class="text-center">
                                    <a href="/admin/reports/<?= $pc['id'] ?>" class="btn btn-warning btn-sm" style="font-size:0.72rem;">
                                        <i class="fas fa-folder-open me-1"></i> Ouvrir le dossier
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Third Row: Models + Recent Leads -->
<div class="row g-4">
    <!-- Models Overview -->
    <?php if (!empty($stats['models_stats'])): ?>
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-layer-group me-2" style="color:var(--vx-primary);"></i>Modèles d'Évaluation</span>
                <a href="/admin/evaluation-models" class="btn btn-outline-secondary btn-sm">Gérer</a>
            </div>
            <div class="card-body">
                <?php foreach ($stats['models_stats'] as $ms): ?>
                    <div class="nova-model-item">
                        <div class="nova-model-icon" style="background:<?= $ms['color'] ?>18;color:<?= $ms['color'] ?>;border:1px solid <?= $ms['color'] ?>28;">
                            <i class="fas <?= $ms['icon'] ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="nova-model-name"><?= e($ms['name']) ?></div>
                            <div class="nova-model-meta"><?= $ms['domains_count'] ?> domaines · <?= $ms['questions_count'] ?> questions</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Leads -->
    <div class="col-lg-8">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-users me-2" style="color:var(--vx-info);"></i>Prospects Récents</span>
                <a href="/admin/leads" class="btn btn-outline-secondary btn-sm">Voir tout</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="table nova-table">
                    <thead><tr><th>Nom</th><th>Entreprise</th><th>Secteur</th><th>Score</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (!empty($stats['recent_leads'])): ?>
                            <?php foreach ($stats['recent_leads'] as $l): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--vx-text-primary);">
                                        <a href="/admin/leads/detail/<?= $l['id'] ?>" style="color:var(--vx-text-primary);text-decoration:none;"><?= e($l['firstname'] . ' ' . $l['lastname']) ?></a>
                                    </td>
                                    <td><?= e($l['company']) ?></td>
                                    <td><span class="badge" style="background:var(--vx-info-light);color:var(--vx-info);"><?= e($l['sector'] ?? '-') ?></span></td>
                                    <td>
                                        <?php if ($l['total_score']): ?>
                                            <div class="nova-mini-progress">
                                                <div class="nova-mini-progress-bar" style="width:<?= (float)$l['total_score'] ?>%;background:var(--vx-success-gradient);"></div>
                                                <span class="nova-mini-progress-text"><?= e($l['total_score']) ?>%</span>
                                            </div>
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
        <div class="card nova-glass-card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="/admin/evaluation-models/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau modèle</a>
                    <a href="/admin/questions/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle question</a>
                    <a href="/admin/leads" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye me-1"></i>Voir les prospects</a>
                    <a href="/admin/lead-fields" class="btn btn-outline-secondary btn-sm"><i class="fas fa-id-card me-1"></i>Champs personnalisés</a>
                    <a href="/admin/reports" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-alt me-1"></i>Rapports PDF</a>
                    <a href="/admin/reportstudio" class="btn btn-outline-primary btn-sm"><i class="fas fa-palette me-1"></i>Report Studio</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$monthlyLabels = json_encode(array_column($chartData['monthly_assessments'], 'month'));
$monthlyData = json_encode(array_column($chartData['monthly_assessments'], 'total'));
$monthlyLeadsData = json_encode(array_column($chartData['monthly_leads'], 'total'));
$scoreDistData = json_encode(array_map(fn($d) => $d['count'], $chartData['score_distribution']));
$scoreDistLabels = json_encode(array_map(fn($d) => $d['level'], $chartData['score_distribution']));
$scoreDistColors = json_encode(array_map(fn($d) => $d['color'], $chartData['score_distribution']));
$domainNames = json_encode(array_map(fn($d) => $d['name_fr'] ?: $d['name'], $domainAverages));
$domainAvgs = json_encode(array_map(fn($d) => round((float)$d['avg_percent'], 1), $domainAverages));
$avgScore = number_format($stats['average_score'], 1);

$extraScripts = <<<SCRIPTS
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sparkData = [
        [3,5,4,6,5,7,6,8,7,9,8,10],
        [1,2,2,3,4,4,5,5,6,7,7,8],
        [0,1,1,2,2,3,3,4,4,5,5,6],
        [45,48,52,55,58,62,65,68,70,72,75,79],
        [3,3,3,3,3,3,3,3,3,3,3,3],
        [42,43,44,45,46,47,48,49,50,50,50,50]
    ];
    var sparkColors = ['#6366f1','#10b981','#06b6d4','#f59e0b','#8b5cf6','#ef4444'];

    sparkData.forEach(function(data, i) {
        var el = document.getElementById('spark-' + i);
        if (el) {
            new ApexCharts(el, {
                chart: { type: 'area', height: 40, sparkline: { enabled: true }, animations: { enabled: true, speed: 800 } },
                series: [{ data: data }],
                colors: [sparkColors[i]],
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] } },
                tooltip: { enabled: false }
            }).render();
        }
    });

    // Score Gauge - Radial Bar
    if (document.getElementById('scoreGauge')) {
        new ApexCharts(document.getElementById('scoreGauge'), {
            chart: { type: 'radialBar', height: 280, background: 'transparent', animations: { enabled: true, speed: 1200, animateGradually: { enabled: true, delay: 150 } } },
            series: [{$avgScore}],
            plotOptions: {
                radialBar: {
                    startAngle: -135, endAngle: 135,
                    hollow: { size: '65%', background: 'transparent', margin: 0, dropShadow: { blur: 8, opacity: 0.15, color: '#3B82B8' } },
                    track: { background: '#E5E9ED', strokeWidth: '100%', margin: 4 },
                    dataLabels: {
                        name: { offsetY: -10, color: '#64748b', fontSize: '0.7rem', fontWeight: 600 },
                        value: { offsetY: 20, color: '#17212B', fontSize: '2.2rem', fontWeight: 800, formatter: function(v) { return v + '%'; } }
                    },
                    barLabels: { enabled: false }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', shadeIntensity: 0.4, gradientToColors: ['#5BA3D0'], stops: [0, 100] } },
            stroke: { lineCap: 'round' },
            labels: ['Score AQMI']
        }).render();
    }

    // Radar Chart - Domain Performance
    if (document.getElementById('radarChart')) {
        new ApexCharts(document.getElementById('radarChart'), {
            chart: { type: 'radar', height: 280, background: 'transparent', foreColor: '#64748b', animations: { enabled: true, speed: 1000 } },
            series: [{ name: 'Score %', data: {$domainAvgs} }],
            labels: {$domainNames},
            colors: ['#3B82B8'],
            fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.4, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
            stroke: { curve: 'smooth', width: 2 },
            markers: { size: 4, colors: ['#3B82B8'], strokeColors: '#ffffff', strokeWidth: 2 },
            xaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
            yaxis: { show: false, min: 0, max: 100 },
            tooltip: { theme: 'light', y: { formatter: function(v) { return v + '%'; } } }
        }).render();
    }

    // Score Distribution Donut
    if (document.getElementById('scoreDonut')) {
        new ApexCharts(document.getElementById('scoreDonut'), {
            chart: { type: 'donut', height: 280, background: 'transparent', animations: { enabled: true, speed: 1000 } },
            series: {$scoreDistData},
            labels: {$scoreDistLabels},
            colors: {$scoreDistColors},
            stroke: { width: 0 },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { color: '#64748b', fontSize: '0.7rem', fontWeight: 600 },
                            value: { color: '#17212B', fontSize: '1.5rem', fontWeight: 800 },
                            total: { show: true, label: 'Total', color: '#64748b', fontSize: '0.65rem', fontWeight: 600 }
                        }
                    }
                }
            },
            legend: { position: 'bottom', labels: { colors: '#475569' }, fontSize: '11px', markers: { width: 8, height: 8 } },
            tooltip: { theme: 'light' }
        }).render();
    }

    // Monthly Area Chart
    if (document.getElementById('monthlyChart')) {
        new ApexCharts(document.getElementById('monthlyChart'), {
            chart: { type: 'area', height: 300, toolbar: { show: false }, background: 'transparent', foreColor: '#64748b', animations: { enabled: true, speed: 1000 } },
            series: [
                { name: 'Évaluations', data: {$monthlyData} },
                { name: 'Prospects', data: {$monthlyLeadsData} }
            ],
            xaxis: { categories: {$monthlyLabels}, labels: { style: { fontSize: '11px', colors: '#64748b' } } },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            colors: ['#3B82B8', '#22A06B'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.03, stops: [0, 100] }
            },
            stroke: { curve: 'smooth', width: [3, 2] },
            dataLabels: { enabled: false },
            grid: { borderColor: '#E5E9ED', strokeDashArray: 4 },
            tooltip: { theme: 'light', shared: true },
            legend: { show: false },
            markers: { size: 0, hover: { size: 5 } }
        }).render();
    }

    // Domain Bars - Horizontal
    if (document.getElementById('domainBars')) {
        new ApexCharts(document.getElementById('domainBars'), {
            chart: { type: 'bar', height: 320, background: 'transparent', foreColor: '#64748b', animations: { enabled: true, speed: 800, dynamicAnimation: { enabled: true } } },
            series: [{ name: 'Moyenne %', data: {$domainAvgs} }],
            xaxis: { categories: {$domainNames}, labels: { style: { colors: '#64748b', fontSize: '10px' } } },
            yaxis: { max: 100, labels: { style: { colors: '#64748b' }, formatter: function(v) { return v + '%'; } } },
            colors: ['#22A06B'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '60%', distributed: true } },
            fill: { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', shadeIntensity: 0.3, gradientToColors: ['#34c98a'], stops: [0, 100] } },
            dataLabels: { enabled: true, textAnchor: 'middle', formatter: function(v) { return v > 0 ? v + '%' : ''; }, style: { colors: ['#17212B'], fontSize: '9px', fontWeight: 600 } },
            grid: { borderColor: '#E5E9ED', strokeDashArray: 4 },
            tooltip: { theme: 'light', y: { formatter: function(v) { return v + '%'; } } }
        }).render();
    }
});
</script>
SCRIPTS;

$extraStyles = <<<STYLES
<style>
/* KPI Cards */
.nova-kpi-card {
    background: var(--vx-card-bg);
    backdrop-filter: var(--vx-glass-blur);
    -webkit-backdrop-filter: var(--vx-glass-blur);
    border: 1px solid var(--vx-card-border);
    border-radius: var(--vx-radius-lg);
    padding: 1.1rem 1rem 0.5rem;
    transition: all var(--vx-transition);
    position: relative;
    overflow: hidden;
}
.nova-kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--kpi-color);
    opacity: 0.6;
    transition: opacity var(--vx-transition);
}
.nova-kpi-card:hover {
    transform: translateY(-3px);
    border-color: var(--kpi-color);
    box-shadow: 0 8px 24px rgba(15,23,42,0.10), 0 0 16px color-mix(in srgb, var(--kpi-color) 12%, transparent);
}
.nova-kpi-card:hover::before { opacity: 1; }
.nova-kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.nova-kpi-icon {
    width: 38px; height: 38px;
    border-radius: var(--vx-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}
.nova-kpi-trend {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}
.nova-kpi-trend.up { color: var(--vx-success); background: var(--vx-success-light); }
.nova-kpi-trend.down { color: var(--vx-danger); background: var(--vx-danger-light); }
.nova-kpi-trend.neutral { color: var(--vx-text-muted); background: var(--vx-input-bg); }
.nova-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.nova-kpi-label {
    color: var(--vx-text-muted);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-top: 0.15rem;
}
.nova-kpi-spark { margin-top: 0.25rem; }

/* Glass Cards */
.nova-glass-card {
    background: var(--vx-card-bg);
    backdrop-filter: var(--vx-glass-blur);
    -webkit-backdrop-filter: var(--vx-glass-blur);
    border: 1px solid var(--vx-card-border);
    border-radius: var(--vx-radius-lg);
    transition: border-color var(--vx-transition);
}
.nova-glass-card:hover { border-color: var(--vx-card-border-hover); }

.nova-card-title {
    color: var(--vx-text-primary);
    font-weight: 600;
    font-size: 0.82rem;
    margin-bottom: 0;
}

.nova-badge-primary {
    font-size: 0.55rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    background: var(--vx-accent-light); color: var(--vx-accent);
    border: 1px solid rgba(59,130,184,0.20); padding: 0.15rem 0.5rem; border-radius: 0.3rem;
}
.nova-badge-info {
    font-size: 0.55rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    background: var(--vx-info-light); color: var(--vx-info);
    border: 1px solid rgba(59,130,184,0.20); padding: 0.15rem 0.5rem; border-radius: 0.3rem;
}
.nova-badge-warning {
    font-size: 0.55rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    background: var(--vx-warning-light); color: #C9951F;
    border: 1px solid rgba(242,184,75,0.25); padding: 0.15rem 0.5rem; border-radius: 0.3rem;
}

.nova-legend-dot {
    font-size: 0.65rem; color: var(--vx-text-muted); font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.3rem;
}
.nova-legend-dot::before {
    content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--dot);
}

.nova-gauge-footer {
    text-align: center;
    padding-top: 0.5rem;
}
.nova-gauge-stat {
    font-size: 0.72rem;
    color: var(--vx-text-muted);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.nova-gauge-stat-dot {
    width: 8px; height: 8px; border-radius: 50%;
}

/* Model items */
.nova-model-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--vx-divider);
}
.nova-model-item:last-child { border-bottom: none; }
.nova-model-icon {
    width: 36px; height: 36px;
    border-radius: var(--vx-radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; flex-shrink: 0;
}
.nova-model-name {
    color: var(--vx-text-primary);
    font-weight: 600;
    font-size: 0.78rem;
}
.nova-model-meta {
    color: var(--vx-text-muted);
    font-size: 0.65rem;
}

/* Table */
.nova-table {
    color: var(--vx-text-secondary);
    font-size: 0.78rem;
}
.nova-table thead th {
    color: var(--vx-text-muted);
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 700;
    border-bottom: 1px solid var(--vx-card-border);
    padding: 0.6rem 0.75rem;
}
.nova-table tbody td {
    border-bottom: 1px solid var(--vx-divider);
    padding: 0.6rem 0.75rem;
    vertical-align: middle;
}
.nova-table tbody tr:last-child td { border-bottom: none; }

/* Mini progress bar */
.nova-mini-progress {
    position: relative;
    width: 80px;
    height: 18px;
    background: var(--vx-input-bg);
    border-radius: 0.25rem;
    overflow: hidden;
}
.nova-mini-progress-bar {
    height: 100%;
    border-radius: 0.25rem;
    transition: width 0.8s ease;
}
.nova-mini-progress-text {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    font-size: 0.6rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}
</style>
STYLES;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
