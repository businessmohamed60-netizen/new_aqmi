<?php
$title = 'AQMI - Questionnaire Premium';
$isModelSelection = isset($models) && !isset($domainQuestions);
$totalQuestions = 0;
$questionsFlat = [];
$domainsFlat = [];
$completionPercent = $completionPercent ?? 0;
$answers = $answers ?? [];
$domainQuestions = $domainQuestions ?? [];
$currentDomainIndex = $currentDomainIndex ?? 0;
$currentDomain = $currentDomain ?? null;

if (!$isModelSelection) {
foreach ($domainQuestions as $dq) {
    $domainsFlat[] = [
        'id' => $dq['domain']['id'],
        'name' => $dq['domain']['name_fr'] ?: $dq['domain']['name'],
        'name_en' => $dq['domain']['name'],
        'name_ar' => $dq['domain']['name_ar'] ?? null,
        'name_es' => $dq['domain']['name_es'] ?? null,
        'icon' => ($dq['domain']['icon'] ?? null) ?: 'fa-industry',
        'description' => ($dq['domain']['description_fr'] ?? null) ?: ($dq['domain']['description'] ?? ''),
        'description_ar' => $dq['domain']['description_ar'] ?? null,
        'description_es' => $dq['domain']['description_es'] ?? null,
    ];
    foreach ($dq['questions'] as $q) {
        $answered = false;
        $answeredScore = null;
        $answerText = null;
        $answerValue = null;
        foreach ($answers as $a) {
            if ($a['question_id'] == $q['id']) {
                $answered = true;
                $answeredScore = $a['score'];
                $answerText = $a['answer_text'] ?? null;
                $answerValue = $a['answer_value'] ?? null;
                break;
            }
        }
        $questionsFlat[] = [
            'id' => $q['id'],
            'domain_id' => $q['domain_id'],
            'title' => $q['title_fr'] ?: $q['title'],
            'title_en' => $q['title'],
            'title_ar' => $q['title_ar'] ?? null,
            'title_es' => $q['title_es'] ?? null,
            'description' => $q['description_fr'] ?: $q['description'],
            'description_ar' => $q['description_ar'] ?? null,
            'description_es' => $q['description_es'] ?? null,
            'help_text' => $q['help_text_fr'] ?? $q['help_text'] ?? null,
            'help_text_ar' => $q['help_text_ar'] ?? null,
            'help_text_es' => $q['help_text_es'] ?? null,
            'question_type' => $q['question_type'] ?? 'rating_scale',
            'options' => $q['options'] ?? null,
            'options_json' => $q['options'] ?? null,
            'weight' => (float)($q['weight'] ?? 1),
            'sort_order' => $q['sort_order'] ?? 0,
            'answered' => $answered,
            'score' => $answeredScore !== null ? (int)$answeredScore : null,
            'answer_text' => $answerText,
            'answer_value' => $answerValue,
        ];
        $totalQuestions++;
    }
}
}

// Build the data for the JS engine
$config = [
    'assessmentId' => $assessment['id'],
    'totalQuestions' => $totalQuestions,
    'questions' => $questionsFlat,
    'domains' => $domainsFlat,
    'answers' => $answers ?? [],
    'currentQuestion' => 0,
    'completionPercent' => $completionPercent ?? 0,
    'lang' => $_SESSION['lang'] ?? 'fr',
    'isModelSelection' => $isModelSelection,
    'models' => $models ?? [],
];

