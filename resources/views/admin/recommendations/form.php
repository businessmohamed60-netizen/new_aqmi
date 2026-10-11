<?php
$title = $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation';
ob_start();
$langs = [
    'fr' => ['label' => 'Français',   'flag' => '🇫🇷', 'code' => 'FR', 'dir' => 'ltr'],
    'en' => ['label' => 'English',    'flag' => '🇬🇧', 'code' => 'EN', 'dir' => 'ltr'],
    'ar' => ['label' => 'العربية',    'flag' => '🇸🇦', 'code' => 'AR', 'dir' => 'rtl'],
    'es' => ['label' => 'Español',    'flag' => '🇪🇸', 'code' => 'ES', 'dir' => 'ltr'],
];
$priorityOptions = [
    'critical' => ['label' => 'Critical', 'color' => 'var(--auto-red)',    'icon' => 'fa-circle-exclamation'],
    'high'     => ['label' => 'High',     'color' => 'var(--auto-yellow)', 'icon' => 'fa-bolt'],
    'medium'   => ['label' => 'Medium',   'color' => 'var(--auto-cyan)',   'icon' => 'fa-arrow-right'],
    'low'      => ['label' => 'Low',      'color' => 'var(--auto-green)',  'icon' => 'fa-check'],
];
?>
<style>
.auto-form-wrap { max-width: 1100px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 1rem; margin-bottom: 0; }
.auto-form-section-title {
  color: var(--auto-text-primary); font-weight: 700; font-size: 0.8rem;
  margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--auto-border);
}
.auto-form-section-title i { color: var(--auto-cyan); margin-right: 0.5rem; }

/* Language tabs */
.auto-lang-tabs { display: flex; gap: 0.25rem; margin-bottom: 0.75rem; border-bottom: 1px solid var(--auto-border); }
.auto-lang-tab {
  padding: 0.45rem 0.9rem; font-size: 0.72rem; font-weight: 600; cursor: pointer;
  border: none; background: transparent; color: var(--auto-text-muted);
  border-bottom: 2px solid transparent; transition: var(--auto-transition);
  display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap;
}
.auto-lang-tab:hover { color: var(--auto-text-secondary); }
.auto-lang-tab.active { color: var(--auto-cyan); border-bottom-color: var(--auto-cyan); }
.auto-lang-tab .lang-flag { font-size: 0.85rem; }
.auto-lang-pane { display: none; }
.auto-lang-pane.active { display: block; animation: auto-fade-in 0.2s ease; }
.auto-lang-pane .auto-label { display: flex; align-items: center; gap: 0.35rem; }
.auto-lang-pane .auto-label .lang-flag { font-size: 0.8rem; }

/* RTL for Arabic */
.auto-lang-pane[data-lang="ar"] textarea,
.auto-lang-pane[data-lang="ar"] input { direction: rtl; text-align: right; }

.auto-field-group {
  background: var(--auto-bg-card-solid);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-sm);
  padding: 1rem;
  margin-bottom: 1rem;
}

/* Priority selector */
.auto-priority-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; }
.auto-priority-card {
  position: relative; padding: 0.65rem 0.5rem; border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-sm); cursor: pointer; transition: var(--auto-transition);
  text-align: center; background: transparent;
}
.auto-priority-card i { font-size: 0.85rem; margin-bottom: 0.25rem; display: block; }
.auto-priority-card .auto-priority-label { font-size: 0.65rem; font-weight: 600; color: var(--auto-text-secondary); }
.auto-priority-card:hover { border-color: var(--auto-cyan-glow); background: var(--auto-cyan-dim); }
.auto-priority-card.selected { border-width: 2px; }
.auto-priority-card.selected .auto-priority-label { color: var(--auto-text-primary); }

.auto-sticky-actions {
  position: sticky; bottom: 0; z-index: 10;
  background: linear-gradient(to top, var(--auto-bg-card-solid) 60%, transparent);
  padding: 1rem 0 0.5rem;
}

.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }

