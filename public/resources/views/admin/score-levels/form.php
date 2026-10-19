<?php
$title = $level ? 'Modifier le niveau' : 'Nouveau niveau';
ob_start();
?>
<style>
.auto-form-wrap { max-width: 800px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-layer-group me-2" style="color:var(--auto-cyan);"></i><?= $level ? 'Modifier le niveau' : 'Nouveau niveau' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/score-levels/save">
      <?= csrf_field() ?>
      <?php if ($level): ?><input type="hidden" name="id" value="<?= $level['id'] ?>"><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Nom (EN) *</label>
          <input type="text" name="name" class="auto-input" value="<?= e($level['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Nom (FR)</label>
          <input type="text" name="name_fr" class="auto-input" value="<?= e($level['name_fr'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Nom (AR)</label>
          <input type="text" name="name_ar" class="auto-input" value="<?= e($level['name_ar'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="auto-label">Min (%) *</label>
          <input type="number" name="min_percent" class="auto-input" step="0.01" value="<?= e($level['min_percent'] ?? '0') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="auto-label">Max (%) *</label>
          <input type="number" name="max_percent" class="auto-input" step="0.01" value="<?= e($level['max_percent'] ?? '30') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="auto-label">Couleur</label>
          <input type="color" name="color" class="auto-input" style="padding:0.25rem;" value="<?= e($level['color'] ?? '#6c757d') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Icône</label>
          <input type="text" name="icon" class="auto-input" value="<?= e($level['icon'] ?? 'fa-chart-bar') ?>">
        </div>
        <div class="col-md-6">
          <label class="auto-label">Ordre</label>
          <input type="number" name="sort_order" class="auto-input" value="<?= e($level['sort_order'] ?? '0') ?>">
        </div>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($level['is_active']) || $level['is_active'] ? 'checked' : '' ?>>
          <label class="form-check-label">Actif</label>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;">
        <div class="d-flex gap-2">
          <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
          <a href="/admin/score-levels" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
        </div>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>