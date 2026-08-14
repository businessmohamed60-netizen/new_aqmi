<?php $title = 'Tableau de bord - Espace Client'; ob_start(); ?>
<style>
:root {
  --vx-primary: #6366f1;
  --vx-primary-dark: #5558e3;
  --vx-primary-light: rgba(99,102,241,0.12);
  --vx-primary-glow: rgba(99,102,241,0.25);
  --vx-primary-gradient: linear-gradient(135deg, #6366f1, #818cf8);
  --vx-success: #10b981;
  --vx-success-light: rgba(16,185,129,0.12);
  --vx-success-gradient: linear-gradient(135deg, #10b981, #34d399);
  --vx-danger: #ef4444;
  --vx-danger-light: rgba(239,68,68,0.12);
  --vx-danger-gradient: linear-gradient(135deg, #ef4444, #f87171);
  --vx-warning: #f59e0b;
  --vx-warning-light: rgba(245,158,11,0.12);
  --vx-warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
  --vx-info: #06b6d4;
  --vx-info-light: rgba(6,182,212,0.12);
  --vx-info-gradient: linear-gradient(135deg, #06b6d4, #22d3ee);
  --vx-body-bg: #f5f3ed;
  --vx-card-bg: #fffdf8;
  --vx-card-border: #e7e1d7;
  --vx-card-border-hover: #cfc3b3;
  --vx-divider: #eee9e1;
  --vx-text-primary: #17212b;
  --vx-text-secondary: #475569;
  --vx-text-muted: #7d8794;
  --vx-radius-sm: 0.375rem;
  --vx-radius-md: 0.625rem;
  --vx-radius-lg: 0.875rem;
  --vx-radius-xl: 1.25rem;
  --vx-shadow-md: 0 4px 24px rgba(80,64,42,0.07);
  --vx-shadow-lg: 0 12px 48px rgba(80,64,42,0.11);
  --vx-transition: 0.25s ease;
}
.nova-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 1rem;
  border-radius: var(--vx-radius-sm);
  font-size: 0.8125rem;
  font-weight: 600;
  text-decoration: none;
  transition: all var(--vx-transition);
  cursor: pointer;
  border: none;
}
.nova-btn-primary {
  background: var(--vx-primary-gradient);
  color: #fff;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
}
.nova-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 24px var(--vx-primary-glow);
  filter: brightness(1.1);
  color: #fff;
}
.nova-btn-outline {
  background: rgba(255,255,255,0.03);
  color: var(--vx-text-secondary);
  border: 1px solid var(--vx-card-border);
}
.nova-btn-outline:hover {
  background: rgba(99,102,241,0.06);
  border-color: var(--vx-card-border-hover);
  color: var(--vx-text-primary);
  transform: translateY(-1px);
}
.badge {
  font-size: 0.625rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: var(--vx-radius-sm);
}
.card-header {
  background: transparent;
  border-bottom: 1px solid var(--vx-divider);
  padding: 0.875rem 1.25rem;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--vx-text-primary);
}
.row { display: flex; flex-wrap: wrap; }
.col-4 { flex: 0 0 33.3333%; max-width: 33.3333%; }
.col-sm-6, .col-md-4, .col-lg-2, .col-lg-4, .col-lg-6, .col-lg-8 { position: relative; width: 100%; }
@media (min-width: 576px) { .col-sm-6 { flex: 0 0 50%; max-width: 50%; } }
@media (min-width: 768px) { .col-md-4 { flex: 0 0 33.3333%; max-width: 33.3333%; } .d-md-none { display: none !important; } .d-md-inline { display: inline !important; } }
@media (min-width: 992px) { .col-lg-2 { flex: 0 0 16.6667%; max-width: 16.6667%; } .col-lg-4 { flex: 0 0 33.3333%; max-width: 33.3333%; } .col-lg-6 { flex: 0 0 50%; max-width: 50%; } .col-lg-8 { flex: 0 0 66.6667%; max-width: 66.6667%; } }
.g-3 { gap: 1rem; }
.g-4 { gap: 1.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mt-3 { margin-top: 0.75rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.gap-3 { gap: 1rem; }
.gap-4 { gap: 1.5rem; }
.d-flex { display: flex; }
.flex-wrap { flex-wrap: wrap; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-center { justify-content: center; }
.text-decoration-none { text-decoration: none; }
.table { width: 100%; font-size: 0.75rem; color: var(--vx-text-secondary); }
.table th { font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--vx-text-muted); font-weight: 600; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--vx-divider); }
.table td { padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--vx-divider); }
.table tbody tr:last-child td { border-bottom: none; }
.user-dashboard {
  min-height: 100vh;
  background: var(--vx-body-bg);
  background-image:
    radial-gradient(ellipse 60% 50% at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 80% 100%, rgba(6,182,212,0.04) 0%, transparent 60%);
  background-attachment: fixed;
  padding-bottom: 5rem;
}
.user-topbar {
  background: rgba(255,253,248,0.94);
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
  background: var(--vx-primary-gradient);
  border-radius: var(--vx-radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #fff;
  font-weight: 800;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
  transition: transform 0.3s ease;
}
.user-topbar .brand:hover .brand-icon {
  transform: scale(1.05) rotate(-3deg);
}
.user-content {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem;
}
.user-welcome {
  background: linear-gradient(135deg, rgba(31,111,235,0.08) 0%, rgba(255,253,248,0.94) 45%, #fffdf8 100%);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  padding: 1.75rem 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
}
.user-welcome::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 60%);
  border-radius: 50%;
  animation: userGlowPulse 6s ease-in-out infinite;
}
@keyframes userGlowPulse {
  0%, 100% { opacity: 0.4; transform: scale(0.95); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
.user-welcome h2 {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin-bottom: 0.15rem;
  letter-spacing: -0.3px;
  position: relative;
  z-index: 1;
}
.user-welcome p {
  color: var(--vx-text-muted);
  font-size: 0.8rem;
  position: relative;
  z-index: 1;
}
.user-stat-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  padding: 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all var(--vx-transition);
  min-height: 80px;
  position: relative;
  overflow: hidden;
}
.user-stat-card::after {
  content: '';
  position: absolute;
  top: 0; right: 0;
  width: 60px; height: 60px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 70%);
  opacity: 0;
  transition: opacity var(--vx-transition);
}
.user-stat-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--vx-shadow-lg);
  border-color: var(--vx-card-border-hover);
}
.user-stat-card:hover::after {
  opacity: 0.3;
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
  transition: transform var(--vx-transition);
}
.user-stat-card:hover .user-stat-icon {
  transform: scale(1.08);
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
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
}
.user-assessment-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.875rem 1.25rem;
  border-bottom: 1px solid var(--vx-divider);
  transition: all var(--vx-transition);
}
.user-assessment-item:last-child {
  border-bottom: none;
}
.user-assessment-item:hover {
  background: rgba(99,102,241,0.03);
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
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--vx-primary-light);
  border: 1px solid rgba(99,102,241,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: var(--vx-primary);
  font-size: 1.5rem;
  animation: userEmptyPulse 3s ease-in-out infinite;
}
@keyframes userEmptyPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
  50% { box-shadow: 0 0 24px 4px rgba(99,102,241,0.15); }
}
.user-empty-state p {
  color: var(--vx-text-muted);
  font-size: 0.85rem;
  margin-bottom: 1rem;
}
@media (max-width: 768px) {
  .user-topbar { padding: 0.5rem 1rem; }
  .user-content { padding: 1rem; }
  .user-welcome { padding: 1.25rem; }
  .user-welcome h2 { font-size: 1.15rem; }
  .user-welcome p { font-size: 0.72rem; }
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
      <a href="/logout" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
      </a>
    </div>
  </div>

  <!-- Content -->
  <div class="user-content">
    <!-- Welcome -->
    <div class="user-welcome">
      <h2>Bonjour, <?= e($user['firstname'] ?? '') ?></h2>
      <p>Bienvenue dans votre espace client NOVAQYS.</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="user-stat-card">
          <div class="user-stat-icon" style="background:var(--vx-primary-light);color:var(--vx-primary);border:1px solid rgba(99,102,241,0.15);">
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
          <div class="user-stat-icon" style="background:var(--vx-success-light);color:var(--vx-success);border:1px solid rgba(16,185,129,0.15);">
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
          <div class="user-stat-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(245,158,11,0.15);">
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
        <div style="display:flex;gap:0.5rem;">
          <a href="/user/consolidated" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
            <i class="fas fa-layer-group me-1"></i>Consolider
          </a>
          <a href="/assessment/start" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
            <i class="fas fa-plus me-1"></i>Nouvelle
          </a>
        </div>
      </div>

      <?php if (!empty($assessments)): ?>
        <?php foreach ($assessments as $a):
          $statusBadge = $a['status'] === 'completed' ? 'var(--vx-success)' : 'var(--vx-warning)';
          $statusBg = $a['status'] === 'completed' ? 'var(--vx-success-light)' : 'var(--vx-warning-light)';
          $statusLabel = $a['status'] === 'completed' ? 'Terminée' : 'En cours';
          $reportBadge = '';
          if ($a['report_status'] === 'certified') $reportBadge = '<span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);"><i class="fas fa-certificate me-1"></i>Certifié</span>';
          elseif ($a['report_status'] === 'approved') $reportBadge = '<span class="badge" style="background:var(--vx-info-light);color:var(--vx-info);"><i class="fas fa-thumbs-up me-1"></i>Approuvé</span>';
          elseif ($a['report_status'] === 'under_review') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-magnifying-glass me-1"></i>En examen</span>';
          elseif ($a['report_status'] === 'certification_requested') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-hourglass me-1"></i>En attente de validation</span>';
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
            <div style="display:flex;gap:0.5rem;">
              <?php if ($a['status'] === 'completed'): ?>
                <a href="/assessment/<?= $a['id'] ?>/results" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-file-alt me-1"></i>Résultats
                </a>
                <?php if ($a['report_status'] === 'certified'): ?>
                  <a href="/report/<?= $a['id'] ?>/download" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                    <i class="fas fa-certificate me-1"></i>Télécharger mon certificat AQMI
                  </a>
                <?php endif; ?>
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
  <nav style="position:fixed;bottom:0;left:0;right:0;z-index:1050;background:rgba(255,253,248,0.96);border-top:1px solid var(--vx-card-border);display:flex;padding:0.35rem 0;justify-content:space-around;backdrop-filter:blur(12px);" class="d-md-none">
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-primary);padding:0.25rem 0.5rem;gap:0.15rem;" href="/dashboard">
      <i class="fas fa-home" style="font-size:0.9rem;"></i><span>Accueil</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/assessment/start">
      <i class="fas fa-plus-circle" style="font-size:0.9rem;"></i><span>Nouveau</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/">
      <i class="fas fa-globe" style="font-size:0.9rem;"></i><span>Site</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-danger);padding:0.25rem 0.5rem;gap:0.15rem;" href="/logout">
      <i class="fas fa-sign-out-alt" style="font-size:0.9rem;"></i><span>Quitter</span>
    </a>
  </nav>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>