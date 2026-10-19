<?php
$title = 'Dashboard';
ob_start();
$s = $adminStats;
?>

<!-- Welcome Hero -->
<div class="nova-welcome-card mb-4">
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:1;">
        <div>
            <div class="nova-welcome-title">Tableau de Bord</div>
            <div class="nova-welcome-sub"><?= $s['total_users'] ?> utilisateurs · <?= $s['online_users_count'] ?> en ligne</div>
            <div class="d-flex align-items-center gap-4 mt-3">
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $s['total_users'] ?></span>
                    <span class="nova-welcome-stat-label">Utilisateurs</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-success-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $s['online_users_count'] ?></span>
                    <span class="nova-welcome-stat-label">En ligne</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-info-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $s['total_models'] ?></span>
                    <span class="nova-welcome-stat-label">Modèles</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-warning-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $s['total_domains'] ?></span>
                    <span class="nova-welcome-stat-label">Domaines</span>
                </div>
                <div style="width:1px;height:2rem;background:var(--vx-card-border);"></div>
                <div class="nova-welcome-stat">
                    <span class="nova-welcome-stat-value" style="background:var(--vx-danger-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $s['total_questions'] ?></span>
                    <span class="nova-welcome-stat-label">Questions</span>
                </div>
            </div>
        </div>
        <div class="d-none d-md-block" style="position:relative;">
            <div style="width:72px;height:72px;border-radius:var(--vx-radius-xl);background:var(--vx-primary-gradient);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;box-shadow:0 8px 32px var(--vx-primary-glow);">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <?php
    $metrics = [
        ['label' => 'Utilisateurs', 'value' => $s['total_users'], 'icon' => 'fa-users', 'color' => '#3B82B8', 'gradient' => 'var(--vx-primary-gradient)', 'link' => '/admin/users'],
        ['label' => 'En ligne', 'value' => $s['online_users_count'], 'icon' => 'fa-circle-dot', 'color' => '#22A06B', 'gradient' => 'var(--vx-success-gradient)', 'link' => '#'],
        ['label' => 'Modèles', 'value' => $s['total_models'], 'icon' => 'fa-layer-group', 'color' => '#06b6d4', 'gradient' => 'var(--vx-info-gradient)', 'link' => '/admin/evaluation-models'],
        ['label' => 'Domaines', 'value' => $s['total_domains'], 'icon' => 'fa-folder', 'color' => '#f59e0b', 'gradient' => 'var(--vx-warning-gradient)', 'link' => '/admin/domains'],
        ['label' => 'Questions', 'value' => $s['total_questions'], 'icon' => 'fa-question-circle', 'color' => '#ef4444', 'gradient' => 'var(--vx-danger-gradient)', 'link' => '/admin/questions'],
        ['label' => 'Évaluations', 'value' => $s['total_assessments'], 'icon' => 'fa-clipboard-check', 'color' => '#8b5cf6', 'gradient' => 'linear-gradient(135deg,#8b5cf6,#a78bfa)', 'link' => '/admin/leads'],
    ];
    foreach ($metrics as $m):
    ?>
        <div class="col-sm-6 col-md-4 col-lg-2">
            <a href="<?= $m['link'] ?>" class="text-decoration-none">
                <div class="nova-kpi-card" style="--kpi-color:<?= $m['color'] ?>;">
                    <div class="nova-kpi-top">
                        <div class="nova-kpi-icon" style="background:<?= $m['color'] ?>18;color:<?= $m['color'] ?>;border:1px solid <?= $m['color'] ?>28;">
                            <i class="fas <?= $m['icon'] ?>"></i>
                        </div>
                    </div>
                    <div class="nova-kpi-value" style="background:<?= $m['gradient'] ?>;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= e($m['value']) ?></div>
                    <div class="nova-kpi-label"><?= $m['label'] ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pending Certification Requests -->