// i18n labels for the questionnaire UI
$i18n = [
    'fr' => [
        'choose_lang' => 'Choisissez votre langue',
        'choose_lang_desc' => 'Sélectionnez la langue dans laquelle vous souhaitez répondre au questionnaire',
        'start' => 'Commencer le questionnaire',
        'time_remaining' => 'Temps restant',
        'question' => 'Question',
        'domain' => 'Domaine',
        'maturity' => 'Maturité',
        'learn_more' => 'En savoir plus',
        'prev' => 'Précédent',
        'next' => 'Suivant',
        'saved' => 'Sauvegardé',
        'score_label' => 'Score Global',
        'rating_labels' => ['Inexistant', 'Initial', 'Basique', 'Maîtrisé', 'Performant', 'Excellence'],
        'gauge_labels' => ['Inexistant', 'Initial', 'Basique', 'Maîtrisé', 'Performant', 'Excellence'],
        'gauge_waiting' => 'En attente',
        'yes_label' => 'Oui',
        'partial_label' => 'Partiellement',
        'no_label' => 'Non',
        'na_label' => 'Non concerné',
        'yes_sub' => 'Bonne pratique totalement appliquée',
        'partial_sub' => 'Bonne pratique partiellement appliquée',
        'no_sub' => 'Bonne pratique non appliquée',
        'na_sub' => 'Cette question ne s\'applique pas',
        'text_placeholder' => 'Saisissez votre réponse...',
        'numeric_placeholder' => 'Saisissez une valeur',
        'no_options' => 'Aucune option disponible',
        'completion_title' => 'Questionnaire terminé !',
        'completion_desc' => 'Merci d\'avoir répondu à toutes les questions. Nous préparons votre diagnostic personnalisé.',
        'completion_btn' => 'Voir mes résultats',
        'domain_transition_label' => 'DOMAINE',
        'choose_model' => 'Choisissez votre modèle d\'évaluation',
        'choose_model_desc' => 'Sélectionnez le modèle qui correspond à votre contexte',
        'start_assessment' => 'Commencer l\'évaluation',
    ],
    'en' => [
        'choose_lang' => 'Choose your language',
        'choose_lang_desc' => 'Select the language in which you want to answer the questionnaire',
        'start' => 'Start questionnaire',
        'time_remaining' => 'Time remaining',
        'question' => 'Question',
        'domain' => 'Domain',
        'maturity' => 'Maturity',
        'learn_more' => 'Learn more',
        'prev' => 'Previous',
        'next' => 'Next',
        'saved' => 'Saved',
        'score_label' => 'Global Score',
        'rating_labels' => ['Non-Existent', 'Initial', 'Basic', 'Controlled', 'Performing', 'Excellence'],
        'gauge_labels' => ['Non-Existent', 'Initial', 'Basic', 'Controlled', 'Performing', 'Excellence'],
        'gauge_waiting' => 'Pending',
        'yes_label' => 'Yes',
        'partial_label' => 'Partially',
        'no_label' => 'No',
        'na_label' => 'Not applicable',
        'yes_sub' => 'Best practice fully applied',
        'partial_sub' => 'Best practice partially applied',
        'no_sub' => 'Best practice not applied',
        'na_sub' => 'This question does not apply',
        'text_placeholder' => 'Enter your answer...',
        'numeric_placeholder' => 'Enter a value',
        'no_options' => 'No options available',
        'completion_title' => 'Questionnaire completed!',
        'completion_desc' => 'Thank you for answering all the questions. We are preparing your personalized diagnostic.',
        'completion_btn' => 'See my results',
        'domain_transition_label' => 'DOMAIN',
        'choose_model' => 'Choose your evaluation model',
        'choose_model_desc' => 'Select the model that matches your context',
        'start_assessment' => 'Start assessment',
    ],
    'ar' => [
        'choose_lang' => 'اختر لغتك',
        'choose_lang_desc' => 'اختر اللغة التي تريد الإجابة بها على الاستبيان',
        'start' => 'بدء الاستبيان',
        'time_remaining' => 'الوقت المتبقي',
        'question' => 'سؤال',
        'domain' => 'المجال',
        'maturity' => 'النضج',
        'learn_more' => 'اعرف المزيد',
        'prev' => 'السابق',
        'next' => 'التالي',
        'saved' => 'تم الحفظ',
        'score_label' => 'النتيجة الإجمالية',
        'rating_labels' => ['غير موجود', 'أولي', 'أساسي', 'مسيطر عليه', 'متميز', 'امتياز'],
        'gauge_labels' => ['غير موجود', 'أولي', 'أساسي', 'مسيطر عليه', 'متميز', 'امتياز'],
        'gauge_waiting' => 'في الانتظار',
        'yes_label' => 'نعم',
        'partial_label' => 'جزئياً',
        'no_label' => 'لا',
        'na_label' => 'غير مطبق',
        'yes_sub' => 'الممارسة الجيدة مطبقة بالكامل',
        'partial_sub' => 'الممارسة الجيدة مطبقة جزئياً',
        'no_sub' => 'الممارسة الجيدة غير مطبقة',
        'na_sub' => 'هذا السؤال غير مطبق',
        'text_placeholder' => 'أدخل إجابتك...',
        'numeric_placeholder' => 'أدخل قيمة',
        'no_options' => 'لا توجد خيارات متاحة',
        'completion_title' => 'اكتمل الاستبيان!',
        'completion_desc' => 'شكراً لإجابتك على جميع الأسئلة. نحن نجهز تشخيصك المخصص.',
        'completion_btn' => 'عرض نتائجي',
        'domain_transition_label' => 'المجال',
        'choose_model' => 'اختر نموذج التقييم الخاص بك',
        'choose_model_desc' => 'اختر النموذج الذي يناسب سياقك',
        'start_assessment' => 'بدء التقييم',
    ],
    'es' => [
        'choose_lang' => 'Elija su idioma',
        'choose_lang_desc' => 'Seleccione el idioma en el que desea responder el cuestionario',
        'start' => 'Comenzar el cuestionario',
        'time_remaining' => 'Tiempo restante',
        'question' => 'Pregunta',
        'domain' => 'Dominio',
        'maturity' => 'Madurez',
        'learn_more' => 'Más información',
        'prev' => 'Anterior',
        'next' => 'Siguiente',
        'saved' => 'Guardado',
        'score_label' => 'Puntuación Global',
        'rating_labels' => ['Inexistente', 'Inicial', 'Básico', 'Controlado', 'Eficiente', 'Excelencia'],
        'gauge_labels' => ['Inexistente', 'Inicial', 'Básico', 'Controlado', 'Eficiente', 'Excelencia'],
        'gauge_waiting' => 'En espera',
        'yes_label' => 'Sí',
        'partial_label' => 'Parcialmente',
        'no_label' => 'No',
        'na_label' => 'No aplicable',
        'yes_sub' => 'Buena práctica totalmente aplicada',
        'partial_sub' => 'Buena práctica parcialmente aplicada',
        'no_sub' => 'Buena práctica no aplicada',
        'na_sub' => 'Esta pregunta no aplica',
        'text_placeholder' => 'Ingrese su respuesta...',
        'numeric_placeholder' => 'Ingrese un valor',
        'no_options' => 'No hay opciones disponibles',
        'completion_title' => '¡Cuestionario completado!',
        'completion_desc' => 'Gracias por responder todas las preguntas. Estamos preparando su diagnóstico personalizado.',
        'completion_btn' => 'Ver mis resultados',
        'domain_transition_label' => 'DOMINIO',
        'choose_model' => 'Elija su modelo de evaluación',
        'choose_model_desc' => 'Seleccione el modelo que corresponde a su contexto',
        'start_assessment' => 'Comenzar la evaluación',
    ],
];

