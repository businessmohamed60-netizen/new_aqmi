<?php
$title = 'Rapports';
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
}
.auto-table .btn-auto-action:hover {
  border-color: var(--auto-cyan-glow);
  color: var(--auto-cyan);
  background: var(--auto-cyan-dim);
}
.auto-table .btn-auto-validate:hover {
  border-color: var(--auto-green-glow);
  color: var(--auto-green);
  background: rgba(0,245,160,0.08);
}
.auto-table .btn-auto-reject:hover {
  border-color: var(--auto-red-glow);
  color: var(--auto-red);
  background: rgba(255,51,102,0.08);
}
.auto-table td .auto-badge { font-size: 0.6rem; padding: 0.15rem 0.5rem; }
@media (max-width: 768px) {
  .auto-reports-wrap { overflow-x: auto; }
}
</style>

<div class="auto-reports-wrap auto-fade-in">
  <div class="auto-reports-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-file-alt me-2" style="color:var(--auto-cyan);"></i>Gestion des Rapports</h5>
    <span class="auto-badge auto-badge-cyan"><?= count($reports) ?> rapport(s)</span>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Entreprise</th>
            <th>Contact</th>
            <th>Statut</th>
            <th>Validé par</th>
            <th>Généré le</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $r):
              $statusBadge = match($r['status']) {
                'validated' => '<span class="auto-badge auto-badge-green"><i class="fas fa-check-circle me-1"></i>Validé</span>',
                'rejected' => '<span class="auto-badge auto-badge-red"><i class="fas fa-times-circle me-1"></i>Rejeté</span>',
                default => '<span class="auto-badge auto-badge-yellow"><i class="fas fa-hourglass me-1"></i>En attente</span>'
              };
            ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;font-family:var(--auto-font-mono);"><?= $r['id'] ?></td>
                <td style="color:var(--auto-text-primary);font-weight:600;"><?= e($r['company'] ?? 'N/A') ?></td>
                <td><?= e($r['firstname'] ?? '') ?> <?= e($r['lastname'] ?? '') ?></td>
                <td><?= $statusBadge ?></td>
                <td style="font-size:0.7rem;"><?= e($r['validated_by'] ?? '-') ?></td>
                <td style="font-size:0.7rem;"><?= formatDate($r['generated_at']) ?></td>
                <td class="text-center">
                  <div style="display:flex;justify-content:center;gap:0.35rem;">
                    <?php if ($r['status'] === 'validated' && $r['file_path'] && file_exists(BASE_PATH . '/storage/reports/' . $r['file_path'])): ?>
                      <a href="/report/<?= $r['assessment_id'] ?>/download" class="btn-auto-action" title="Télécharger">
                        <i class="fas fa-download"></i>
                      </a>
                    <?php endif; ?>
                    <?php if ($r['status'] === 'pending'): ?>
                      <form method="post" action="/admin/reports/validate/<?= $r['id'] ?>" style="display:inline;">
                        <button type="submit" class="btn-auto-action btn-auto-validate" title="Valider">
                          <i class="fas fa-check"></i>
                        </button>
                      </form>
                      <form method="post" action="/admin/reports/reject/<?= $r['id'] ?>" style="display:inline;">
                        <button type="submit" class="btn-auto-action btn-auto-reject" title="Rejeter" onclick="return confirm('Rejeter ce rapport ?');">
                          <i class="fas fa-times"></i>
                        </button>
                      </form>
                    <?php elseif ($r['status'] === 'validated'): ?>
                      <span class="auto-badge auto-badge-green" style="font-size:0.6rem;"><i class="fas fa-check-circle me-1"></i>Validé</span>
                    <?php elseif ($r['status'] === 'rejected'): ?>
                      <span class="auto-badge auto-badge-red" style="font-size:0.6rem;"><i class="fas fa-times-circle me-1"></i>Rejeté</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align:center;color:var(--auto-text-muted);padding:2rem;font-size:0.8rem;">Aucun rapport généré</td></tr>
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