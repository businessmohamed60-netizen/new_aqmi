<?php
$title = $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation';
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
    <h5><i class="fas fa-lightbulb me-2" style="color:var(--auto-cyan);"></i><?= $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/recommendations/save">
      <?= csrf_field() ?>
      <?php if ($recommendation): ?><input type="hidden" name="id" value="<?= $recommendation['id'] ?>"><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Domaine</label>
          <select name="domain_id" class="auto-select">
            <option value="">Global</option>
            <?php foreach ($domains as $d): ?>
              <option value="<?= $d['id'] ?>" <?= ($recommendation['domain_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                <?= e($d['name_fr'] ?: $d['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Priorité</label>
          <select name="priority" class="auto-select">
            <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($recommendation['priority'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="auto-label">Champ condition</label>
          <input type="text" name="condition_field" class="auto-input" value="<?= e($recommendation['condition_field'] ?? 'global_score') ?>" placeholder="global_score">
        </div>
        <div class="col-md-4">
          <label class="auto-label">Opérateur</label>
          <select name="condition_operator" class="auto-select">
            <?php foreach (['<' => '<', '>' => '>', '<=' => '≤', '>=' => '≥', '==' => '='] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($recommendation['condition_operator'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="auto-label">Valeur seuil (%)</label>
          <input type="number" name="condition_value" class="auto-input" step="0.01" value="<?= e($recommendation['condition_value'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Texte (EN)</label>
          <textarea name="recommendation_text" class="auto-textarea" rows="3"><?= e($recommendation['recommendation_text'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Texte (FR)</label>
          <textarea name="recommendation_text_fr" class="auto-textarea" rows="3"><?= e($recommendation['recommendation_text_fr'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="mb-4">
        <label class="auto-label">Texte (AR)</label>
        <textarea name="recommendation_text_ar" class="auto-textarea" rows="2" dir="rtl"><?= e($recommendation['recommendation_text_ar'] ?? '') ?></textarea>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($recommendation['is_active']) || $recommendation['is_active'] ? 'checked' : '' ?>>
          <label class="form-check-label">Actif</label>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;">
        <div class="d-flex gap-2">
          <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
          <a href="/admin/recommendations" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
        </div>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>