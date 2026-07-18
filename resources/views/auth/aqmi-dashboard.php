<?php
$title = 'Tableau de bord - AQMI';
$showFooter = true;
$user = $user ?? [];
$assessments = $assessments ?? [];
$totalAssessments = $totalAssessments ?? 0;
$completedCount = $completedCount ?? 0;
$lastLogin = $lastLogin ?? null;
ob_start();
?>
<div class="aqmi-dash-wrap">
  <!-- Top bar -->
  <header class="aqmi-dash-header">
    <div class="aqmi-dash-header-inner">
      <div class="aqmi-dash-brand">
        <img src="<?= asset('img/logo-novaqys-aqmi.png') ?>" alt="NOVAQYS AQMI" width="42" height="28" style="height:28px;width:auto">
        <span>AQMI</span>
      </div>
      <div class="aqmi-dash-user">
        <span class="aqmi-dash-name"><?= e($user['firstname'] ?? '') ?> <?= e($user['lastname'] ?? '') ?></span>
        <div class="aqmi-dash-avatar"><?= strtoupper(substr($user['firstname'] ?? 'U', 0, 1)) ?></div>
      </div>
    </div>
  </header>

  <main class="aqmi-dash-main">
    <!-- Welcome -->
    <div class="aqmi-dash-welcome">
      <div>
        <h1>Bonjour, <?= e($user['firstname'] ?? '') ?> 👋</h1>
        <p>Bienvenue dans votre espace AQMI</p>
      </div>
      <div class="aqmi-dash-actions">
        <a href="/assessment/start" class="aqmi-btn aqmi-btn-primary">
          <i class="fas fa-plus"></i>
          <span>Nouvelle évaluation</span>
        </a>
        <a href="/aqmi/history" class="aqmi-btn aqmi-btn-outline">
          <i class="fas fa-clock-rotate"></i>
          <span>Historique</span>
        </a>
        <a href="/aqmi/logout" class="aqmi-btn aqmi-btn-ghost" style="color:var(--nova-text-tertiary)">
          <i class="fas fa-right-from-bracket"></i>
        </a>
      </div>
    </div>

    <?php if (\App\Helpers\Session::hasFlash('success')): ?>
      <div class="aqmi-alert aqmi-alert-success" style="margin-bottom:1.5rem">
        <i class="fas fa-check-circle"></i>
        <?= e(\App\Helpers\Session::getFlash('success')) ?>
      </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="aqmi-dash-stats">
      <div class="aqmi-dash-stat">
        <div class="aqmi-dash-stat-icon" style="background:rgba(59,130,246,0.15);color:#3b82f6">
          <i class="fas fa-file-pen"></i>
        </div>
        <div>
          <div class="aqmi-dash-stat-value"><?= $totalAssessments ?></div>
          <div class="aqmi-dash-stat-label">Évaluations totales</div>
        </div>
      </div>
      <div class="aqmi-dash-stat">
        <div class="aqmi-dash-stat-icon" style="background:rgba(16,185,129,0.15);color:#10b981">
          <i class="fas fa-check-circle"></i>
        </div>
        <div>
          <div class="aqmi-dash-stat-value"><?= $completedCount ?></div>
          <div class="aqmi-dash-stat-label">Terminées</div>
        </div>
      </div>
      <div class="aqmi-dash-stat">
        <div class="aqmi-dash-stat-icon" style="background:rgba(139,92,246,0.15);color:#8b5cf6">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <div class="aqmi-dash-stat-value"><?= $totalAssessments > 0 ? round(($completedCount / $totalAssessments) * 100) . '%' : '—' ?></div>
          <div class="aqmi-dash-stat-label">Taux de complétion</div>
        </div>
      </div>
      <div class="aqmi-dash-stat">
        <div class="aqmi-dash-stat-icon" style="background:rgba(6,182,212,0.15);color:#06b6d4">
          <i class="fas fa-shield-halved"></i>
        </div>
        <div>
          <div class="aqmi-dash-stat-value">IATF</div>
          <div class="aqmi-dash-stat-label">16949 Conforme</div>
        </div>
      </div>
    </div>

    <!-- Last login info -->
    <?php if ($lastLogin): ?>
    <div class="aqmi-dash-lastlogin">
      <i class="fas fa-clock-rotate"></i>
      <span>Dernière connexion : <?= date('d/m/Y à H:i', strtotime($lastLogin['login_date'])) ?>
        · IP : <?= e($lastLogin['ip_address'] ?? 'N/A') ?></span>
    </div>
    <?php endif; ?>

    <!-- Assessments -->
    <div class="aqmi-dash-section">
      <div class="aqmi-dash-section-header">
        <h2>Mes évaluations</h2>
        <a href="/assessment/start" class="aqmi-link">Nouvelle évaluation <i class="fas fa-arrow-right"></i></a>
      </div>

      <?php if (empty($assessments)): ?>
        <div class="aqmi-dash-empty">
          <div class="aqmi-dash-empty-icon">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <h3>Aucune évaluation pour le moment</h3>
          <p>Lancez votre première évaluation de maturité qualité</p>
          <a href="/assessment/start" class="aqmi-btn aqmi-btn-primary" style="margin-top:1rem">
            <i class="fas fa-plus"></i>
            <span>Commencer</span>
          </a>
        </div>
      <?php else: ?>
        <div class="aqmi-dash-table-wrap">
          <table class="aqmi-dash-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Statut</th>
                <th>Score</th>
                <th>Niveau</th>
                <th>Rapport</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($assessments as $a): ?>
                <tr>
                  <td data-label="Date"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
                  <td data-label="Statut">
                    <span class="aqmi-badge <?= $a['status'] === 'completed' ? 'aqmi-badge-success' : 'aqmi-badge-warning' ?>">
                      <?= $a['status'] === 'completed' ? 'Terminée' : 'En cours' ?>
                    </span>
                  </td>
                  <td data-label="Score">
                    <?= $a['total_score'] !== null ? number_format($a['total_score'], 1) . '%' : '—' ?>
                  </td>
                  <td data-label="Niveau"><?= e($a['maturity_level'] ?? '—') ?></td>
                  <td data-label="Rapport">
                    <?php if ($a['report_id'] && $a['report_status'] === 'validated'): ?>
                      <a href="/report/<?= $a['report_id'] ?>/download" class="aqmi-link">
                        <i class="fas fa-file-pdf"></i> Télécharger
                      </a>
                    <?php elseif ($a['report_id']): ?>
                      <span class="aqmi-text-muted">En validation</span>
                    <?php else: ?>
                      <span class="aqmi-text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td data-label="Actions">
                    <?php if ($a['status'] === 'in_progress'): ?>
                      <a href="/assessment/<?= $a['id'] ?>" class="aqmi-link">Continuer</a>
                    <?php elseif ($a['status'] === 'completed'): ?>
                      <a href="/assessment/<?= $a['id'] ?>/results" class="aqmi-link">Voir</a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>