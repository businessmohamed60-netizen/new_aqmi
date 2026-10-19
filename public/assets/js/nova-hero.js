/* ═══════════════════════════════════════════════════════════════════
   NOVAQYS — Premium Hero Animations
   Holographic Globe · Digital Connections · Particles · Parallax
   ═══════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  const APP = {};

  // ─── DOM Cache ──────────────────────────────────────────────────
  APP.cache = {};

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

  function cacheDOM() {
    APP.cache.hero = document.querySelector('.nova-hero');
    if (!APP.cache.hero) return false;
    APP.cache.canvas = APP.cache.hero.querySelector('.nova-hero-canvas');
    APP.cache.content = APP.cache.hero.querySelector('.nova-hero-content');
    APP.cache.rays = APP.cache.hero.querySelectorAll('.nova-hero-ray');
    return true;
  }

  // ─── Engine Neuron ──────────────────────────────────────────────
  APP.engine = {
    mouseX: 0,
    mouseY: 0,
    time: 0,
    animId: null,
    particles: [],
    aiPulse: 0
  };

  APP.initEngineNeuron = function() {
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

    const w = canvas.width;
    const h = canvas.height;
    const cx = w / 2;
    const cy = h / 2;
    const scale = Math.min(w, h) * 0.003;

    // ─── Engine Geometry ──────────────────────────────────────────
    const engineConfig = {
      // V-engine: two banks of cylinders
      cylindersPerBank: 4,
      bankAngle: 0.4,        // radians, V-angle
      bore: 60 * scale,       // cylinder width
      stroke: 100 * scale,    // piston travel
      rodLength: 140 * scale, // connecting rod length
      engineWidth: 200 * scale,
      engineHeight: 160 * scale,
      crankRadius: 30 * scale,
    };

    const cylColors = ['#3b82f6', '#06b6d4', '#8b5cf6', '#22c55e'];

    // ─── Neural Particles ─────────────────────────────────────────
    APP.engine.particles = [];
    const particleCount = 100;
    for (let i = 0; i < particleCount; i++) {
      APP.engine.particles.push({
        x: (Math.random() - 0.5) * engineConfig.engineWidth * 3,
        y: (Math.random() - 0.5) * engineConfig.engineHeight * 3,
        vx: (Math.random() - 0.5) * 0.5,
        vy: (Math.random() - 0.5) * 0.5,
        size: 1 + Math.random() * 2,
        color: cylColors[Math.floor(Math.random() * cylColors.length)],
        phase: Math.random() * Math.PI * 2,
        life: Math.random()
      });
    }

    // ─── Draw Function ────────────────────────────────────────────
    function draw() {
      APP.engine.time += 0.016;
      APP.engine.aiPulse = (APP.engine.aiPulse + 0.008) % (Math.PI * 2);

      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const rotX = APP.engine.mouseY * 0.3;
      const rotY = APP.engine.mouseX * 0.3;
      const t = APP.engine.time;

      // ── 1. Neural Particles ──
      APP.engine.particles.forEach(function(p) {
        p.x += p.vx;
        p.y += p.vy;
        p.life += 0.002;

        // Boundary wrapping
        const halfW = engineConfig.engineWidth * 1.5;
        const halfH = engineConfig.engineHeight * 1.5;
        if (p.x > halfW) p.x = -halfW;
        if (p.x < -halfW) p.x = halfW;
        if (p.y > halfH) p.y = -halfH;
        if (p.y < -halfH) p.y = halfH;

        // Slight oscillation
        p.vx += Math.sin(t + p.phase) * 0.002;
        p.vy += Math.cos(t * 0.7 + p.phase) * 0.002;

        // Clamp velocity
        const maxV = 1.2;
        if (Math.abs(p.vx) > maxV) p.vx = Math.sign(p.vx) * maxV;
        if (Math.abs(p.vy) > maxV) p.vy = Math.sign(p.vy) * maxV;
      });

      // ── 2. Draw Neural Connections ──
      APP.engine.particles.forEach(function(a, i) {
        APP.engine.particles.forEach(function(b, j) {
          if (j <= i) return;
          const dx = (a.x - b.x);
          const dy = (a.y - b.y);
          const dist = Math.sqrt(dx * dx + dy * dy);
          const maxDist = engineConfig.engineWidth * 0.7;
          if (dist < maxDist) {
            const alpha = (1 - dist / maxDist) * 0.12;
            // Pulse wave effect
            const pulseVal = Math.sin(APP.engine.aiPulse + dist * 0.02) * 0.5 + 0.5;
            ctx.beginPath();
            ctx.moveTo(cx + a.x, cy + a.y);
            ctx.lineTo(cx + b.x, cy + b.y);
            ctx.strokeStyle = 'rgba(59, 130, 246, ' + (alpha * (0.5 + pulseVal * 0.5)) + ')';
            ctx.lineWidth = 0.5;
            ctx.stroke();
          }
        });
      });

      // ── 3. Draw Engine Block ──
      const bankW = engineConfig.engineWidth * 0.5;
      const bankH = engineConfig.engineHeight * 0.6;
      const crankY = cy + bankH * 0.5 + engineConfig.crankRadius;

      // Engine block outline (two banks)
      for (let bank = -1; bank <= 1; bank += 2) {
        const bankX = cx + bank * bankW * 0.3;
        const bankAngle = bank * engineConfig.bankAngle;

        // Cylinder block shape
        ctx.save();
        ctx.translate(bankX, cy - bankH * 0.3);
        ctx.rotate(bankAngle);

        // Block body
        const blockW = engineConfig.bore * 1.3;
        const blockH = bankH * 1.1;
        const grad = ctx.createLinearGradient(0, -blockH / 2, 0, blockH / 2);
        grad.addColorStop(0, 'rgba(59, 130, 246, 0.03)');
        grad.addColorStop(0.5, 'rgba(6, 182, 212, 0.06)');
        grad.addColorStop(1, 'rgba(59, 130, 246, 0.03)');
        ctx.fillStyle = grad;
        ctx.strokeStyle = 'rgba(59, 130, 246, 0.15)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.roundRect(-blockW / 2, -blockH / 2, blockW, blockH, 4);
        ctx.fill();
        ctx.stroke();

        // Cylinders (per bank)
        for (let c = 0; c < engineConfig.cylindersPerBank; c++) {
          const cylIndex = (bank === 1 ? c : engineConfig.cylindersPerBank - 1 - c);
          const cylX = -blockW / 2 + engineConfig.bore * 0.2 + (c + 0.5) * (blockW / engineConfig.cylindersPerBank);
          const cylTop = -blockH / 2 + 10 * scale;
          const cylBot = blockH / 2 - 10 * scale;
          const cylH = cylBot - cylTop;

          // Cylinder wall
          ctx.strokeStyle = 'rgba(6, 182, 212, 0.1)';
          ctx.lineWidth = 0.5;
          ctx.beginPath();
          const cylW = engineConfig.bore * 0.6;
          ctx.moveTo(cylX - cylW / 2, cylTop);
          ctx.lineTo(cylX - cylW / 2, cylBot);
          ctx.moveTo(cylX + cylW / 2, cylTop);
          ctx.lineTo(cylX + cylW / 2, cylBot);
          ctx.stroke();

          // ── Piston ──
          const pistonPhase = (c / engineConfig.cylindersPerBank) * Math.PI * 2 + t * 1.2 * bank;
          const pistonY = cylBot - engineConfig.stroke * 0.5 * (1 + Math.sin(pistonPhase));
          const pistonH = engineConfig.stroke * 0.25;
          const pistonW = engineConfig.bore * 0.5;

          // Piston body
          const pistonGrad = ctx.createLinearGradient(0, pistonY - pistonH / 2, 0, pistonY + pistonH / 2);
          pistonGrad.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
          pistonGrad.addColorStop(0.5, 'rgba(6, 182, 212, 0.6)');
          pistonGrad.addColorStop(1, 'rgba(59, 130, 246, 0.4)');
          ctx.fillStyle = pistonGrad;
          ctx.strokeStyle = 'rgba(6, 182, 212, 0.3)';
          ctx.lineWidth = 0.5;
          ctx.beginPath();
          ctx.roundRect(cylX - pistonW / 2, pistonY - pistonH / 2, pistonW, pistonH, 2);
          ctx.fill();
          ctx.stroke();

          // ── Connecting Rod ──
          const crankAngle = pistonPhase + Math.PI * 0.5;
          const crankPinX = engineConfig.crankRadius * Math.cos(crankAngle);
          const crankPinY = engineConfig.crankRadius * Math.sin(crankAngle);
          const rodEndX = crankPinX;
          const rodEndY = engineConfig.crankRadius + crankPinY;

          ctx.strokeStyle = 'rgba(148, 163, 184, 0.25)';
          ctx.lineWidth = 1.5;
          ctx.beginPath();
          ctx.moveTo(cylX, pistonY + pistonH / 2);
          ctx.lineTo(cylX + rodEndX, rodEndY);
          ctx.stroke();

          // ── Spark Plug Node ──
          const sparkPhase = Math.sin(pistonPhase);
          const sparkIntensity = Math.max(0, sparkPhase > 0.85 ? (sparkPhase - 0.85) / 0.15 : 0);

          // Spark glow
          if (sparkIntensity > 0) {
            const sparkGrad = ctx.createRadialGradient(cylX, cylTop, 0, cylX, cylTop, engineConfig.bore * 0.8);
            sparkGrad.addColorStop(0, 'rgba(255, 200, 50, ' + (sparkIntensity * 0.6) + ')');
            sparkGrad.addColorStop(0.3, 'rgba(255, 150, 50, ' + (sparkIntensity * 0.3) + ')');
            sparkGrad.addColorStop(1, 'rgba(255, 100, 50, 0)');
            ctx.fillStyle = sparkGrad;
            ctx.beginPath();
            ctx.arc(cylX, cylTop, engineConfig.bore * 0.8, 0, Math.PI * 2);
            ctx.fill();
          }

          // Spark plug dot
          ctx.beginPath();
          ctx.arc(cylX, cylTop, 2 * scale + sparkIntensity * 4 * scale, 0, Math.PI * 2);
          ctx.fillStyle = sparkIntensity > 0 ? '#ffd700' : cylColors[cylIndex % cylColors.length];
          ctx.globalAlpha = 0.6 + sparkIntensity * 0.4;
          ctx.fill();
          ctx.globalAlpha = 1;
        }

        ctx.restore();
      }

      // ── 4. Crankshaft ──
      const crankW = engineConfig.engineWidth * 0.5;

      // Main shaft
      ctx.strokeStyle = 'rgba(148, 163, 184, 0.15)';
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.moveTo(cx - crankW, crankY);
      ctx.lineTo(cx + crankW, crankY);
      ctx.stroke();

      // Crank throw (animated)
      for (let bank = -1; bank <= 1; bank += 2) {
        const bankX = cx + bank * bankW * 0.3;
        for (let c = 0; c < engineConfig.cylindersPerBank; c++) {
          const pistonPhase = (c / engineConfig.cylindersPerBank) * Math.PI * 2 + t * 1.2 * bank;
          const crankAngle = pistonPhase + Math.PI * 0.5;
          const crankPinX = bankX + engineConfig.crankRadius * Math.cos(crankAngle);
          const crankPinY = crankY + engineConfig.crankRadius * Math.sin(crankAngle);

          // Crank pin dot
          ctx.beginPath();
          ctx.arc(crankPinX, crankPinY, 1.5 * scale, 0, Math.PI * 2);
          ctx.fillStyle = 'rgba(148, 163, 184, 0.4)';
          ctx.fill();
        }
      }

      // ── 5. Neural Engine Core ──
      // AI brain glow in center
      const aiIntensity = 0.5 + 0.5 * Math.sin(APP.engine.aiPulse * 2);
      const coreGrad = ctx.createRadialGradient(cx, cy, 0, cx, cy, engineConfig.engineWidth * 0.5);
      coreGrad.addColorStop(0, 'rgba(59, 130, 246, ' + (0.04 * aiIntensity) + ')');
      coreGrad.addColorStop(0.5, 'rgba(6, 182, 212, ' + (0.02 * aiIntensity) + ')');
      coreGrad.addColorStop(1, 'rgba(59, 130, 246, 0)');
      ctx.fillStyle = coreGrad;
      ctx.beginPath();
      ctx.arc(cx, cy, engineConfig.engineWidth * 0.5, 0, Math.PI * 2);
      ctx.fill();

      // ── 6. Neural Nodes ──
      const nodePositions = [];
      for (let bank = -1; bank <= 1; bank += 2) {
        const bankX = cx + bank * bankW * 0.3;
        const bankAngle = bank * engineConfig.bankAngle;
        for (let c = 0; c < engineConfig.cylindersPerBank; c++) {
          const ax = bankX + (c - 1.5) * engineConfig.bore * 0.5;
          const ay = cy - bankH * 0.3;
          const rx = ax * Math.cos(bankAngle);
          const ry = ay;
          nodePositions.push({ x: cx + rx, y: ry + bankAngle * rx });
        }
      }

      // Draw neural connections between nodes
      nodePositions.forEach(function(a, i) {
        // Connection to center
        ctx.beginPath();
        ctx.moveTo(a.x, a.y);
        ctx.lineTo(cx, cy);
        ctx.strokeStyle = 'rgba(59, 130, 246, ' + (0.03 + 0.03 * Math.sin(APP.engine.aiPulse + i)) + ')';
        ctx.lineWidth = 0.5;
        ctx.stroke();

        // Connection to nearby nodes
        nodePositions.forEach(function(b, j) {
          if (j <= i) return;
          const dx = a.x - b.x;
          const dy = a.y - b.y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < engineConfig.engineWidth * 0.5) {
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.strokeStyle = 'rgba(6, 182, 212, ' + (0.02 * (1 - dist / (engineConfig.engineWidth * 0.5))) + ')';
            ctx.lineWidth = 0.3;
            ctx.stroke();
          }
        });
      });

      // ── 7. AI Label ──
      ctx.save();
      ctx.font = 'bold ' + (9 * scale) + 'px "Inter", sans-serif';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      const labelAlpha = 0.2 + 0.15 * Math.sin(APP.engine.aiPulse * 1.5);
      ctx.fillStyle = 'rgba(59, 130, 246, ' + labelAlpha + ')';
      ctx.fillText('⚡ NEURAL ENGINE', cx, cy + engineConfig.engineHeight * 0.8);

      // Sub-label
      ctx.font = (6 * scale) + 'px "Inter", sans-serif';
      ctx.fillStyle = 'rgba(6, 182, 212, ' + (labelAlpha * 0.5) + ')';
      ctx.fillText('AI-Powered • Automotive Quality', cx, cy + engineConfig.engineHeight * 0.8 + 12 * scale);
      ctx.restore();

      // ── 8. Neural Particles (draw as dots) ──
      APP.engine.particles.forEach(function(p) {
        const px = cx + p.x;
        const py = cy + p.y;
        const pulseSize = p.size + Math.sin(APP.engine.time * 2 + p.phase) * 0.3;
        ctx.beginPath();
        ctx.arc(px, py, pulseSize, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.globalAlpha = 0.2 + 0.2 * Math.sin(APP.engine.time + p.phase);
        ctx.fill();
        ctx.globalAlpha = 1;
      });

      // ── 9. AI Pulse Ring ──
      const pulseRadius = engineConfig.engineWidth * 0.3 + Math.sin(APP.engine.aiPulse) * engineConfig.engineWidth * 0.2;
      const pulseAlpha = 0.1 - 0.1 * Math.abs(Math.sin(APP.engine.aiPulse));
      if (pulseAlpha > 0.01) {
        ctx.beginPath();
        ctx.arc(cx, cy, pulseRadius, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(59, 130, 246, ' + pulseAlpha + ')';
        ctx.lineWidth = 1;
        ctx.stroke();

        // Second pulse ring (offset)
        const pulseRadius2 = engineConfig.engineWidth * 0.3 + Math.sin(APP.engine.aiPulse + 1) * engineConfig.engineWidth * 0.2;
        const pulseAlpha2 = 0.08 - 0.08 * Math.abs(Math.sin(APP.engine.aiPulse + 1));
        if (pulseAlpha2 > 0.01) {
          ctx.beginPath();
          ctx.arc(cx, cy, pulseRadius2, 0, Math.PI * 2);
          ctx.strokeStyle = 'rgba(6, 182, 212, ' + pulseAlpha2 + ')';
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }

      APP.engine.animId = requestAnimationFrame(draw);
    }

    draw();

    document.addEventListener('mousemove', function(e) {
      const rect = canvas.getBoundingClientRect();
      APP.engine.mouseX = (e.clientX - rect.left - rect.width / 2) / rect.width;
      APP.engine.mouseY = (e.clientY - rect.top - rect.height / 2) / rect.height;
    });
  };

  // ─── Floating Particles ─────────────────────────────────────────
  APP.floatingParticles = [];

  APP.initFloatingParticles = function() {
    const count = 30;
    for (let i = 0; i < count; i++) {
      const el = document.createElement('div');
      el.style.cssText =
        'position:fixed;pointer-events:none;z-index:1;' +
        'width:' + (1 + Math.random() * 2) + 'px;' +
        'height:' + (1 + Math.random() * 2) + 'px;' +
        'background:' + (Math.random() > 0.5 ? '#2563eb' : '#06b6d4') + ';' +
        'border-radius:50%;opacity:0;';
      document.body.appendChild(el);
      APP.floatingParticles.push({
        el: el,
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        vx: (Math.random() - 0.5) * 0.2,
        vy: -0.1 - Math.random() * 0.2,
        alpha: 0.05 + Math.random() * 0.15,
        phase: Math.random() * Math.PI * 2
      });
    }

    function animate() {
      APP.floatingParticles.forEach(function(p) {
        p.x += p.vx;
        p.y += p.vy;
        if (p.y < -10) { p.y = window.innerHeight + 10; p.x = Math.random() * window.innerWidth; }
        if (p.x < -10 || p.x > window.innerWidth + 10) { p.x = Math.random() * window.innerWidth; }
        p.el.style.transform = 'translate(' + p.x + 'px,' + p.y + 'px)';
        p.el.style.opacity = p.alpha * (0.5 + 0.5 * Math.sin(Date.now() * 0.001 + p.phase));
      });
      requestAnimationFrame(animate);
    }
    animate();
  };

  // ─── GSAP ScrollTrigger ─────────────────────────────────────────
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
    // ─── 1. HERO WORD ANIMATION TIMELINE ───────────────────────
    var heroTl = gsap.timeline({ delay: 0.2, defaults: { ease: 'power3.out' } });

    var badge = document.querySelector('.nova-hero-badge');
    var words = document.querySelectorAll('.nova-hero-word');
    var subtitle = document.querySelector('.nova-hero-subtitle');
    var tagline = document.querySelector('.nova-hero-tagline');
    var actions = document.querySelector('.nova-hero-actions');
    var stats = document.querySelector('.nova-hero-stats');
    var scroll = document.querySelector('.nova-hero-scroll');

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

    // ─── 2. HERO PARALLAX ─────────────────────────────────────
    gsap.to('.nova-hero-content', {
      y: 60,
      ease: 'none',
      scrollTrigger: { trigger: '.nova-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });
    gsap.to('.nova-hero-canvas', {
      scale: 1.08,
      ease: 'none',
      scrollTrigger: { trigger: '.nova-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });
    gsap.to('.nova-hero-overlay', {
      opacity: 0.3,
      ease: 'none',
      scrollTrigger: { trigger: '.nova-hero', start: 'top top', end: 'bottom top', scrub: 1 }
    });

    // ─── 3. MARQUEE SECTION ───────────────────────────────────
    gsap.fromTo('.marquee-section',
      { opacity: 0, y: 30 },
      { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out',
        scrollTrigger: { trigger: '.marquee-section', start: 'top 85%', end: 'top 50%', scrub: 0.5 }
      }
    );

    // ─── 4. SLOGAN SECTION ────────────────────────────────────
    gsap.fromTo('.slogan-content',
      { opacity: 0, y: 40, filter: 'blur(4px)' },
      { opacity: 1, y: 0, filter: 'blur(0px)', duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '.slogan-section', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
      }
    );

    // ─── 5. CHALLENGES SECTION ────────────────────────────────
    var challengeCards = document.querySelectorAll('.challenge-card');
    challengeCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(4px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
          scrollTrigger: { trigger: '.challenges-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 6. ECOSYSTEM SECTION ─────────────────────────────────
    var ecoCards = document.querySelectorAll('.ecosystem-card');
    ecoCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
          scrollTrigger: { trigger: '.ecosystem-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 7. PLATFORM SECTIONS (AQMI, NARA, LMS, QMS, ASIN) ───
    var platformSections = document.querySelectorAll('.platform-section');
    platformSections.forEach(function(section) {
      var content = section.querySelector('.platform-content');
      var visual = section.querySelector('.platform-visual');
      if (content) {
        gsap.fromTo(content,
          { opacity: 0, x: -30, filter: 'blur(3px)' },
          { opacity: 1, x: 0, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
            scrollTrigger: { trigger: section, start: 'top 75%', end: 'top 40%', scrub: 0.5 }
          }
        );
      }
      if (visual) {
        gsap.fromTo(visual,
          { opacity: 0, x: 30, filter: 'blur(3px)' },
          { opacity: 1, x: 0, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
            scrollTrigger: { trigger: section, start: 'top 75%', end: 'top 40%', scrub: 0.5 }
          }
        );
      }
    });

    // ─── 8. BENEFITS SECTION ──────────────────────────────────
    var benefitCards = document.querySelectorAll('.benefit-card');
    benefitCards.forEach(function(card) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
          scrollTrigger: { trigger: '.benefits-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });

    // ─── 9. REPORT EXAMPLE SECTION ────────────────────────────
    var reportPreview = document.querySelector('.report-example-preview');
    var reportInfo = document.querySelector('.report-example-info');
    if (reportPreview) {
      gsap.fromTo(reportPreview,
        { opacity: 0, x: -30, filter: 'blur(4px)' },
        { opacity: 1, x: 0, filter: 'blur(0px)', duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: '.report-example-layout', start: 'top 75%', end: 'top 40%', scrub: 0.5 }
        }
      );
    }
    if (reportInfo) {
      gsap.fromTo(reportInfo,
        { opacity: 0, x: 30, filter: 'blur(4px)' },
        { opacity: 1, x: 0, filter: 'blur(0px)', duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: '.report-example-layout', start: 'top 75%', end: 'top 40%', scrub: 0.5 }
        }
      );
    }

    // ─── 10. SUCCESS SECTION ──────────────────────────────────
    var successCard = document.querySelector('.success-card');
    if (successCard) {
      gsap.fromTo(successCard,
        { opacity: 0, y: 40, scale: 0.97, filter: 'blur(4px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 1.2, ease: 'power3.out',
          scrollTrigger: { trigger: '.success-layout', start: 'top 75%', end: 'top 40%', scrub: 0.5 }
        }
      );
    }

    // ─── 11. CTA SECTION ──────────────────────────────────────
    gsap.fromTo('.cta-content',
      { opacity: 0, y: 40, scale: 0.97 },
      { opacity: 1, y: 0, scale: 1, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '.cta-section', start: 'top 80%', end: 'top 50%', scrub: 0.5 }
      }
    );

    // ─── 12. CONTACT SECTION ──────────────────────────────────
    var contactCard = document.querySelector('.contact-card');
    if (contactCard) {
      gsap.fromTo(contactCard,
        { opacity: 0, y: 30, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.8, ease: 'power3.out',
          scrollTrigger: { trigger: '.contact-section', start: 'top 75%', end: 'top 40%', scrub: 0.5 }
        }
      );
    }

    // ─── 13. ARCHITECTURE CARDS (existing) ────────────────────
    var archCards = document.querySelectorAll('.nova-arch-card');
    archCards.forEach(function(card, i) {
      gsap.fromTo(card,
        { opacity: 0, y: 20, scale: 0.95, filter: 'blur(3px)' },
        { opacity: 1, y: 0, scale: 1, filter: 'blur(0px)', duration: 0.5, delay: i * 0.05, ease: 'power2.out',
          scrollTrigger: { trigger: '.nova-arch-grid', start: 'top 80%', end: 'top 40%', scrub: 0.5 }
        }
      );
    });
    gsap.fromTo('.nova-arch-hub',
      { opacity: 0, y: 30, filter: 'blur(3px)' },
      { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.8, ease: 'power2.out',
        scrollTrigger: { trigger: '.nova-arch-hub', start: 'top 85%', end: 'top 50%', scrub: 0.5 }
      }
    );

    ScrollTrigger.refresh();
  };

  // ─── Init ───────────────────────────────────────────────────────
  APP.init = function() {
    if (!cacheDOM()) return;
    APP.initEngineNeuron();
    APP.initFloatingParticles();
    APP.initGSAP();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', APP.init);
  } else {
    APP.init();
  }

})();