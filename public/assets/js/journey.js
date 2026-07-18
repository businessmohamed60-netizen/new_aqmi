/* ═══════════════════════════════════════════════════════════════════
   NOVAQYS — Interactive Journey Animations
   GSAP + ScrollTrigger powered experience
   ═══════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  // ─── URL Configuration ──────────────────────────────────────────
  const JOURNEY_URLS = {
    'decouverte': '/aqmi/register',
    'aqmi-starter': '/assessment/start',
    'formation-lms': '/lms',
    'digitalisation-qms': '/novaqys-qms',
    'aqmi-professional': '/assessment/start',
    'nara': '/nara',
    'asin': '/asin',
    'acces-constructeurs': '/asin',
    'developpement': '/contact'
  };

  // ─── DOM Refs ───────────────────────────────────────────────────
  let els = {};

  function cacheDOM() {
    els.section = document.querySelector('.journey-section');
    if (!els.section) return false;
    els.container = els.section.querySelector('.journey-container');
    els.path = els.section.querySelector('.journey-path');
    els.pathFill = els.section.querySelector('.journey-path-fill');
    els.pathGlow = els.section.querySelector('.journey-path-glow');
    els.steps = els.section.querySelectorAll('.journey-step');
    els.cards = els.section.querySelectorAll('.journey-step-card');
    els.gauges = els.section.querySelectorAll('.journey-gauge-fill');
    els.logoItems = els.section.querySelectorAll('.journey-logo-item');
    els.scoreBadges = els.section.querySelectorAll('.journey-score-badge');
    return true;
  }

  // ─── State ──────────────────────────────────────────────────────
  let state = {
    activeIndex: -1,
    isAnimating: false,
    totalSteps: 0,
    pathProgress: 0
  };

  // ─── Init GSAP Animations ───────────────────────────────────────
  function initGSAP() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      // Retry after GSAP loads
      if (typeof gsap === 'undefined') {
        const checkGSAP = setInterval(function() {
          if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            clearInterval(checkGSAP);
            setupScrollTrigger();
          }
        }, 200);
        // Timeout after 10s
        setTimeout(function() { clearInterval(checkGSAP); }, 10000);
      }
      return;
    }
    setupScrollTrigger();
  }

  function setupScrollTrigger() {
    if (!els.section || state.totalSteps === 0) return;

    // ─── Path Fill Animation ────────────────────────────────────
    gsap.to(els.pathFill, {
      height: '100%',
      ease: 'none',
      scrollTrigger: {
        trigger: els.section,
        start: 'top 10%',
        end: 'bottom 60%',
        scrub: 0.5,
        invalidateOnRefresh: true
      }
    });

    // ─── Path Glow Follower ─────────────────────────────────────
    if (els.pathGlow) {
      gsap.to(els.pathGlow, {
        top: '100%',
        ease: 'none',
        scrollTrigger: {
          trigger: els.section,
          start: 'top 10%',
          end: 'bottom 60%',
          scrub: 0.5,
          invalidateOnRefresh: true
        }
      });
    }

    // ─── Step Animations ────────────────────────────────────────
    els.steps.forEach(function(step, index) {
      const card = step.querySelector('.journey-step-card');
      if (!card) return;

      // Entry animation
      gsap.fromTo(card,
        {
          opacity: 0.3,
          y: 40,
          scale: 0.95,
          filter: 'blur(4px)'
        },
        {
          opacity: 1,
          y: 0,
          scale: 1,
          filter: 'blur(0px)',
          duration: 1.2,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: step,
            start: 'top 75%',
            end: 'top 35%',
            scrub: 0.8,
            invalidateOnRefresh: true,
            id: 'step-' + index
          }
        }
      );

      // Marker animation
      const dot = step.querySelector('.journey-step-dot');
      const dotInner = step.querySelector('.journey-step-dot-inner');
      if (dotInner) {
        gsap.fromTo(dotInner,
          { scale: 0, opacity: 0 },
          {
            scale: 1, opacity: 1,
            duration: 0.5,
            ease: 'back.out(2)',
            scrollTrigger: {
              trigger: step,
              start: 'top 70%',
              end: 'top 40%',
              scrub: 0.5,
              invalidateOnRefresh: true
            }
          }
        );
      }
    });

    // ─── Logo Items Stagger ─────────────────────────────────────
    els.logoItems.forEach(function(item) {
      gsap.fromTo(item,
        { opacity: 0, y: 10 },
        {
          opacity: 1, y: 0,
          duration: 0.4,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: item.closest('.journey-step'),
            start: 'top 60%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Score Badges Stagger ───────────────────────────────────
    els.scoreBadges.forEach(function(badge) {
      gsap.fromTo(badge,
        { opacity: 0, scale: 0.8 },
        {
          opacity: 1, scale: 1,
          duration: 0.5,
          ease: 'back.out(2)',
          scrollTrigger: {
            trigger: badge.closest('.journey-step'),
            start: 'top 60%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Gauge Fill ─────────────────────────────────────────────
    els.gauges.forEach(function(gauge) {
      const value = gauge.getAttribute('data-value') || '0%';
      gsap.fromTo(gauge,
        { width: '0%' },
        {
          width: value,
          duration: 1.5,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: gauge.closest('.journey-step'),
            start: 'top 65%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Active Step Tracking ───────────────────────────────────
    els.steps.forEach(function(step, index) {
      ScrollTrigger.create({
        trigger: step,
        start: 'top 65%',
        end: 'top 30%',
        onEnter: function() { setActiveStep(index); },
        onEnterBack: function() { setActiveStep(index); }
      });
    });

    // Refresh ScrollTrigger
    ScrollTrigger.refresh();
  }

  // ─── Active Step Management ─────────────────────────────────────
  function setActiveStep(index) {
    if (state.activeIndex === index) return;
    state.activeIndex = index;

    els.steps.forEach(function(step, i) {
      step.classList.remove('active', 'dimmed');
      if (i === index) {
        step.classList.add('active');
      } else if (i < index) {
        step.classList.add('dimmed');
      }
    });

    // Update path glow position
    updatePathGlow(index);
  }

  function updatePathGlow(index) {
    if (!els.pathGlow || !els.steps[index]) return;
    const step = els.steps[index];
    const marker = step.querySelector('.journey-step-marker');
    if (marker) {
      const rect = marker.getBoundingClientRect();
      const sectionRect = els.section.getBoundingClientRect();
      const relativeTop = (rect.top - sectionRect.top + rect.height / 2) / sectionRect.height * 100;
      els.pathGlow.style.top = Math.min(100, Math.max(0, relativeTop)) + '%';
      els.pathGlow.style.opacity = '1';
    }
  }

  // ─── Particles ──────────────────────────────────────────────────
  function createParticles() {
    if (!els.section) return;
    const container = document.createElement('div');
    container.className = 'journey-particles';
    els.section.appendChild(container);

    const colors = ['#7367f0', '#00cfe8', '#28c76f', '#9b8cf7', '#ff9f43'];
    for (let i = 0; i < 30; i++) {
      const particle = document.createElement('div');
      particle.className = 'journey-particle';
      const size = 1.5 + Math.random() * 2.5;
      particle.style.width = size + 'px';
      particle.style.height = size + 'px';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.background = colors[Math.floor(Math.random() * colors.length)];
      particle.style.animationDuration = (6 + Math.random() * 8) + 's';
      particle.style.animationDelay = (Math.random() * 10) + 's';
      container.appendChild(particle);
    }
  }

  // ─── Init ───────────────────────────────────────────────────────
  function init() {
    if (!cacheDOM()) return;
    state.totalSteps = els.steps.length;
    if (state.totalSteps === 0) return;

    createParticles();
    initGSAP();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();