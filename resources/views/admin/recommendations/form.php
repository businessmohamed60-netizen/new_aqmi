<?php
$title = $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation';
ob_start();

$langs = [
    'fr' => ['label' => 'Français', 'flag' => '🇫🇷', 'code' => 'FR', 'dir' => 'ltr', 'color' => 'blue',  'placeholder' => 'Saisissez le texte de la recommandation…'],
    'en' => ['label' => 'English',  'flag' => '🇬🇧', 'code' => 'EN', 'dir' => 'ltr', 'color' => 'green', 'placeholder' => 'Enter the recommendation text…'],
    'ar' => ['label' => 'العربية',  'flag' => '🇸🇦', 'code' => 'AR', 'dir' => 'rtl', 'color' => 'amber', 'placeholder' => 'أدخل نص التوصية…'],
    'es' => ['label' => 'Español',  'flag' => '🇪🇸', 'code' => 'ES', 'dir' => 'ltr', 'color' => 'rose',  'placeholder' => 'Ingrese el texto de la recomendación…'],
];
$priorityOptions = [
    'critical' => ['label' => 'Critique', 'icon' => 'fa-circle-exclamation', 'hint' => 'Action immédiate'],
    'high'     => ['label' => 'Haute',    'icon' => 'fa-bolt',               'hint' => 'À traiter vite'],
    'medium'   => ['label' => 'Moyenne',  'icon' => 'fa-arrow-right',        'hint' => 'À planifier'],
    'low'      => ['label' => 'Basse',    'icon' => 'fa-check',              'hint' => 'Suggestion'],
];
$currentPriority = $recommendation['priority'] ?? 'medium';
$currentOp       = $recommendation['condition_operator'] ?? '<';

