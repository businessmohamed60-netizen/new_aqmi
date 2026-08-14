<?php $title = 'Consolider mes rapports'; ob_start(); ?>
<style>
:root {
  --vx-primary: #6366f1;
  --vx-primary-dark: #5558e3;
  --vx-primary-light: rgba(99,102,241,0.12);
  --vx-primary-glow: rgba(99,102,241,0.25);
  --vx-success: #10b981;
  --vx-success-light: rgba(16,185,129,0.12);
  --vx-danger: #ef4444;
  --vx-danger-light: rgba(239,68,68,0.12);
  --vx-warning: #f59e0b;
  --vx-warning-light: rgba(245,158,11,0.12);
  --vx-info: #06b6d4;
  --vx-info-light: rgba(6,182,212,0.12);
  --vx-body-bg: #f5f3ed;
  --vx-card-bg: #fffdf8;
  --vx-card-border: #e7e1d7;
  --vx-text-primary: #17212b;
  --vx-text-secondary: #475569;
  --vx-text-muted: #7d8794;
  --vx-radius-md: 0.625rem;
  --vx-radius-lg: 0.875rem;
  --vx-radius-xl: 1.25rem;
  --vx-shadow-md: 0 4px 24px rgba(80,64,42,0.07);
  --vx-shadow-lg: 0 12px 48px rgba(80,64,42,0.11);
  --vx-transition: 0.25s ease;
}
.consolidated-page { min-height: 100vh; background: var(--vx-body-bg); padding: 2rem 1rem 5rem; }
.consolidated-wrap { max-width: 900px; margin: 0 auto; }
.consolidated-header { margin-bottom: 2rem; }
.consolidated-header h1 { font-size: 1.5rem; font-weight: 800; color: var(--vx-text-primary); margin-bottom: 0.25rem; }
.consolidated-header p { color: var(--vx-text-muted); font-size: 0.85rem; }
.consolidated-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); margin-bottom: 1.5rem; overflow: hidden; }
.consolidated-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #eee9e1; font-weight: 700; font-size: 0.9rem; color: var(--vx-text-primary); display: flex; align-items: center; gap: 0.5rem; }
.consolidated-card-body { padding: 1.25rem; }
.assessment-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md); margin-bottom: 0.5rem; transition: all var(--vx-transition); cursor: pointer; }
.assessment-row:hover { border-color: var(--vx-primary); background: var(--vx-primary-light); }
.assessment-row.selected { border-color: var(--vx-primary); background: var(--vx-primary-light); }
.assessment-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--vx-primary); cursor: pointer; }
.assessment-info { flex: 1; }
.assessment-model { font-weight: 600; font-size: 0.85rem; color: var(--vx-text-primary); }
.assessment-meta { font-size: 0.72rem; color: var(--vx-text-muted); margin-top: 0.15rem; }
.assessment-score { font-weight: 700; font-size: 0.9rem; color: var(--vx-primary); }
.form-group { margin-bottom: 1rem; }
.form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--vx-text-secondary); margin-bottom: 0.4rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md); font-size: 0.85rem; background: #fff; color: var(--vx-text-primary); transition: border-color var(--vx-transition); }
.form-control:focus { outline: none; border-color: var(--vx-primary); box-shadow: 0 0 0 3px var(--vx-primary-light); }
.btn-primary { background: linear-gradient(135deg, #6366f1, #818cf8); color: #fff; border: none; padding: 0.625rem 1.5rem; border-radius: var(--vx-radius-md); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all var(--vx-transition); }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px var(--vx-primary-glow); filter: brightness(1.1); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.btn-outline { background: transparent; color: var(--vx-text-secondary); border: 1px solid var(--vx-card-border); padding: 0.625rem 1.5rem; border-radius: var(--vx-radius-md); font-weight: 600; font-size: 0.85rem; cursor: pointer; text-decoration: none; transition: all var(--vx-transition); }
.btn-outline:hover { border-color: var(--vx-primary); color: var(--vx-primary); }
.empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--vx-text-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.5; }
.empty-state p { font-size: 0.85rem; margin-bottom: 1rem; }
.existing-report { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid #eee9e1; transition: background var(--vx-transition); }
.existing-report:hover { background: var(--vx-primary-light); }
.existing-report:last-child { border-bottom: none; }
.existing-report-title { font-weight: 600; font-size: 0.85rem; color: var(--vx-text-primary); }
.existing-report-meta { font-size: 0.72rem; color: var(--vx-text-muted); margin-top: 0.15rem; }
.status-badge { font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 0.375rem; }
.back-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--vx-text-secondary); font-size: 0.8rem; text-decoration: none; margin-bottom: 1rem; transition: color var(--vx-transition); }
.back-link:hover { color: var(--vx-primary); }
</style>

