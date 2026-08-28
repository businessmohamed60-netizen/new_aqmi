/* ================================================================
   AQMI Premium Questionnaire Engine v2
   Full-screen, one-question-at-a-time, GSAP animations
   Enhanced with stagger, typing, confetti, domain transitions
   ================================================================ */

(function() {
  'use strict';

  var cfg = window.AQMI_CONFIG;
  if (!cfg) return;

  var i18nData = window.AQMI_I18N || {};
  var currentLang = cfg.lang || 'fr';
  var t = i18nData[currentLang] || i18nData['fr'] || {};
  function tr(key) { return t[key] !== undefined ? t[key] : key; }

  function updateBrandMark(lang) {
    var mark = document.getElementById('aqmiBrandMark');
    if (!mark) return;
    var letters = ['A','Q','M','I'];
    mark.innerHTML = '';
    letters.forEach(function(l) { var s = document.createElement('span'); s.textContent = l; mark.appendChild(s); });
  }

  updateBrandMark(currentLang);

  var questions = cfg.questions || [];
  for (var _i = 0; _i < questions.length; _i++) {
    if (questions[_i].weight != null) questions[_i].weight = parseFloat(questions[_i].weight) || 1;
    if (questions[_i].score != null) questions[_i].score = parseInt(questions[_i].score, 10);
  }
  var domains = cfg.domains || [];
  var totalQ = cfg.totalQuestions || questions.length;
  var currentIdx = cfg.currentQuestion || 0;
  var assessmentId = cfg.assessmentId;
  var isTransitioning = false;
  var answeredCount = questions.filter(function(q) { return q.answered; }).length;
  var lastDomainId = null;

  // ── DOM refs ──
  var $id = function(id) { return document.getElementById(id); };
  var el = {
    currentNum: $id('aqmiCurrentNum'),
    domainLabel: $id('aqmiDomainLabel'),
    percentLabel: $id('aqmiPercentLabel'),
    progressFill: $id('aqmiProgressFill'),
    progressTrack: $id('aqmiProgressTrack'),
    scene: $id('aqmiScene'),
    domainBadge: $id('aqmiDomainBadge'),
    domainIcon: $id('aqmiDomainIcon'),
    domainBadgeText: $id('aqmiDomainBadgeText'),
    questionNumber: $id('aqmiQuestionNumber'),
    questionTitle: $id('aqmiQuestionTitle'),
    questionDesc: $id('aqmiQuestionDesc'),
    learnMore: $id('aqmiLearnMore'),
    learnMoreBtn: $id('aqmiLearnMoreBtn'),
    learnMoreContent: $id('aqmiLearnMoreContent'),
    learnMoreBody: $id('aqmiLearnMoreBody'),
    answers: $id('aqmiAnswers'),
    ratingGrid: $id('aqmiRatingGrid'),
    prevBtn: $id('aqmiPrevBtn'),
    nextBtn: $id('aqmiNextBtn'),
    saveIndicator: $id('aqmiSaveIndicator'),
    gauge: $id('aqmiGauge'),
    gaugeCircle: $id('aqmiGaugeCircle'),
    gaugeValue: $id('aqmiGaugeValue'),
    mainGauge: $id('aqmiMainGauge'),
    mainGaugeCircle: $id('aqmiMainGaugeCircle'),
    mainGaugeValue: $id('aqmiMainGaugeValue'),
    mainGaugeIcon: $id('aqmiMainGaugeIcon'),
    mainGaugeLabel: $id('aqmiMainGaugeLabel'),
    mainGaugePulse: $id('aqmiMainGaugePulse'),
    timeRemaining: $id('aqmiTimeRemaining'),
    questionInner: $id('aqmiQuestionInner'),
    illustrationPanel: $id('aqmiIllustrationPanel'),
    domainTransition: $id('aqmiDomainTransition'),
    particles: $id('aqmiParticles'),
  };

  // ── Domain scenes / illustrations ──
  var domainScenes = {
    'production': { icon: 'fa-industry', scene: 'factory', gradient: 'rgba(31,111,235,0.04)' },
    'qualité': { icon: 'fa-flask', scene: 'lab', gradient: 'rgba(46,196,182,0.04)' },
    'qualite': { icon: 'fa-flask', scene: 'lab', gradient: 'rgba(46,196,182,0.04)' },
    'maintenance': { icon: 'fa-robot', scene: 'robot', gradient: 'rgba(157,143,209,0.04)' },
    'supply chain': { icon: 'fa-truck-fast', scene: 'logistics', gradient: 'rgba(46,196,182,0.04)' },
    'logistique': { icon: 'fa-truck-fast', scene: 'logistics', gradient: 'rgba(46,196,182,0.04)' },
    'management': { icon: 'fa-users', scene: 'meeting', gradient: 'rgba(157,143,209,0.04)' },
    'compétences': { icon: 'fa-graduation-cap', scene: 'training', gradient: 'rgba(46,196,182,0.04)' },
    'competences': { icon: 'fa-graduation-cap', scene: 'training', gradient: 'rgba(46,196,182,0.04)' },
    'documentation': { icon: 'fa-file-lines', scene: 'docs', gradient: 'rgba(31,111,235,0.04)' },
    'traçabilité': { icon: 'fa-qrcode', scene: 'trace', gradient: 'rgba(157,143,209,0.04)' },
    'tracabilite': { icon: 'fa-qrcode', scene: 'trace', gradient: 'rgba(157,143,209,0.04)' },
    'gouvernance': { icon: 'fa-building', scene: 'meeting', gradient: 'rgba(157,143,209,0.04)' },
    'risques': { icon: 'fa-shield-halved', scene: 'robot', gradient: 'rgba(229,72,77,0.04)' },
    'default': { icon: 'fa-clipboard-check', scene: 'default', gradient: 'rgba(31,111,235,0.04)' },
  };

  function getDomainKey(name) {
    if (!name) return 'default';
    var n = name.toLowerCase().trim();
    if (domainScenes[n]) return n;
    for (var key in domainScenes) {
      if (n.indexOf(key) !== -1) return key;
    }
    return 'default';
  }

  // ── Particle System ──
  function createParticles() {
    if (!el.particles) return;
    var html = '';
    var colors = ['var(--aqmi-accent)', 'var(--aqmi-warning)', 'var(--aqmi-info)', 'var(--aqmi-success)'];
    for (var i = 0; i < 20; i++) {
      var size = 2 + Math.random() * 4;
      var left = Math.random() * 100;
      var top = Math.random() * 100;
      var dur = 6 + Math.random() * 10;
      var delay = Math.random() * 5;
      var color = colors[Math.floor(Math.random() * colors.length)];
      html += '<div class="aqmi-particle" style="left:' + left + '%;top:' + top + '%;width:' + size + 'px;height:' + size + 'px;background:' + color + ';animation-duration:' + dur + 's;animation-delay:' + delay + 's;"></div>';
    }
    el.particles.innerHTML = html;
  }

  // ── Render Scene with Enhanced Animations ──
  function renderScene(domainName) {
    var key = getDomainKey(domainName);
    var scene = domainScenes[key];
    if (!scene) scene = domainScenes['default'];

    var html = '<div class="scene-icon">';
    html += '<div class="icon-main"><i class="fas ' + scene.icon + '"></i></div>';

    var accents = [];
    switch (scene.scene) {
      case 'factory':
        accents = ['fa-cogs', 'fa-gear', 'fa-industry'];
        break;
      case 'lab':
        accents = ['fa-microscope', 'fa-flask', 'fa-vial'];
        break;
      case 'robot':
        accents = ['fa-microchip', 'fa-cogs', 'fa-gear'];
        break;
      case 'logistics':
        accents = ['fa-truck', 'fa-box', 'fa-warehouse'];
        break;
      case 'meeting':
        accents = ['fa-chart-line', 'fa-presentation', 'fa-users-gear'];
        break;
      case 'training':
        accents = ['fa-book', 'fa-certificate', 'fa-graduation-cap'];
        break;
      case 'docs':
        accents = ['fa-file', 'fa-folder', 'fa-file-pen'];
        break;
      case 'trace':
        accents = ['fa-qrcode', 'fa-barcode', 'fa-fingerprint'];
        break;
      default:
        accents = ['fa-chart-simple', 'fa-clipboard', 'fa-check-double'];
    }
    accents.forEach(function(ic) {
      html += '<div class="icon-accent" style="color:var(--aqmi-accent);opacity:0.15;"><i class="fas ' + ic + '"></i></div>';
    });

    // Floating decorative rings
    html += '<div class="scene-ring ring-1"></div>';
    html += '<div class="scene-ring ring-2"></div>';

    html += '</div>';

    // Animate scene transition
    if (el.scene) {
      gsap.timeline()
        .to(el.scene, { opacity: 0, scale: 0.9, duration: 0.2, ease: 'power2.in' })
        .call(function() {
          el.scene.innerHTML = html;
        })
        .to(el.scene, { opacity: 1, scale: 1, duration: 0.4, ease: 'power2.out' });
    }
  }

  // ── Domain Transition Banner ──
  function showDomainTransition(domainName, domainIcon) {
    if (!el.domainTransition) return;
    var icon = domainIcon || 'fa-clipboard';
    el.domainTransition.innerHTML = [
      '<div class="aqmi-domain-transition-content">',
      '  <div class="aqmi-domain-transition-icon"><i class="fas ' + icon + '"></i></div>',
      '  <div class="aqmi-domain-transition-label">' + tr('domain_transition_label') + '</div>',
      '  <div class="aqmi-domain-transition-name">' + domainName + '</div>',
      '</div>'
    ].join('');

    var tl = gsap.timeline({
      onComplete: function() {
        el.domainTransition.style.display = 'none';
      }
    });
    el.domainTransition.style.display = '';
    tl.fromTo(el.domainTransition, { opacity: 0 }, { opacity: 1, duration: 0.3, ease: 'power2.out' })
      .to(el.domainTransition, { opacity: 0, duration: 0.3, ease: 'power2.in' }, '+=0.8');
  }

  // ── Stagger Answer Cards Entrance ──
  function animateAnswerEntrance() {
    var cards = document.querySelectorAll('.aqmi-answer-card, .aqmi-rating-card');
    if (cards.length === 0) return;
    gsap.fromTo(cards,
      { opacity: 0, y: 20, scale: 0.97 },
      {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 0.35,
        stagger: 0.06,
        ease: 'power2.out',
        onComplete: function() {
          isTransitioning = false;
        }
      }
    );
  }

  // ── Render question ──
  function localized(obj, base) {
    if (currentLang === 'ar' && obj[base + '_ar']) return obj[base + '_ar'];
    if (currentLang === 'en' && obj[base + '_en']) return obj[base + '_en'];
    return obj[base];
  }

  function renderQuestion(index) {
    var q = questions[index];
    if (!q) return;

    var domain = getDomain(q.domain_id);
    var domainName = domain ? localized(domain, 'name') : '';

    // Check for domain change
    if (domain && domain.id !== lastDomainId && lastDomainId !== null) {
      showDomainTransition(domainName, domain ? domain.icon : 'fa-clipboard');
    }
    lastDomainId = domain ? domain.id : null;

    // Update top bar
    el.currentNum.textContent = index + 1;
    el.domainLabel.textContent = domainName;
    el.domainBadgeText.textContent = domainName;
    el.domainLabel.textContent = domainName;
    if (domain && domain.icon) {
      el.domainIcon.className = 'fas ' + domain.icon;
    }

    // Question header
    el.questionNumber.textContent = tr('question').toUpperCase() + ' ' + (index + 1);
    el.questionTitle.textContent = localized(q, 'title');

    // Description
    var qDesc = localized(q, 'description');
    if (qDesc) {
      el.questionDesc.textContent = qDesc;
      el.questionDesc.style.display = '';
    } else {
      el.questionDesc.style.display = 'none';
    }

    // Learn more
    var qHelp = localized(q, 'help_text');
    if (qHelp) {
      el.learnMore.style.display = '';
      el.learnMoreBody.innerHTML = '<p>' + qHelp.replace(/\n/g, '</p><p>') + '</p>';
      el.learnMoreContent.classList.remove('open');
      el.learnMoreBtn.classList.remove('open');
    } else {
      el.learnMore.style.display = 'none';
    }

    // Render answers
    var qt = q.question_type || 'rating_scale';
    el.answers.removeAttribute('data-qtype');
    if (qt === 'yes_no') {
      el.answers.setAttribute('data-qtype', 'yes_no');
      renderYesNo(q, index);
    } else if (qt === 'multiple_choice') {
      el.answers.setAttribute('data-qtype', 'multiple_choice');
      renderMultipleChoice(q, index);
    } else if (qt === 'text_input') {
      el.answers.setAttribute('data-qtype', 'text_input');
      renderTextInput(q, index);
    } else if (qt === 'numeric') {
      el.answers.setAttribute('data-qtype', 'numeric');
      renderNumericInput(q, index);
    } else {
      renderRatingScale(q, index);
    }

    // Update scene
    renderScene(domainName);

    // Update navigation
    el.prevBtn.style.display = index > 0 ? '' : 'none';
    el.nextBtn.style.display = 'none';
    el.nextBtn.className = 'aqmi-nav-btn primary';

    // Check if all answered
    updateProgress();
    updateRightGauge();
    updateMainGauge(currentIdx);
    animateAnswerEntrance();
  }

  function getDomain(domainId) {
    for (var i = 0; i < domains.length; i++) {
      if (domains[i].id === domainId) return domains[i];
    }
    return null;
  }

  // ── Answer renderers ──
  function renderYesNo(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';

    var options = [
      { value: 5, label: tr('yes_label'), sublabel: tr('yes_sub'), icon: 'fa-check', cls: 'yes' },
      { value: 3, label: tr('partial_label'), sublabel: tr('partial_sub'), icon: 'fa-circle-half-stroke', cls: 'partial' },
      { value: 0, label: tr('no_label'), sublabel: tr('no_sub'), icon: 'fa-xmark', cls: 'no' },
      { value: 0, label: tr('na_label'), sublabel: tr('na_sub'), icon: 'fa-dash', cls: 'na' },
    ];

    var html = '';
    for (var i = 0; i < options.length; i++) {
      var opt = options[i];
      var selected = q.answered && q.score === opt.value && opt.cls !== 'na';
      var selectedNA = q.answered && q.score === 0 && opt.cls === 'na' && q.answer_text === 'N/A';
      var selClass = '';
      if (selected) selClass = 'selected-' + opt.cls;
      if (selectedNA) selClass = 'selected-na';
      html += '<div class="aqmi-answer-card ' + selClass + '" data-score="' + opt.value + '" data-cls="' + opt.cls + '" data-qid="' + q.id + '" data-idx="' + index + '">';
      html += '  <div class="aqmi-answer-icon"><i class="fas ' + opt.icon + '"></i></div>';
      html += '  <div class="aqmi-answer-label">' + opt.label + '<div class="aqmi-answer-sublabel">' + opt.sublabel + '</div></div>';
      html += '</div>';
    }
    el.answers.innerHTML = html;
  }

  function renderMultipleChoice(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';

    var opts = [];
    try { opts = JSON.parse(q.options_json || '[]'); } catch(e) { opts = []; }
    if (opts.length === 0 && q.options) {
      try { opts = JSON.parse(q.options); } catch(e) { opts = []; }
    }

    var letters = ['A','B','C','D','E','F','G','H','I','J','K','L'];
    var html = '';
    for (var i = 0; i < opts.length; i++) {
      var opt = opts[i];
      var label = opt.label || opt.value || 'Option ' + (i + 1);
      var selected = q.answered && q.score === i;
      var letter = letters[i] || (i + 1);
      html += '<div class="aqmi-answer-card ' + (selected ? 'selected' : '') + '" data-score="' + i + '" data-cls="mc" data-qid="' + q.id + '" data-idx="' + index + '">';
      html += '  <div class="aqmi-answer-icon">' + letter + '</div>';
      html += '  <div class="aqmi-answer-label">' + label + '</div>';
      html += '</div>';
    }
    if (opts.length === 0) {
      html += '<div style="color:var(--aqmi-text-tertiary);font-size:0.85rem;">' + tr('no_options') + '</div>';
    }
    el.answers.innerHTML = html;
  }

  function renderTextInput(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';
    var val = q.answered && q.answer_text ? q.answer_text : '';
    var maxLen = 1000;
    var html = '<div class="aqmi-text-input-wrap">';
    html += '<textarea class="aqmi-text-input" data-qid="' + q.id + '" data-idx="' + index + '" rows="4" maxlength="' + maxLen + '" placeholder="' + tr('text_placeholder') + '">' + val + '</textarea>';
    html += '<div class="aqmi-text-input-meta">';
    html += '  <span class="aqmi-text-input-hint"><i class="fas fa-keyboard"></i> ' + (currentLang === 'ar' ? 'اكتب إجابتك' : currentLang === 'en' ? 'Type your answer' : 'Saisissez votre réponse') + '</span>';
    html += '  <span class="aqmi-text-input-counter" data-max="' + maxLen + '">' + val.length + ' / ' + maxLen + '</span>';
    html += '</div></div>';
    el.answers.innerHTML = html;

    var ta = el.answers.querySelector('.aqmi-text-input');
    var counter = el.answers.querySelector('.aqmi-text-input-counter');
    if (ta && counter) {
      ta.addEventListener('input', function() {
        var len = ta.value.length;
        counter.textContent = len + ' / ' + maxLen;
        counter.classList.toggle('near-limit', len > maxLen * 0.8 && len < maxLen);
        counter.classList.toggle('at-limit', len >= maxLen);
      });
    }
  }

  function renderNumericInput(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';
    var val = q.answered && q.answer_value ? q.answer_value : '';
    var unit = '';
    var minVal = null, maxVal = null, step = 1;
    try {
      var parsed = JSON.parse(q.options_json || q.options || '{}');
      unit = parsed.unit || '';
      minVal = parsed.min !== undefined ? parseFloat(parsed.min) : null;
      maxVal = parsed.max !== undefined ? parseFloat(parsed.max) : null;
      step = parsed.step !== undefined ? parseFloat(parsed.step) : 1;
    } catch(e) {}

    var html = '';
    html += '<div class="aqmi-numeric-display">';
    html += '  <span class="aqmi-numeric-value ' + (val === '' ? 'empty' : '') + '" id="aqmiNumericDisplay">' + (val !== '' ? val : tr('numeric_placeholder')) + '</span>';
    if (unit) html += '  <span class="aqmi-numeric-unit">' + unit + '</span>';
    html += '</div>';

    html += '<div class="aqmi-numeric-controls">';
    html += '  <button class="aqmi-numeric-btn" data-action="decrease" type="button"><i class="fas fa-minus"></i></button>';
    html += '  <input type="number" class="aqmi-numeric-input" data-qid="' + q.id + '" data-idx="' + index + '" placeholder="' + tr('numeric_placeholder') + '" value="' + val + '"';
    if (minVal !== null) html += ' min="' + minVal + '"';
    if (maxVal !== null) html += ' max="' + maxVal + '"';
    html += ' step="' + step + '">';
    html += '  <button class="aqmi-numeric-btn" data-action="increase" type="button"><i class="fas fa-plus"></i></button>';
    html += '</div>';

    if (minVal !== null && maxVal !== null) {
      html += '<div class="aqmi-numeric-range-hint"><span>' + minVal + '</span><span>' + maxVal + '</span></div>';
    }

    el.answers.innerHTML = html;

    var input = el.answers.querySelector('.aqmi-numeric-input');
    var display = el.answers.querySelector('#aqmiNumericDisplay');
    var decBtn = el.answers.querySelector('.aqmi-numeric-btn[data-action="decrease"]');
    var incBtn = el.answers.querySelector('.aqmi-numeric-btn[data-action="increase"]');

    function updateDisplay(v) {
      if (v === '' || v === null || isNaN(v)) {
        display.textContent = tr('numeric_placeholder');
        display.classList.add('empty');
      } else {
        display.textContent = v;
        display.classList.remove('empty');
      }
    }

    if (input) {
      input.addEventListener('input', function() { updateDisplay(input.value); });
    }
    if (decBtn) {
      decBtn.addEventListener('click', function() {
        var cur = parseFloat(input.value) || 0;
        var newVal = cur - step;
        if (minVal !== null && newVal < minVal) newVal = minVal;
        input.value = newVal;
        updateDisplay(newVal);
        input.dispatchEvent(new Event('blur'));
      });
    }
    if (incBtn) {
      incBtn.addEventListener('click', function() {
        var cur = parseFloat(input.value) || 0;
        var newVal = cur + step;
        if (maxVal !== null && newVal > maxVal) newVal = maxVal;
        input.value = newVal;
        updateDisplay(newVal);
        input.dispatchEvent(new Event('blur'));
      });
    }
  }

  function renderRatingScale(q, index) {
    el.answers.style.display = 'none';
    el.ratingGrid.style.display = '';

    var colors = ['#E5484D', '#9d8fd1', '#9d8fd1', '#2EC4B6', '#1F6FEB', '#1F6FEB'];
    var labels = (t.rating_labels || ['Inexistant', 'Initial', 'Basique', 'Maîtrisé', 'Performant', 'Excellence']).slice();

    var html = '';
    for (var s = 0; s <= 5; s++) {
      var selected = q.answered && q.score === s;
      html += '<div class="aqmi-rating-card ' + (selected ? 'selected' : '') + '" data-score="' + s + '" data-qid="' + q.id + '" data-idx="' + index + '">';
      html += '  <div class="aqmi-rating-score" style="color:' + colors[s] + '">' + s + '</div>';
      html += '  <div class="aqmi-rating-label">' + labels[s] + '</div>';
      html += '</div>';
    }
    el.ratingGrid.innerHTML = html;
  }

  // ── Answer click handler ──
  function handleAnswerClick(e) {
    var card = e.currentTarget;
    if (isTransitioning) return;
    if (card.classList.contains('selected') ||
        card.classList.contains('selected-yes') ||
        card.classList.contains('selected-partial') ||
        card.classList.contains('selected-no') ||
        card.classList.contains('selected-na')) return;

    var score = parseInt(card.getAttribute('data-score'), 10);
    var qid = parseInt(card.getAttribute('data-qid'), 10);
    var idx = parseInt(card.getAttribute('data-idx'), 10);
    var cls = card.getAttribute('data-cls');

    // Clear selection in same group
    var parent = card.parentNode;
    var cards = parent.querySelectorAll('.aqmi-answer-card, .aqmi-rating-card');
    for (var i = 0; i < cards.length; i++) {
      cards[i].classList.remove('selected', 'selected-yes', 'selected-partial', 'selected-no', 'selected-na');
    }

    // Apply selection class
    if (parent.classList.contains('aqmi-rating-grid')) {
      card.classList.add('selected');
    } else if (cls === 'mc') {
      card.classList.add('selected');
    } else if (cls) {
      card.classList.add('selected-' + cls);
    }

    // Mark as answered
    if (questions[idx]) {
      questions[idx].answered = true;
      questions[idx].score = score;
    }

    // Auto-save
    saveAnswer(qid, score);

    // Update progress
    if (idx === currentIdx) {
      answeredCount = 0;
      for (var j = 0; j < questions.length; j++) {
        if (questions[j].answered) answeredCount++;
      }
      updateProgress();
      updateRightGauge();
      updateMainGauge(currentIdx);
    }

    // Show next button
    el.nextBtn.style.display = '';

    // Animate selection with spring
    gsap.fromTo(card, { scale: 0.95 }, { scale: 1, duration: 0.3, ease: 'back.out(2.5)' });

    // Ripple effect on card
    var ripple = document.createElement('div');
    ripple.className = 'aqmi-ripple';
    card.appendChild(ripple);
    gsap.to(ripple, {
      scale: 4,
      opacity: 0,
      duration: 0.6,
      ease: 'power2.out',
      onComplete: function() { ripple.remove(); }
    });

    // NOTE: la complétion n'est plus déclenchée automatiquement ici.
    // Elle est désormais uniquement pilotée par le clic sur le bouton "Suivant"
    // via goNext() (voir plus bas), pour éviter le double-déclenchement
    // et le blocage observé sur la dernière question.
  }

  // ── Save answer ──
  function saveAnswer(questionId, score) {
    el.saveIndicator.classList.remove('show');
    jQuery.ajax({
      url: '/assessment/save-answer',
      method: 'GET',
      data: {
        assessment_id: assessmentId,
        question_id: questionId,
        score: score
      },
      success: function() {
        el.saveIndicator.classList.add('show');
        setTimeout(function() { el.saveIndicator.classList.remove('show'); }, 2000);
      },
      error: function() {
        // Silent fail
      }
    });
  }

  // ── Navigation ──
  function goToQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    if (isTransitioning) return;

    isTransitioning = true;
    currentIdx = index;
    console.log('[AQMI DEBUG] goToQuestion -> currentIdx =', currentIdx, '/ questions.length =', questions.length);

    var inner = el.questionInner;
    var duration = 0.35;

    gsap.to(inner, {
      opacity: 0,
      y: -15,
      duration: duration,
      ease: 'power2.in',
      onComplete: function() {
        renderQuestion(currentIdx);
        gsap.set(inner, { opacity: 0, y: 20 });
        gsap.to(inner, {
          opacity: 1,
          y: 0,
          duration: duration,
          ease: 'power2.out',
          onComplete: function() {
            isTransitioning = false;
          }
        });
      }
    });
  }

  var completionShown = false;

  function goNext() {
    console.log('[AQMI DEBUG] goNext() called, currentIdx =', currentIdx, '/ questions.length =', questions.length);
    if (isTransitioning) return;
    if (currentIdx < questions.length - 1) {
      goToQuestion(currentIdx + 1);
    } else {
      if (completionShown) return;
      completionShown = true;
      console.log('[AQMI DEBUG] showCompletion triggered from goNext()');
      showCompletion();
    }
  }

  function goPrev() {
    if (currentIdx > 0) {
      goToQuestion(currentIdx - 1);
    }
  }

  // ── Progress with Shine Animation ──
  function updateProgress() {
    var pct = totalQ > 0 ? Math.round((answeredCount / totalQ) * 100) : 0;
    el.percentLabel.textContent = pct + '%';
    el.progressFill.style.width = pct + '%';

    gsap.fromTo(el.progressFill, { opacity: 0.7 }, { opacity: 1, duration: 0.3, ease: 'power2.out' });

    var remaining = totalQ - answeredCount;
    var mins = Math.max(1, Math.ceil(remaining * 0.35));
    el.timeRemaining.textContent = '~' + mins + ' min';
  }

  // ── Maturity Gauge with GSAP Animation ──
  var GAUGE_CIRCUMFERENCE = 2 * Math.PI * 17;
  var MAIN_GAUGE_CIRCUMFERENCE = 2 * Math.PI * 85;
  var gaugeInitialized = false;

  function updateRightGauge() {
    if (answeredCount === 0) {
      el.gauge.classList.remove('show');
      gaugeInitialized = false;
      return;
    }
    el.gauge.classList.add('show');

    var totalScore = 0;
    var totalWeight = 0;
    for (var i = 0; i < questions.length; i++) {
      if (questions[i].answered) {
        var s = questions[i].score;
        if (s >= 0 && s <= 5) {
          totalScore += s * questions[i].weight;
          totalWeight += questions[i].weight;
        }
      }
    }
    var pct = totalWeight > 0 ? Math.round((totalScore / (totalWeight * 5)) * 100) : 0;

    var offset = GAUGE_CIRCUMFERENCE - (pct / 100) * GAUGE_CIRCUMFERENCE;

    // On first display, set the starting offset directly so GSAP
    // animates from the correct origin (full circle = empty gauge).
    if (!gaugeInitialized) {
      el.gaugeCircle.style.strokeDashoffset = String(GAUGE_CIRCUMFERENCE);
      gaugeInitialized = true;
    }

    var startOffset = parseFloat(el.gaugeCircle.style.strokeDashoffset) || GAUGE_CIRCUMFERENCE;
    var proxy = { offset: startOffset };
    gsap.to(proxy, {
      offset: offset,
      duration: 0.8,
      ease: 'power2.out',
      onUpdate: function() {
        el.gaugeCircle.style.strokeDashoffset = proxy.offset;
      }
    });

    var color = pct >= 70 ? '#2EC4B6' : (pct >= 40 ? '#9d8fd1' : '#E5484D');
    el.gaugeCircle.style.stroke = color;
    el.gaugeValue.style.color = color;

    // Pulse ring on update
    var pulse = document.getElementById('aqmiGaugePulse');
    if (pulse) {
      pulse.style.borderColor = color;
      pulse.style.display = 'block';
      pulse.style.animation = 'none';
      // Force reflow to restart animation
      void pulse.offsetWidth;
      pulse.style.animation = 'aqmi-gauge-ping 1.5s ease-out';
      setTimeout(function() { pulse.style.display = 'none'; }, 1500);
    }

    var numProxy = { val: parseInt(el.gaugeValue.textContent) || 0 };
    gsap.to(numProxy, {
      val: pct,
      duration: 0.6,
      ease: 'power2.out',
      onUpdate: function() {
        el.gaugeValue.textContent = Math.round(numProxy.val) + '%';
      }
    });

  }

  function updateMainGauge(qIdx) {
    var q = questions[qIdx];
    var score = (q && q.answered && q.score != null) ? q.score : 0;
    var pct = Math.round((score / 5) * 100);

    if (!el.mainGaugeCircle) return;

    var mainOffset = MAIN_GAUGE_CIRCUMFERENCE - (pct / 100) * MAIN_GAUGE_CIRCUMFERENCE;
    var mainStart = parseFloat(el.mainGaugeCircle.style.strokeDashoffset) || MAIN_GAUGE_CIRCUMFERENCE;
    var mainProxy = { offset: mainStart };
    gsap.to(mainProxy, {
      offset: mainOffset,
      duration: 0.8,
      ease: 'power2.out',
      onUpdate: function() {
        el.mainGaugeCircle.style.strokeDashoffset = mainProxy.offset;
      }
    });

    var color = score === 0 ? '#E5484D' : score === 1 ? '#E8823A' : score === 2 ? '#9d8fd1' : score === 3 ? '#1F6FEB' : '#2EC4B6';
    el.mainGaugeIcon.style.color = color;

    var mainNumProxy = { val: parseInt(el.mainGaugeValue.textContent) || 0 };
    gsap.to(mainNumProxy, {
      val: pct,
      duration: 0.6,
      ease: 'power2.out',
      onUpdate: function() {
        el.mainGaugeValue.textContent = Math.round(mainNumProxy.val) + '%';
      }
    });

    var icon = 'fa-gauge-high';
    var label = tr('gauge_waiting');
    if (q && q.answered) {
      var gaugeLabels = t.gauge_labels || ['Inexistant', 'Initial', 'Basique', 'Maîtrisé', 'Performant', 'Excellence'];
      if (score === 0) { icon = 'fa-circle-xmark'; label = gaugeLabels[0]; }
      else if (score === 1) { icon = 'fa-circle-dot'; label = gaugeLabels[1]; }
      else if (score === 2) { icon = 'fa-circle-half-stroke'; label = gaugeLabels[2]; }
      else if (score === 3) { icon = 'fa-circle-check'; label = gaugeLabels[3]; }
      else if (score === 4) { icon = 'fa-circle-check'; label = gaugeLabels[4]; }
      else if (score === 5) { icon = 'fa-award'; label = gaugeLabels[5]; }
    }
    el.mainGaugeIcon.innerHTML = '<i class="fas ' + icon + '"></i>';
    el.mainGaugeLabel.textContent = label;

    if (el.mainGaugePulse) {
      el.mainGaugePulse.style.borderColor = color;
      el.mainGaugePulse.style.display = 'block';
      el.mainGaugePulse.style.animation = 'none';
      void el.mainGaugePulse.offsetWidth;
      el.mainGaugePulse.style.animation = 'aqmi-gauge-ping 1.5s ease-out';
      setTimeout(function() { el.mainGaugePulse.style.display = 'none'; }, 1500);
    }
  }

  // ── Confetti Celebration ──
  function createConfetti() {
    var container = document.createElement('div');
    container.className = 'aqmi-confetti-container';
    container.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99999;overflow:hidden;';
    document.body.appendChild(container);

    var colors = ['#1F6FEB', '#9d8fd1', '#2EC4B6', '#9d8fd1', '#E5484D', '#2EC4B6', '#1F6FEB'];
    var shapes = ['circle', 'square', 'triangle'];

    for (var i = 0; i < 60; i++) {
      var piece = document.createElement('div');
      var color = colors[Math.floor(Math.random() * colors.length)];
      var size = 6 + Math.random() * 8;
      var left = Math.random() * 100;
      var delay = Math.random() * 2;
      var dur = 2 + Math.random() * 3;
      var shape = shapes[Math.floor(Math.random() * shapes.length)];
      var rotation = Math.random() * 360;

      piece.style.cssText = [
        'position:absolute',
        'left:' + left + '%',
        'top:-10px',
        'width:' + size + 'px',
        'height:' + size + 'px',
        'background:' + color,
        'border-radius:' + (shape === 'circle' ? '50%' : shape === 'square' ? '2px' : '0'),
        'opacity:0',
        'transform:rotate(' + rotation + 'deg)',
        'clip-path:' + (shape === 'triangle' ? 'polygon(50% 0%, 0% 100%, 100% 100%)' : 'none'),
      ].join(';');

      container.appendChild(piece);

      gsap.to(piece, {
        y: window.innerHeight + 20,
        x: (Math.random() - 0.5) * 200,
        opacity: 1,
        rotation: rotation + 360 + Math.random() * 360,
        duration: dur,
        delay: delay,
        ease: 'power1.out',
        onComplete: function() {
          if (piece.parentNode) piece.remove();
        }
      });
    }

    setTimeout(function() {
      if (container.parentNode) container.remove();
    }, 6000);
  }

  // ── Completion Screen ──
  function showCompletion() {
    console.log('[AQMI DEBUG] showCompletion() executed');
    createConfetti();

    var overlay = document.createElement('div');
    overlay.className = 'aqmi-completion-overlay';
    overlay.innerHTML = [
      '<div class="aqmi-completion-content">',
      '  <div class="aqmi-completion-check"><i class="fas fa-check"></i></div>',
      '  <h2 class="aqmi-completion-title">' + tr('completion_title') + '</h2>',
      '  <p class="aqmi-completion-desc">' + tr('completion_desc') + '</p>',
      '  <a href="/assessment/' + assessmentId + '/complete" class="aqmi-completion-btn">',
      '    ' + tr('completion_btn') + ' <i class="fas fa-arrow-right"></i>',
      '  </a>',
      '</div>'
    ].join('');
    document.querySelector('.aqmi-premium').appendChild(overlay);

    // Active pointer-events (voir .aqmi-completion-overlay.show en CSS) :
    // sans cette classe, le lien "Voir mes résultats" reste visuellement
    // affiché mais non cliquable (pointer-events: none par défaut).
    overlay.classList.add('show');

    var tl = gsap.timeline();
    tl.to(overlay, { opacity: 1, duration: 0.5, ease: 'power2.out' })
      .fromTo(overlay.querySelector('.aqmi-completion-check'), { scale: 0, rotation: -180 }, { scale: 1, rotation: 0, duration: 0.6, ease: 'back.out(2)' }, '-=0.2')
      .fromTo(overlay.querySelector('.aqmi-completion-title'), { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' }, '-=0.3')
      .fromTo(overlay.querySelector('.aqmi-completion-desc'), { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' }, '-=0.2')
      .fromTo(overlay.querySelector('.aqmi-completion-btn'), { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' }, '-=0.2');
  }

  // ── Keyboard Navigation ──
  document.addEventListener('keydown', function(e) {
    if (isTransitioning) return;
    var activeEl = document.activeElement;
    var isTyping = activeEl && (activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'INPUT');
    if (isTyping) return;
    if (e.key === 'ArrowRight' || e.key === ' ') {
      e.preventDefault();
      if (el.nextBtn.style.display !== 'none') {
        goNext();
      }
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      goPrev();
    } else if (e.key >= '1' && e.key <= '6') {
      var idx = parseInt(e.key, 10) - 1;
      var activeAnswers = document.querySelectorAll('.aqmi-answer-card, .aqmi-rating-card');
      if (activeAnswers[idx]) {
        activeAnswers[idx].click();
      }
    }
  });

  // ── Learn More toggle ──
  if (el.learnMoreBtn) {
    el.learnMoreBtn.addEventListener('click', function() {
      var isOpen = el.learnMoreContent.classList.contains('open');
      if (isOpen) {
        el.learnMoreContent.classList.remove('open');
        el.learnMoreBtn.classList.remove('open');
      } else {
        el.learnMoreContent.classList.add('open');
        el.learnMoreBtn.classList.add('open');
      }
    });
  }

  // ── Event delegation for answers ──
  if (el.answers) {
    el.answers.addEventListener('click', function(e) {
      var card = e.target.closest('.aqmi-answer-card');
      if (card) handleAnswerClick({ currentTarget: card });
    });
  }

  if (el.ratingGrid) {
    el.ratingGrid.addEventListener('click', function(e) {
      var card = e.target.closest('.aqmi-rating-card');
      if (card) handleAnswerClick({ currentTarget: card });
    });
  }

  // Text input auto-save on blur
  if (el.answers) {
    el.answers.addEventListener('blur', function(e) {
      var input = e.target.closest('.aqmi-text-input');
      if (!input) {
        input = e.target.closest('.aqmi-numeric-input');
      }
      if (!input) return;

      var qid = parseInt(input.getAttribute('data-qid'), 10);
      var idx = parseInt(input.getAttribute('data-idx'), 10);
      var val = input.value.trim();
      if (val.length === 0) return;

      if (questions[idx]) {
        questions[idx].answered = true;
        questions[idx].score = 3;
        if (input.classList.contains('aqmi-text-input')) {
          questions[idx].answer_text = val;
        } else {
          questions[idx].answer_value = val;
        }
      }

      answeredCount = questions.filter(function(q) { return q.answered; }).length;
      updateProgress();
      updateRightGauge();
      updateMainGauge(currentIdx);

      el.nextBtn.style.display = '';

      jQuery.ajax({
        url: '/assessment/save-answer',
        method: 'GET',
        data: {
          assessment_id: assessmentId,
          question_id: qid,
          score: 3,
          answer_text: input.classList.contains('aqmi-text-input') ? val : '',
          answer_value: input.classList.contains('aqmi-numeric-input') ? val : ''
        }
      });
    }, true);
  }

  // ── Navigation buttons ──
  if (el.prevBtn) el.prevBtn.addEventListener('click', goPrev);
  if (el.nextBtn) el.nextBtn.addEventListener('click', goNext);

  // ── Init ──
  function init() {
    createParticles();
    // Ensure gauge circle starts at the correct circumference before any animation
    if (el.gaugeCircle) {
      el.gaugeCircle.style.strokeDasharray = String(GAUGE_CIRCUMFERENCE);
      el.gaugeCircle.style.strokeDashoffset = String(GAUGE_CIRCUMFERENCE);
    }
    if (el.mainGaugeCircle) {
      el.mainGaugeCircle.style.strokeDasharray = String(MAIN_GAUGE_CIRCUMFERENCE);
      el.mainGaugeCircle.style.strokeDashoffset = String(MAIN_GAUGE_CIRCUMFERENCE);
    }
    renderQuestion(currentIdx);
    updateProgress();
    updateRightGauge();
    updateMainGauge(currentIdx);
    gsap.from(el.questionInner, { opacity: 0, y: 20, duration: 0.5, ease: 'power2.out' });
  }

  // ── Language Selection Screen ──
  var langScreen = document.getElementById('aqmiLangScreen');
  var langChoices = document.querySelectorAll('.aqmi-lang-choice');
  var langStartBtn = document.getElementById('aqmiLangStartBtn');
  var langStartText = document.getElementById('aqmiLangStartText');
  var langTitle = document.getElementById('aqmiLangTitle');
  var langDesc = document.getElementById('aqmiLangDesc');
  var selectedLang = currentLang;
  var langScreenVisible = !!langScreen && langScreen.offsetParent !== null;

  function applyI18nLabels(lang) {
    var labels = i18nData[lang] || i18nData['fr'] || {};
    document.querySelectorAll('[data-i18n]').forEach(function(el) {
      var key = el.getAttribute('data-i18n');
      if (labels[key] !== undefined) el.textContent = labels[key];
    });
    if (langTitle && labels.choose_lang) langTitle.textContent = labels.choose_lang;
    if (langDesc && labels.choose_lang_desc) langDesc.textContent = labels.choose_lang_desc;
    if (langStartText && labels.start) langStartText.textContent = labels.start;
  }

  function dismissLangScreen() {
    if (!langScreen) return;
    gsap.to(langScreen, {
      opacity: 0, duration: 0.5, ease: 'power2.inOut',
      onComplete: function() {
        langScreen.style.display = 'none';
        langScreenVisible = false;
        // If model selection is needed, show model screen instead of init
        if (cfg.isModelSelection) {
          showModelScreen();
        } else {
          init();
        }
      }
    });
  }

  // ── Model Selection Screen ──
  var modelScreen = document.getElementById('aqmiModelScreen');
  var modelChoices = document.querySelectorAll('.aqmi-model-choice');
  var modelStartBtn = document.getElementById('aqmiModelStartBtn');
  var modelStartText = document.getElementById('aqmiModelStartText');
  var modelTitle = document.getElementById('aqmiModelTitle');
  var modelDesc = document.getElementById('aqmiModelDesc');
  var selectedModelId = null;

  function applyModelLabels(lang) {
    var labels = i18nData[lang] || i18nData['fr'] || {};
    if (modelTitle && labels.choose_model) modelTitle.textContent = labels.choose_model;
    if (modelDesc && labels.choose_model_desc) modelDesc.textContent = labels.choose_model_desc;
    if (modelStartText && labels.start_assessment) modelStartText.textContent = labels.start_assessment;
  }

  function showModelScreen() {
    if (!modelScreen) { init(); return; }
    modelScreen.style.display = '';
    applyModelLabels(currentLang);
    gsap.fromTo(modelScreen, { opacity: 0 }, { opacity: 1, duration: 0.4, ease: 'power2.out' });
  }

  function dismissModelScreen() {
    if (!modelScreen) return;
    gsap.to(modelScreen, {
      opacity: 0, duration: 0.4, ease: 'power2.inOut',
      onComplete: function() {
        modelScreen.style.display = 'none';
        // Submit model selection then reload to get model-filtered questions
        var formData = new FormData();
        formData.append('assessment_id', assessmentId);
        formData.append('model_id', selectedModelId);
        fetch('/assessment/select-model', { method: 'POST', body: formData })
          .then(function(r) { return r.json(); })
          .then(function(data) {
            if (data.success && data.redirect) {
              window.location.href = data.redirect;
            } else {
              window.location.reload();
            }
          })
          .catch(function() { window.location.reload(); });
      }
    });
  }

  if (modelScreen) {
    modelChoices.forEach(function(btn) {
      btn.addEventListener('click', function() {
        modelChoices.forEach(function(b) { b.classList.remove('selected'); });
        btn.classList.add('selected');
        selectedModelId = btn.getAttribute('data-model');
        modelStartBtn.disabled = false;
      });
    });

    if (modelStartBtn) {
      modelStartBtn.addEventListener('click', function() {
        if (modelStartBtn.disabled || !selectedModelId) return;
        dismissModelScreen();
      });
    }
  }

  if (langScreen) {
    // Pre-select current lang
    langChoices.forEach(function(btn) {
      if (btn.getAttribute('data-lang') === selectedLang) btn.classList.add('selected');
      btn.addEventListener('click', function() {
        langChoices.forEach(function(b) { b.classList.remove('selected'); });
        btn.classList.add('selected');
        selectedLang = btn.getAttribute('data-lang');
        langStartBtn.disabled = false;
        applyI18nLabels(selectedLang);
        // Set RTL for Arabic
        document.documentElement.setAttribute('dir', selectedLang === 'ar' ? 'rtl' : 'ltr');
        updateBrandMark(selectedLang);
      });
    });

    if (langStartBtn) {
      langStartBtn.addEventListener('click', function() {
        if (langStartBtn.disabled) return;
        // Save language to session via AJAX
        fetch('/lang/' + selectedLang, { method: 'GET', redirect: 'manual' })
          .catch(function() {}) // ignore errors, session is set server-side
          .finally(function() {
            currentLang = selectedLang;
            t = i18nData[currentLang] || i18nData['fr'] || {};
            updateBrandMark(currentLang);
            dismissLangScreen();
          });
      });
    }

    // Apply initial labels
    applyI18nLabels(selectedLang);
  }

  // If no language screen, show model screen or init directly
  if (!langScreenVisible) {
    if (cfg.isModelSelection) {
      showModelScreen();
    } else {
      init();
    }
  }

})();