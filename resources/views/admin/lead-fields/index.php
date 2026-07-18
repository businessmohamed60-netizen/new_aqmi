<?php
$title = 'Champs Personnalisés Prospects';
ob_start();
?>
<style>
.auto-field-header { margin-bottom: 1.5rem; }
.auto-field-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-field-header p { color: var(--auto-text-muted); font-size: 0.75rem; margin-bottom: 0; margin-top: 0.15rem; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
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
.auto-field-info {
  margin-top: 1.25rem;
  padding: 0.75rem 1rem;
  background: rgba(0,212,255,0.06);
  border: 1px solid rgba(0,212,255,0.15);
  border-radius: var(--auto-radius-sm);
}
.auto-field-info small { color: var(--auto-cyan); font-size: 0.7rem; font-weight: 500; }
@media (max-width: 768px) { .auto-field-wrap { overflow-x: auto; } }
</style>

<div class="auto-field-wrap auto-fade-in">
  <div class="auto-field-header d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
    <div>
      <h5><i class="fas fa-id-card me-2" style="color:var(--auto-cyan);"></i>Champs Personnalisés</h5>
      <p>Définissez les champs supplémentaires à collecter auprès des prospects.</p>
    </div>
    <a href="/admin/lead-fields/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouveau champ</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Libellé (FR)</th>
            <th>Type</th>
            <th>Section</th>
            <th class="text-center">Requis</th>
            <th class="text-center">Ordre</th>
            <th class="text-center">Actif</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($fields)): ?>
            <?php foreach ($fields as $f): ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $f['id'] ?></td>
                <td><span style="color:var(--auto-text-primary);font-weight:500;font-size:0.78rem;"><?= e($f['label_fr'] ?: $f['label']) ?></span></td>
                <td><span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($f['field_type']) ?></span></td>
                <td><span class="auto-badge" style="background:var(--auto-purple)18;border-color:var(--auto-purple)35;color:var(--auto-purple);font-size:0.6rem;"><?= e($f['section']) ?></span></td>
                <td class="text-center">
                  <?php if ($f['is_required']): ?>
                    <i class="fas fa-check-circle" style="color:var(--auto-green);"></i>
                  <?php else: ?>
                    <i class="fas fa-times-circle" style="color:var(--auto-text-muted);"></i>
                  <?php endif; ?>
                </td>
                <td class="text-center" style="color:var(--auto-text-secondary);font-size:0.75rem;"><?= $f['sort_order'] ?></td>
                <td class="text-center">
                  <div class="form-check form-switch d-inline-block" style="padding-left:2.5rem;">
                    <input class="form-check-input toggle-field" type="checkbox" data-id="<?= $f['id'] ?>" <?= $f['is_active'] ? 'checked' : '' ?>>
                  </div>
                </td>
                <td class="text-center">
                  <div style="display:flex;justify-content:center;gap:0.3rem;">
                    <a href="/admin/lead-fields/edit/<?= $f['id'] ?>" class="btn-action" title="Modifier"><i class="fas fa-edit"></i></a>
                    <a href="/admin/lead-fields/delete/<?= $f['id'] ?>" class="btn-action btn-action-danger" data-confirm="Supprimer ce champ ?" title="Supprimer"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucun champ personnalisé trouvé</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="auto-field-info">
    <div class="d-flex align-items-center gap-2">
      <i class="fas fa-info-circle"></i>
      <small>
        <strong>Types disponibles :</strong> text, textarea, number, select, multiselect, phone, date, file.
        Les champs apparaîtront automatiquement dans le formulaire d'inscription des prospects.
      </small>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>