@media (max-width: 768px) {
  .auto-lang-tabs { overflow-x: auto; flex-wrap: nowrap; }
  .auto-lang-tab { flex-shrink: 0; }
  .auto-priority-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-lightbulb me-2" style="color:var(--auto-cyan);"></i><?= $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/recommendations/save" class="needs-validation" novalidate>
      <?= csrf_field() ?>
      <?php if ($recommendation): ?><input type="hidden" name="id" value="<?= $recommendation['id'] ?>"><?php endif; ?>

      <div class="row g-4">
        <!-- Left Column - Configuration -->
        <div class="col-lg-4">
          <div class="auto-field-group">
            <div class="auto-form-section-title"><i class="fas fa-cog"></i>Configuration</div>

            <div class="mb-3">
              <label class="auto-label">Domaine</label>
              <select name="domain_id" class="auto-select">
                <option value="">Global</option>
                <?php foreach ($domains as $d): ?>
                  <option value="<?= $d['id'] ?>" <?= ($recommendation['domain_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                    <?= e($d['name_fr'] ?: $d['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small style="color:var(--auto-text-muted);font-size:0.65rem;">Laissez « Global » pour une recommandation transverse</small>
            </div>

            <div class="mb-3">
              <label class="auto-label">Priorité</label>
              <input type="hidden" name="priority" id="priorityInput" value="<?= e($recommendation['priority'] ?? 'medium') ?>">
              <div class="auto-priority-grid">
                <?php foreach ($priorityOptions as $val => $opt): ?>
                  <div class="auto-priority-card <?= ($recommendation['priority'] ?? 'medium') === $val ? 'selected' : '' ?>"
                       data-priority="<?= $val ?>"
                       style="<?= ($recommendation['priority'] ?? 'medium') === $val ? "border-color:{$opt['color']};" : '' ?>">
                    <i class="fas <?= $opt['icon'] ?>" style="color:<?= $opt['color'] ?>"></i>
                    <div class="auto-priority-label"><?= $opt['label'] ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="auto-form-section-title mt-3"><i class="fas fa-filter"></i>Condition</div>

            <div class="mb-2">
              <label class="auto-label">Champ condition</label>
              <input type="text" name="condition_field" class="auto-input" value="<?= e($recommendation['condition_field'] ?? 'global_score') ?>" placeholder="global_score">
              <small style="color:var(--auto-text-muted);font-size:0.65rem;">Ex: global_score, domain_1, score_quality</small>
            </div>

            <div class="row g-2">
              <div class="col-6">
                <label class="auto-label">Opérateur</label>
                <select name="condition_operator" class="auto-select">
                  <?php foreach (['<' => '<', '>' => '>', '<=' => '≤', '>=' => '≥', '==' => '='] as $val => $label): ?>
                    <option value="<?= $val ?>" <?= ($recommendation['condition_operator'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6">
                <label class="auto-label">Seuil (%)</label>
                <input type="number" name="condition_value" class="auto-input" step="0.01" value="<?= e($recommendation['condition_value'] ?? '') ?>">
              </div>
            </div>

            <div class="mt-3">
              <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($recommendation['is_active']) || $recommendation['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label">Recommandation active</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Multilingual Content -->
        <div class="col-lg-8">
          <div class="auto-field-group">
            <div class="auto-form-section-title"><i class="fas fa-language"></i>Contenu multilingue</div>

            <!-- Language Tabs -->
            <div class="auto-lang-tabs" role="tablist">
              <?php foreach ($langs as $code => $lang): ?>
                <button type="button" class="auto-lang-tab <?= $code === 'fr' ? 'active' : '' ?>" data-lang="<?= $code ?>">
                  <span class="lang-flag"><?= $lang['flag'] ?></span><?= $lang['code'] ?>
                </button>
              <?php endforeach; ?>
            </div>

            <!-- Text fields per language -->
            <?php foreach ($langs as $code => $lang): ?>
              <div class="auto-lang-pane <?= $code === 'fr' ? 'active' : '' ?>" data-lang="<?= $code ?>">
                <div class="mb-0">
                  <label class="auto-label"><span class="lang-flag"><?= $lang['flag'] ?></span> Texte de la recommandation (<?= $lang['label'] ?>) <?= $code === 'fr' ? '*' : '' ?></label>
                  <textarea
                    name="recommendation_text_<?= $code === 'en' ? '' : $code ?>"
                    class="auto-textarea"
                    rows="5"
                    dir="<?= $lang['dir'] ?>"
                    <?= $code === 'fr' ? 'required' : '' ?>
                    placeholder="<?= $code === 'fr' ? 'Saisissez le texte de la recommandation...' : ($code === 'en' ? 'Enter the recommendation text...' : ($code === 'ar' ? 'أدخل نص التوصية...' : 'Ingrese el texto de la recomendación...')) ?>"
                  ><?= e($recommendation["recommendation_text_{$code}"] ?? ($code === 'en' ? ($recommendation['recommendation_text'] ?? '') : '')) ?></textarea>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="auto-sticky-actions" style="border-top:1px solid var(--auto-border);padding-top:1.25rem;margin-top:1rem;display:flex;gap:0.75rem;">
        <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        <a href="/admin/recommendations" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
      </div>
    </form>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).ready(function() {
    // Language tabs
    $('.auto-lang-tab').on('click', function() {
        var lang = $(this).data('lang');
        $('.auto-lang-tab').removeClass('active');
        $(this).addClass('active');
        $('.auto-lang-pane').removeClass('active');
        $('.auto-lang-pane[data-lang="' + lang + '"]').addClass('active');
    });

    // Priority selector
    var priorityColors = {
        'critical': 'var(--auto-red)',
        'high': 'var(--auto-yellow)',
        'medium': 'var(--auto-cyan)',
        'low': 'var(--auto-green)'
    };
    $('.auto-priority-card').on('click', function() {
        $('.auto-priority-card').removeClass('selected').css('border-width', '1px');
        $(this).addClass('selected').css('border-width', '2px');
        var val = $(this).data('priority');
        $('#priorityInput').val(val);
        $(this).css('border-color', priorityColors[val] || 'var(--auto-border)');
    });
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
