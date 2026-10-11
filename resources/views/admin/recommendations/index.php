<?php
$title = 'Gestion des Recommandations';
ob_start();
?>
<style>
.auto-rec-header { margin-bottom: 1.5rem; }
.auto-rec-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-rec-stats { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.auto-rec-stat {
  display: inline-flex; align-items: center; gap: 0.35rem;
  padding: 0.3rem 0.65rem; border-radius: var(--auto-radius-sm);
  background: var(--auto-bg-card-solid); border: 1px solid var(--auto-border);
  font-size: 0.68rem; font-weight: 600; color: var(--auto-text-secondary);
}
.auto-rec-stat .stat-val { color: var(--auto-cyan); font-weight: 700; }

.auto-table .rec-text { color: var(--auto-text-primary); font-weight: 500; font-size: 0.78rem; line-height: 1.4; }
.auto-table .rec-text-secondary { color: var(--auto-text-secondary); font-weight: 400; font-size: 0.75rem; line-height: 1.4; }
.auto-table .rec-condition code { color: var(--auto-cyan); font-size: 0.68rem; font-family: var(--auto-font-mono); }

/* Language status indicators */
.auto-lang-status { display: inline-flex; gap: 0.15rem; }
.auto-lang-dot {
  width: 16px; height: 16px; border-radius: 50%;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 0.5rem; font-weight: 700; border: 1px solid var(--auto-border);
  background: var(--auto-bg-dark); color: var(--auto-text-muted);
}
.auto-lang-dot.filled { color: #fff; border-color: transparent; }
.auto-lang-dot.fr.filled { background: #3B82B8; }
.auto-lang-dot.en.filled { background: #22A06B; }
.auto-lang-dot.ar.sar.filled { background: #F2B84B; }
.auto-lang-dot.ar.filled { background: #F2B84B; }
.auto-lang-dot.es.filled { background: #E35D6A; }

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

@media (max-width: 768px) { .auto-rec-wrap { overflow-x: auto; } .auto-rec-stats { display: none; } }
</style>

<div class="auto-rec-wrap auto-fade-in">
  <div class="auto-rec-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
      <h5><i class="fas fa-lightbulb me-2" style="color:var(--auto-cyan);"></i><?= count($recommendations) ?> règle(s)</h5>
      <div class="auto-rec-stats">
        <span class="auto-rec-stat"><i class="fas fa-circle-check" style="color:var(--auto-green);"></i> Actives: <span class="stat-val"><?= count(array_filter($recommendations, fn($r) => $r['is_active'])) ?></span></span>
        <span class="auto-rec-stat"><i class="fas fa-circle-xmark" style="color:var(--auto-red);"></i> Inactives: <span class="stat-val"><?= count(array_filter($recommendations, fn($r) => !$r['is_active'])) ?></span></span>
      </div>
    </div>
    <a href="/admin/recommendations/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle règle</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Recommandation</th>
            <th>Langues</th>
            <th>Domaine</th>
            <th>Condition</th>
            <th>Priorité</th>
            <th class="text-center">Actif</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recommendations)): ?>
            <?php foreach ($recommendations as $r): ?>
              <?php
                $hasFr = !empty($r['recommendation_text_fr']);
                $hasEn = !empty($r['recommendation_text']);
                $hasAr = !empty($r['recommendation_text_ar']);
                $hasEs = !empty($r['recommendation_text_es']);
                $langCount = $hasFr + $hasEn + $hasAr + $hasEs;
              ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $r['id'] ?></td>
                <td style="min-width:280px;">
                  <div class="rec-text"><?= e(truncate($r['recommendation_text_fr'] ?: $r['recommendation_text'], 90)) ?></div>
                  <?php if ($hasEs): ?>
                    <div class="rec-text-secondary mt-1"><?= e(truncate($r['recommendation_text_es'], 90)) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="auto-lang-status" title="<?= $langCount ?>/4 langues">
                    <span class="auto-lang-dot fr <?= $hasFr ? 'filled' : '' ?>" title="Français">FR</span>
                    <span class="auto-lang-dot en <?= $hasEn ? 'filled' : '' ?>" title="English">EN</span>
                    <span class="auto-lang-dot ar <?= $hasAr ? 'filled' : '' ?>" title="العربية">AR</span>
                    <span class="auto-lang-dot es <?= $hasEs ? 'filled' : '' ?>" title="Español">ES</span>
                  </div>
                </td>
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
            <tr><td colspan="8" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucune règle trouvée</td></tr>
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
