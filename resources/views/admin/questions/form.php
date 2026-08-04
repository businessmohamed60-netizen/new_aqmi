<?php
$title = $question ? 'Modifier la question' : 'Nouvelle question';
ob_start();
$evaluationModels = \App\Models\EvaluationModel::allActive();
$questionType = $question['question_type'] ?? 'rating_scale';
$options = $question ? json_decode($question['options'] ?? '[]', true) : [];
?>
<style>
.auto-form-wrap { max-width: 1000px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-form-section-title { color: var(--auto-text-primary); font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--auto-border); }
.auto-form-section-title i { color: var(--auto-cyan); margin-right: 0.5rem; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }
.auto-table .input-group .auto-input { font-size: 0.75rem; }
.auto-table .btn-action-sm {
  padding: 0.25rem 0.5rem;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.7rem;
}
.auto-table .btn-action-sm:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-table .btn-action-sm-danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
.auto-preview-box {
  background: var(--auto-bg-card);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  backdrop-filter: blur(20px);
}
.auto-preview-box .preview-title { color: var(--auto-text-primary); font-weight: 600; font-size: 0.8rem; margin-bottom: 1rem; }
.auto-preview-box .preview-title i { color: var(--auto-text-muted); }
.auto-preview-inner { background: var(--auto-bg-dark); border-radius: var(--auto-radius-sm); padding: 1rem; border: 1px solid var(--auto-border); }
.auto-preview-inner .preview-label { color: var(--auto-text-secondary); font-size: 0.7rem; font-weight: 600; margin-bottom: 0.5rem; }
.auto-preview-inner .preview-option {
  text-align: center; padding: 0.5rem 0.4rem; flex: 1;
  border: 1px solid var(--auto-border); border-radius: var(--auto-radius-sm);
  background: transparent; cursor: default;
}
.auto-preview-inner .preview-option .val { font-weight: 700; font-size: 0.8rem; color: var(--auto-text-primary); }
.auto-preview-inner .preview-option .lbl { font-size: 0.55rem; color: var(--auto-text-muted); }
.auto-preview-inner .form-check-label { color: var(--auto-text-secondary); font-size: 0.75rem; }
.auto-preview-inner textarea.auto-input, .auto-preview-inner input.auto-input { background: rgba(0,0,0,0.3); }
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-question-circle me-2" style="color:var(--auto-cyan);"></i><?= $question ? 'Modifier la question' : 'Nouvelle question' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/questions/save" class="needs-validation" novalidate>
      <?= csrf_field() ?>
      <?php if ($question): ?><input type="hidden" name="id" value="<?= $question['id'] ?>"><?php endif; ?>

      <div class="row g-4">
        <!-- Left Column -->
        <div class="col-md-6">
          <div class="auto-form-section-title"><i class="fas fa-cog"></i>Contexte de la question</div>

          <div class="mb-3">
            <label class="auto-label">Modèle d'évaluation</label>
            <select name="model_id" class="auto-select">
              <option value="">Tous les modèles</option>
              <?php foreach ($evaluationModels as $em): ?>
                <option value="<?= $em['id'] ?>" <?= ($question['model_id'] ?? '') == $em['id'] ? 'selected' : '' ?>>
                  <?= e($em['name_fr'] ?: $em['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small style="color:var(--auto-text-muted);font-size:0.65rem;">Liez cette question à un modèle d'évaluation spécifique</small>
          </div>

          <div class="mb-3">
            <label class="auto-label">Domaine *</label>
            <select name="domain_id" class="auto-select" required>
              <option value="">Sélectionnez un domaine</option>
              <?php foreach ($domains as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($question['domain_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                  <?= e($d['name_fr'] ?: $d['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="auto-label">Type de question</label>
            <select name="question_type" class="auto-select" id="questionType">
              <option value="rating_scale" <?= $questionType === 'rating_scale' ? 'selected' : '' ?>>Échelle de notation (1-5)</option>
              <option value="yes_no" <?= $questionType === 'yes_no' ? 'selected' : '' ?>>Oui / Non</option>
              <option value="multiple_choice" <?= $questionType === 'multiple_choice' ? 'selected' : '' ?>>Choix multiple</option>
              <option value="text_input" <?= $questionType === 'text_input' ? 'selected' : '' ?>>Texte libre</option>
              <option value="numeric" <?= $questionType === 'numeric' ? 'selected' : '' ?>>Valeur numérique</option>
              <option value="date_input" <?= $questionType === 'date_input' ? 'selected' : '' ?>>Date</option>
            </select>
            <small style="color:var(--auto-text-muted);font-size:0.65rem;">Le type détermine comment le candidat répondra à cette question</small>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-4">
              <label class="auto-label">Poids</label>
              <input type="number" name="weight" class="auto-input" step="0.5" min="0.5" max="5" value="<?= e($question['weight'] ?? '1') ?>">
            </div>
            <div class="col-4">
              <label class="auto-label">Ordre</label>
              <input type="number" name="sort_order" class="auto-input" value="<?= e($question['sort_order'] ?? '0') ?>">
            </div>
            <div class="col-4">
              <label class="auto-label">Requis</label>
              <div class="form-check form-switch" style="padding-left:2.5rem;padding-top:0.2rem;">
                <input type="hidden" name="is_required" value="0">
                <input class="form-check-input" type="checkbox" name="is_required" value="1" <?= (isset($question['is_required']) ? $question['is_required'] : 1) ? 'checked' : '' ?>>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="auto-label">Actif</label>
            <div class="form-check form-switch" style="padding-left:2.5rem;">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($question['is_active']) || $question['is_active'] ? 'checked' : '' ?>>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
          <div class="auto-form-section-title"><i class="fas fa-pen"></i>Contenu de la question</div>

          <div class="mb-3">
            <label class="auto-label">Titre (FR) *</label>
            <textarea name="title_fr" class="auto-textarea" rows="2" required><?= e($question['title_fr'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Titre (EN)</label>
            <textarea name="title" class="auto-textarea" rows="2"><?= e($question['title'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Titre (AR)</label>
            <textarea name="title_ar" class="auto-textarea" rows="2"><?= e($question['title_ar'] ?? '') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="auto-label">Texte d'aide (FR)</label>
            <textarea name="help_text_fr" class="auto-textarea" rows="2" placeholder="Sélectionnez le niveau qui correspond le mieux à votre situation..."><?= e($question['help_text_fr'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Texte d'aide (EN)</label>
            <textarea name="help_text" class="auto-textarea" rows="2"><?= e($question['help_text'] ?? '') ?></textarea>
          </div>

          <!-- Options for multiple choice -->
          <div class="mb-3" id="optionsSection" style="display:<?= in_array($questionType, ['multiple_choice', 'yes_no']) ? 'block' : 'none' ?>;">
            <label class="auto-label">Options de réponse</label>
            <div id="optionsContainer">
              <?php if (!empty($options)): ?>
                <?php foreach ($options as $i => $opt): ?>
                  <div class="input-group mb-2 option-row" style="gap:0.35rem;">
                    <input type="text" name="options[<?= $i ?>][value]" class="auto-input" style="flex:1;min-width:0;" placeholder="Valeur" value="<?= e($opt['value'] ?? '') ?>">
                    <input type="text" name="options[<?= $i ?>][label]" class="auto-input" style="flex:1;min-width:0;" placeholder="Libellé" value="<?= e($opt['label'] ?? '') ?>">
                    <button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="input-group mb-2 option-row" style="gap:0.35rem;">
                  <input type="text" name="options[0][value]" class="auto-input" style="flex:1;min-width:0;" placeholder="Valeur" value="option_1">
                  <input type="text" name="options[0][label]" class="auto-input" style="flex:1;min-width:0;" placeholder="Libellé" value="Option 1">
                  <button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button>
                </div>
                <div class="input-group mb-2 option-row" style="gap:0.35rem;">
                  <input type="text" name="options[1][value]" class="auto-input" style="flex:1;min-width:0;" placeholder="Valeur" value="option_2">
                  <input type="text" name="options[1][label]" class="auto-input" style="flex:1;min-width:0;" placeholder="Libellé" value="Option 2">
                  <button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button>
                </div>
                <div class="input-group mb-2 option-row" style="gap:0.35rem;">
                  <input type="text" name="options[2][value]" class="auto-input" style="flex:1;min-width:0;" placeholder="Valeur" value="option_3">
                  <input type="text" name="options[2][label]" class="auto-input" style="flex:1;min-width:0;" placeholder="Libellé" value="Option 3">
                  <button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button>
                </div>
              <?php endif; ?>
            </div>
            <button type="button" class="btn-action-sm" id="addOption" style="margin-top:0.35rem;"><i class="fas fa-plus me-1"></i>Ajouter une option</button>
          </div>

          <div class="mb-3">
            <label class="auto-label">Description / Instructions (FR)</label>
            <textarea name="description_fr" class="auto-textarea" rows="2"><?= e($question['description_fr'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Description / Instructions (EN)</label>
            <textarea name="description" class="auto-textarea" rows="2"><?= e($question['description'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="auto-label">Description / Instructions (AR)</label>
            <textarea name="description_ar" class="auto-textarea" rows="2"><?= e($question['description_ar'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;margin-top:1rem;display:flex;gap:0.75rem;">
        <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        <a href="/admin/questions" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
      </div>
    </form>
  </div>

  <!-- Type Preview -->
  <div class="auto-preview-box p-4 mt-4">
    <div class="preview-title"><i class="fas fa-eye me-2"></i>Aperçu du type de question</div>
    <div class="auto-preview-inner" id="typePreview">
      <?php if ($questionType === 'rating_scale'): ?>
        <div class="preview-label">Question avec échelle de notation</div>
        <div class="d-flex gap-2 mt-2">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="preview-option">
              <div class="val"><?= $i ?></div>
              <div class="lbl"><?= ['Inexistant', 'Partiel', 'Moyen', 'Avancé', 'Optimisé'][$i-1] ?></div>
            </div>
          <?php endfor; ?>
        </div>
      <?php elseif ($questionType === 'yes_no'): ?>
        <div class="preview-label">Question Oui / Non</div>
        <div class="d-flex gap-3 mt-2">
          <div class="preview-option" style="padding:0.75rem;"><i class="fas fa-check-circle" style="color:var(--auto-green);margin-right:0.35rem;"></i> Oui</div>
          <div class="preview-option" style="padding:0.75rem;"><i class="fas fa-times-circle" style="color:var(--auto-red);margin-right:0.35rem;"></i> Non</div>
        </div>
      <?php elseif ($questionType === 'multiple_choice'): ?>
        <div class="preview-label">Question à choix multiple</div>
        <?php foreach (($options ?: [['label' => 'Option 1'], ['label' => 'Option 2'], ['label' => 'Option 3']]) as $opt): ?>
          <div class="form-check mt-1">
            <input class="form-check-input" type="radio" disabled>
            <label class="form-check-label"><?= e($opt['label'] ?? $opt['value'] ?? 'Option') ?></label>
          </div>
        <?php endforeach; ?>
      <?php elseif ($questionType === 'text_input'): ?>
        <div class="preview-label">Question à réponse libre</div>
        <textarea class="auto-textarea mt-2" rows="2" placeholder="Saisissez votre réponse..." disabled style="background:rgba(0,0,0,0.3);"></textarea>
      <?php elseif ($questionType === 'numeric'): ?>
        <div class="preview-label">Question numérique</div>
        <input type="number" class="auto-input mt-2" placeholder="Ex: 42" disabled style="background:rgba(0,0,0,0.3);max-width:200px;">
      <?php elseif ($questionType === 'date_input'): ?>
        <div class="preview-label">Question date</div>
        <input type="date" class="auto-input mt-2" disabled style="background:rgba(0,0,0,0.3);max-width:200px;">
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).ready(function() {
    var optIndex = <?= json_encode(count($options)) ?>;
    if (typeof optIndex !== 'number' || isNaN(optIndex)) optIndex = 3;

    $('#questionType').on('change', function() {
        var type = $(this).val();
        $('#optionsSection').toggle(type === 'multiple_choice' || type === 'yes_no');
        var preview = '';
        switch(type) {
            case 'rating_scale':
                preview = '<div class="preview-label">Question avec échelle de notation</div><div class="d-flex gap-2 mt-2">';
                var labels = ['Inexistant', 'Partiel', 'Moyen', 'Avancé', 'Optimisé'];
                for (var i = 1; i <= 5; i++) {
                    preview += '<div class="preview-option"><div class="val">' + i + '</div><div class="lbl">' + labels[i-1] + '</div></div>';
                }
                preview += '</div>';
                break;
            case 'yes_no':
                preview = '<div class="preview-label">Question Oui / Non</div><div class="d-flex gap-3 mt-2"><div class="preview-option" style="padding:0.75rem;"><i class="fas fa-check-circle" style="color:var(--auto-green);margin-right:0.35rem;"></i> Oui</div><div class="preview-option" style="padding:0.75rem;"><i class="fas fa-times-circle" style="color:var(--auto-red);margin-right:0.35rem;"></i> Non</div></div>';
                break;
            case 'multiple_choice':
                preview = '<div class="preview-label">Question à choix multiple</div>';
                $('.option-row').each(function() {
                    var label = $(this).find('input[placeholder*="Libellé"]').val() || 'Option';
                    preview += '<div class="form-check mt-1"><input class="form-check-input" type="radio" disabled><label class="form-check-label">' + label + '</label></div>';
                });
                break;
            case 'text_input':
                preview = '<div class="preview-label">Question à réponse libre</div><textarea class="auto-textarea mt-2" rows="2" placeholder="Saisissez votre réponse..." disabled style="background:rgba(0,0,0,0.3);"></textarea>';
                break;
            case 'numeric':
                preview = '<div class="preview-label">Question numérique</div><input type="number" class="auto-input mt-2" placeholder="Ex: 42" disabled style="background:rgba(0,0,0,0.3);max-width:200px;"></div>';
                break;
            case 'date_input':
                preview = '<div class="preview-label">Question date</div><input type="date" class="auto-input mt-2" disabled style="background:rgba(0,0,0,0.3);max-width:200px;"></div>';
                break;
        }
        $('#typePreview').html(preview);
    });

    $('#addOption').on('click', function() {
        var html = '<div class="input-group mb-2 option-row" style="gap:0.35rem;">' +
            '<input type="text" name="options[' + optIndex + '][value]" class="auto-input" style="flex:1;min-width:0;" placeholder="Valeur">' +
            '<input type="text" name="options[' + optIndex + '][label]" class="auto-input" style="flex:1;min-width:0;" placeholder="Libellé">' +
            '<button type="button" class="btn-action-sm btn-action-sm-danger remove-option"><i class="fas fa-times"></i></button></div>';
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