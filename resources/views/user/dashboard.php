<?php $title = 'Tableau de bord - Espace Client'; ob_start(); ?>
<style>
.user-dashboard {
  min-height: 100vh;
  background: var(--vx-body-bg);
  background-image:
    radial-gradient(ellipse at 20% 50%, rgba(115,103,240,0.03) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 20%, rgba(40,199,111,0.03) 0%, transparent 50%);
  padding-bottom: 5rem;
}
.user-topbar {
  background: rgba(22,29,49,0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--vx-card-border);
  padding: 0.75rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
}
.user-topbar .brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 800;
  font-size: 1.05rem;
  color: var(--vx-text-primary);
  letter-spacing: -0.3px;
}
.user-topbar .brand .brand-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, var(--vx-primary), #9b8cf7);
  border-radius: var(--vx-radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #fff;
  font-weight: 800;
}
.user-content {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem;
}
.user-content .greeting h2 {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin-bottom: 0.15rem;
  letter-spacing: -0.3px;
}
.user-content .greeting p {
  color: var(--vx-text-muted);
  font-size: 0.8rem;
}
.user-stat-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  padding: 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all var(--vx-transition);
  min-height: 80px;
}
.user-stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--vx-shadow-md);
}
.user-stat-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: var(--vx-radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.125rem;
  flex-shrink: 0;
}
.user-stat-value {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  line-height: 1;
}
.user-stat-label {
  font-size: 0.6875rem;
  color: var(--vx-text-muted);
  font-weight: 500;
  margin-top: 0.125rem;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.user-assessment-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-card);
}
.user-assessment-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.75rem 1.25rem;
  border-bottom: 1px solid var(--vx-divider);
  transition: background var(--vx-transition);
}
.user-assessment-item:last-child {
  border-bottom: none;
}
.user-assessment-item:hover {
  background: rgba(115,103,240,0.02);
}
.user-assessment-company {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--vx-text-primary);
}
.user-assessment-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.25rem;
  font-size: 0.6875rem;
  color: var(--vx-text-muted);
  flex-wrap: wrap;
}
.user-empty-state {
  text-align: center;
  padding: 3rem 1rem;
}
.user-empty-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--vx-primary-light);
  border: 1px solid rgba(115,103,240,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: var(--vx-primary);
  font-size: 1.25rem;
}
.user-empty-state p {
  color: var(--vx-text-muted);
  font-size: 0.85rem;
  margin-bottom: 1rem;
}
@media (max-width: 768px) {
  .user-topbar { padding: 0.5rem 1rem; }
  .user-content { padding: 1rem; }
  .user-content .greeting h2 { font-size: 1.1rem; }
  .user-content .greeting p { font-size: 0.7rem; }
  .user-assessment-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
}
</style>