ob_start();
?>
<div class="aqmi-premium" id="aqmiApp">

  <!-- Unified Model + Language Selection Screen -->
  <div class="aqmi-select-screen" id="aqmiSelectScreen"<?= (!$isModelSelection) ? ' style="display:none;"' : '' ?>>
    <div class="aqmi-select-screen-inner">
      <div class="aqmi-select-hero">
        <div class="aqmi-select-hero-icon">
          <i class="fas fa-clipboard-check"></i>
        </div>
        <h1 class="aqmi-select-hero-title" id="aqmiSelectTitle">Nouvelle évaluation</h1>
        <p class="aqmi-select-hero-desc" id="aqmiSelectDesc">Choisissez votre modèle d'évaluation et votre langue pour commencer</p>
      </div>

      <div class="aqmi-select-card">
      <!-- Step 1: Model Selection -->
      <div class="aqmi-select-section" id="aqmiSelectModelSection">
        <div class="aqmi-select-section-header">
          <span class="aqmi-select-step-badge">01<i class="fas fa-clipboard-list"></i></span>
          <h2 class="aqmi-select-section-title" id="aqmiSelectModelTitle">Choisissez votre modèle d'évaluation</h2>
          <p class="aqmi-select-section-sub" id="aqmiSelectModelDesc">Sélectionnez le modèle qui correspond à votre contexte</p>
        </div>
        <div class="aqmi-select-model-grid" id="aqmiModelChoices">
          <?php foreach (($config['models'] ?? []) as $m): ?>
            <button class="aqmi-select-model-card" data-model="<?= (int)$m['id'] ?>" type="button">
              <span class="aqmi-select-model-icon" style="background:<?= e($m['color'] ?: '#1F6FEB') ?>1a;color:<?= e($m['color'] ?: '#1F6FEB') ?>">
                <i class="fas <?= e($m['icon'] ?: 'fa-clipboard-check') ?>"></i>
              </span>
              <div class="aqmi-select-model-info">
                <span class="aqmi-select-model-name"><?= e($m['name_fr'] ?: $m['name']) ?></span>
                <?php if (!empty($m['description_fr']) || !empty($m['description'])): ?>
                  <span class="aqmi-select-model-desc"><?= e($m['description_fr'] ?: $m['description']) ?></span>
                <?php endif; ?>
              </div>
              <span class="aqmi-select-model-check"><i class="fas fa-check-circle"></i></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Step 2: Language Selection -->
      <div class="aqmi-select-section" id="aqmiSelectLangSection">
        <div class="aqmi-select-section-header">
          <span class="aqmi-select-step-badge">02<i class="fas fa-language"></i></span>
          <h2 class="aqmi-select-section-title" id="aqmiSelectLangTitle">Choisissez votre langue</h2>
          <p class="aqmi-select-section-sub" id="aqmiSelectLangDesc">Sélectionnez la langue dans laquelle vous souhaitez répondre</p>
        </div>
        <div class="aqmi-select-lang-row">
          <button class="aqmi-select-lang-card" data-lang="fr" type="button">
            <span class="aqmi-select-lang-flag">FR</span>
            <span class="aqmi-select-lang-name">Français</span>
          </button>
          <button class="aqmi-select-lang-card" data-lang="en" type="button">
            <span class="aqmi-select-lang-flag">EN</span>
            <span class="aqmi-select-lang-name">English</span>
          </button>
          <button class="aqmi-select-lang-card" data-lang="ar" type="button">
            <span class="aqmi-select-lang-flag">AR</span>
            <span class="aqmi-select-lang-name">العربية</span>
          </button>
          <button class="aqmi-select-lang-card" data-lang="es" type="button">
            <span class="aqmi-select-lang-flag">ES</span>
            <span class="aqmi-select-lang-name">Español</span>
          </button>
        </div>
      </div>

      <!-- Start Button (Footer) -->
      <div class="aqmi-select-card-footer">
        <button class="aqmi-select-start-btn" id="aqmiSelectStartBtn" type="button" disabled>
          <span id="aqmiSelectStartText">Commencer l'évaluation</span>
          <i class="fas fa-arrow-right"></i>
        </button>
      </div>
      </div><!-- /.aqmi-select-card -->
    </div>
  </div>

  <!-- Top Bar -->
  <div class="aqmi-topbar"<?= $isModelSelection ? ' style="display:none;"' : '' ?> id="aqmiTopbar">
    <div class="aqmi-topbar-brand">
      <div class="aqmi-brand-mark" id="aqmiBrandMark"><span>A</span><span>Q</span><span>M</span><span>I</span></div>
      <div class="aqmi-brand-copy">
        <strong>Automotive Quality</strong>
        <small>Maturity Index</small>
      </div>
    </div>
    <div class="aqmi-topbar-right">
      <div class="aqmi-topbar-estimate">
        <i class="far fa-clock" style="font-size:0.65rem;"></i>
        <span data-i18n="time_remaining">Temps restant</span> : <strong id="aqmiTimeRemaining">~<?= ceil($totalQuestions * 0.35) ?> min</strong>
      </div>
    </div>
  </div>

  <!-- Progress Bar -->
  <div class="aqmi-progress-wrap"<?= $isModelSelection ? ' style="display:none;"' : '' ?> id="aqmiProgressWrap">
    <div class="aqmi-progress-header">
      <div class="aqmi-progress-question">
        <span data-i18n="question">Question</span> <span class="num" id="aqmiCurrentNum">1</span>
        <span style="color:var(--aqmi-text-tertiary);font-weight:400;">/ <?= $totalQuestions ?></span>
        <span class="aqmi-progress-domain" id="aqmiDomainLabel"><?= e($domainsFlat[0]['name'] ?? '') ?></span>
      </div>
      <div class="aqmi-progress-percent" id="aqmiPercentLabel"><?= $completionPercent ?>%</div>
    </div>
    <div class="aqmi-progress-track">
      <div class="aqmi-progress-fill" id="aqmiProgressFill" style="width:<?= $completionPercent ?>%"></div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="aqmi-content"<?= $isModelSelection ? ' style="display:none;"' : '' ?> id="aqmiContent">
    <!-- Left: Maturity Gauge -->
    <div class="aqmi-illustration">
      <div class="aqmi-illustration-inner">
        <div class="aqmi-illustration-glow"></div>
        <div class="aqmi-particles" id="aqmiParticles">
          <!-- Particles rendered by JS -->
        </div>
        <div class="aqmi-main-gauge" id="aqmiMainGauge">
          <svg viewBox="0 0 200 200">
            <defs>
              <linearGradient id="aqmiGaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#E5484D"/>
                <stop offset="25%" stop-color="#E8823A"/>
                <stop offset="50%" stop-color="#9d8fd1"/>
                <stop offset="75%" stop-color="#1F6FEB"/>
                <stop offset="100%" stop-color="#2EC4B6"/>
              </linearGradient>
            </defs>
            <circle class="aqmi-main-gauge-bg" cx="100" cy="100" r="85"/>
            <circle class="aqmi-main-gauge-fg" id="aqmiMainGaugeCircle" cx="100" cy="100" r="85"/>
          </svg>
          <div class="aqmi-main-gauge-center">
            <div class="aqmi-main-gauge-icon" id="aqmiMainGaugeIcon"><i class="fas fa-gauge-high"></i></div>
            <div class="aqmi-main-gauge-value" id="aqmiMainGaugeValue">0%</div>
            <div class="aqmi-main-gauge-label" id="aqmiMainGaugeLabel" data-i18n="maturity">Maturité</div>
          </div>
          <div class="aqmi-main-gauge-pulse" id="aqmiMainGaugePulse" style="display:none;"></div>
        </div>
        <div class="aqmi-illustration-scene" id="aqmiScene" style="display:none;">
          <!-- Rendered by JS -->
        </div>
      </div>
    </div>

    <!-- Right: Question -->
    <div class="aqmi-question-panel" id="aqmiQuestionPanel">
      <div class="aqmi-question">
        <div class="aqmi-question-domain-label" id="aqmiDomainBadge">
          <i class="fas <?= e($domainsFlat[0]['icon'] ?? 'fa-industry') ?>" id="aqmiDomainIcon"></i>
          <span id="aqmiDomainBadgeText"><?= e($domainsFlat[0]['name'] ?? '') ?></span>
        </div>
        <div class="aqmi-question-number" id="aqmiQuestionNumber">QUESTION 1</div>

        <div class="aqmi-question-stage" id="aqmiQuestionStage">
          <div class="aqmi-question-inner active" id="aqmiQuestionInner">
            <h2 class="aqmi-question-title" id="aqmiQuestionTitle"></h2>
            <div class="aqmi-question-desc" id="aqmiQuestionDesc"></div>

            <!-- Learn More -->
            <div class="aqmi-learn-more" id="aqmiLearnMore" style="display:none;">
              <button class="aqmi-learn-more-btn" id="aqmiLearnMoreBtn" type="button">
                <i class="fas fa-info-circle" style="font-size:0.7rem;"></i>
                <span data-i18n="learn_more">En savoir plus</span>
                <span class="icon"><i class="fas fa-chevron-down" style="font-size:0.55rem;"></i></span>
              </button>
              <div class="aqmi-learn-more-content" id="aqmiLearnMoreContent">
                <div class="aqmi-learn-more-body" id="aqmiLearnMoreBody"></div>
              </div>
            </div>

            <!-- Answers (yes_no / multiple_choice) -->
            <div class="aqmi-answers" id="aqmiAnswers"></div>

            <!-- Rating Scale -->
            <div class="aqmi-rating-grid" id="aqmiRatingGrid" style="display:none;"></div>

          </div>

          <!-- Sticky Navigation Footer -->
          <div class="aqmi-question-footer">
            <div class="aqmi-nav">
              <button class="aqmi-nav-btn" id="aqmiPrevBtn">
                <i class="fas fa-arrow-left"></i>
                <span class="aqmi-nav-btn-text" data-i18n="prev">Précédent</span>
              </button>
              <div class="aqmi-nav-spacer"></div>
              <span class="aqmi-nav-save-indicator" id="aqmiSaveIndicator">
                <i class="fas fa-check-circle"></i> <span data-i18n="saved">Sauvegardé</span>
              </span>
              <button class="aqmi-nav-btn primary" id="aqmiNextBtn">
                <span class="aqmi-nav-btn-text" data-i18n="next">Suivant</span>
                <i class="fas fa-arrow-right"></i>
              </button>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Maturity Gauge -->
  <div class="aqmi-gauge" id="aqmiGauge"<?= $isModelSelection ? ' style="display:none;"' : '' ?>>
    <div class="aqmi-gauge-ring">
      <svg viewBox="0 0 40 40">
        <circle class="bg" cx="20" cy="20" r="17"/>
        <circle class="fg" id="aqmiGaugeCircle" cx="20" cy="20" r="17"/>
      </svg>
      <div class="aqmi-gauge-value" id="aqmiGaugeValue">0%</div>
      <div class="aqmi-gauge-pulse" id="aqmiGaugePulse" style="display:none;"></div>
    </div>
    <div class="aqmi-gauge-label" data-i18n="score_label">Score Global</div>
  </div>

  <!-- Domain Transition Banner -->
  <div class="aqmi-domain-transition" id="aqmiDomainTransition"></div>