<?php if (!empty($s['pending_certifications'])): ?>
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card nova-glass-card" style="border-color:rgba(245,158,11,0.35);box-shadow:0 0 24px rgba(245,158,11,0.08);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:rgba(245,158,11,0.04);border-bottom:1px solid rgba(245,158,11,0.18);">
                <span class="nova-card-title" style="margin:0;color:var(--vx-warning);">
                    <i class="fas fa-certificate me-2"></i>Demandes de Certification en attente
                </span>
                <span class="nova-badge-warning"><?= $s['pending_certifications_count'] ?> à traiter</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="table nova-table">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($s['pending_certifications'] as $pc):
                            $pcStatus = $pc['status'] === 'certification_requested'
                                ? '<span class="nova-badge-warning"><i class="fas fa-hourglass me-1"></i>En attente</span>'
                                : '<span class="nova-badge-info"><i class="fas fa-magnifying-glass me-1"></i>En examen</span>';
                        ?>
                            <tr>
                                <td style="font-weight:600;color:var(--vx-text-primary);"><?= e($pc['company'] ?? 'N/A') ?></td>
                                <td><?= e(trim(($pc['firstname'] ?? '') . ' ' . ($pc['lastname'] ?? ''))) ?></td>
                                <td style="font-size:0.72rem;color:var(--vx-text-muted);"><?= e($pc['email'] ?? '-') ?></td>
                                <td><?= $pcStatus ?></td>
                                <td style="color:var(--vx-text-muted);font-size:0.7rem;"><?= formatDate($pc['certification_requested_at']) ?></td>
                                <td class="text-center">
                                    <a href="/admin/reports/<?= $pc['id'] ?>" class="btn btn-warning btn-sm" style="font-size:0.72rem;">
                                        <i class="fas fa-folder-open me-1"></i> Ouvrir
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