<div class="user-dashboard">
  <!-- Topbar -->
  <div class="user-topbar">
    <div class="brand">
      <span class="brand-icon">N</span>
      <span>NOVAQYS</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-md-inline" style="font-size:0.75rem;color:var(--vx-text-secondary);">
        <i class="fas fa-user me-1" style="color:var(--vx-primary);"></i><?= e($user['firstname'] ?? '') ?> <?= e($user['lastname'] ?? '') ?>
      </span>
      <a href="/user/logout" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
      </a>
    </div>
  </div>

  <!-- Content -->
  <div class="user-content">
    <div class="greeting mb-4">
      <h2>Bonjour, <?= e($user['firstname'] ?? '') ?></h2>
      <p>Bienvenue dans votre espace client.</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="user-stat-card">
          <div class="user-stat-icon" style="background:var(--vx-primary-light);color:var(--vx-primary);border:1px solid rgba(115,103,240,0.15);">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div>
            <div class="user-stat-value"><?= $totalAssessments ?></div>
            <div class="user-stat-label">Totales</div>
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="user-stat-card">
          <div class="user-stat-icon" style="background:var(--vx-success-light);color:var(--vx-success);border:1px solid rgba(40,199,111,0.15);">
            <i class="fas fa-check-circle"></i>
          </div>
          <div>
            <div class="user-stat-value" style="color:var(--vx-success);"><?= $completedCount ?></div>
            <div class="user-stat-label">Terminées</div>
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="user-stat-card">
          <div class="user-stat-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(255,159,67,0.15);">
            <i class="fas fa-hourglass-half"></i>
          </div>
          <div>
            <div class="user-stat-value" style="color:var(--vx-warning);"><?= $totalAssessments - $completedCount ?></div>
            <div class="user-stat-label">En cours</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Assessment list -->
    <div class="user-assessment-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-file-alt me-2" style="color:var(--vx-primary);"></i>Mes évaluations</span>
        <a href="/assessment/start" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
          <i class="fas fa-plus me-1"></i>Nouvelle
        </a>
      </div>

      <?php if (!empty($assessments)): ?>
        <?php foreach ($assessments as $a):
          $statusBadge = $a['status'] === 'completed' ? 'var(--vx-success)' : 'var(--vx-warning)';
          $statusBg = $a['status'] === 'completed' ? 'var(--vx-success-light)' : 'var(--vx-warning-light)';
          $statusLabel = $a['status'] === 'completed' ? 'Terminée' : 'En cours';
          $reportBadge = '';
          if ($a['report_status'] === 'validated') $reportBadge = '<span class="badge" style="background:var(--vx-info-light);color:var(--vx-info);"><i class="fas fa-check-circle me-1"></i>Validé</span>';
          elseif ($a['report_status'] === 'pending') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-hourglass me-1"></i>Attente</span>';
          elseif ($a['report_status'] === 'rejected') $reportBadge = '<span class="badge" style="background:var(--vx-danger-light);color:var(--vx-danger);"><i class="fas fa-times-circle me-1"></i>Rejeté</span>';
        ?>
          <div class="user-assessment-item">
            <div>
              <div class="user-assessment-company"><?= e($a['lead_company'] ?? ($a['lead_firstname'] ?? '') . ' ' . ($a['lead_lastname'] ?? '')) ?></div>
              <div class="user-assessment-meta">
                <span class="badge" style="background:<?= $statusBg ?>;color:<?= $statusBadge ?>;"><?= $statusLabel ?></span>
                <?php if ($a['total_score'] !== null): ?><span>Score: <strong style="color:var(--vx-primary);"><?= round($a['total_score']) ?>/100</strong></span><?php endif; ?>
                <span><?= date('d/m/Y', strtotime($a['created_at'])) ?></span>
                <?= $reportBadge ?>
              </div>
            </div>
            <div>
              <?php if ($a['status'] === 'completed'): ?>
                <a href="/assessment/<?= $a['id'] ?>/results" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-file-alt me-1"></i>Résultats
                </a>
              <?php else: ?>
                <a href="/assessment/<?= $a['id'] ?>" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-arrow-right me-1"></i>Continuer
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="user-empty-state">
          <div class="user-empty-icon"><i class="fas fa-clipboard-list"></i></div>
          <p>Vous n'avez pas encore d'évaluation.</p>
          <a href="/assessment/start" class="nova-btn nova-btn-primary"><i class="fas fa-plus me-1"></i>Commencer</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile Bottom Navigation -->
  <nav style="position:fixed;bottom:0;left:0;right:0;z-index:1050;background:var(--vx-card-bg);border-top:1px solid var(--vx-card-border);display:flex;padding:0.35rem 0;justify-content:space-around;backdrop-filter:blur(12px);" class="d-md-none">
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-primary);padding:0.25rem 0.5rem;gap:0.15rem;" href="/user/dashboard">
      <i class="fas fa-home" style="font-size:0.9rem;"></i><span>Accueil</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/assessment/start">
      <i class="fas fa-plus-circle" style="font-size:0.9rem;"></i><span>Nouveau</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/">
      <i class="fas fa-globe" style="font-size:0.9rem;"></i><span>Site</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-danger);padding:0.25rem 0.5rem;gap:0.15rem;" href="/user/logout">
      <i class="fas fa-sign-out-alt" style="font-size:0.9rem;"></i><span>Quitter</span>
    </a>
  </nav>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>