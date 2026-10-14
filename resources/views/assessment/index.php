<?php
$user = \App\Helpers\Auth::user();
$activeSection = 'assessments';
$pageTitle = __('dashboard.nav.assessments') ?: 'Évaluations';
$pageSubtitle = __('assessment.title');
$title = 'AQMI - ' . __('assessment.title');
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
        'name' => ($dq['domain']['name_fr'] ?? null) ?: $dq['domain']['name'],
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
            'title' => ($q['title_fr'] ?? null) ?: $q['title'],
            'title_en' => $q['title'],
            'title_ar' => $q['title_ar'] ?? null,
            'title_es' => $q['title_es'] ?? null,
            'description' => ($q['description_fr'] ?? null) ?: ($q['description'] ?? ''),
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
        'elapsed_time' => 'Temps écoulé',
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
        'elapsed_time' => 'Elapsed time',
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
        'elapsed_time' => 'الوقت المنقضي',
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
        'elapsed_time' => 'Tiempo transcurrido',
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

$premiumCssPath = BASE_PATH . '/public/css/aqmi-premium.css';
$premiumCssVersion = is_file($premiumCssPath) ? filemtime($premiumCssPath) : time();
$extraStyles = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">'
    . '<link rel="stylesheet" href="/css/aqmi-premium.css?v=' . $premiumCssVersion . '">'
    . '<link rel="preconnect" href="https://fonts.googleapis.com">'
    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">'
    . '<style>'
    . '.aqmi-premium { --aqmi-font: "Inter", "Manrope", sans-serif; }'
    . '.ud-shell { overflow: visible; }'
    . '.ud-content { max-width: 100% !important; padding: 0 !important; margin: 0 !important; width: 100% !important; }'
    . '.ud-page-title-bar { display: none; }'
    // Override fixed positioning: embed inside dashboard, below the 60px sticky topbar
    . '.aqmi-premium {'
    . '  position: fixed !important;'
    . '  top: 60px !important;'
    . '  left: 0 !important;'
    . '  right: 0 !important;'
    . '  bottom: 0 !important;'
    . '  z-index: 10 !important;'
    . '  height: auto !important;'
    . '  overflow-y: auto;'
    . '  overflow-x: hidden;'
    . '}'
    // Hide the AQMI-specific topbar (dashboard already provides navigation)
    . '.aqmi-topbar { display: none !important; }'
    // Adjust progress wrap padding since topbar is hidden
    . '.aqmi-progress-wrap { padding-top: 1rem; }'
    // Select screen: absolute within .aqmi-premium instead of fixed to viewport
    . '.aqmi-select-screen { position: absolute !important; inset: 0 !important; z-index: 50 !important; overflow-y: auto; }'
    // Floating gauge: absolute within .aqmi-premium
    . '.aqmi-gauge { position: absolute !important; }'
    // Domain transition: absolute within container
    . '.aqmi-domain-transition { position: absolute !important; }'
    // Mobile: leave room for bottom nav (60px topbar + ~60px bottom nav)
    . '@media (max-width: 768px) {'
    . '.aqmi-premium { bottom: 60px !important; }'
    . '.ud-content { padding-bottom: 0 !important; }'
    . '}'
    // ===== SELECT v3 (choix langue + modèle) : supprimer ce bloc pour revenir à l'ancien rendu =====
    . '.aqmi-select-screen-inner { max-width: 1000px; margin: 0 auto; padding: 2rem 1.5rem 7rem; box-sizing: border-box; }'
    . '.aqmi-select-hero { text-align: center; margin-bottom: 1.5rem; }'
    . '.aqmi-select-card { display: flex; flex-direction: column; gap: 1.75rem; }'
    . '.aqmi-select-divider { display: none; }'
    . '.aqmi-select-section-header { display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem 0.75rem; margin-bottom: 1rem; }'
    . '.aqmi-select-section-sub { flex-basis: 100%; margin: 0; }'
    . '.aqmi-select-lang-row { display: flex; flex-wrap: wrap; gap: 0.6rem; }'
    . '.aqmi-select-lang-card { flex: 1 1 130px; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 0.6rem; padding: 0.65rem 1rem; }'
    . '.aqmi-select-model-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }'
    . '.aqmi-select-model-card { display: flex; align-items: flex-start; gap: 1rem; text-align: left; width: 100%; min-height: 96px; }'
    . '.aqmi-select-model-info { flex: 1 1 auto; min-width: 0; }'
    . '.aqmi-select-model-check { margin-left: auto; opacity: 0; transition: opacity 0.2s ease; }'
    . '.aqmi-select-model-card.selected .aqmi-select-model-check { opacity: 1; }'
    . '.aqmi-select-card-footer { position: sticky; bottom: 0; display: flex; justify-content: flex-end; padding: 1rem 0; z-index: 5; }'
    . '@media (max-width: 640px) {'
    . '.aqmi-select-screen-inner { padding: 1.25rem 1rem 7rem; }'
    . '.aqmi-select-model-grid { grid-template-columns: 1fr; }'
    . '.aqmi-select-start-btn { width: 100%; }'
    . '}'
    // ===== FIN SELECT v3 =====
    // ===== QUESTION v4 (design moderne) : supprimer ce bloc pour revenir a l ancien rendu =====
    . '.aqmi-premium { --q-bg:#f3f6fb; --q-card:#ffffff; --q-ink:#0f172a; --q-muted:#64748b; --q-line:rgba(15,23,42,0.09); --q-accent:var(--aqmi-accent,#1F6FEB); --q-radius:22px; --q-shadow:0 1px 2px rgba(15,23,42,0.04), 0 18px 40px -18px rgba(15,23,42,0.22); background: radial-gradient(900px 420px at 90% -8%, rgba(31,111,235,0.12), transparent 62%), radial-gradient(700px 380px at -5% 105%, rgba(46,196,182,0.10), transparent 60%), var(--q-bg); color: var(--q-ink); }'
    . '.aqmi-progress-wrap { max-width: none; width: 100%; margin: 0; padding: 0.55rem 1.25rem 0.25rem; box-sizing: border-box; }'
    . '.aqmi-progress-question { font-weight: 600; color: var(--q-ink); }'
    . '.aqmi-progress-domain { display: inline-block; margin-left: 0.7rem; padding: 0.22rem 0.75rem; border-radius: 999px; background: rgba(31,111,235,0.10); color: var(--q-accent); font-size: 0.78rem; font-weight: 700; }'
    . '.aqmi-progress-percent { font-size: 1.15rem; font-weight: 800; color: var(--q-accent); }'
    . '.aqmi-progress-track { height: 8px; border-radius: 999px; background: rgba(15,23,42,0.08); overflow: hidden; }'
    . '.aqmi-progress-fill { border-radius: 999px; background: linear-gradient(90deg, var(--q-accent), #2EC4B6); }'
    . '.aqmi-content:not([style*="none"]) { display: grid !important; grid-template-columns: minmax(0, 1fr); gap: 0.9rem; align-items: start; max-width: none; width: 100%; margin: 0; padding: 0.4rem 1.25rem 5rem; box-sizing: border-box; }'
    . '.aqmi-question-panel { order: 1; min-width: 0; max-width: none; }'
    . '.aqmi-question { background: var(--q-card); border: 1px solid var(--q-line); border-radius: var(--q-radius); box-shadow: var(--q-shadow); padding: 1.5rem 2rem 1.2rem; width: 100%; box-sizing: border-box; }'
    . '.aqmi-question-domain-label { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.85rem; border-radius: 999px; background: rgba(31,111,235,0.09); color: var(--q-accent); font-size: 0.78rem; font-weight: 700; letter-spacing: 0.03em; }'
    . '.aqmi-question-number { margin: 0.9rem 0 0.35rem; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.14em; color: var(--q-muted); }'
    . '.aqmi-question-title { margin: 0 0 0.6rem; font-size: clamp(1.4rem, 2.2vw, 1.95rem); line-height: 1.28; font-weight: 800; letter-spacing: -0.015em; color: var(--q-ink); }'
    . '.aqmi-question-desc { max-width: 120ch; font-size: 1rem; line-height: 1.6; color: var(--q-muted); margin-bottom: 1.25rem; }'
    . '.aqmi-learn-more-btn { border-radius: 999px; }'
    . '.aqmi-answers:not([style*="none"]) { display: grid; gap: 0.75rem; }'
    . '.aqmi-answers[data-qtype="yes_no"]:not([style*="none"]) { grid-template-columns: repeat(2, minmax(0, 1fr)); }'
    . '.aqmi-rating-grid:not([style*="none"]) { display: grid !important; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; }'
    . '.aqmi-answer-card, .aqmi-rating-card { position: relative; overflow: hidden; display: flex; align-items: center; gap: 1rem; padding: 1.05rem 1.2rem; border-radius: 16px; background: #fff; border: 1.5px solid var(--q-line); box-shadow: 0 1px 2px rgba(15,23,42,0.04); cursor: pointer; transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease; }'
    . '.aqmi-answer-card:hover, .aqmi-rating-card:hover { border-color: var(--q-accent); box-shadow: 0 8px 22px -10px rgba(31,111,235,0.35); }'
    . '.aqmi-answer-icon { flex: 0 0 auto; width: 42px; height: 42px; display: grid; place-items: center; border-radius: 12px; background: rgba(15,23,42,0.05); font-weight: 800; color: var(--q-ink); }'
    . '.aqmi-answer-card[data-cls="yes"] .aqmi-answer-icon { background: rgba(46,196,182,0.16); color: #139488; }'
    . '.aqmi-answer-card[data-cls="partial"] .aqmi-answer-icon { background: rgba(232,130,58,0.16); color: #c4631a; }'
    . '.aqmi-answer-card[data-cls="no"] .aqmi-answer-icon { background: rgba(229,72,77,0.14); color: #d0343a; }'
    . '.aqmi-answer-label { font-weight: 700; font-size: 1rem; color: var(--q-ink); }'
    . '.aqmi-answer-sublabel { margin-top: 0.15rem; font-weight: 400; font-size: 0.8rem; color: var(--q-muted); }'
    . '.aqmi-rating-card { flex-direction: column; justify-content: center; gap: 0.25rem; text-align: center; padding: 1.1rem 0.5rem; }'
    . '.aqmi-rating-score { font-size: 1.9rem; font-weight: 800; line-height: 1; }'
    . '.aqmi-rating-label { font-size: 0.82rem; font-weight: 600; color: var(--q-muted); }'
    . '.aqmi-answer-card.selected, .aqmi-rating-card.selected { border-color: var(--q-accent); background: rgba(31,111,235,0.07); box-shadow: 0 0 0 3px rgba(31,111,235,0.16); }'
    . '.aqmi-answer-card.selected-yes { border-color: #2EC4B6; background: rgba(46,196,182,0.10); box-shadow: 0 0 0 3px rgba(46,196,182,0.20); }'
    . '.aqmi-answer-card.selected-partial { border-color: #E8823A; background: rgba(232,130,58,0.10); box-shadow: 0 0 0 3px rgba(232,130,58,0.20); }'
    . '.aqmi-answer-card.selected-no { border-color: #E5484D; background: rgba(229,72,77,0.09); box-shadow: 0 0 0 3px rgba(229,72,77,0.18); }'
    . '.aqmi-answer-card.selected-na { border-color: #94a3b8; background: rgba(148,163,184,0.14); box-shadow: 0 0 0 3px rgba(148,163,184,0.22); }'
    . '.aqmi-text-input, .aqmi-numeric-input, .aqmi-date-input { width: 100%; box-sizing: border-box; padding: 0.95rem 1.05rem; border-radius: 14px; border: 1.5px solid var(--q-line); background: #fff; color: var(--q-ink); font: inherit; transition: border-color 0.18s ease, box-shadow 0.18s ease; }'
    . '.aqmi-text-input:focus, .aqmi-numeric-input:focus, .aqmi-date-input:focus { outline: none; border-color: var(--q-accent); box-shadow: 0 0 0 4px rgba(31,111,235,0.14); }'
    . '.aqmi-question-footer { position: sticky; bottom: 0; margin-top: 1.25rem; padding-top: 1rem; background: linear-gradient(180deg, rgba(255,255,255,0), var(--q-card) 35%); }'
    . '.aqmi-nav-btn { display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.8rem 1.35rem; border-radius: 14px; font-weight: 700; border: 1.5px solid var(--q-line); background: #fff; color: var(--q-ink); cursor: pointer; transition: box-shadow 0.18s ease, border-color 0.18s ease; }'
    . '.aqmi-nav-btn:hover { border-color: var(--q-accent); }'
    . '.aqmi-nav-btn.primary { background: linear-gradient(135deg, var(--q-accent), #3b82f6); border-color: transparent; color: #fff; box-shadow: 0 10px 24px -10px rgba(31,111,235,0.7); }'
    . '.aqmi-completion-time { margin: 0.25rem 0 1rem; font-size: 1rem; font-weight: 700; opacity: 0.85; font-variant-numeric: tabular-nums; }'
    . '@media (max-width: 900px) {'
    . '.aqmi-content:not([style*="none"]) { grid-template-columns: 1fr; gap: 1rem; padding: 0.5rem 1rem 6rem; }'
    . '.aqmi-question { padding: 1.4rem 1.2rem 1rem; }'
    . '.aqmi-answers[data-qtype="yes_no"]:not([style*="none"]) { grid-template-columns: 1fr; }'
    . '.aqmi-rating-grid:not([style*="none"]) { grid-template-columns: repeat(2, minmax(0, 1fr)); }'
    . '}'
    . '.aqmi-question, .aqmi-question-stage, .aqmi-question-inner, .aqmi-answers, .aqmi-rating-grid { max-width: none !important; width: 100%; box-sizing: border-box; }'
    . '@media (min-width: 1100px) {'
    . '.aqmi-rating-grid:not([style*="none"]) { grid-template-columns: repeat(6, minmax(0, 1fr)); }'
    . '.aqmi-answers[data-qtype="yes_no"]:not([style*="none"]) { grid-template-columns: repeat(4, minmax(0, 1fr)); }'
    . '}'
    . '.aqmi-question-panel { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; box-sizing: border-box; }'
    . '.aqmi-question { margin: 0 !important; }'
    // ===== FIN QUESTION v4 =====
    // ===== HUD v6 (barre horloge + score + maturite au-dessus de la question) : supprimer ce bloc pour revenir a l ancien rendu =====
    . '@keyframes aqmi-gauge-ping { 0% { transform: scale(0.92); opacity: 0.55; } 100% { transform: scale(1.22); opacity: 0; } }'
    . '.aqmi-hud { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); align-items: stretch; gap: 1rem; width: 100%; margin-bottom: 0; }'
    . '.hud-card { display: flex; align-items: center; justify-content: center; min-height: 116px; gap: 1.1rem; min-width: 0; box-sizing: border-box; padding: 0.9rem 1.2rem; background: var(--q-card); border: 1px solid var(--q-line); border-radius: var(--q-radius); box-shadow: var(--q-shadow); color: var(--q-ink); }'
    . '.hud-label { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; line-height: 1.3; color: var(--q-muted); }'
    . '.hud-meta { display: flex; flex-direction: column; gap: 0.35rem; min-width: 0; }'
    . '.hud-level { display: flex; align-items: center; gap: 0.45rem; min-width: 0; font-size: 1.02rem; font-weight: 800; color: var(--q-ink); }'
    . '#aqmiMainGaugeLabel { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }'
    . '.hud-level-icon { flex: 0 0 auto; display: inline-flex; font-size: 0.95rem; }'
    . '.hud-ring { position: relative; flex: 0 0 auto; width: 92px; height: 92px; container-type: size; }'
    . '.hud-score .hud-ring { width: 80px; height: 80px; }'
    . '.hud-ring svg { position: absolute; inset: 0; width: 100%; height: 100%; display: block; overflow: visible; }'
    . '.hud-ring-value { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-size: 22cqw; font-weight: 800; line-height: 1; font-variant-numeric: tabular-nums; white-space: nowrap; color: var(--q-ink); }'
    . '.hud-pulse { position: absolute; inset: 0; border-radius: 50%; border: 2px solid; pointer-events: none; }'
    . '.hud-maturity .hud-ring svg .fg { filter: drop-shadow(0 0 3px var(--gauge-color, transparent)); }'
    . '.hud-score:not(.show) .hud-ring { opacity: 0.55; }'
    . '.hud-chrono { background: linear-gradient(145deg, #0b1220 0%, #16233b 100%); border-color: rgba(255,255,255,0.06); box-shadow: 0 18px 40px -16px rgba(11,18,32,0.7); color: #fff; transition: opacity 0.3s ease; }'
    . '.hud-chrono .hud-label { color: #94a3b8; }'
    . '.hud-chrono-face { flex: 0 0 auto; width: 86px; height: 86px; border-radius: 50%; background: radial-gradient(circle at 50% 35%, rgba(255,255,255,0.10), rgba(255,255,255,0.02) 70%); box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08), 0 0 26px rgba(46,196,182,0.20); }'
    . '.hud-chrono-face svg { width: 100%; height: 100%; display: block; }'
    . '.hud-chrono-readout { display: flex; flex-direction: column; gap: 0.35rem; min-width: 0; }'
    . '.hud-chrono-time { font-size: 2.7rem; font-weight: 800; line-height: 1; letter-spacing: 0.02em; font-variant-numeric: tabular-nums; color: #ffffff; white-space: nowrap; }'
    . '.hud-chrono.is-paused { opacity: 0.55; }'
    . '@media (max-width: 1100px) and (min-width: 901px) {'
    . '.hud-chrono-face { width: 68px; height: 68px; }'
    . '.hud-chrono-time { font-size: 2.1rem; }'
    . '.hud-card { gap: 0.8rem; padding: 0.8rem 0.9rem; }'
    . '.hud-ring, .hud-score .hud-ring { width: 72px; height: 72px; }'
    . '}'
    . '@media (max-width: 900px) {'
    . '.aqmi-hud { grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.9rem; }'
    . '.hud-chrono { grid-column: 1 / -1; justify-content: center; padding: 0.65rem 1rem; }'
    . '.hud-chrono-face { width: 56px; height: 56px; }'
    . '.hud-chrono-time { font-size: 2.1rem; }'
    . '.hud-card { padding: 0.65rem 0.85rem; gap: 0.7rem; min-height: 0; }'
    . '.hud-ring, .hud-score .hud-ring { width: 60px; height: 60px; }'
    . '.hud-level { font-size: 0.88rem; }'
    . '}'
    . '@media (max-width: 480px) {'
    . '.hud-ring, .hud-score .hud-ring { width: 52px; height: 52px; }'
    . '.hud-label { font-size: 0.6rem; letter-spacing: 0.08em; }'
    . '}'
    // ===== FIN HUD v6 =====
    . '</style>';

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
      <!-- Step 1: Language Selection -->
      <div class="aqmi-select-section" id="aqmiSelectLangSection">
        <div class="aqmi-select-section-header">
          <span class="aqmi-select-step-badge">01<i class="fas fa-language"></i></span>
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

      <div class="aqmi-select-divider"><span></span></div>

      <!-- Step 2: Model Selection -->
      <div class="aqmi-select-section" id="aqmiSelectModelSection">
        <div class="aqmi-select-section-header">
          <span class="aqmi-select-step-badge">02<i class="fas fa-clipboard-list"></i></span>
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

      <!-- Start Button (Footer) -->
      <div class="aqmi-select-card-footer">
        <button class="aqmi-select-start-btn" id="aqmiSelectStartBtn" type="button" disabled>
          <span class="aqmi-select-start-btn-content">
            <span id="aqmiSelectStartText">Commencer l'évaluation</span>
            <i class="fas fa-arrow-right"></i>
          </span>
          <span class="aqmi-select-start-btn-spinner" style="display:none;">
            <i class="fas fa-circle-notch fa-spin"></i>
          </span>
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
    <!-- HUD : horloge + score global + maturite (au-dessus de la question) -->
    <div class="aqmi-hud" id="aqmiHud">
      <div class="hud-card hud-chrono" id="aqmiChrono" role="timer" aria-live="off">
        <div class="hud-chrono-face">
          <svg viewBox="0 0 120 120" aria-hidden="true">
            <circle cx="60" cy="60" r="55" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
            <?php for ($i = 0; $i < 12; $i++): ?>
            <line x1="60" y1="<?= $i % 3 === 0 ? 9 : 12 ?>" x2="60" y2="18" stroke="rgba(255,255,255,0.6)" stroke-width="<?= $i % 3 === 0 ? 3 : 1.6 ?>" stroke-linecap="round" transform="rotate(<?= $i * 30 ?> 60 60)"/>
            <?php endfor; ?>
            <line id="aqmiChronoMin" x1="60" y1="60" x2="60" y2="30" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round"/>
            <line id="aqmiChronoSec" x1="60" y1="68" x2="60" y2="16" stroke="#2EC4B6" stroke-width="2.2" stroke-linecap="round"/>
            <circle cx="60" cy="60" r="4.5" fill="#2EC4B6"/>
          </svg>
        </div>
        <div class="hud-chrono-readout">
          <span class="hud-label" data-i18n="elapsed_time">Temps écoulé</span>
          <span class="hud-chrono-time" id="aqmiChronoTime">00:00</span>
        </div>
      </div>

      <div class="hud-card hud-score" id="aqmiGauge">
        <div class="hud-ring">
          <svg viewBox="0 0 40 40" aria-hidden="true">
            <circle cx="20" cy="20" r="17" fill="none" stroke="rgba(15,23,42,0.09)" stroke-width="3.6"/>
            <circle class="fg" id="aqmiGaugeCircle" cx="20" cy="20" r="17" fill="none" stroke="#94a3b8" stroke-width="3.6" stroke-linecap="round" transform="rotate(-90 20 20)"/>
          </svg>
          <div class="hud-ring-value" id="aqmiGaugeValue">0%</div>
          <div class="hud-pulse" id="aqmiGaugePulse" style="display:none;"></div>
        </div>
        <div class="hud-meta">
          <span class="hud-label" data-i18n="score_label">Score Global</span>
        </div>
      </div>

      <div class="hud-card hud-maturity">
        <div class="hud-ring" id="aqmiMainGauge">
          <svg viewBox="0 0 200 200" aria-hidden="true">
            <circle cx="100" cy="100" r="85" fill="none" stroke="rgba(15,23,42,0.09)" stroke-width="12"/>
            <circle class="fg" id="aqmiMainGaugeCircle" cx="100" cy="100" r="85" fill="none" stroke="#94a3b8" stroke-width="12" stroke-linecap="round" transform="rotate(-90 100 100)"/>
          </svg>
          <div class="hud-ring-value" id="aqmiMainGaugeValue">&ndash;</div>
          <div class="hud-pulse" id="aqmiMainGaugePulse" style="display:none;"></div>
        </div>
        <div class="hud-meta">
          <span class="hud-label" data-i18n="maturity">Maturité</span>
          <span class="hud-level">
            <span class="hud-level-icon" id="aqmiMainGaugeIcon"><i class="fas fa-gauge-high"></i></span>
            <span id="aqmiMainGaugeLabel">&hellip;</span>
          </span>
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

  <!-- Domain Transition Banner -->
  <div class="aqmi-domain-transition" id="aqmiDomainTransition"></div>

</div>

<?php
$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;
$configJson = json_encode($config, $jsonFlags) ?: '[]';
$i18nJson = json_encode($i18n, $jsonFlags) ?: '{}';
$jsPath = BASE_PATH . '/public/js/aqmi-premium.js';
$jsVersion = is_file($jsPath) ? filemtime($jsPath) : time();

$extraScripts = <<<SCRIPT
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
var AQMI_CONFIG = {$configJson};
var AQMI_I18N = {$i18nJson};
</script>
<script src="/js/aqmi-premium.js?v={$jsVersion}"></script>
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
?>