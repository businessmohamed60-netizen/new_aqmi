<?php
$title = 'AQMI - Questionnaire Premium';
$totalQuestions = 0;
$questionsFlat = [];
$domainsFlat = [];

foreach ($domainQuestions as $dq) {
    $domainsFlat[] = [
        'id' => $dq['domain']['id'],
        'name' => $dq['domain']['name_fr'] ?: $dq['domain']['name'],
        'name_en' => $dq['domain']['name'],
        'name_ar' => $dq['domain']['name_ar'] ?? null,
        'icon' => ($dq['domain']['icon'] ?? null) ?: 'fa-industry',
        'description' => ($dq['domain']['description_fr'] ?? null) ?: ($dq['domain']['description'] ?? ''),
        'description_ar' => $dq['domain']['description_ar'] ?? null,
    ];
    foreach ($dq['questions'] as $q) {
        $answered = false;
        $answeredScore = null;
        foreach ($answers as $a) {
            if ($a['question_id'] == $q['id']) { $answered = true; $answeredScore = $a['score']; break; }
        }
        $questionsFlat[] = [
            'id' => $q['id'],
            'domain_id' => $q['domain_id'],
            'title' => $q['title_fr'] ?: $q['title'],
            'title_en' => $q['title'],
            'title_ar' => $q['title_ar'] ?? null,
            'description' => $q['description_fr'] ?: $q['description'],
            'description_ar' => $q['description_ar'] ?? null,
            'help_text' => $q['help_text_fr'] ?? $q['help_text'] ?? null,
            'help_text_ar' => $q['help_text_ar'] ?? null,
            'question_type' => $q['question_type'] ?? 'rating_scale',
            'options' => $q['options'] ?? null,
            'weight' => (float)($q['weight'] ?? 1),
            'sort_order' => $q['sort_order'] ?? 0,
            'answered' => $answered,
            'score' => $answeredScore !== null ? (int)$answeredScore : null,
        ];
        $totalQuestions++;
    }
}

// Build the data for the JS engine
$config = [
    'assessmentId' => $assessment['id'],
    'totalQuestions' => $totalQuestions,
    'questions' => $questionsFlat,
    'domains' => $domainsFlat,
    'answers' => $answers,
    'currentQuestion' => 0,
    'completionPercent' => $completionPercent,
    'lang' => $_SESSION['lang'] ?? 'fr',
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
    ],
];

ob_start();
?>
<div class="aqmi-premium" id="aqmiApp">

  <!-- Language Selection Screen -->
  <div class="aqmi-lang-screen" id="aqmiLangScreen">
    <div class="aqmi-lang-screen-inner">
      <div class="aqmi-lang-screen-icon">
        <i class="fas fa-globe"></i>
      </div>
      <h1 class="aqmi-lang-screen-title" id="aqmiLangTitle">Choisissez votre langue</h1>
      <p class="aqmi-lang-screen-desc" id="aqmiLangDesc">Sélectionnez la langue dans laquelle vous souhaitez répondre au questionnaire</p>
      <div class="aqmi-lang-choices">
        <button class="aqmi-lang-choice" data-lang="fr" type="button">
          <span class="aqmi-lang-choice-flag">FR</span>
          <span class="aqmi-lang-choice-name">Français</span>
        </button>
        <button class="aqmi-lang-choice" data-lang="en" type="button">
          <span class="aqmi-lang-choice-flag">EN</span>
          <span class="aqmi-lang-choice-name">English</span>
        </button>
        <button class="aqmi-lang-choice" data-lang="ar" type="button">
          <span class="aqmi-lang-choice-flag">AR</span>
          <span class="aqmi-lang-choice-name">العربية</span>
        </button>
      </div>
      <button class="aqmi-lang-start-btn" id="aqmiLangStartBtn" type="button" disabled>
        <span id="aqmiLangStartText">Commencer le questionnaire</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </div>
  </div>

  <!-- Top Bar -->
  <div class="aqmi-topbar">
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
  <div class="aqmi-progress-wrap">
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
  <div class="aqmi-content">
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
                <stop offset="50%" stop-color="#C9A227"/>
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

            <!-- Navigation -->
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

  <!-- Maturity Gauge -->
  <div class="aqmi-gauge" id="aqmiGauge">
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
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>