/** Valeur d'un texte par langue (EN = colonne historique recommendation_text) */
$textFor = function (string $code) use ($recommendation): string {
    if (!$recommendation) return '';
    if ($code === 'en') return $recommendation['recommendation_text_en'] ?? $recommendation['recommendation_text'] ?? '';
    return $recommendation["recommendation_text_{$code}"] ?? '';
};
?>
<style>
.rec-form-wrap {
  --rec-red-bg:#FDECEE;    --rec-red-fg:#B4233A;    --rec-red-bd:#F6C4CB;
  --rec-amber-bg:#FFF3D6;  --rec-amber-fg:#8A5A00;  --rec-amber-bd:#F5DA9B;
  --rec-blue-bg:#E6F1FB;   --rec-blue-fg:#1F5F99;   --rec-blue-bd:#BBD8F2;
  --rec-green-bg:#E3F6EB;  --rec-green-fg:#17744A;  --rec-green-bd:#B5E3C9;
  --rec-violet-bg:#EFEAFB; --rec-violet-fg:#5B3FB0; --rec-violet-bd:#D3C8F2;
  --rec-rose-bg:#FCE8F1;   --rec-rose-fg:#A8346B;   --rec-rose-bd:#F4C3DA;
  --rec-gray-bg:#EEF1F4;   --rec-gray-fg:#5B6670;   --rec-gray-bd:#D9DEE3;
  max-width:1280px;
}
.rec-form-header { margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
.rec-form-header h5 { color:var(--auto-text-primary); font-weight:700; font-size:1.1rem; margin:0; display:flex; align-items:center; gap:.5rem; }
.rec-form-header h5 i { color:var(--auto-cyan); }
.rec-back { font-size:.82rem; color:var(--auto-text-secondary); text-decoration:none; }
.rec-back:hover { color:var(--auto-cyan); }

.rec-section { background:var(--auto-bg-card-solid); border:1px solid var(--auto-border); border-radius:var(--auto-radius-sm); padding:1.1rem; margin-bottom:1rem; }
.rec-section-title { color:var(--auto-text-primary); font-weight:700; font-size:.9rem; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
.rec-section-title i { width:28px; height:28px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; }
.rec-section-title i.blue   { background:var(--rec-blue-bg);   color:var(--rec-blue-fg); }
.rec-section-title i.violet { background:var(--rec-violet-bg); color:var(--rec-violet-fg); }
.rec-section-title i.amber  { background:var(--rec-amber-bg);  color:var(--rec-amber-fg); }
.rec-help { display:block; margin-top:.3rem; color:var(--auto-text-muted); font-size:.74rem; }

/* Priorité */
.rec-priority-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.5rem; }
.rec-priority {
  text-align:left; padding:.75rem .8rem; border:1.5px solid var(--auto-border); border-radius:var(--auto-radius-sm);
  background:transparent; cursor:pointer; display:flex; gap:.55rem; align-items:center; transition:var(--auto-transition);
}
.rec-priority .ico { width:30px; height:30px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; flex-shrink:0; }
.rec-priority .lbl { font-size:.82rem; font-weight:700; color:var(--auto-text-primary); display:block; line-height:1.2; }
.rec-priority .sub { font-size:.7rem; color:var(--auto-text-muted); display:block; }
.rec-priority[data-priority="critical"] .ico { background:var(--rec-red-bg);   color:var(--rec-red-fg); }
.rec-priority[data-priority="high"] .ico     { background:var(--rec-amber-bg); color:var(--rec-amber-fg); }
.rec-priority[data-priority="medium"] .ico   { background:var(--rec-blue-bg);  color:var(--rec-blue-fg); }
.rec-priority[data-priority="low"] .ico      { background:var(--rec-green-bg); color:var(--rec-green-fg); }
.rec-priority:hover { border-color:var(--rec-gray-bd); }
.rec-priority:focus-visible { outline:2px solid var(--auto-cyan); outline-offset:2px; }
.rec-priority.selected[data-priority="critical"] { border-color:var(--rec-red-fg);   background:var(--rec-red-bg); }
.rec-priority.selected[data-priority="high"]     { border-color:var(--rec-amber-fg); background:var(--rec-amber-bg); }
.rec-priority.selected[data-priority="medium"]   { border-color:var(--rec-blue-fg);  background:var(--rec-blue-bg); }
.rec-priority.selected[data-priority="low"]      { border-color:var(--rec-green-fg); background:var(--rec-green-bg); }
.rec-priority.selected .lbl { color:#1d2733; }
.rec-priority.selected .sub { color:#52606d; }

/* Aperçu de la condition */
.rec-preview { margin-top:.9rem; padding:.7rem .8rem; border-radius:var(--auto-radius-sm); background:var(--rec-violet-bg); border:1px solid var(--rec-violet-bd); color:var(--rec-violet-fg); font-size:.82rem; line-height:1.55; }
.rec-preview b { color:#3b2a85; }
.rec-preview code { background:rgba(255,255,255,.7); color:#3b2a85; padding:.05rem .35rem; border-radius:5px; font-size:.7rem; }

/* Onglets de langue */
.rec-tabs { display:flex; gap:.4rem; margin-bottom:.9rem; flex-wrap:wrap; }
.rec-tab {
  display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .9rem; border-radius:999px; font-size:.8rem; font-weight:700; cursor:pointer;
  border:1px solid var(--rec-gray-bd); background:transparent; color:var(--auto-text-secondary); transition:var(--auto-transition);
}
.rec-tab .dot { width:8px; height:8px; border-radius:50%; background:var(--rec-gray-bd); }
.rec-tab.filled .dot { background:#3DB57C; }
.rec-tab:hover { border-color:var(--auto-cyan-glow); }
.rec-tab:focus-visible { outline:2px solid var(--auto-cyan); outline-offset:2px; }
.rec-tab.active[data-lang="fr"] { background:var(--rec-blue-bg);  color:var(--rec-blue-fg);  border-color:var(--rec-blue-bd); }
.rec-tab.active[data-lang="en"] { background:var(--rec-green-bg); color:var(--rec-green-fg); border-color:var(--rec-green-bd); }
.rec-tab.active[data-lang="ar"] { background:var(--rec-amber-bg); color:var(--rec-amber-fg); border-color:var(--rec-amber-bd); }
.rec-tab.active[data-lang="es"] { background:var(--rec-rose-bg);  color:var(--rec-rose-fg);  border-color:var(--rec-rose-bd); }

.rec-pane { display:none; }
.rec-pane.active { display:block; }
.rec-pane[data-lang="ar"] textarea { direction:rtl; text-align:right; }
.rec-pane label.auto-label { display:flex; align-items:center; gap:.35rem; }
.rec-counter { display:flex; justify-content:space-between; margin-top:.35rem; font-size:.74rem; color:var(--auto-text-muted); }
.rec-copy { border:none; background:none; padding:0; font-size:.74rem; color:var(--auto-cyan); cursor:pointer; }
.rec-copy:hover { text-decoration:underline; }

.form-switch .form-check-input { background-color:var(--auto-border); border-color:var(--auto-border); cursor:pointer; }
.form-switch .form-check-input:checked { background-color:#3DB57C; border-color:#3DB57C; }
.form-switch .form-check-label { color:var(--auto-text-secondary); font-size:.86rem; }
.rec-text-input { min-height:200px; font-size:.9rem; line-height:1.6; resize:vertical; }
.rec-meta { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; border-radius:999px; font-size:.72rem; font-weight:600; background:var(--rec-gray-bg); color:var(--rec-gray-fg); border:1px solid var(--rec-gray-bd); }
.rec-meta.green { background:var(--rec-green-bg); color:var(--rec-green-fg); border-color:var(--rec-green-bd); }

.rec-actions {
  position:sticky; bottom:0; z-index:10; display:flex; gap:.75rem; align-items:center;
  margin-top:1rem; padding:1rem 0 .5rem; border-top:1px solid var(--auto-border);
  background:linear-gradient(to top, var(--auto-bg-card-solid) 70%, transparent);
}
.rec-actions .spacer { flex:1; }
.rec-actions small { color:var(--auto-text-muted); font-size:.74rem; }

@media (min-width:576px) { .rec-priority-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:768px) { .rec-tabs { flex-wrap:nowrap; overflow-x:auto; } .rec-tab { flex-shrink:0; } }
@media (prefers-reduced-motion:reduce) { .rec-priority, .rec-tab { transition:none; } }
</style>

<div class="rec-form-wrap auto-fade-in">
  <div class="rec-form-header">
    <h5>
      <i class="fas fa-lightbulb"></i><?= $recommendation ? 'Modifier la recommandation' : 'Nouvelle recommandation' ?>
      <?php if ($recommendation): ?>
        <span class="rec-meta">#<?= (int)$recommendation['id'] ?></span>
        <span class="rec-meta <?= !empty($recommendation['is_active']) ? 'green' : '' ?>"><?= !empty($recommendation['is_active']) ? 'Active' : 'Inactive' ?></span>
      <?php endif; ?>
    </h5>
    <a href="/admin/recommendations" class="rec-back"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/recommendations/save" class="needs-validation" novalidate>
      <?= csrf_field() ?>
      <?php if ($recommendation): ?><input type="hidden" name="id" value="<?= (int)$recommendation['id'] ?>"><?php endif; ?>

      <div class="row g-4">
        <!-- Colonne gauche : configuration -->
        <div class="col-xl-4 col-lg-5">
          <div class="rec-section">
            <div class="rec-section-title"><i class="fas fa-sliders blue"></i>Configuration</div>

            <div class="mb-3">
              <label class="auto-label" for="domainSelect">Domaine</label>
              <select name="domain_id" id="domainSelect" class="auto-select">
                <option value="">Global</option>
                <?php foreach ($domains as $d): ?>
                  <option value="<?= $d['id'] ?>" <?= ($recommendation['domain_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                    <?= e($d['name_fr'] ?: $d['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="rec-help">Laissez « Global » pour une recommandation transverse.</small>
            </div>

            <div class="mb-3">
              <label class="auto-label" id="priorityLabel">Priorité</label>
              <input type="hidden" name="priority" id="priorityInput" value="<?= e($currentPriority) ?>">
              <div class="rec-priority-grid" role="radiogroup" aria-labelledby="priorityLabel">
                <?php foreach ($priorityOptions as $val => $opt): ?>
                  <button type="button" role="radio"
                          class="rec-priority <?= $currentPriority === $val ? 'selected' : '' ?>"
                          aria-checked="<?= $currentPriority === $val ? 'true' : 'false' ?>"
                          data-priority="<?= $val ?>">
                    <span class="ico"><i class="fas <?= $opt['icon'] ?>"></i></span>
                    <span><span class="lbl"><?= $opt['label'] ?></span><span class="sub"><?= $opt['hint'] ?></span></span>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="form-check form-switch">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                     <?= !isset($recommendation['is_active']) || $recommendation['is_active'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="isActive">Recommandation active</label>
            </div>
          </div>

          <div class="rec-section">
            <div class="rec-section-title"><i class="fas fa-filter violet"></i>Condition de déclenchement</div>

            <div class="mb-2">
              <label class="auto-label" for="condField">Champ</label>
              <input type="text" name="condition_field" id="condField" class="auto-input"
                     value="<?= e($recommendation['condition_field'] ?? 'global_score') ?>" placeholder="global_score">
              <small class="rec-help">Ex : global_score, domain_1, score_quality</small>
            </div>

            <div class="row g-2">
              <div class="col-6">
                <label class="auto-label" for="condOp">Opérateur</label>
                <select name="condition_operator" id="condOp" class="auto-select">
                  <?php foreach (['<' => '<  inférieur', '>' => '>  supérieur', '<=' => '≤  inf. ou égal', '>=' => '≥  sup. ou égal', '==' => '=  égal'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" data-sym="<?= e(trim(explode('  ', $label)[0])) ?>" <?= $currentOp === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6">
                <label class="auto-label" for="condValue">Seuil (%)</label>
                <input type="number" name="condition_value" id="condValue" class="auto-input" step="0.01" min="0" max="100"
                       value="<?= e($recommendation['condition_value'] ?? '') ?>">
              </div>
            </div>

            <div class="rec-preview" id="condPreview" aria-live="polite"></div>
          </div>
        </div>

        <!-- Colonne droite : contenu multilingue -->
        <div class="col-xl-8 col-lg-7">
          <div class="rec-section">
            <div class="rec-section-title"><i class="fas fa-language amber"></i>Contenu multilingue</div>

            <div class="rec-tabs" role="tablist">
              <?php foreach ($langs as $code => $lang): ?>
                <button type="button" role="tab" id="tab-<?= $code ?>"
                        class="rec-tab <?= $code === 'fr' ? 'active' : '' ?> <?= $textFor($code) !== '' ? 'filled' : '' ?>"
                        aria-selected="<?= $code === 'fr' ? 'true' : 'false' ?>"
                        aria-controls="pane-<?= $code ?>" data-lang="<?= $code ?>">
                  <span class="dot"></span><?= $lang['flag'] ?> <?= $lang['code'] ?>
                </button>
              <?php endforeach; ?>
            </div>

            <?php foreach ($langs as $code => $lang): ?>
              <div class="rec-pane <?= $code === 'fr' ? 'active' : '' ?>" role="tabpanel" id="pane-<?= $code ?>"
                   aria-labelledby="tab-<?= $code ?>" data-lang="<?= $code ?>">
                <label class="auto-label" for="text-<?= $code ?>">
                  <?= $lang['flag'] ?> Texte de la recommandation (<?= $lang['label'] ?>)<?= $code === 'fr' ? ' *' : '' ?>
                </label>
                <textarea
                  id="text-<?= $code ?>"
                  name="<?= $code === 'en' ? 'recommendation_text' : 'recommendation_text_' . $code ?>"
                  class="auto-textarea rec-text-input"
                  data-lang="<?= $code ?>"
                  rows="9"
                  dir="<?= $lang['dir'] ?>"
                  <?= $code === 'fr' ? 'required' : '' ?>
                  placeholder="<?= e($lang['placeholder']) ?>"><?= e($textFor($code)) ?></textarea>
                <div class="rec-counter">
                  <span><span class="rec-chars">0</span> caractères</span>
                  <?php if ($code !== 'fr'): ?>
                    <button type="button" class="rec-copy" data-copy-from="fr" data-target="text-<?= $code ?>">Copier le texte français</button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="rec-actions">
        <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-floppy-disk me-1"></i>Enregistrer</button>
        <a href="/admin/recommendations" class="auto-btn auto-btn-secondary"><i class="fas fa-xmark me-1"></i>Annuler</a>
        <span class="spacer"></span>
        <small>* Le français est obligatoire</small>
      </div>
    </form>
  </div>
</div>

<?php
$extraScripts = <<<'SCRIPTS'
<script>
$(document).ready(function () {
  // Onglets de langue
  function showLang(lang) {
    $('.rec-tab').removeClass('active').attr('aria-selected', 'false');
    $('.rec-tab[data-lang="' + lang + '"]').addClass('active').attr('aria-selected', 'true');
    $('.rec-pane').removeClass('active');
    $('.rec-pane[data-lang="' + lang + '"]').addClass('active');
  }
  $('.rec-tab').on('click', function () { showLang($(this).data('lang')); });

  // Si la validation bloque sur un champ caché (FR), on revient sur son onglet
  document.querySelector('form').addEventListener('invalid', function (e) {
    var l = e.target.getAttribute && e.target.getAttribute('data-lang');
    if (l) showLang(l);
  }, true);

  // Compteur de caractères + pastille « renseigné »
  function refresh($ta) {
    var v = $ta.val().trim(), lang = $ta.data('lang');
    $ta.closest('.rec-pane').find('.rec-chars').text(v.length);
    $('.rec-tab[data-lang="' + lang + '"]').toggleClass('filled', v.length > 0);
  }
  $('.rec-text-input').each(function () { refresh($(this)); })
                      .on('input', function () { refresh($(this)); });

  // Copier le texte FR comme point de départ d'une traduction
  $('.rec-copy').on('click', function () {
    var $t = $('#' + $(this).data('target'));
    if ($t.val().trim() && !confirm('Remplacer le texte existant ?')) return;
    $t.val($('#text-fr').val()).trigger('input').focus();
  });

  // Priorité
  $('.rec-priority').on('click', function () {
    $('.rec-priority').removeClass('selected').attr('aria-checked', 'false');
    $(this).addClass('selected').attr('aria-checked', 'true');
    $('#priorityInput').val($(this).data('priority'));
  });

  // Aperçu lisible de la condition
  function preview() {
    var f = $('#condField').val().trim() || '…';
    var sym = $('#condOp option:selected').data('sym');
    var v = $('#condValue').val();
    $('#condPreview').html(
      'Affichée quand <code></code> <b></b> <b></b>'
    );
    var $p = $('#condPreview');
    $p.find('code').text(f);
    $p.find('b').eq(0).text(sym);
    $p.find('b').eq(1).text(v === '' ? '?' : v + ' %');
  }
  $('#condField, #condOp, #condValue').on('input change', preview);
  preview();
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
