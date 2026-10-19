<?php
$title = 'Niveaux de Score';
ob_start();
?>
<style>
.auto-level-header { margin-bottom: 1.5rem; }
.auto-level-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-level-card {
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  padding: 1.25rem;
  transition: var(--auto-transition);
  position: relative;
  overflow: hidden;
}
.auto-level-card:hover { border-color: var(--auto-border-glow); box-shadow: var(--auto-shadow-glow); transform: translateY(-2px); }
.auto-level-card h6 { color: var(--auto-text-primary); font-weight: 600; font-size: 0.9rem; margin-bottom: 0.35rem; }
.auto-level-card .level-en { color: var(--auto-text-muted); font-size: 0.7rem; }
.auto-level-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }
.auto-level-actions a {
  width: 30px; height: 30px;
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
  font-size: 0.75rem;
}
.auto-level-actions a:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-level-actions .danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
</style>

<div class="auto-fade-in">
  <div class="auto-level-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-layer-group me-2" style="color:var(--auto-cyan);"></i><?= count($levels) ?> niveau(x)</h5>
    <a href="/admin/score-levels/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouveau niveau</a>
  </div>

  <div class="row g-3">
    <?php foreach ($levels as $l): ?>
      <div class="col-md-4">
        <div class="auto-level-card" style="border-left: 3px solid <?= e($l['color']) ?>;">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6><?= e($l['name_fr'] ?: $l['name']) ?></h6>
              <div style="margin-bottom:0.5rem;">
                <span class="auto-badge" style="background:<?= e($l['color']) ?>18;border-color:<?= e($l['color']) ?>35;color:<?= e($l['color']) ?>;">
                  <?= e($l['min_percent']) ?>% - <?= e($l['max_percent']) ?>%
                </span>
              </div>
              <div class="level-en"><?= e($l['name']) ?></div>
            </div>
            <div class="auto-level-actions">
              <a href="/admin/score-levels/edit/<?= $l['id'] ?>" title="Modifier"><i class="fas fa-edit"></i></a>
              <a href="/admin/score-levels/delete/<?= $l['id'] ?>" class="danger" data-confirm="Supprimer ce niveau ?" title="Supprimer"><i class="fas fa-trash"></i></a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>