<?php
$title = 'Demandes de Certification';
ob_start();
?>
<style>
.auto-reports-header {
  margin-bottom: 1.5rem;
}
.auto-reports-header h5 {
  color: var(--auto-text-primary);
  font-weight: 700;
  font-size: 1rem;
  margin-bottom: 0;
}
.auto-reports-header .auto-badge {
  font-size: 0.6rem;
  padding: 0.2rem 0.7rem;
}
.auto-table .btn-auto-action {
  padding: 0.3rem 0.6rem;
  font-size: 0.75rem;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  text-decoration: none;
}
.auto-table .btn-auto-action:hover {
  border-color: var(--auto-cyan-glow);
  color: var(--auto-cyan);
  background: var(--auto-cyan-dim);
}
.auto-table td .auto-badge { font-size: 0.6rem; padding: 0.15rem 0.5rem; }
@media (max-width: 768px) {
  .auto-reports-wrap { overflow-x: auto; }
}
</style>

<div class="auto-reports-wrap auto-fade-in">
  <div class="auto-reports-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-certificate me-2" style="color:var(--auto-cyan);"></i>Demandes de Certification AQMI</h5>
    <span class="auto-badge auto-badge-cyan"><?= count($reports) ?> demande(s)</span>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr>
            <th>#</th>
            <th>N° Rapport</th>
            <th>Entreprise</th>
            <th>Utilisateur</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Date demande</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $r):
              $statusBadge = match($r['status']) {
                'certification_requested' => '<span class="auto-badge auto-badge-yellow"><i class="fas fa-hourglass me-1"></i>En attente</span>',
                'under_review' => '<span class="auto-badge auto-badge-cyan"><i class="fas fa-magnifying-glass me-1"></i>En examen</span>',
                'approved' => '<span class="auto-badge" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="fas fa-thumbs-up me-1"></i>Approuvé</span>',
                'certified' => '<span class="auto-badge auto-badge-green"><i class="fas fa-certificate me-1"></i>Certifié</span>',
                'rejected' => '<span class="auto-badge auto-badge-red"><i class="fas fa-times-circle me-1"></i>Rejeté</span>',
                default => '<span class="auto-badge">' . e($r['status']) . '</span>'
              };
              $userName = trim(($r['user_firstname'] ?? '') . ' ' . ($r['user_lastname'] ?? ''));
            ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;font-family:var(--auto-font-mono);"><?= $r['id'] ?></td>
                <td style="font-size:0.7rem;font-family:var(--auto-font-mono);color:var(--auto-text-secondary);"><?= e($r['report_number'] ?? '—') ?></td>
                <td style="color:var(--auto-text-primary);font-weight:600;"><?= e($r['company'] ?? 'N/A') ?></td>
                <td style="font-size:0.75rem;"><?= $userName !== '' ? e($userName) : '<span style="color:var(--auto-text-muted);">—</span>' ?></td>
                <td style="font-size:0.75rem;font-weight:600;"><?= isset($r['total_score']) ? round($r['total_score']) . '%' : '—' ?></td>
                <td><?= $statusBadge ?></td>
                <td style="font-size:0.7rem;"><?= $r['certification_requested_at'] ? formatDate($r['certification_requested_at']) : formatDate($r['generated_at']) ?></td>
                <td class="text-center">
                  <a href="/admin/reports/<?= $r['id'] ?>" class="btn-auto-action" title="Ouvrir le dossier">
                    <i class="fas fa-folder-open"></i> Ouvrir
                  </a>
                  <form method="POST" action="/admin/reports/delete/<?= $r['id'] ?>" class="d-inline"
                        onsubmit="return confirm('Supprimer définitivement ce rapport (<?= e($r['report_number'] ?? '#' . $r['id']) ?>) ? Cette action est irréversible.');">
                    <button type="submit" class="btn-auto-action" title="Supprimer le rapport"
                            style="color:var(--auto-danger,#ef4444);border-color:var(--auto-border);">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align:center;color:var(--auto-text-muted);padding:2rem;font-size:0.8rem;">Aucune demande de certification</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
