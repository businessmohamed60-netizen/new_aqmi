/* ============================================
   AQMI STARTER — Premium Interactive Animations
   ============================================ */

(function() {
  'use strict';

  // ============================================
  // CANVAS HERO
  // ============================================
  function initHeroCanvas() {
    var canvas = document.querySelector('.aqmi-hero-canvas');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var W, H, time = 0, animId;
    var mouseX = 0, mouseY = 0;

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

    // --- Factory elements ---
    var machines = [];
    for (var i = 0; i < 8; i++) {
      machines.push({
        x: (Math.random() - 0.5) * 600,
        y: (Math.random() - 0.5) * 300 + 100,
        z: Math.random() * 200 + 100,
        w: 30 + Math.random() * 40,
        h: 50 + Math.random() * 30,
        color: ['#4fc3f7','#7367f0','#00d4ff','#28c76f'][i % 4],
        phase: Math.random() * Math.PI * 2,
        speed: 0.3 + Math.random() * 0.3
      });
    }

    // --- Data flow particles ---
    var particles = [];
    for (var i = 0; i < 120; i++) {
      particles.push({
        x: (Math.random() - 0.5) * 800,
        y: (Math.random() - 0.5) * 500,
        z: Math.random() * 400,
        vx: (Math.random() - 0.5) * 0.6,
        vy: (Math.random() - 0.5) * 0.6,
        size: 1 + Math.random() * 2.5,
        opacity: 0.2 + Math.random() * 0.5,
        trail: [],
        trailLen: 5 + Math.floor(Math.random() * 8)
      });
    }

    // --- Background stars ---
    var stars = [];
    for (var i = 0; i < 60; i++) {
      stars.push({
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
      return { x: x2 * scale + W/2, y: y2 * scale + H/2, scale: scale, z: z2 };
    }

    function draw() {
      ctx.clearRect(0, 0, W, H);

      var grad = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, W*0.7);
      grad.addColorStop(0, '#0f172a');
      grad.addColorStop(0.5, '#0a0e17');
      grad.addColorStop(1, '#030712');
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, W, H);

      var pitch = mouseY * 0.05;
      var yaw = time * 0.0001 + mouseX * 0.05;

      // Stars
      for (var i = 0; i < stars.length; i++) {
        var s = stars[i];
        var p = project(s.x, s.y, s.z, pitch, yaw);
        if (p.scale > 0 && p.z > -200) {
          ctx.globalAlpha = s.opacity * Math.min(1, p.scale * 2);
          ctx.fillStyle = '#4fc3f7';
          ctx.beginPath();
          ctx.arc(p.x, p.y, s.size * p.scale, 0, Math.PI * 2);
          ctx.fill();
        }
      }

      // Data particles with trails
      for (var i = 0; i < particles.length; i++) {
        var pt = particles[i];
        pt.x += pt.vx;
        pt.y += pt.vy;
        if (Math.abs(pt.x) > 500) pt.vx *= -1;
        if (Math.abs(pt.y) > 400) pt.vy *= -1;

        pt.trail.push({ x: pt.x, y: pt.y, z: pt.z });
        if (pt.trail.length > pt.trailLen) pt.trail.shift();

        for (var t = 0; t < pt.trail.length; t++) {
          var tp = pt.trail[t];
          var tpP = project(tp.x, tp.y, tp.z, pitch, yaw);
          if (tpP.scale > 0 && tpP.z > -200) {
            ctx.globalAlpha = (t / pt.trail.length) * pt.opacity * 0.3;
            ctx.fillStyle = '#00d4ff';
            ctx.beginPath();
            ctx.arc(tpP.x, tpP.y, pt.size * 0.3 * tpP.scale, 0, Math.PI * 2);
            ctx.fill();
          }
        }

        var ptP = project(pt.x, pt.y, pt.z, pitch, yaw);
        if (ptP.scale > 0 && ptP.z > -200) {
          ctx.globalAlpha = pt.opacity * Math.min(1, ptP.scale * 2);
          ctx.fillStyle = '#4fc3f7';
          ctx.beginPath();
          ctx.arc(ptP.x, ptP.y, pt.size * ptP.scale, 0, Math.PI * 2);
          ctx.fill();
          ctx.globalAlpha = pt.opacity * 0.1;
          ctx.shadowColor = '#4fc3f7';
          ctx.shadowBlur = 15;
          ctx.beginPath();
          ctx.arc(ptP.x, ptP.y, pt.size * 2 * ptP.scale, 0, Math.PI * 2);
          ctx.fill();
          ctx.shadowBlur = 0;
        }
      }

      // Machines (factory silhouette)
      for (var i = 0; i < machines.length; i++) {
        var m = machines[i];
        var offY = Math.sin(time * 0.001 * m.speed + m.phase) * 8;
        var mp = project(m.x, m.y + offY, m.z, pitch, yaw);
        if (mp.scale > 0 && mp.z > -200) {
          ctx.save();
          ctx.translate(mp.x, mp.y);
          ctx.scale(mp.scale, mp.scale);
          ctx.globalAlpha = 0.3 * Math.min(1, mp.scale * 2);

          // Machine body
          ctx.fillStyle = m.color;
          ctx.shadowColor = m.color;
          ctx.shadowBlur = 10;
          ctx.fillRect(-m.w/2, -m.h/2, m.w, m.h);
          ctx.shadowBlur = 0;

          // Machine detail
          ctx.fillStyle = 'rgba(255,255,255,0.15)';
          ctx.fillRect(-m.w/4, -m.h/4, m.w/2, 2);
          ctx.fillRect(-m.w/4, 0, m.w/2, 2);
          ctx.fillRect(-m.w/4, m.h/4, m.w/2, 2);

          ctx.restore();
        }
      }

      // Center glow
      var cx = W/2 + mouseX * 20;
      var cy = H/2 + mouseY * 15;
      var cg = ctx.createRadialGradient(cx, cy, 0, cx, cy, 250);
      cg.addColorStop(0, 'rgba(79,195,247,0.04)');
      cg.addColorStop(0.5, 'rgba(79,195,247,0.02)');
      cg.addColorStop(1, 'transparent');
      ctx.fillStyle = cg;
      ctx.fillRect(0, 0, W, H);

      ctx.globalAlpha = 1;
      time++;
      animId = requestAnimationFrame(draw);
    }

    draw();
    window.addEventListener('beforeunload', function() {
      if (animId) cancelAnimationFrame(animId);
    });
  }

  // ============================================
  // GSAP ANIMATIONS
  // ============================================
  function initGSAP() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      initFallback();
      return;
    }

    // --- Why Cards ---
    var whyCards = document.querySelectorAll('.aqmi-why-card');
    if (whyCards.length) {
      gsap.from(whyCards, {
        opacity: 0, y: 40, duration: 0.8, stagger: 0.1, ease: 'power3.out',
        scrollTrigger: { trigger: '.aqmi-why-grid', start: 'top 82%', toggleActions: 'play none none none' }
      });
    }

    // --- Analysis Cards ---
    var analysisCards = document.querySelectorAll('.aqmi-analysis-card');
    if (analysisCards.length) {
      gsap.from(analysisCards, {
        opacity: 0, y: 30, scale: 0.95, duration: 0.6, stagger: 0.08, ease: 'power3.out',
        scrollTrigger: { trigger: '.aqmi-analysis-grid', start: 'top 82%', toggleActions: 'play none none none' }
      });
    }

    // --- Score Ring ---
    var scoreFill = document.querySelector('.aqmi-score-fill');
    if (scoreFill) {
      ScrollTrigger.create({
        trigger: '.aqmi-score-display',
        start: 'top 80%',
        onEnter: function() {
          var circumference = 879.65;
          scoreFill.style.strokeDashoffset = (circumference * 0.32).toString();
          scoreFill.style.stroke = '#4fc3f7';
        }
      });
    }

    // --- Dashboard Counters ---
    var dashValues = document.querySelectorAll('.aqmi-dash-value');
    if (dashValues.length) {
      dashValues.forEach(function(el) {
        var target = parseFloat(el.getAttribute('data-target') || el.textContent.replace(/[^0-9.]/g, ''));
        var suffix = el.textContent.replace(/[0-9.]/g, '');
        gsap.fromTo(el, { textContent: 0 }, {
          textContent: target, duration: 2, ease: 'power2.out',
          snap: { textContent: target > 100 ? 1 : 0.1 },
          scrollTrigger: { trigger: el.closest('.aqmi-dash-grid'), start: 'top 80%', toggleActions: 'play none none none' },
          onUpdate: function() {
            if (suffix && target > 0) {
              var val = Math.round(parseFloat(el.textContent));
              el.textContent = val + suffix;
            }
          }
        });
      });
    }

    // --- Domain Bars ---
    var domainBars = document.querySelectorAll('.aqmi-domain-bar-fill');
    if (domainBars.length) {
      domainBars.forEach(function(el) {
        var target = parseFloat(el.getAttribute('data-width') || '0');
        gsap.to(el, {
          width: target + '%', duration: 1.5, ease: 'power3.out',
          scrollTrigger: { trigger: el.closest('.aqmi-domains-grid'), start: 'top 80%', toggleActions: 'play none none none' }
        });
      });
    }

    // --- Roadmap Steps ---
    var roadmapSteps = document.querySelectorAll('.aqmi-roadmap-step');
    if (roadmapSteps.length) {
      gsap.from(roadmapSteps, {
        opacity: 0, x: -40, duration: 0.8, stagger: 0.15, ease: 'power3.out',
        scrollTrigger: { trigger: '.aqmi-roadmap-flow', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }

    // --- Eco Nodes ---
    var ecoNodes = document.querySelectorAll('.aqmi-eco-node');
    if (ecoNodes.length) {
      gsap.from(ecoNodes, {
        opacity: 0, x: -40, duration: 0.8, stagger: 0.15, ease: 'power3.out',
        scrollTrigger: { trigger: '.aqmi-eco-flow', start: 'top 80%', toggleActions: 'play none none none' }
      });
    }
  }

  // ============================================
  // FALLBACK
  // ============================================
  function initFallback() {
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('aqmi-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    document.querySelectorAll('.aqmi-fade-up, .aqmi-stagger').forEach(function(el) {
      observer.observe(el);
    });
  }

  // ============================================
  // AUTO-ÉVALUATION INTERACTION
  // ============================================
  function initEval() {
    var opts = document.querySelectorAll('.aqmi-eval-opt');
    var gaugeFill = document.querySelector('.aqmi-eval-gauge-circle .aqmi-gauge-fill');
    var gaugeValue = document.querySelector('.aqmi-eval-gauge-value');
    var score = 0;
    var circumference = 502.65;

    opts.forEach(function(opt) {
      opt.addEventListener('click', function() {
        var parent = this.closest('.aqmi-eval-options');
        if (!parent) return;
        parent.querySelectorAll('.aqmi-eval-opt').forEach(function(o) {
          o.classList.remove('active');
        });
        this.classList.add('active');

        // Update score
        score = Math.min(100, score + 15 + Math.floor(Math.random() * 10));
        var offset = circumference - (circumference * score / 100);
        if (gaugeFill) gaugeFill.style.strokeDashoffset = offset.toString();
        if (gaugeValue) gaugeValue.textContent = score + '%';
      });
    });
  }

  // ============================================
  // SCORE ANIMATION
  // ============================================
  function initScoreAnimation() {
    var scoreNumber = document.querySelector('.aqmi-score-number');
    var scoreFill = document.querySelector('.aqmi-score-fill');
    var scoreColors = document.querySelectorAll('.aqmi-score-color');
    if (!scoreNumber || !scoreFill) return;

    var circumference = 879.65;
    var scores = [0, 35, 52, 68, 82, 95];
    var colors = ['#ea5455', '#ff9f43', '#fbbf24', '#28c76f', '#4fc3f7'];
    var currentIndex = 0;

    function animateScore() {
      if (currentIndex >= scores.length) return;
      scoreNumber.textContent = scores[currentIndex] + '%';
      var offset = circumference - (circumference * scores[currentIndex] / 100);
      scoreFill.style.strokeDashoffset = offset.toString();
      if (currentIndex > 0) {
        scoreFill.style.stroke = colors[currentIndex - 1];
        if (scoreColors[currentIndex - 1]) {
          scoreColors.forEach(function(c) { c.classList.remove('active'); });
          scoreColors[currentIndex - 1].classList.add('active');
        }
      }
      currentIndex++;
      setTimeout(animateScore, 600);
    }

    // Trigger on scroll
    var triggered = false;
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting && !triggered) {
          triggered = true;
          setTimeout(animateScore, 300);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    var el = document.querySelector('.aqmi-score-display');
    if (el) observer.observe(el);
  }

  // ============================================
  // NAVIGATION
  // ============================================
  function initNav() {
    var nav = document.querySelector('.aqmi-nav');
    if (!nav) return;
    window.addEventListener('scroll', function() {
      nav.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });
  }

  // ============================================
  // INIT
  // ============================================
  function init() {
    initHeroCanvas();
    initNav();
    initEval();

    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      setTimeout(function() { initGSAP(); }, 100);
    } else {
      var check = setInterval(function() {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
          clearInterval(check);
          initGSAP();
        }
      }, 200);
      setTimeout(function() { clearInterval(check); initFallback(); }, 5000);
    }

    initScoreAnimation();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();