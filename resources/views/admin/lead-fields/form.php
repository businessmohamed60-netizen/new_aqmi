<?php
$title = $field ? 'Modifier le champ : ' . e($field['label_fr'] ?: $field['label']) : 'Nouveau champ personnalisé';
ob_start();
$options = $field ? json_decode($field['options'] ?? '[]', true) : [];
?>
<style>
.auto-form-wrap { max-width: 800px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }
.auto-table .btn-action-sm {
  padding: 0.25rem 0.55rem;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  font-size: 0.7rem;
}
.auto-table .btn-action-sm:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-table .btn-action-sm-danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-id-card me-2" style="color:var(--auto-cyan);"></i><?= $field ? 'Modifier le champ' : 'Nouveau champ personnalisé' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/lead-fields/save" class="needs-validation" novalidate>
      <?= csrf_field() ?>
      <?php if ($field): ?><input type="hidden" name="id" value="<?= $field['id'] ?>"><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Libellé (FR) *</label>
          <input type="text" name="label_fr" class="auto-input" value="<?= e($field['label_fr'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Libellé (EN)</label>
          <input type="text" name="label" class="auto-input" value="<?= e($field['label'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Libellé (AR)</label>
          <input type="text" name="label_ar" class="auto-input" value="<?= e($field['label_ar'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="auto-label">Type de champ *</label>
          <select name="field_type" class="auto-select" id="fieldType" required>
            <option value="text" <?= ($field['field_type'] ?? '') == 'text' ? 'selected' : '' ?>>Texte court</option>
            <option value="textarea" <?= ($field['field_type'] ?? '') == 'textarea' ? 'selected' : '' ?>>Texte long</option>
            <option value="number" <?= ($field['field_type'] ?? '') == 'number' ? 'selected' : '' ?>>Nombre</option>
            <option value="select" <?= ($field['field_type'] ?? '') == 'select' ? 'selected' : '' ?>>Liste déroulante</option>
            <option value="multiselect" <?= ($field['field_type'] ?? '') == 'multiselect' ? 'selected' : '' ?>>Sélection multiple</option>
            <option value="phone" <?= ($field['field_type'] ?? '') == 'phone' ? 'selected' : '' ?>>Téléphone</option>
            <option value="date" <?= ($field['field_type'] ?? '') == 'date' ? 'selected' : '' ?>>Date</option>
            <option value="file" <?= ($field['field_type'] ?? '') == 'file' ? 'selected' : '' ?>>Fichier</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="auto-label">Section</label>
          <select name="section" class="auto-select">
            <option value="general" <?= ($field['section'] ?? '') == 'general' ? 'selected' : '' ?>>Général</option>
            <option value="company" <?= ($field['section'] ?? '') == 'company' ? 'selected' : '' ?>>Entreprise</option>
            <option value="industrial" <?= ($field['section'] ?? '') == 'industrial' ? 'selected' : '' ?>>Industriel</option>
            <option value="certifications" <?= ($field['section'] ?? '') == 'certifications' ? 'selected' : '' ?>>Certifications</option>
            <option value="contact" <?= ($field['section'] ?? '') == 'contact' ? 'selected' : '' ?>>Contact</option>
            <option value="engagement" <?= ($field['section'] ?? '') == 'engagement' ? 'selected' : '' ?>>Engagement</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="auto-label">Ordre</label>
          <input type="number" name="sort_order" class="auto-input" value="<?= e($field['sort_order'] ?? '0') ?>">
        </div>
        <div class="col-md-2">
          <label class="auto-label">Requis</label>
          <div class="form-check form-switch" style="padding-left:2.5rem;padding-top:0.2rem;">
            <input type="hidden" name="is_required" value="0">
            <input class="form-check-input" type="checkbox" name="is_required" value="1" <?= ($field['is_required'] ?? 0) ? 'checked' : '' ?>>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Placeholder (FR)</label>
          <input type="text" name="placeholder_fr" class="auto-input" value="<?= e($field['placeholder_fr'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="auto-label">Placeholder (EN)</label>
          <input type="text" name="placeholder" class="auto-input" value="<?= e($field['placeholder'] ?? '') ?>">
        </div>
      </div>

      <!-- Options for select/multiselect -->
      <div class="mb-4" id="optionsSection" style="display:<?= in_array($field['field_type'] ?? '', ['select', 'multiselect']) ? 'block' : 'none' ?>;">
        <label class="auto-label">Options</label>
        <div id="optionsContainer">
          <?php if (!empty($options)): ?>
            <?php foreach ($options as $i => $opt): ?>
              <div class="row g-2 mb-2 option-row">
                <div class="col-5"><input type="text" name="options[<?= $i ?>][value]" class="auto-input" placeholder="Valeur" value="<?= e($opt['value'] ?? '') ?>"></div>
                <div class="col-5"><input type="text" name="options[<?= $i ?>][label]" class="auto-input" placeholder="Libellé" value="<?= e($opt['label'] ?? '') ?>"></div>
                <div class="col-2"><button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="button" class="btn-action-sm" id="addOption" style="margin-top:0.35rem;"><i class="fas fa-plus me-1"></i>Ajouter une option</button>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($field['is_active']) || $field['is_active'] ? 'checked' : '' ?>>
          <label class="form-check-label">Actif</label>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;">
        <div class="d-flex gap-2">
          <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
          <a href="/admin/lead-fields" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).ready(function() {
    var optIndex = <?= json_encode(count($options)) ?>;
    if (typeof optIndex !== 'number' || isNaN(optIndex)) optIndex = 0;

    $('#fieldType').on('change', function() {
        $('#optionsSection').toggle($(this).val() === 'select' || $(this).val() === 'multiselect');
    });

    $('#addOption').on('click', function() {
        var html = '<div class="row g-2 mb-2 option-row">' +
            '<div class="col-5"><input type="text" name="options[' + optIndex + '][value]" class="auto-input" placeholder="Valeur"></div>' +
            '<div class="col-5"><input type="text" name="options[' + optIndex + '][label]" class="auto-input" placeholder="Libellé"></div>' +
            '<div class="col-2"><button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button></div></div>';
        $('#optionsContainer').append(html);
        optIndex++;
    });

    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
    });
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>