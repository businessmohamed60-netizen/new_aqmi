<?php
$title = 'Gestion des Recommandations';
ob_start();
?>
<style>
.auto-rec-header { margin-bottom: 1.5rem; }
.auto-rec-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-table .rec-text { color: var(--auto-text-primary); font-weight: 500; font-size: 0.78rem; }
.auto-table .rec-condition code { color: var(--auto-cyan); font-size: 0.68rem; font-family: var(--auto-font-mono); }
.auto-table .btn-action {
  width: 28px; height: 28px;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 0.7rem;
}
.auto-table .btn-action:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-table .btn-action-danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
@media (max-width: 768px) { .auto-rec-wrap { overflow-x: auto; } }
</style>

<div class="auto-rec-wrap auto-fade-in">
  <div class="auto-rec-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-lightbulb me-2" style="color:var(--auto-cyan);"></i><?= count($recommendations) ?> règle(s)</h5>
    <a href="/admin/recommendations/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle règle</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr><th>#</th><th>Recommandation</th><th>Domaine</th><th>Condition</th><th>Priorité</th><th class="text-center">Actif</th><th class="text-center">Actions</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($recommendations)): ?>
            <?php foreach ($recommendations as $r): ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $r['id'] ?></td>
                <td><div class="rec-text"><?= e(truncate($r['recommendation_text_fr'] ?: $r['recommendation_text'], 80)) ?></div></td>
                <td><span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($r['domain_name_fr'] ?: $r['domain_name'] ?: 'Global') ?></span></td>
                <td><span class="rec-condition"><code><?= e($r['condition_field'] ?? '-') ?> <?= e($r['condition_operator'] ?? '-') ?> <?= e($r['condition_value'] ?? '-') ?>%</code></span></td>
                <td>
                  <?php
                    $pClass = match($r['priority']) {
                      'critical' => 'auto-badge-red',
                      'high' => 'auto-badge-yellow',
                      'medium' => 'auto-badge-cyan',
                      default => 'auto-badge'
                    };
                  ?>
                  <span class="auto-badge <?= $pClass ?>" style="font-size:0.6rem;">
                    <?= ucfirst($r['priority']) ?>
                  </span>
                </td>
                <td class="text-center">
                  <span class="auto-badge <?= $r['is_active'] ? 'auto-badge-green' : 'auto-badge-red' ?>">
                    <?= $r['is_active'] ? 'Oui' : 'Non' ?>
                  </span>
                </td>
                <td class="text-center">
                  <div style="display:flex;justify-content:center;gap:0.3rem;">
                    <a href="/admin/recommendations/edit/<?= $r['id'] ?>" class="btn-action" title="Modifier"><i class="fas fa-edit"></i></a>
                    <a href="/admin/recommendations/delete/<?= $r['id'] ?>" class="btn-action btn-action-danger" data-confirm="Supprimer cette règle ?" title="Supprimer"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucune règle trouvée</td></tr>
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