</div>

<?php
$configJson = json_encode($config);
$i18nJson = json_encode($i18n);
$jsPath = BASE_PATH . '/public/js/aqmi-premium.js';
$jsVersion = is_file($jsPath) ? filemtime($jsPath) : time();

$extraScripts = <<<SCRIPT
<script>
var AQMI_CONFIG = {$configJson};
var AQMI_I18N = {$i18nJson};
</script>
<script src="/js/aqmi-premium.js?v={$jsVersion}"></script>
<script>
// Filet de sécurité : garantit la bascule select-screen -> questionnaire
// même si aqmi-premium.js ne gère pas (encore) ces éléments.
document.addEventListener('DOMContentLoaded', function () {
  var startBtn = document.getElementById('aqmiSelectStartBtn');
  if (!startBtn) return;
  startBtn.addEventListener('click', function () {
    if (startBtn.disabled) return;
    var selectScreen = document.getElementById('aqmiSelectScreen');
    var topbar = document.getElementById('aqmiTopbar');
    var progressWrap = document.getElementById('aqmiProgressWrap');
    var content = document.getElementById('aqmiContent');
    var gauge = document.getElementById('aqmiGauge');
    if (selectScreen) selectScreen.style.display = 'none';
    if (topbar) topbar.style.display = '';
    if (progressWrap) progressWrap.style.display = '';
    if (content) content.style.display = '';
    if (gauge) gauge.style.display = '';
  });
});
</script>
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>