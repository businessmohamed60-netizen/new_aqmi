<?php
$title = $domain ? 'Modifier le domaine' : 'Nouveau domaine';
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
    <h5><i class="fas fa-folder me-2" style="color:var(--auto-cyan);"></i><?= $domain ? 'Modifier le domaine' : 'Nouveau domaine' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/domains/save">
      <?= csrf_field() ?>
      <?php if ($domain): ?><input type="hidden" name="id" value="<?= $domain['id'] ?>"><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Nom (EN) *</label>
          <input type="text" name="name" class="auto-input" value="<?= e($domain['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Nom (FR)</label>
          <input type="text" name="name_fr" class="auto-input" value="<?= e($domain['name_fr'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Nom (AR)</label>
          <input type="text" name="name_ar" class="auto-input" value="<?= e($domain['name_ar'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Description (EN)</label>
          <textarea name="description" class="auto-textarea" rows="3"><?= e($domain['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Description (FR)</label>
          <textarea name="description_fr" class="auto-textarea" rows="3"><?= e($domain['description_fr'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="auto-label">Icône (FontAwesome)</label>
          <input type="text" name="icon" class="auto-input" value="<?= e($domain['icon'] ?? 'fa-folder') ?>">
        </div>
        <div class="col-md-4">
          <label class="auto-label">Poids</label>
          <input type="number" name="weight" class="auto-input" step="0.5" value="<?= e($domain['weight'] ?? '1') ?>">
        </div>
        <div class="col-md-4">
          <label class="auto-label">Ordre</label>
          <input type="number" name="sort_order" class="auto-input" value="<?= e($domain['sort_order'] ?? '0') ?>">
        </div>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($domain['is_active']) || $domain['is_active'] ? 'checked' : '' ?>>
          <label class="form-check-label">Actif</label>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;">
        <div class="d-flex gap-2">
          <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
          <a href="/admin/domains" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
        </div>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>