<!-- Online Users + Recent Users -->
<div class="row g-4 mb-4">
    <!-- Online Users -->
    <div class="col-lg-6">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-circle-dot me-2" style="color:var(--vx-success);"></i>Utilisateurs en ligne</span>
                <span class="nova-badge-info"><?= $s['online_users_count'] ?> actifs</span>
            </div>
            <div class="card-body" style="max-height:380px;overflow-y:auto;">
                <?php if (!empty($s['online_users'])): ?>
                    <?php foreach ($s['online_users'] as $u): ?>
                        <div class="nova-user-item">
                            <div class="nova-user-avatar-sm" style="background:var(--vx-success-gradient);">
                                <?= strtoupper(substr(e($u['firstname'] ?? 'U'), 0, 1)) ?><?= strtoupper(substr(e($u['lastname'] ?? ''), 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="nova-user-name"><?= e($u['firstname'] . ' ' . $u['lastname']) ?></div>
                                <div class="nova-user-meta"><?= e($u['email']) ?> · <?= e($u['role_name']) ?></div>
                            </div>
                            <span class="nova-online-dot"></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center;color:var(--vx-text-muted);padding:2rem;">
                        <i class="fas fa-user-slash" style="font-size:2rem;opacity:0.3;"></i>
                        <div style="margin-top:0.5rem;">Aucun utilisateur en ligne</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-users me-2" style="color:var(--vx-primary);"></i>Utilisateurs récents</span>
                <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Voir tout</a>
            </div>
            <div class="card-body" style="max-height:380px;overflow-y:auto;">
                <?php if (!empty($s['recent_users'])): ?>
                    <?php foreach ($s['recent_users'] as $u): ?>
                        <div class="nova-user-item">
                            <div class="nova-user-avatar-sm" style="background:var(--vx-primary-gradient);">
                                <?= strtoupper(substr(e($u['firstname'] ?? 'U'), 0, 1)) ?><?= strtoupper(substr(e($u['lastname'] ?? ''), 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="nova-user-name">
                                    <a href="/admin/users/edit/<?= $u['id'] ?>" style="color:var(--vx-text-primary);text-decoration:none;"><?= e($u['firstname'] . ' ' . $u['lastname']) ?></a>
                                </div>
                                <div class="nova-user-meta"><?= e($u['email']) ?> · <?= e($u['role_name']) ?></div>
                            </div>
                            <?php if ($u['is_active']): ?>
                                <span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);font-size:0.6rem;">Actif</span>
                            <?php else: ?>
                                <span class="badge" style="background:var(--vx-danger-light);color:var(--vx-danger);font-size:0.6rem;">Inactif</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center;color:var(--vx-text-muted);padding:2rem;">Aucun utilisateur</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Models + Domains -->
<div class="row g-4 mb-4">
    <!-- Evaluation Models -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-layer-group me-2" style="color:var(--vx-primary);"></i>Modèles d'Évaluation</span>
                <a href="/admin/evaluation-models" class="btn btn-outline-secondary btn-sm">Gérer</a>
            </div>
            <div class="card-body" style="max-height:380px;overflow-y:auto;">
                <?php if (!empty($s['evaluation_models'])): ?>
                    <?php foreach ($s['evaluation_models'] as $ms): ?>
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
                <?php else: ?>
                    <div style="text-align:center;color:var(--vx-text-muted);padding:2rem;">Aucun modèle</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Domains -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-folder me-2" style="color:var(--vx-warning);"></i>Domaines</span>
                <a href="/admin/domains" class="btn btn-outline-secondary btn-sm">Gérer</a>
            </div>
            <div class="card-body" style="max-height:380px;overflow-y:auto;">
                <?php if (!empty($s['domains'])): ?>
                    <?php foreach ($s['domains'] as $d): ?>
                        <div class="nova-model-item">
                            <div class="nova-model-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(245,158,11,0.18);">
                                <i class="fas <?= e($d['icon'] ?? 'fa-folder') ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="nova-model-name"><?= e($d['name_fr'] ?: $d['name']) ?></div>
                                <div class="nova-model-meta"><?= $d['questions_count'] ?> questions</div>
                            </div>
                            <?php if ($d['is_active']): ?>
                                <span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);font-size:0.6rem;">Actif</span>
                            <?php else: ?>
                                <span class="badge" style="background:var(--vx-input-bg);color:var(--vx-text-muted);font-size:0.6rem;">Inactif</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center;color:var(--vx-text-muted);padding:2rem;">Aucun domaine</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Questions -->
    <div class="col-lg-4">
        <div class="card nova-glass-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:transparent;border-bottom:1px solid var(--vx-card-border);">
                <span class="nova-card-title" style="margin:0;"><i class="fas fa-question-circle me-2" style="color:var(--vx-danger);"></i>Questions</span>
                <a href="/admin/questions" class="btn btn-outline-secondary btn-sm">Gérer</a>
            </div>
            <div style="overflow-x:auto;max-height:380px;overflow-y:auto;">
                <table class="table nova-table mb-0">
                    <thead>
                        <tr><th>Question</th><th>Domaine</th><th>Statut</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($s['questions'])): ?>
                            <?php foreach ($s['questions'] as $q): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--vx-text-primary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        <?= e($q['title_fr'] ?: $q['title']) ?>
                                    </td>
                                    <td style="font-size:0.7rem;color:var(--vx-text-muted);"><?= e($q['domain_name_fr'] ?: $q['domain_name']) ?></td>
                                    <td>
                                        <?php if ($q['is_active']): ?>
                                            <span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);font-size:0.55rem;">Actif</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:var(--vx-input-bg);color:var(--vx-text-muted);font-size:0.55rem;">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;color:var(--vx-text-muted);padding:2rem;">Aucune question</td></tr>
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
                    <a href="/admin/users/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouvel utilisateur</a>
                    <a href="/admin/evaluation-models/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau modèle</a>
                    <a href="/admin/domains/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau domaine</a>
                    <a href="/admin/questions/create" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle question</a>
                    <a href="/admin/leads" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye me-1"></i>Voir les prospects</a>
                    <a href="/admin/reports" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-alt me-1"></i>Rapports</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
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
    display: block;
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

/* User items */
.nova-user-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--vx-divider);
}
.nova-user-item:last-child { border-bottom: none; }
.nova-user-avatar-sm {
    width: 36px; height: 36px;
    border-radius: var(--vx-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.nova-user-name {
    color: var(--vx-text-primary);
    font-weight: 600;
    font-size: 0.78rem;
}
.nova-user-meta {
    color: var(--vx-text-muted);
    font-size: 0.65rem;
}
.nova-online-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--vx-success);
    box-shadow: 0 0 8px rgba(34,160,107,0.5);
    flex-shrink: 0;
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
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
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
</style>
STYLES;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
