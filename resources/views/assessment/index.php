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
        'icon' => ($dq['domain']['icon'] ?? null) ?: 'fa-industry',
        'description' => ($dq['domain']['description_fr'] ?? null) ?: ($dq['domain']['description'] ?? ''),
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
            'description' => $q['description_fr'] ?: $q['description'],
            'help_text' => $q['help_text_fr'] ?? $q['help_text'] ?? null,
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
];

ob_start();
?>
<div class="aqmi-premium" id="aqmiApp">
  <!-- Top Bar -->
  <div class="aqmi-topbar">
    <div class="aqmi-topbar-brand">
      <span>A</span>QMI
      <span style="font-weight:400;font-size:0.7rem;color:var(--aqmi-text-tertiary);">Automotive Quality Maturity Index</span>
    </div>
    <div class="aqmi-topbar-right">
      <div class="aqmi-topbar-estimate">
        <i class="far fa-clock" style="font-size:0.65rem;"></i>
        Temps restant : <strong id="aqmiTimeRemaining">~<?= ceil($totalQuestions * 0.35) ?> min</strong>
      </div>
    </div>
  </div>

  <!-- Progress Bar -->
  <div class="aqmi-progress-wrap">
    <div class="aqmi-progress-header">
      <div class="aqmi-progress-question">
        Question <span class="num" id="aqmiCurrentNum">1</span>
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
    <!-- Left: Illustration -->
    <div class="aqmi-illustration">
      <div class="aqmi-illustration-inner">
        <div class="aqmi-illustration-glow"></div>
        <div class="aqmi-particles" id="aqmiParticles">
          <!-- Particles rendered by JS -->
        </div>
        <div class="aqmi-illustration-scene" id="aqmiScene">
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
                En savoir plus
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
                <span class="aqmi-nav-btn-text">Précédent</span>
              </button>
              <div class="aqmi-nav-spacer"></div>
              <span class="aqmi-nav-save-indicator" id="aqmiSaveIndicator">
                <i class="fas fa-check-circle"></i> Sauvegardé
              </span>
              <button class="aqmi-nav-btn primary" id="aqmiNextBtn">
                <span class="aqmi-nav-btn-text">Suivant</span>
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
    <div class="aqmi-gauge-label">Maturité</div>
  </div>

  <!-- Domain Transition Banner -->
  <div class="aqmi-domain-transition" id="aqmiDomainTransition"></div>

  <!-- Keyboard Hint -->
  <div class="aqmi-keyboard-hint" id="aqmiKeyboardHint">
    <kbd>←</kbd> <kbd>→</kbd> navigation
    <span style="margin:0 0.5rem;">·</span>
    <kbd>1</kbd> <kbd>2</kbd> <kbd>3</kbd> <kbd>4</kbd> réponses
  </div>
</div>

<?php
$configJson = json_encode($config);
$jsPath = BASE_PATH . '/public/js/aqmi-premium.js';
$jsVersion = is_file($jsPath) ? filemtime($jsPath) : time();

$extraScripts = <<<SCRIPT
<script>
var AQMI_CONFIG = {$configJson};
</script>
<script src="/js/aqmi-premium.js?v={$jsVersion}"></script>
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>