<div class="consolidated-page">
  <div class="consolidated-wrap">
    <a href="/user/dashboard" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

    <div class="consolidated-header">
      <h1><i class="fas fa-layer-group" style="color:var(--vx-primary);"></i> Consolider mes rapports</h1>
      <p>Sélectionnez plusieurs évaluations terminées pour générer un rapport consolidé. Vous pourrez ensuite demander la certification officielle.</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert" style="background:var(--vx-success-light);color:var(--vx-success);padding:0.75rem 1rem;border-radius:var(--vx-radius-md);margin-bottom:1rem;font-size:0.85rem;font-weight:600;">
        <i class="fas fa-check-circle"></i> <?= e($_SESSION['success']) ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert" style="background:var(--vx-danger-light);color:var(--vx-danger);padding:0.75rem 1rem;border-radius:var(--vx-radius-md);margin-bottom:1rem;font-size:0.85rem;font-weight:600;">
        <i class="fas fa-exclamation-circle"></i> <?= e($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (count($completedAssessments) >= 2): ?>
      <div class="consolidated-card">
        <div class="consolidated-card-header">
          <i class="fas fa-plus-circle" style="color:var(--vx-primary);"></i> Créer un nouveau rapport consolidé
        </div>
        <div class="consolidated-card-body">
          <form action="/user/consolidated/create" method="POST">
            <div class="form-group">
              <label class="form-label">Titre du rapport consolidé</label>
              <input type="text" name="title" class="form-control" placeholder="Ex: Évaluation globale 2026" required>
            </div>

            <label class="form-label">Évaluations à inclure (cochez au moins 2)</label>
            <?php foreach ($completedAssessments as $a): ?>
              <label class="assessment-row">
                <input type="checkbox" name="assessment_ids[]" value="<?= $a['id'] ?>">
                <div class="assessment-info">
                  <div class="assessment-model"><?= e($a['model_name_fr'] ?: $a['model_name'] ?: 'Modèle') ?></div>
                  <div class="assessment-meta">
                    <?= e($a['company'] ?? 'N/A') ?> &middot; <?= date('d/m/Y', strtotime($a['completed_at'])) ?>
                  </div>
                </div>
                <div class="assessment-score"><?= round($a['total_score'] ?? 0) ?>/100</div>
              </label>
            <?php endforeach; ?>

            <div style="margin-top:1rem;display:flex;gap:0.75rem;">
              <button type="submit" class="btn-primary"><i class="fas fa-layer-group me-1"></i> Créer le rapport consolidé</button>
              <a href="/user/dashboard" class="btn-outline">Annuler</a>
            </div>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div class="consolidated-card">
        <div class="empty-state">
          <i class="fas fa-info-circle"></i>
          <p>Vous devez avoir au moins <strong>2 évaluations terminées</strong> pour créer un rapport consolidé.</p>
          <a href="/assessment/start" class="btn-primary" style="display:inline-flex;align-items:center;gap:0.4rem;text-decoration:none;">
            <i class="fas fa-plus"></i> Commencer une évaluation
          </a>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($consolidatedReports)): ?>
      <div class="consolidated-card">
        <div class="consolidated-card-header">
          <i class="fas fa-folder" style="color:var(--vx-primary);"></i> Mes rapports consolidés
        </div>
        <?php foreach ($consolidatedReports as $r):
          $statusColors = [
            'draft' => ['bg' => '#e9ecef', 'color' => '#6c757d', 'label' => 'Brouillon', 'icon' => 'fa-file'],
            'certification_requested' => ['bg' => 'var(--vx-warning-light)', 'color' => 'var(--vx-warning)', 'label' => 'En attente', 'icon' => 'fa-hourglass'],
            'under_review' => ['bg' => 'var(--vx-warning-light)', 'color' => 'var(--vx-warning)', 'label' => 'En examen', 'icon' => 'fa-magnifying-glass'],
            'approved' => ['bg' => 'var(--vx-info-light)', 'color' => 'var(--vx-info)', 'label' => 'Approuvé', 'icon' => 'fa-thumbs-up'],
            'rejected' => ['bg' => 'var(--vx-danger-light)', 'color' => 'var(--vx-danger)', 'label' => 'Rejeté', 'icon' => 'fa-times-circle'],
            'certified' => ['bg' => 'var(--vx-success-light)', 'color' => 'var(--vx-success)', 'label' => 'Certifié', 'icon' => 'fa-certificate'],
          ];
          $sc = $statusColors[$r['status']] ?? $statusColors['draft'];
        ?>
          <a href="/user/consolidated/<?= $r['id'] ?>" class="existing-report" style="text-decoration:none;">
            <div>
              <div class="existing-report-title"><?= e($r['title']) ?></div>
              <div class="existing-report-meta">
                Score: <strong style="color:var(--vx-primary);"><?= round($r['consolidated_score'] ?? 0) ?>/100</strong> &middot;
                <?= date('d/m/Y', strtotime($r['created_at'])) ?>
              </div>
            </div>
            <span class="status-badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
              <i class="fas <?= $sc['icon'] ?> me-1"></i><?= $sc['label'] ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
