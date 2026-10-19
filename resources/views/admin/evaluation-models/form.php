<?php
$title = $model ? 'Modifier le modèle : ' . e($model['name_fr'] ?: $model['name']) : "Nouveau modèle d'évaluation";
ob_start();
?>
<style>
.auto-form-wrap { max-width: 1000px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-form-section-title { color: var(--auto-text-primary); font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--auto-border); }
.auto-form-section-title i { color: var(--auto-cyan); margin-right: 0.5rem; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.auto-preview-wrap { background: var(--auto-bg-card); border: 1px solid var(--auto-border); border-radius: var(--auto-radius-lg); backdrop-filter: blur(20px); }
.auto-preview-wrap h6 { color: var(--auto-text-primary); font-weight: 600; }
.auto-preview-content { background: var(--auto-bg-dark); border: 2px dashed var(--auto-border); border-radius: var(--auto-radius-lg); }
.input-group-text { background: var(--auto-bg-card-solid); border: 1px solid var(--auto-border); color: var(--auto-text-secondary); font-size: 0.75rem; border-radius: var(--auto-radius-sm); padding: 0.4rem 0.7rem; }
.domain-checkbox-list { max-height:220px; overflow-y:auto; border:1px solid var(--auto-border); border-radius: var(--auto-radius-sm); padding:0.5rem; }
.domain-checkbox-list .form-check-label { color: var(--auto-text-secondary); font-size: 0.78rem; }
.auto-preview-wrap .badge-style { border-radius: 100px; padding: 0.25rem 0.85rem; font-size: 0.7rem; }
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-layer-group me-2" style="color:var(--auto-cyan);"></i><?= $model ? 'Modifier le modèle' : 'Nouveau modèle' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/evaluation-models/save" class="needs-validation" novalidate>
      <?= csrf_field() ?>
      <?php if ($model): ?><input type="hidden" name="id" value="<?= $model['id'] ?>"><?php endif; ?>

      <div class="row g-4">
        <!-- Left Column: Model Info -->
        <div class="col-md-6">
          <div class="auto-form-section-title"><i class="fas fa-info-circle"></i>Informations du modèle</div>

          <div class="mb-3">
            <label class="auto-label">Nom (EN) *</label>
            <input type="text" name="name" class="auto-input" value="<?= e($model['name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="auto-label">Nom (FR) *</label>
            <input type="text" name="name_fr" class="auto-input" value="<?= e($model['name_fr'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="auto-label">Nom (AR)</label>
            <input type="text" name="name_ar" class="auto-input" value="<?= e($model['name_ar'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="auto-label">Description (FR)</label>
            <textarea name="description_fr" class="auto-textarea" rows="3"><?= e($model['description_fr'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Description (EN)</label>
            <textarea name="description" class="auto-textarea" rows="3"><?= e($model['description'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Description (AR)</label>
            <textarea name="description_ar" class="auto-textarea" rows="2"><?= e($model['description_ar'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Right Column: Appearance & Settings -->
        <div class="col-md-6">
          <div class="auto-form-section-title"><i class="fas fa-palette"></i>Apparence & Configuration</div>

          <div class="mb-3">
            <label class="auto-label">Icône (Font Awesome)</label>
            <div style="display:flex;gap:0.35rem;">
              <span style="background:var(--auto-bg-card-solid);border:1px solid var(--auto-border);border-radius:var(--auto-radius-sm);padding:0.4rem 0.7rem;color:var(--auto-text-secondary);display:flex;align-items:center;"><i class="fas <?= e($model['icon'] ?? 'fa-clipboard-check') ?>"></i></span>
              <input type="text" name="icon" class="auto-input" style="flex:1;" value="<?= e($model['icon'] ?? 'fa-clipboard-check') ?>">
            </div>
            <small style="color:var(--auto-text-muted);font-size:0.65rem;">Ex: fa-industry, fa-chart-line, fa-microchip, fa-shield-alt, fa-truck</small>
          </div>

          <div class="mb-3">
            <label class="auto-label">Couleur</label>
            <div style="display:flex;gap:0.35rem;">
              <input type="color" name="color" class="auto-input" style="width:50px;padding:0.25rem;" value="<?= e($model['color'] ?? '#00d4ff') ?>">
              <input type="text" name="color_hex" class="auto-input" style="max-width:100px;flex:1;" value="<?= e($model['color'] ?? '#00d4ff') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="auto-label">Ordre</label>
              <input type="number" name="sort_order" class="auto-input" value="<?= e($model['sort_order'] ?? '0') ?>">
            </div>
            <div class="col-6">
              <label class="auto-label">Actif</label>
              <div class="form-check form-switch" style="padding-left:2.5rem;padding-top:0.2rem;">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($model['is_active']) || $model['is_active'] ? 'checked' : '' ?>>
              </div>
            </div>
          </div>

          <!-- Domain Selection -->
          <div class="mb-3">
            <label class="auto-label">Domaines associés</label>
            <div class="domain-checkbox-list">
              <?php if (!empty($allDomains)): ?>
                <?php foreach ($allDomains as $d): ?>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="domain_ids[]" value="<?= $d['id'] ?>" id="domain_<?= $d['id'] ?>"
                      <?= in_array($d['id'], $selectedDomainIds ?? []) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="domain_<?= $d['id'] ?>">
                      <i class="fas <?= e($d['icon'] ?: 'fa-folder') ?> me-1" style="color:var(--auto-text-muted);"></i>
                      <?= e($d['name_fr'] ?: $d['name']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="color:var(--auto-text-muted);font-size:0.75rem;margin-bottom:0;">Aucun domaine disponible. Créez d'abord des domaines.</p>
              <?php endif; ?>
            </div>
            <small style="color:var(--auto-text-muted);font-size:0.65rem;">Sélectionnez les domaines qui composeront ce modèle d'évaluation</small>
          </div>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;margin-top:1rem;">
        <div class="d-flex gap-2">
          <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
          <a href="/admin/evaluation-models" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
        </div>
      </div>
    </form>
  </div>

  <!-- Preview Card -->
  <?php if (!empty($model)): ?>
  <div class="auto-preview-wrap p-4 mt-4">
    <h6 class="mb-3"><i class="fas fa-eye me-2" style="color:var(--auto-text-muted);"></i>Aperçu du modèle</h6>
    <div class="auto-preview-content p-4 text-center">
      <div style="width:64px;height:64px;background:<?= e($model['color'] ?? '#00d4ff') ?>;border-radius:var(--auto-radius-md);display:flex;align-items:center;justify-content:center;color:#080c18;font-size:1.5rem;margin:0 auto 12px;">
        <i class="fas <?= e($model['icon'] ?? 'fa-clipboard-check') ?>"></i>
      </div>
      <h5 style="color:var(--auto-text-primary);font-weight:700;"><?= e($model['name_fr'] ?: $model['name']) ?></h5>
      <?php if ($model['description_fr']): ?>
        <p style="color:var(--auto-text-muted);font-size:0.8rem;margin-bottom:0.5rem;"><?= e($model['description_fr']) ?></p>
      <?php endif; ?>
      <span class="badge-style" style="background:<?= e($model['color'] ?? '#00d4ff') ?>20;color:<?= e($model['color'] ?? '#00d4ff') ?>;border:1px solid <?= e($model['color'] ?? '#00d4ff') ?>35;">
        <?= count($selectedDomainIds ?? []) ?> domaines · <?= \App\Models\EvaluationModel::getQuestionsCount($model['id']) ?> questions
      </span>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>