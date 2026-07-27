<?php
$title = 'Gestion des Domaines';
ob_start();
?>
<style>
.auto-domain-header { margin-bottom: 1.5rem; }
.auto-domain-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-domain-card {
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  padding: 1.25rem;
  transition: var(--auto-transition);
}
.auto-domain-card:hover { border-color: var(--auto-border-glow); box-shadow: var(--auto-shadow-glow); transform: translateY(-2px); }
.auto-domain-card .domain-icon {
  width: 44px; height: 44px;
  background: var(--auto-cyan-dim);
  border: 1px solid var(--auto-border-glow);
  border-radius: var(--auto-radius-md);
  display: flex; align-items: center; justify-content: center;
  color: var(--auto-cyan); flex-shrink: 0;
}
.auto-domain-card h6 { color: var(--auto-text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.2rem; }
.auto-domain-card .domain-en { color: var(--auto-text-muted); font-size: 0.7rem; margin-bottom: 0.5rem; }
.auto-domain-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }
.auto-domain-actions a {
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
.auto-domain-actions a:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-domain-actions .danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
</style>

<div class="auto-fade-in">
  <div class="auto-domain-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-folder me-2" style="color:var(--auto-cyan);"></i><?= count($domains) ?> domaine(s)</h5>
    <a href="/admin/domains/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouveau domaine</a>
  </div>

  <div class="row g-3">
    <?php foreach ($domains as $d): ?>
      <div class="col-md-6 col-lg-4">
        <div class="auto-domain-card">
          <div class="d-flex align-items-start gap-3">
            <div class="domain-icon">
              <i class="fas <?= e($d['icon'] ?: 'fa-folder') ?>"></i>
            </div>
            <div style="flex:1;min-width:0;">
              <h6><?= e($d['name_fr'] ?: $d['name']) ?></h6>
              <div class="domain-en"><?= e($d['name']) ?></div>
              <div style="display:flex;gap:0.35rem;flex-wrap:wrap;">
                <span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;">Poids: <?= e($d['weight']) ?></span>
                <span class="auto-badge <?= $d['is_active'] ? 'auto-badge-green' : 'auto-badge-red' ?>">
                  <?= $d['is_active'] ? 'Actif' : 'Inactif' ?>
                </span>
              </div>
            </div>
            <div class="auto-domain-actions">
              <a href="/admin/domains/edit/<?= $d['id'] ?>" title="Modifier"><i class="fas fa-edit"></i></a>
              <a href="/admin/domains/delete/<?= $d['id'] ?>" class="danger" data-confirm="Supprimer ce domaine ?" title="Supprimer"><i class="fas fa-trash"></i></a>
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