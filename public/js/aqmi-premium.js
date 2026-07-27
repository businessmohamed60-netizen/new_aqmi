/* ================================================================
   AQMI Premium Questionnaire Engine v2
   Full-screen, one-question-at-a-time, GSAP animations
   Enhanced with stagger, typing, confetti, domain transitions
   ================================================================ */

(function() {
  'use strict';

  var cfg = window.AQMI_CONFIG;
  if (!cfg) return;

  var questions = cfg.questions || [];
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
    // assistant removed
    keyboardHint: $id('aqmiKeyboardHint'),
    timeRemaining: $id('aqmiTimeRemaining'),
    questionInner: $id('aqmiQuestionInner'),
    illustrationPanel: $id('aqmiIllustrationPanel'),
    domainTransition: $id('aqmiDomainTransition'),
    particles: $id('aqmiParticles'),
  };

  // ── Domain scenes / illustrations ──
  var domainScenes = {
    'production': { icon: 'fa-industry', scene: 'factory', gradient: 'rgba(59,130,246,0.04)' },
    'qualité': { icon: 'fa-flask', scene: 'lab', gradient: 'rgba(34,197,94,0.04)' },
    'qualite': { icon: 'fa-flask', scene: 'lab', gradient: 'rgba(34,197,94,0.04)' },
    'maintenance': { icon: 'fa-robot', scene: 'robot', gradient: 'rgba(139,92,246,0.04)' },
    'supply chain': { icon: 'fa-truck-fast', scene: 'logistics', gradient: 'rgba(6,182,212,0.04)' },
    'logistique': { icon: 'fa-truck-fast', scene: 'logistics', gradient: 'rgba(6,182,212,0.04)' },
    'management': { icon: 'fa-users', scene: 'meeting', gradient: 'rgba(245,158,11,0.04)' },
    'compétences': { icon: 'fa-graduation-cap', scene: 'training', gradient: 'rgba(34,197,94,0.04)' },
    'competences': { icon: 'fa-graduation-cap', scene: 'training', gradient: 'rgba(34,197,94,0.04)' },
    'documentation': { icon: 'fa-file-lines', scene: 'docs', gradient: 'rgba(59,130,246,0.04)' },
    'traçabilité': { icon: 'fa-qrcode', scene: 'trace', gradient: 'rgba(139,92,246,0.04)' },
    'tracabilite': { icon: 'fa-qrcode', scene: 'trace', gradient: 'rgba(139,92,246,0.04)' },
    'gouvernance': { icon: 'fa-building', scene: 'meeting', gradient: 'rgba(245,158,11,0.04)' },
    'risques': { icon: 'fa-shield-halved', scene: 'robot', gradient: 'rgba(239,68,68,0.04)' },
    'default': { icon: 'fa-clipboard-check', scene: 'default', gradient: 'rgba(59,130,246,0.04)' },
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
    var colors = ['var(--aqmi-accent)', 'var(--aqmi-purple)', 'var(--aqmi-info)', 'var(--aqmi-success)'];
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
      '  <div class="aqmi-domain-transition-label">DOMAINE</div>',
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
  function renderQuestion(index) {
    var q = questions[index];
    if (!q) return;

    var domain = getDomain(q.domain_id);
    var domainName = domain ? domain.name : '';

    // Check for domain change
    if (domain && domain.id !== lastDomainId && lastDomainId !== null) {
      showDomainTransition(domainName, domain ? domain.icon : 'fa-clipboard');
    }
    lastDomainId = domain ? domain.id : null;

    // Update top bar
    el.currentNum.textContent = index + 1;
    el.domainLabel.textContent = domainName;
    el.domainBadgeText.textContent = domainName;
    if (domain && domain.icon) {
      el.domainIcon.className = 'fas ' + domain.icon;
    }

    // Question header
    el.questionNumber.textContent = 'QUESTION ' + (index + 1);
    el.questionTitle.textContent = q.title;

    // Description
    if (q.description) {
      el.questionDesc.textContent = q.description;
      el.questionDesc.style.display = '';
    } else {
      el.questionDesc.style.display = 'none';
    }

    // Learn more
    if (q.help_text) {
      el.learnMore.style.display = '';
      el.learnMoreBody.innerHTML = '<p>' + q.help_text.replace(/\n/g, '</p><p>') + '</p>';
      el.learnMoreContent.classList.remove('open');
      el.learnMoreBtn.classList.remove('open');
    } else {
      el.learnMore.style.display = 'none';
    }

    // Render answers
    var qt = q.question_type || 'rating_scale';
    if (qt === 'yes_no') {
      renderYesNo(q, index);
    } else if (qt === 'multiple_choice') {
      renderMultipleChoice(q, index);
    } else if (qt === 'text_input') {
      renderTextInput(q, index);
    } else if (qt === 'numeric') {
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
    updateGauge();
    // Stagger entrance animation
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
      { value: 5, label: 'Oui', sublabel: 'Bonne pratique totalement appliquée', icon: 'fa-check-circle', cls: 'yes' },
      { value: 3, label: 'Partiellement', sublabel: 'Bonne pratique partiellement appliquée', icon: 'fa-minus-circle', cls: 'partial' },
      { value: 0, label: 'Non', sublabel: 'Bonne pratique non appliquée', icon: 'fa-times-circle', cls: 'no' },
      { value: 0, label: 'Non concerné', sublabel: 'Cette question ne s\'applique pas', icon: 'fa-ban', cls: 'na' },
    ];

    var html = '';
    for (var i = 0; i < options.length; i++) {
      var opt = options[i];
      var selected = q.answered && q.score === opt.value;
      var selClass = '';
      if (selected) {
        selClass = 'selected-' + opt.cls;
      }
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

    var html = '';
    for (var i = 0; i < opts.length; i++) {
      var opt = opts[i];
      var label = opt.label || opt.value || 'Option ' + (i + 1);
      var selected = q.answered && q.score === i;
      html += '<div class="aqmi-answer-card ' + (selected ? 'selected' : '') + '" data-score="' + i + '" data-cls="mc" data-qid="' + q.id + '" data-idx="' + index + '">';
      html += '  <div class="aqmi-answer-icon"><i class="fas ' + (selected ? 'fa-check-circle' : 'fa-circle') + '"></i></div>';
      html += '  <div class="aqmi-answer-label">' + label + '</div>';
      html += '</div>';
    }
    if (opts.length === 0) {
      html += '<div style="color:var(--aqmi-text-tertiary);font-size:0.85rem;">Aucune option disponible</div>';
    }
    el.answers.innerHTML = html;
  }

  function renderTextInput(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';
    var val = q.answered && q.answer_text ? q.answer_text : '';
    el.answers.innerHTML = '<textarea class="aqmi-text-input" data-qid="' + q.id + '" data-idx="' + index + '" rows="3" placeholder="Saisissez votre réponse..." style="width:100%;padding:0.85rem 1rem;background:rgba(255,255,255,0.03);border:1px solid var(--aqmi-border);border-radius:var(--aqmi-radius-sm);color:var(--aqmi-text);font-family:var(--aqmi-font);font-size:0.9rem;outline:none;resize:vertical;transition:border-color 0.3s;">' + val + '</textarea>';
  }

  function renderNumericInput(q, index) {
    el.ratingGrid.style.display = 'none';
    el.answers.style.display = '';
    var val = q.answered && q.answer_value ? q.answer_value : '';
    el.answers.innerHTML = '<input type="number" class="aqmi-numeric-input" data-qid="' + q.id + '" data-idx="' + index + '" placeholder="Saisissez une valeur" style="width:200px;max-width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.03);border:1px solid var(--aqmi-border);border-radius:var(--aqmi-radius-sm);color:var(--aqmi-text);font-family:var(--aqmi-font);font-size:0.9rem;outline:none;transition:border-color 0.3s;" value="' + val + '">';
  }

  function renderRatingScale(q, index) {
    el.answers.style.display = 'none';
    el.ratingGrid.style.display = '';

    var colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#06b6d4', '#8b5cf6'];
    var labels = ['Inexistant', 'Initial', 'Basique', 'Maîtrisé', 'Performant', 'Excellence'];

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
      updateGauge();
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
  function updateGauge() {
    if (answeredCount === 0) {
      el.gauge.classList.remove('show');
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

    var circumference = 106.8;
    var offset = circumference - (pct / 100) * circumference;

    var proxy = { offset: parseFloat(el.gaugeCircle.style.strokeDashoffset) || circumference };
    gsap.to(proxy, {
      offset: offset,
      duration: 0.8,
      ease: 'power2.out',
      onUpdate: function() {
        el.gaugeCircle.style.strokeDashoffset = proxy.offset;
      }
    });

    var color = pct >= 70 ? 'var(--aqmi-success)' : (pct >= 40 ? 'var(--aqmi-warning)' : 'var(--aqmi-danger)');
    el.gaugeCircle.style.stroke = color;
    el.gaugeValue.style.color = color;

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

  // ── Confetti Celebration ──
  function createConfetti() {
    var container = document.createElement('div');
    container.className = 'aqmi-confetti-container';
    container.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99999;overflow:hidden;';
    document.body.appendChild(container);

    var colors = ['#3b82f6', '#8b5cf6', '#22c55e', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'];
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
      '  <h2 class="aqmi-completion-title">Questionnaire terminé !</h2>',
      '  <p class="aqmi-completion-desc">Merci d\'avoir répondu à toutes les questions. Nous préparons votre diagnostic personnalisé.</p>',
      '  <a href="/assessment/' + assessmentId + '/complete" class="aqmi-completion-btn">',
      '    Voir mes résultats <i class="fas fa-arrow-right"></i>',
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
    if (e.key === 'ArrowRight' || e.key === ' ') {
      e.preventDefault();
      if (el.nextBtn.style.display !== 'none') {
        goNext();
      }
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      goPrev();
    } else if (e.key >= '1' && e.key <= '4') {
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
      updateGauge();

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

  // ── Show keyboard hint after 3s ──
  setTimeout(function() {
    if (el.keyboardHint) el.keyboardHint.classList.add('show');
  }, 3000);

  // ── Init ──
  function init() {
    createParticles();
    renderQuestion(currentIdx);
    updateProgress();
    updateGauge();
    gsap.from(el.questionInner, { opacity: 0, y: 20, duration: 0.5, ease: 'power2.out' });
  }

  init();

})();
