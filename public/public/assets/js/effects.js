/**
 * NOVAQYS — Global Effects: Cursor Follower & Page Particles
 * Lightweight, no dependencies, requestAnimationFrame-based
 */
(function () {
  'use strict';

  // ============================================================
  // CONFIG
  // ============================================================
  const CONFIG = {
    cursor: {
      size: 20,
      color: 'rgba(0, 180, 255, 0.4)',
      smoothFactor: 0.12,
      glowSize: 40,
      trailCount: 6,
      trailLife: 800,
    },
    particles: {
      count: 40,
      minSize: 1.5,
      maxSize: 4,
      minSpeed: 0.15,
      maxSpeed: 0.5,
      minOpacity: 0.15,
      maxOpacity: 0.5,
      colors: ['rgba(0,180,255,', 'rgba(115,103,240,', 'rgba(0,207,232,', 'rgba(40,199,111,'],
    },
    hoverSelector: 'a, button, .hero360__hotspot, .ecosystem-btn, .platform-btn, .benefit-card, .challenge-card, .cta-btn, .nav-header a, .footer a',
  };

  // ============================================================
  // STATE
  // ============================================================
  const state = {
    mouse: { x: -100, y: -100, targetX: -100, targetY: -100 },
    trails: [],
    particles: [],
    rafId: null,
    isRunning: false,
    isTouchDevice: false,
  };

  // ============================================================
  // DOM REFS
  // ============================================================
  let dom = {};

  // ============================================================
  // DETECT TOUCH
  // ============================================================
  function detectTouch() {
    state.isTouchDevice = 'ontouchstart' in window
      || navigator.maxTouchPoints > 0
      || window.matchMedia('(hover: none) and (pointer: coarse)').matches;
  }

  // ============================================================
  // CURSOR FOLLOWER
  // ============================================================
  function createCursor() {
    if (state.isTouchDevice) return;

    // Main cursor
    dom.cursor = document.createElement('div');
    dom.cursor.className = 'nova-cursor';
    dom.cursor.style.width = CONFIG.cursor.size + 'px';
    dom.cursor.style.height = CONFIG.cursor.size + 'px';
    document.body.appendChild(dom.cursor);

    // Trail dots
    for (let i = 0; i < CONFIG.cursor.trailCount; i++) {
      const trail = document.createElement('div');
      trail.className = 'nova-cursor--trail';
      const size = CONFIG.cursor.size * (0.3 + (i / CONFIG.cursor.trailCount) * 0.4);
      trail.style.width = size + 'px';
      trail.style.height = size + 'px';
      trail.style.opacity = 0.15 * (1 - i / CONFIG.cursor.trailCount);
      document.body.appendChild(trail);
      state.trails.push({ el: trail, x: -100, y: -100 });
    }

    // Hover effects on interactive elements
    document.addEventListener('mouseover', (e) => {
      const target = e.target.closest(CONFIG.hoverSelector);
      if (target && dom.cursor) {
        dom.cursor.classList.add('nova-cursor--glow');
      }
    });

    document.addEventListener('mouseout', (e) => {
      const target = e.target.closest(CONFIG.hoverSelector);
      if (target && dom.cursor) {
        dom.cursor.classList.remove('nova-cursor--glow');
      }
    });
  }

  function updateCursor() {
    if (!dom.cursor || state.isTouchDevice) return;

    // Smooth interpolation
    state.mouse.x += (state.mouse.targetX - state.mouse.x) * CONFIG.cursor.smoothFactor;
    state.mouse.y += (state.mouse.targetY - state.mouse.y) * CONFIG.cursor.smoothFactor;

    // Apply to main cursor
    dom.cursor.style.left = state.mouse.x + 'px';
    dom.cursor.style.top = state.mouse.y + 'px';

    // Update trails (chained following)
    for (let i = 0; i < state.trails.length; i++) {
      const prev = i === 0
        ? { x: state.mouse.x, y: state.mouse.y }
        : state.trails[i - 1];
      const t = state.trails[i];
      t.x += (prev.x - t.x) * 0.15;
      t.y += (prev.y - t.y) * 0.15;
      t.el.style.left = t.x + 'px';
      t.el.style.top = t.y + 'px';
    }
  }

  // ============================================================
  // PAGE PARTICLES
  // ============================================================
  function createParticles() {
    dom.particlesContainer = document.createElement('div');
    dom.particlesContainer.className = 'nova-particles';
    document.body.insertBefore(dom.particlesContainer, document.body.firstChild);

    for (let i = 0; i < CONFIG.particles.count; i++) {
      const el = document.createElement('div');
      el.className = 'nova-particle';

      const size = CONFIG.particles.minSize + Math.random() * (CONFIG.particles.maxSize - CONFIG.particles.minSize);
      const color = CONFIG.particles.colors[Math.floor(Math.random() * CONFIG.particles.colors.length)];
      const opacity = CONFIG.particles.minOpacity + Math.random() * (CONFIG.particles.maxOpacity - CONFIG.particles.minOpacity);
      const speed = CONFIG.particles.minSpeed + Math.random() * (CONFIG.particles.maxSpeed - CONFIG.particles.minSpeed);
      const x = Math.random() * 100;
      const y = Math.random() * 100;
      const drift = (Math.random() - 0.5) * 0.3;

      el.style.width = size + 'px';
      el.style.height = size + 'px';
      el.style.left = x + '%';
      el.style.top = y + '%';
      el.style.background = color + opacity + ')';
      el.style.boxShadow = `0 0 ${size * 2}px ${color}${opacity * 0.5 + ')'}`;

      dom.particlesContainer.appendChild(el);

      state.particles.push({
        el,
        x,
        y,
        speed,
        drift,
        size,
        opacity,
        color,
        phase: Math.random() * Math.PI * 2,
      });
    }
  }

  function updateParticles() {
    for (const p of state.particles) {
      // Drift upward with slight horizontal oscillation
      p.y -= p.speed * 0.02;
      p.x += Math.sin(Date.now() * 0.001 + p.phase) * p.drift * 0.02;

      // Wrap around when reaching top
      if (p.y < -5) {
        p.y = 105;
        p.x = Math.random() * 100;
      }

      p.el.style.top = p.y + '%';
      p.el.style.left = p.x + '%';

      // Subtle opacity pulse
      const pulse = 0.7 + 0.3 * Math.sin(Date.now() * 0.002 + p.phase);
      p.el.style.opacity = p.opacity * pulse;
    }
  }

  // ============================================================
  // MAIN LOOP
  // ============================================================
  function loop() {
    updateCursor();
    updateParticles();
    state.rafId = requestAnimationFrame(loop);
  }

  // ============================================================
  // EVENT BINDING
  // ============================================================
  function bindEvents() {
    if (state.isTouchDevice) return;

    document.addEventListener('mousemove', (e) => {
      state.mouse.targetX = e.clientX;
      state.mouse.targetY = e.clientY;
    });

    // Hide cursor when leaving window
    document.addEventListener('mouseleave', () => {
      if (dom.cursor) dom.cursor.style.opacity = '0';
      state.trails.forEach(t => { t.el.style.opacity = '0'; });
    });

    document.addEventListener('mouseenter', () => {
      if (dom.cursor) dom.cursor.style.opacity = '1';
      state.trails.forEach(t => { t.el.style.opacity = ''; });
    });
  }

  // ============================================================
  // INIT
  // ============================================================
  function init() {
    if (state.isRunning) return;
    state.isRunning = true;

    detectTouch();
    createParticles();
    createCursor();
    bindEvents();
    loop();
  }

  // Start on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();