/* ============================================
   NOVAQYS LMS — Premium Interactive Animations
   ============================================ */

(function() {
  'use strict';

  // ============================================
  // CANVAS HERO — Immersive Academy Scene
  // ============================================
  function initHeroCanvas() {
    var canvas = document.querySelector('.lms-hero-canvas');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var W, H;
    var mouseX = 0, mouseY = 0;
    var time = 0;
    var animId;

    function resize() {
      W = canvas.width = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);
    document.addEventListener('mousemove', function(e) {
      mouseX = (e.clientX / W - 0.5) * 2;
      mouseY = (e.clientY / H - 0.5) * 2;
    });

    // --- Floating Holographic Screens ---
    var screens = [];
    var screenTexts = ['IATF 16949', 'APQP', 'PPAP', 'FMEA', 'SPC', 'MSA', '8D', 'Lean', 'QRQC', 'ISO 9001'];
    for (var i = 0; i < 10; i++) {
      screens.push({
        x: (Math.random() - 0.5) * 500,
        y: (Math.random() - 0.5) * 400 - 50,
        z: Math.random() * 300 + 100,
        w: 90 + Math.random() * 60,
        h: 50 + Math.random() * 30,
        text: screenTexts[i % screenTexts.length],
        speed: 0.2 + Math.random() * 0.3,
        phase: Math.random() * Math.PI * 2,
        opacity: 0.15 + Math.random() * 0.2,
        color: ['#4fc3f7','#1a73e8','#00d4ff','#7367f0','#28c76f'][i % 5]
      });
    }

    // --- Floating Books ---
    var books = [];
    for (var i = 0; i < 8; i++) {
      books.push({
        x: (Math.random() - 0.5) * 600,
        y: (Math.random() - 0.5) * 500,
        z: Math.random() * 200 + 50,
        rot: Math.random() * Math.PI * 2,
        size: 20 + Math.random() * 15,
        speed: 0.15 + Math.random() * 0.25,
        phase: Math.random() * Math.PI * 2,
        color: ['#1a73e8','#4fc3f7','#7367f0','#28c76f','#ff9f43'][i % 5]
      });
    }

    // --- Certificates ---
    var certs = [];
    for (var i = 0; i < 5; i++) {
      certs.push({
        x: (Math.random() - 0.5) * 700,
        y: (Math.random() - 0.5) * 500,
        z: Math.random() * 150 + 30,
        rot: (Math.random() - 0.5) * 0.3,
        speed: 0.1 + Math.random() * 0.2,
        phase: Math.random() * Math.PI * 2,
        opacity: 0.1 + Math.random() * 0.15
      });
    }

    // --- Knowledge Flow Particles ---
    var particles = [];
    for (var i = 0; i < 150; i++) {
      particles.push({
        x: (Math.random() - 0.5) * 800,
        y: (Math.random() - 0.5) * 600,
        z: Math.random() * 400,
        vx: (Math.random() - 0.5) * 0.5,
        vy: (Math.random() - 0.5) * 0.5,
        vz: (Math.random() - 0.5) * 0.3,
        size: 1 + Math.random() * 2,
        opacity: 0.2 + Math.random() * 0.5,
        trail: [],
        trailLen: 5 + Math.floor(Math.random() * 10)
      });
    }

    // --- Background Particles ---
    var bgParticles = [];
    for (var i = 0; i < 80; i++) {
      bgParticles.push({
        x: Math.random() * 2000 - 500,
        y: Math.random() * 2000 - 500,
        z: Math.random() * 600 + 200,
        size: 0.5 + Math.random() * 1.5,
        opacity: 0.1 + Math.random() * 0.3
      });
    }

    function project(x, y, z, pitch, yaw) {
      var cosP = Math.cos(pitch), sinP = Math.sin(pitch);
      var cosY = Math.cos(yaw), sinY = Math.sin(yaw);
      var x1 = x * cosY - z * sinY;
      var z1 = x * sinY + z * cosY;
      var x2 = x1;
      var y2 = y * cosP - z1 * sinP;
      var z2 = y * sinP + z1 * cosP;
      var fov = 600;
      var scale = fov / (z2 + fov + 400);
      var sx = x2 * scale + W / 2;
      var sy = y2 * scale + H / 2;
      return { x: sx, y: sy, scale: scale, z: z2 };
    }

    function draw() {
      ctx.clearRect(0, 0, W, H);

      // Background gradient
      var grad = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, W*0.7);
      grad.addColorStop(0, '#0f172a');
      grad.addColorStop(0.5, '#0a0e17');
      grad.addColorStop(1, '#030712');
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, W, H);

      var pitch = mouseY * 0.05;
      var yaw = time * 0.0001 + mouseX * 0.05;

      // Draw background particles
      for (var i = 0; i < bgParticles.length; i++) {
        var p = bgParticles[i];
        var proj = project(p.x, p.y, p.z, pitch, yaw);
        if (proj.scale > 0 && proj.z > -200) {
          ctx.globalAlpha = p.opacity * Math.min(1, proj.scale * 2);
          ctx.fillStyle = '#4fc3f7';
          ctx.beginPath();
          ctx.arc(proj.x, proj.y, p.size * proj.scale, 0, Math.PI * 2);
          ctx.fill();
        }
      }

      // Draw knowledge flow particles with trails
      for (var i = 0; i < particles.length; i++) {
        var p = particles[i];
        p.x += p.vx;
        p.y += p.vy;
        p.z += p.vz;
        if (Math.abs(p.x) > 500) p.vx *= -1;
        if (Math.abs(p.y) > 400) p.vy *= -1;
        if (Math.abs(p.z) > 300) p.vz *= -1;

        p.trail.push({ x: p.x, y: p.y, z: p.z });
        if (p.trail.length > p.trailLen) p.trail.shift();

        // Draw trail
        for (var t = 0; t < p.trail.length; t++) {
          var tp = p.trail[t];
          var tProj = project(tp.x, tp.y, tp.z, pitch, yaw);
          var alpha = (t / p.trail.length) * p.opacity * 0.4;
          if (tProj.scale > 0 && tProj.z > -200) {
            ctx.globalAlpha = alpha * Math.min(1, tProj.scale * 2);
            ctx.fillStyle = '#00d4ff';
            ctx.beginPath();
            ctx.arc(tProj.x, tProj.y, p.size * 0.3 * tProj.scale, 0, Math.PI * 2);
            ctx.fill();
          }
        }

        // Draw particle
        var proj = project(p.x, p.y, p.z, pitch, yaw);
        if (proj.scale > 0 && proj.z > -200) {
          ctx.globalAlpha = p.opacity * Math.min(1, proj.scale * 2);
          ctx.fillStyle = '#4fc3f7';
          ctx.beginPath();
          ctx.arc(proj.x, proj.y, p.size * proj.scale, 0, Math.PI * 2);
          ctx.fill();

          // Glow
          ctx.globalAlpha = p.opacity * 0.15 * Math.min(1, proj.scale * 2);
          ctx.shadowColor = '#4fc3f7';
          ctx.shadowBlur = 20;
          ctx.beginPath();
          ctx.arc(proj.x, proj.y, p.size * 2 * proj.scale, 0, Math.PI * 2);
          ctx.fill();
          ctx.shadowBlur = 0;
        }
      }

      // Draw floating books
      for (var i = 0; i < books.length; i++) {
        var b = books[i];
        var offsetY = Math.sin(time * 0.001 * b.speed + b.phase) * 20;
        var proj = project(b.x, b.y + offsetY, b.z, pitch, yaw);
        if (proj.scale > 0 && proj.z > -200) {
          ctx.save();
          ctx.translate(proj.x, proj.y);
          ctx.scale(proj.scale, proj.scale);
          ctx.globalAlpha = 0.5 * Math.min(1, proj.scale * 2);
          var angle = b.rot + time * 0.0003;
          ctx.rotate(angle);

          // Book cover
          ctx.fillStyle = b.color;
          ctx.shadowColor = b.color;
          ctx.shadowBlur = 15;
          ctx.fillRect(-b.size/2, -b.size * 0.7, b.size, b.size * 1.4);
          ctx.shadowBlur = 0;

          // Book spine line
          ctx.fillStyle = 'rgba(255,255,255,0.2)';
          ctx.fillRect(-1, -b.size * 0.6, 2, b.size * 1.2);

          // Book detail
          ctx.fillStyle = 'rgba(255,255,255,0.1)';
          ctx.fillRect(-b.size/4, -b.size * 0.3, b.size/2, 2);

          ctx.restore();
        }
      }

      // Draw certificates
      for (var i = 0; i < certs.length; i++) {
        var c = certs[i];
        var offsetY = Math.sin(time * 0.001 * c.speed + c.phase + 1) * 15;
        var proj = project(c.x, c.y + offsetY, c.z, pitch, yaw);
        if (proj.scale > 0 && proj.z > -200) {
          ctx.save();
          ctx.translate(proj.x, proj.y);
          ctx.scale(proj.scale, proj.scale);
          ctx.globalAlpha = c.opacity * Math.min(1, proj.scale * 2);
          ctx.rotate(c.rot);

          // Certificate outline
          ctx.strokeStyle = '#4fc3f7';
          ctx.lineWidth = 1.5;
          ctx.shadowColor = '#4fc3f7';
          ctx.shadowBlur = 10;
          ctx.strokeRect(-30, -20, 60, 40);
          ctx.shadowBlur = 0;

          // Certificate seal
          ctx.strokeStyle = '#00d4ff';
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.arc(20, -10, 8, 0, Math.PI * 2);
          ctx.stroke();

          // Certificate text line
          ctx.fillStyle = 'rgba(255,255,255,0.3)';
          ctx.fillRect(-15, -2, 30, 1);

          ctx.restore();
        }
      }

      // Draw holographic screens
      for (var i = 0; i < screens.length; i++) {
        var s = screens[i];
        var offsetY = Math.sin(time * 0.001 * s.speed + s.phase) * 15;
        var proj = project(s.x, s.y + offsetY, s.z, pitch, yaw);
        if (proj.scale > 0 && proj.z > -200) {
          ctx.save();
          ctx.translate(proj.x, proj.y);
          ctx.scale(proj.scale, proj.scale);
          ctx.globalAlpha = s.opacity * Math.min(1, proj.scale * 2);

          // Screen glow
          ctx.shadowColor = s.color;
          ctx.shadowBlur = 20;
          ctx.strokeStyle = s.color;
          ctx.lineWidth = 1;
          ctx.strokeRect(-s.w/2, -s.h/2, s.w, s.h);
          ctx.shadowBlur = 0;

          // Screen fill
          ctx.fillStyle = 'rgba(15,23,42,0.6)';
          ctx.fillRect(-s.w/2, -s.h/2, s.w, s.h);

          // Screen inner border
          ctx.strokeStyle = 'rgba(255,255,255,0.05)';
          ctx.lineWidth = 0.5;
          ctx.strokeRect(-s.w/2 + 4, -s.h/2 + 4, s.w - 8, s.h - 8);

          // Screen text
          ctx.fillStyle = s.color;
          ctx.font = s.w > 100 ? '9px Inter, sans-serif' : '7px Inter, sans-serif';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.fillText(s.text, 0, 0);

          // Scan line
          ctx.globalAlpha = 0.05;
          ctx.fillStyle = '#fff';
          var scanY = (time * 0.02) % s.h - s.h/2;
          ctx.fillRect(-s.w/2, scanY, s.w, 1);

          ctx.restore();
        }
      }

      // Draw center glow
      var cx = W/2 + mouseX * 20;
      var cy = H/2 + mouseY * 15;
      var cGrad = ctx.createRadialGradient(cx, cy, 0, cx, cy, 200);
      cGrad.addColorStop(0, 'rgba(26,115,232,0.04)');
      cGrad.addColorStop(0.5, 'rgba(26,115,232,0.02)');
      cGrad.addColorStop(1, 'transparent');
      ctx.fillStyle = cGrad;
      ctx.fillRect(0, 0, W, H);

      ctx.globalAlpha = 1;
      time++;
      animId = requestAnimationFrame(draw);
    }

    draw();

    // Cleanup
    window.addEventListener('beforeunload', function() {
      if (animId) cancelAnimationFrame(animId);
    });
  }

  // ============================================
  // GSAP ANIMATIONS
  // ============================================
  function initGSAP() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      // Fallback: use IntersectionObserver
      initFallbackAnimations();
      return;
    }

    // --- Journey Timeline ---
    var journeyLine = document.querySelector('.lms-journey-line-fill');
    var journeySteps = document.querySelectorAll('.lms-journey-step');
    if (journeyLine && journeySteps.length) {
      gsap.to(journeyLine, {
        height: '100%',
        ease: 'none',
        scrollTrigger: {
          trigger: '.lms-journey-track',
          start: 'top 15%',
          end: 'bottom 65%',
          scrub: 0.5
        }
      });

      journeySteps.forEach(function(step, i) {
        var card = step.querySelector('.lms-journey-card');
        var dot = step.querySelector('.lms-journey-dot');
        if (card) {
          gsap.fromTo(card, { opacity: 0.3, y: 30, scale: 0.97, filter: 'blur(3px)' },
            { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 1, ease: 'power3.out',
              scrollTrigger: { trigger: step, start: 'top 80%', end: 'top 45%', scrub: 0.6 } });
        }
        if (dot) {
          ScrollTrigger.create({
            trigger: step,
            start: 'top 75%',
            onEnter: function() { dot.classList.add('active'); },
            onLeaveBack: function() { dot.classList.remove('active'); }
          });
        }
      });
    }

    // --- Normes Cards ---
    var normeCards = document.querySelectorAll('.lms-norme-card');
    if (normeCards.length) {
      gsap.from(normeCards, {
        opacity: 0, y: 40, scale: 0.95, duration: 0.8, stagger: 0.08, ease: 'power3.out',
        scrollTrigger: { trigger: '.lms-normes-wall', start: 'top 82%', toggleActions: 'play none none none' }
      });
    }

    // --- Core Tools Cards ---
    var toolCards = document.querySelectorAll('.lms-tool-card');
    if (toolCards.length) {
      gsap.from(toolCards, {
        opacity: 0, y: 50, duration: 0.8, stagger: 0.1, ease: 'power3.out',
        scrollTrigger: { trigger: '.lms-tools-grid', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }

    // --- Méthodes Cards ---
    var methodeCards = document.querySelectorAll('.lms-methode-card');
    if (methodeCards.length) {
      gsap.from(methodeCards, {
        opacity: 0, y: 40, scale: 0.92, duration: 0.7, stagger: 0.07, ease: 'back.out(1.2)',
        scrollTrigger: { trigger: '.lms-methodes-grid', start: 'top 82%', toggleActions: 'play none none none' }
      });
    }

    // --- Catalogue Cards ---
    var catalogueCards = document.querySelectorAll('.lms-catalogue-card');
    if (catalogueCards.length) {
      gsap.from(catalogueCards, {
        opacity: 0, x: 60, duration: 0.8, stagger: 0.1, ease: 'power3.out',
        scrollTrigger: { trigger: '.lms-catalogue', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }

    // --- Dashboard Counter ---
    var dashValues = document.querySelectorAll('.lms-dash-value');
    if (dashValues.length) {
      dashValues.forEach(function(el) {
        var target = parseFloat(el.getAttribute('data-target') || el.textContent.replace(/[^0-9.]/g, ''));
        var suffix = el.textContent.replace(/[0-9.]/g, '');
        gsap.fromTo(el, { textContent: 0 }, {
          textContent: target, duration: 2, ease: 'power2.out',
          snap: { textContent: target > 100 ? 1 : 0.1 },
          scrollTrigger: { trigger: el.closest('.lms-dash-grid'), start: 'top 80%', toggleActions: 'play none none none' },
          onUpdate: function() {
            if (suffix && target > 0) {
              el.textContent = Math.round(parseFloat(el.textContent)) + suffix;
            }
          }
        });
      });
    }

    // --- Skills Progress Bars ---
    var skillFills = document.querySelectorAll('.lms-skill-bar-fill');
    if (skillFills.length) {
      skillFills.forEach(function(el) {
        var target = parseFloat(el.getAttribute('data-width') || '0');
        gsap.to(el, {
          width: target + '%', duration: 1.5, ease: 'power3.out',
          scrollTrigger: { trigger: el.closest('.lms-skills-grid'), start: 'top 80%', toggleActions: 'play none none none' }
        });
      });
    }

    // --- IA Reco Items ---
    var recoItems = document.querySelectorAll('.lms-ia-reco');
    if (recoItems.length) {
      gsap.from(recoItems, {
        opacity: 0, x: -30, duration: 0.6, stagger: 0.1, ease: 'power3.out',
        scrollTrigger: { trigger: '.lms-ia-coach', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }

    // --- Ecosystem Nodes ---
    var ecoNodes = document.querySelectorAll('.lms-eco-node');
    if (ecoNodes.length) {
      gsap.from(ecoNodes, {
        opacity: 0, x: -40, duration: 0.8, stagger: 0.15, ease: 'power3.out',
        scrollTrigger: { trigger: '.lms-eco-flow', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }
  }

  // ============================================
  // FALLBACK INTERSECTION OBSERVER
  // ============================================
  function initFallbackAnimations() {
    var animateEls = document.querySelectorAll('.lms-fade-up, .lms-stagger');
    if (!animateEls.length) return;

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('lms-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    animateEls.forEach(function(el) { observer.observe(el); });
  }

  // ============================================
  // NAVIGATION SCROLL EFFECT
  // ============================================
  function initNav() {
    var nav = document.querySelector('.lms-nav');
    if (!nav) return;

    var ticking = false;
    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(function() {
          nav.classList.toggle('scrolled', window.scrollY > 60);
          ticking = false;
        });
        ticking = true;
      }
    });
  }

  // ============================================
  // QUIZ INTERACTION
  // ============================================
  function initQuiz() {
    var options = document.querySelectorAll('.lms-quiz-option');
    options.forEach(function(opt) {
      opt.addEventListener('click', function() {
        var parent = this.closest('.lms-quiz-options');
        if (!parent) return;
        parent.querySelectorAll('.lms-quiz-option').forEach(function(o) {
          o.classList.remove('selected', 'correct', 'wrong');
        });
        this.classList.add('selected');
      });
    });
  }

  // ============================================
  // CATALOGUE CARD HOVER
  // ============================================
  function initCatalogue() {
    var cards = document.querySelectorAll('.lms-catalogue-card');
    cards.forEach(function(card) {
      card.addEventListener('click', function() {
        var btn = this.querySelector('.lms-catalogue-btn');
        if (btn) btn.textContent = 'Lecture...';
      });
    });
  }

  // ============================================
  // INIT
  // ============================================
  function init() {
    initHeroCanvas();
    initNav();
    initQuiz();
    initCatalogue();

    // Wait for GSAP
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      // Small delay for DOM
      setTimeout(function() {
        initGSAP();
      }, 100);
    } else {
      // Try loading GSAP from CDN
      var checkGSAP = setInterval(function() {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
          clearInterval(checkGSAP);
          initGSAP();
        }
      }, 200);
      // Fallback after 5s
      setTimeout(function() {
        clearInterval(checkGSAP);
        initFallbackAnimations();
      }, 5000);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();