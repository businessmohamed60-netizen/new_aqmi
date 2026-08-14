<?php $title = 'Rapport consolidé'; ob_start(); ?>
<style>
:root {
  --vx-primary: #6366f1;
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
.detail-page { min-height: 100vh; background: var(--vx-body-bg); padding: 2rem 1rem 5rem; }
.detail-wrap { max-width: 800px; margin: 0 auto; }
.back-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--vx-text-secondary); font-size: 0.8rem; text-decoration: none; margin-bottom: 1rem; transition: color var(--vx-transition); }
.back-link:hover { color: var(--vx-primary); }
.detail-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); margin-bottom: 1.5rem; overflow: hidden; }
.detail-header { padding: 1.5rem; background: linear-gradient(135deg, rgba(99,102,241,0.08), transparent); border-bottom: 1px solid #eee9e1; }
.detail-header h1 { font-size: 1.35rem; font-weight: 800; color: var(--vx-text-primary); margin-bottom: 0.25rem; }
.detail-header .meta { font-size: 0.8rem; color: var(--vx-text-muted); }
.score-circle { display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #818cf8); color: #fff; font-size: 1.5rem; font-weight: 800; box-shadow: 0 4px 16px var(--vx-primary-glow); }
.detail-body { padding: 1.25rem; }
.detail-section { margin-bottom: 1.5rem; }
.detail-section h3 { font-size: 0.9rem; font-weight: 700; color: var(--vx-text-primary); margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee9e1; }
.item-row { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md); margin-bottom: 0.5rem; }
.item-info { flex: 1; }
.item-model { font-weight: 600; font-size: 0.85rem; color: var(--vx-text-primary); }
.item-meta { font-size: 0.72rem; color: var(--vx-text-muted); margin-top: 0.15rem; }
.item-score { font-weight: 700; font-size: 1rem; color: var(--vx-primary); }
.status-banner { padding: 0.75rem 1.25rem; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; }
.btn-primary { background: linear-gradient(135deg, #6366f1, #818cf8); color: #fff; border: none; padding: 0.625rem 1.5rem; border-radius: var(--vx-radius-md); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all var(--vx-transition); text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px var(--vx-primary-glow); filter: brightness(1.1); color: #fff; }
.btn-danger { background: linear-gradient(135deg, #ef4444, #f87171); color: #fff; border: none; padding: 0.625rem 1.5rem; border-radius: var(--vx-radius-md); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all var(--vx-transition); }
.btn-danger:hover { transform: translateY(-1px); filter: brightness(1.1); }
.admin-feedback { background: #f8f5f0; border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md); padding: 1rem; margin-top: 0.5rem; }
.admin-feedback p { font-size: 0.8rem; color: var(--vx-text-secondary); margin-bottom: 0.5rem; }
.admin-feedback p:last-child { margin-bottom: 0; }
.cert-number { font-family: monospace; font-size: 1.1rem; font-weight: 700; color: var(--vx-success); }
</style>

<div class="detail-page">
  <div class="detail-wrap">
    <a href="/user/consolidated" class="back-link"><i class="fas fa-arrow-left"></i> Retour</a>

    <?php if (isset($_SESSION['success'])): ?>
      <div style="background:var(--vx-success-light);color:var(--vx-success);padding:0.75rem 1rem;border-radius:var(--vx-radius-md);margin-bottom:1rem;font-size:0.85rem;font-weight:600;">
        <i class="fas fa-check-circle"></i> <?= e($_SESSION['success']) ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div style="background:var(--vx-danger-light);color:var(--vx-danger);padding:0.75rem 1rem;border-radius:var(--vx-radius-md);margin-bottom:1rem;font-size:0.85rem;font-weight:600;">
        <i class="fas fa-exclamation-circle"></i> <?= e($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="detail-card">
      <div class="detail-header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
          <div>
            <h1><?= e($report['title']) ?></h1>
            <div class="meta">Créé le <?= date('d/m/Y', strtotime($report['created_at'])) ?></div>
            <?php if (!empty($report['report_number'])): ?>
              <div class="meta" style="margin-top:0.3rem;">Numéro: <span class="cert-number"><?= e($report['report_number']) ?></span></div>
            <?php endif; ?>
          </div>
          <div class="score-circle"><?= round($report['consolidated_score'] ?? 0) ?></div>
        </div>
      </div>

      <?php
      $statusStyles = [
        'draft' => ['bg' => '#e9ecef', 'color' => '#6c757d', 'label' => 'Brouillon', 'icon' => 'fa-file'],
        'certification_requested' => ['bg' => 'var(--vx-warning-light)', 'color' => 'var(--vx-warning)', 'label' => 'En attente de validation', 'icon' => 'fa-hourglass'],
        'under_review' => ['bg' => 'var(--vx-warning-light)', 'color' => 'var(--vx-warning)', 'label' => 'En cours d\'examen', 'icon' => 'fa-magnifying-glass'],
        'approved' => ['bg' => 'var(--vx-info-light)', 'color' => 'var(--vx-info)', 'label' => 'Approuvé', 'icon' => 'fa-thumbs-up'],
        'rejected' => ['bg' => 'var(--vx-danger-light)', 'color' => 'var(--vx-danger)', 'label' => 'Rejeté', 'icon' => 'fa-times-circle'],
        'certified' => ['bg' => 'var(--vx-success-light)', 'color' => 'var(--vx-success)', 'label' => 'Certifié', 'icon' => 'fa-certificate'],
      ];
      $ss = $statusStyles[$report['status']] ?? $statusStyles['draft'];
      ?>
      <div class="status-banner" style="background:<?= $ss['bg'] ?>;color:<?= $ss['color'] ?>;">
        <i class="fas <?= $ss['icon'] ?>"></i> <?= $ss['label'] ?>
      </div>

      <div class="detail-body">
        <div class="detail-section">
          <h3><i class="fas fa-chart-bar me-2" style="color:var(--vx-primary);"></i>Score global</h3>
          <p style="font-size:0.85rem;color:var(--vx-text-secondary);">
            Score consolidé: <strong style="color:var(--vx-primary);font-size:1.1rem;"><?= round($report['consolidated_score'] ?? 0) ?>/100</strong><br>
            Niveau de maturité: <strong><?= e($report['maturity_level'] ?? 'N/A') ?></strong>
          </p>
        </div>

        <div class="detail-section">
          <h3><i class="fas fa-list me-2" style="color:var(--vx-primary);"></i>Évaluations incluses (<?= count($items) ?>)</h3>
          <?php foreach ($items as $item): ?>
            <div class="item-row">
              <div class="item-info">
                <div class="item-model"><?= e($item['model_name'] ?? 'Modèle') ?></div>
                <div class="item-meta">
                  <?= e($item['company'] ?? $item['firstname'] ?? 'N/A') ?> &middot;
                  <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                </div>
              </div>
              <div class="item-score"><?= round($item['score'] ?? 0) ?>/100</div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($report['status'] === 'rejected' && !empty($report['admin_comment'])): ?>
          <div class="detail-section">
            <h3><i class="fas fa-comment me-2" style="color:var(--vx-danger);"></i>Commentaire de l'administration</h3>
            <div class="admin-feedback">
              <p><?= e($report['admin_comment']) ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($report['status'] === 'certified'): ?>
          <div class="detail-section">
            <h3><i class="fas fa-certificate me-2" style="color:var(--vx-success);"></i>Certification</h3>
            <div class="admin-feedback">
              <p><strong>Numéro:</strong> <span class="cert-number"><?= e($report['report_number']) ?></span></p>
              <p><strong>Certifié le:</strong> <?= date('d/m/Y H:i', strtotime($report['certified_at'])) ?></p>
              <p><strong>Validé par:</strong> <?= e($report['validated_by'] ?? 'N/A') ?></p>
              <?php if (!empty($report['aqmi_level_assigned'])): ?>
                <p><strong>Niveau AQMI attribué:</strong> <?= e($report['aqmi_level_assigned']) ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:1.5rem;">
          <?php if (in_array($report['status'], ['draft', 'rejected'])): ?>
            <form action="/user/consolidated/<?= $report['id'] ?>/request" method="POST">
              <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Demander la certification</button>
            </form>
          <?php endif; ?>

          <?php if (in_array($report['status'], ['draft', 'rejected'])): ?>
            <form action="/user/consolidated/<?= $report['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer ce rapport consolidé ?');">
              <button type="submit" class="btn-danger"><i class="fas fa-trash me-1"></i> Supprimer</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
