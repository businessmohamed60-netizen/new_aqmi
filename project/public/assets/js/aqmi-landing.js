/* ═══════════════════════════════════════════════════════════════════
   AQMI — Automotive Quality Maturity Index
   Premium Landing Page Animations
   ═══════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  const APP = {};

  // ─── DOM Cache ──────────────────────────────────────────────────
  APP.cache = {};

  function cacheDOM() {
    APP.cache.page = document.querySelector('.aqmi-page');
    if (!APP.cache.page) return false;
    APP.cache.hero = APP.cache.page.querySelector('.aqmi-hero');
    APP.cache.canvas = APP.cache.page.querySelector('.aqmi-hero-canvas');
    APP.cache.sections = APP.cache.page.querySelectorAll('.aqmi-section-fade');
    APP.cache.scoreLevels = APP.cache.page.querySelectorAll('.aqmi-score-level');
    APP.cache.domainCards = APP.cache.page.querySelectorAll('.aqmi-domain-card');
    APP.cache.featureCards = APP.cache.page.querySelectorAll('.aqmi-feature-card');
    APP.cache.processSteps = APP.cache.page.querySelectorAll('.aqmi-process-step');
    return true;
  }

  // ─── Canvas roundRect polyfill ──────────────────────────────────
  if (!CanvasRenderingContext2D.prototype.roundRect) {
    CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r) {
      r = Math.min(r, w / 2, h / 2);
      this.moveTo(x + r, y);
      this.lineTo(x + w - r, y);
      this.quadraticCurveTo(x + w, y, x + w, y + r);
      this.lineTo(x + w, y + h - r);
      this.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
      this.lineTo(x + r, y + h);
      this.quadraticCurveTo(x, y + h, x, y + h - r);
      this.lineTo(x, y + r);
      this.quadraticCurveTo(x, y, x + r, y);
      this.closePath();
    };
  }

  // ─── Hero Quality Globe Canvas ──────────────────────────────────
  APP.globe = {
    particles: [],
    mouseX: 0,
    mouseY: 0,
    rotation: 0,
    animId: null
  };

  APP.initGlobe = function() {
    const canvas = APP.cache.canvas;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    function resize() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const radius = Math.min(canvas.width, canvas.height) * 0.22;
    const count = 250;

    APP.globe.particles = [];
    for (let i = 0; i < count; i++) {
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(2 * Math.random() - 1);
      APP.globe.particles.push({
        theta: theta,
        phi: phi,
        size: 1 + Math.random() * 2.5,
        color: Math.random() > 0.5 ? '#22c55e' : Math.random() > 0.5 ? '#2563eb' : '#06b6d4',
        speed: 0.3 + Math.random() * 0.5,
        glow: Math.random() > 0.7
      });
    }

    function project3D(x, y, z) {
      const fl = 350;
      const scale = fl / (fl + z + radius);
      return {
        x: x * scale + canvas.width / 2,
        y: y * scale + canvas.height / 2,
        scale: scale
      };
    }

    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const rotX = APP.globe.mouseY * 0.008;
      const rotY = APP.globe.rotation + APP.globe.mouseX * 0.008;

      const positions = [];
      APP.globe.particles.forEach(function(p) {
        const theta = p.theta + rotY;
        const phi = p.phi + rotX;
        const x = radius * Math.sin(phi) * Math.cos(theta);
        const y = radius * Math.cos(phi);
        const z = radius * Math.sin(phi) * Math.sin(theta);
        positions.push({ x: x, y: y, z: z, color: p.color, size: p.size, glow: p.glow });
      });

      // Sort by z
      const sorted = positions.map(function(p, i) { return { idx: i, z: p.z }; });
      sorted.sort(function(a, b) { return a.z - b.z; });

      // Draw ring
      ctx.beginPath();
      ctx.arc(canvas.width / 2, canvas.height / 2, radius + 10, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(34, 197, 94, 0.04)';
      ctx.lineWidth = 1;
      ctx.stroke();

      ctx.beginPath();
      ctx.arc(canvas.width / 2, canvas.height / 2, radius + 20, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(37, 99, 235, 0.02)';
      ctx.lineWidth = 0.5;
      ctx.stroke();

      // Draw particles
      sorted.forEach(function(s) {
        const p = positions[s.idx];
        const proj = project3D(p.x, p.y, p.z);
        const alpha = 0.3 + 0.7 * (p.z + radius) / (radius * 2);
        const size = p.size * proj.scale;
        if (size < 0.5) return;

        ctx.beginPath();
        ctx.arc(proj.x, proj.y, size, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.globalAlpha = alpha * 0.8;
        ctx.fill();

        // Glow
        if (p.glow && size > 1.5) {
          ctx.beginPath();
          ctx.arc(proj.x, proj.y, size * 4, 0, Math.PI * 2);
          ctx.fillStyle = p.color;
          ctx.globalAlpha = 0.04;
          ctx.fill();
        }

        ctx.globalAlpha = 1;
      });

      APP.globe.rotation += 0.002;
      APP.globe.animId = requestAnimationFrame(draw);
    }

    draw();

    document.addEventListener('mousemove', function(e) {
      const rect = canvas.getBoundingClientRect();
      APP.globe.mouseX = (e.clientX - rect.left - rect.width / 2) / rect.width;
      APP.globe.mouseY = (e.clientY - rect.top - rect.height / 2) / rect.height;
    });
  };

  // ─── GSAP Animations ───────────────────────────────────────────
  APP.initGSAP = function() {
    if (typeof gsap === 'undefined') {
      var check = setInterval(function() {
        if (typeof gsap !== 'undefined') {
          clearInterval(check);
          APP.setupGSAP();
        }
      }, 200);
      setTimeout(function() { clearInterval(check); }, 10000);
      return;
    }
    APP.setupGSAP();
  };

  APP.setupGSAP = function() {
    // ─── 1. Hero Word Animation ────────────────────────────────
    var heroTl = gsap.timeline({ delay: 0.2, defaults: { ease: 'power3.out' } });

    var badge = document.querySelector('.aqmi-hero-badge');
    var words = document.querySelectorAll('.aqmi-hero-word');
    var subtitle = document.querySelector('.aqmi-hero-subtitle');
    var tagline = document.querySelector('.aqmi-hero-tagline');
    var actions = document.querySelector('.aqmi-hero-actions');
    var stats = document.querySelector('.aqmi-hero-stats');
    var scroll = document.querySelector('.aqmi-hero-scroll');

    if (badge) heroTl.fromTo(badge, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.6 });
    if (words.length) {
      heroTl.fromTo(words,
        { opacity: 0, y: 40, rotateX: -20 },
        { opacity: 1, y: 0, rotateX: 0, duration: 0.7, stagger: 0.05 },
        '-=0.2'
      );
    }
    if (subtitle) heroTl.fromTo(subtitle, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.5 }, '-=0.2');
    if (tagline) heroTl.fromTo(tagline, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.5 }, '-=0.3');
    if (actions) heroTl.fromTo(actions, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.6 }, '-=0.2');
    if (stats) heroTl.fromTo(stats, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.6 }, '-=0.1');
    if (scroll) heroTl.fromTo(scroll, { opacity: 0 }, { opacity: 1, duration: 0.5 }, '-=0.3');

    // ─── 2. Hero Parallax ─────────────────────────────────────
    gsap.to('.aqmi-hero-content', {
      y: 60,
      ease: 'none',
      scrollTrigger: { trigger: '.aqmi-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });
    gsap.to('.aqmi-hero-canvas', {
      scale: 1.08,
      ease: 'none',
      scrollTrigger: { trigger: '.aqmi-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });
    gsap.to('.aqmi-hero-overlay', {
      opacity: 0.3,
      ease: 'none',
      scrollTrigger: { trigger: '.aqmi-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });

    // ─── 3. Score Levels ──────────────────────────────────────
    var scoreLevels = document.querySelectorAll('.aqmi-score-level');
    scoreLevels.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95 },
        { opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-score-visual', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 4. Domain Cards ──────────────────────────────────────
    var domainCards = document.querySelectorAll('.aqmi-domain-card');
    domainCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-domains-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 5. Feature Cards ─────────────────────────────────────
    var featureCards = document.querySelectorAll('.aqmi-feature-card');
    featureCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-features-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 6. Process Steps ─────────────────────────────────────
    var processSteps = document.querySelectorAll('.aqmi-process-step');
    processSteps.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95 },
        { opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-process-steps', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 7. Report Card ──────────────────────────────────────
    var reportCard = document.querySelector('.aqmi-report-card');
    if (reportCard) {
      gsap.fromTo(reportCard,
        { opacity: 0, y: 30, scale: 0.97 },
        { opacity: 1, y: 0, scale: 1, duration: 0.8, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-report-card', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    }

    // Report domain bars animation
    var domainFills = document.querySelectorAll('.aqmi-report-domain-fill');
    domainFills.forEach(function(fill) {
      var w = fill.style.width;
      gsap.fromTo(fill, { width: '0%' }, { width: w, duration: 1, ease: 'power2.out',
        scrollTrigger: { trigger: '.aqmi-report-domains', start: 'top 75%', end: 'top 40%', scrub: 0.5 }
      });
    });

    // ─── 8. Benchmark Cards ──────────────────────────────────
    var benchmarkCards = document.querySelectorAll('.aqmi-benchmark-card');
    benchmarkCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95 },
        { opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-benchmark-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 9. Why AQMI Cards ───────────────────────────────────
    var whyCards = document.querySelectorAll('.aqmi-why-card');
    whyCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.6, ease: 'power2.out',
          scrollTrigger: { trigger: '.aqmi-why-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 10. CTA ──────────────────────────────────────────────
    gsap.fromTo('.aqmi-cta-content',
      { opacity: 0, y: 40, scale: 0.97 },
      { opacity: 1, y: 0, scale: 1, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '.aqmi-cta', start: 'top 80%', end: 'top 50%', scrub: 0.5 }
      }
    );

    ScrollTrigger.refresh();
  };

  // ─── Init ───────────────────────────────────────────────────────
  APP.init = function() {
    if (!cacheDOM()) return;
    APP.initGlobe();
    APP.initGSAP();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', APP.init);
  } else {
    APP.init();
  }

})();