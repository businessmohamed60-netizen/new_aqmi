/**
 * NOVAQYS Hero 360° Immersif — JavaScript
 * Architecture modulaire : Panorama, Hotspots, Particles, Globe, Navigation
 * Dépendances : GSAP, hero360-config.js
 */

(function () {
  'use strict';

  // ============================================================
  // 1. STATE
  // ============================================================
  const state = {
    mouse: { x: 0.5, y: 0.5, targetX: 0.5, targetY: 0.5 },
    scroll: { progress: 0, target: 0 },
    isMobile: false,
    isTouching: false,
    gyro: { gamma: 0, beta: 0 },
    gyroAvailable: false,
    rafId: null,
    initialized: false,
  };

  // ============================================================
  // 2. DOM REFS
  // ============================================================
  let els = {};

  function cacheElements() {
    els.root = document.querySelector('.hero360');
    els.panorama = document.querySelector('.hero360__panorama');
    els.canvas = document.querySelector('.hero360__canvas');
    els.globe = document.querySelector('.hero360__globe');
    els.hotspotsContainer = document.querySelector('.hero360__hotspots');
    els.tooltip = document.querySelector('.hero360__tooltip');
    els.iconsContainer = document.querySelector('.hero360__icons');
    els.loader = document.querySelector('.hero360__loader');
    els.content = document.querySelector('.hero360__content');
  }

  // ============================================================
  // 3. MOBILE DETECTION
  // ============================================================
  function detectMobile() {
    state.isMobile = window.innerWidth <= 768
      || ('ontouchstart' in window)
      || (navigator.maxTouchPoints > 0);
  }

  // ============================================================
  // 4. PANORAMA — Mouse parallax 360° with inertia
  // ============================================================
  class Panorama {
    constructor() {
      this.sensitivityX = HERO360_CONFIG.sensitivity.x;
      this.sensitivityY = HERO360_CONFIG.sensitivity.y;
      this.currentX = 0.5;
      this.currentY = 0.5;
    }

    init() {
      this.bindEvents();
    }

    bindEvents() {
      if (!els.root) return;
      if (!state.isMobile) {
        document.addEventListener('mousemove', (e) => {
          if (!els.root) return;
          const rect = els.root.getBoundingClientRect();
          if (!rect) return;
          state.mouse.targetX = (e.clientX - rect.left) / rect.width;
          state.mouse.targetY = (e.clientY - rect.top) / rect.height;
        });
      }

      // Touch support
      els.root.addEventListener('touchmove', (e) => {
        if (!state.isTouching || !els.root) return;
        const touch = e.touches[0];
        const rect = els.root.getBoundingClientRect();
        if (!rect) return;
        state.mouse.targetX = (touch.clientX - rect.left) / rect.width;
        state.mouse.targetY = (touch.clientY - rect.top) / rect.height;
      }, { passive: true });

      els.root.addEventListener('touchstart', () => {
        state.isTouching = true;
      }, { passive: true });

      els.root.addEventListener('touchend', () => {
        state.isTouching = false;
        state.mouse.targetX = 0.5;
        state.mouse.targetY = 0.5;
      }, { passive: true });

      // Gyroscope
      if (state.isMobile && window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', (e) => {
          if (e.gamma !== null && e.beta !== null) {
            state.gyroAvailable = true;
            state.gyro.gamma = e.gamma;   // left/right
            state.gyro.beta = e.beta;      // front/back
          }
        }, { passive: true });
      }
    }

    update() {
      // Smooth interpolation (inertia)
      state.mouse.x += (state.mouse.targetX - state.mouse.x) * 0.06;
      state.mouse.y += (state.mouse.targetY - state.mouse.y) * 0.06;

      let offsetX = 0.5;
      let offsetY = 0.5;

      if (state.isMobile && state.gyroAvailable) {
        // Gyroscope-based movement
        offsetX = 0.5 + (state.gyro.gamma / 45) * 0.08;
        offsetY = 0.5 + (state.gyro.beta / 45) * 0.05;
      } else {
        offsetX = state.mouse.x;
        offsetY = state.mouse.y;
      }

      // Clamp
      offsetX = Math.max(0.35, Math.min(0.65, offsetX));
      offsetY = Math.max(0.4, Math.min(0.6, offsetY));

      // Map to 360° panorama translation (300vw = 360°, 120° = 100vw)
      // mouseX 0 → shows left 120° segment, 0.5 → center, 1 → right 120°
      const translateX = -offsetX * 200;

      // Apply scroll zoom
      const zoom = HERO360_CONFIG.scrollZoom.start
        + (HERO360_CONFIG.scrollZoom.end - HERO360_CONFIG.scrollZoom.start)
        * Math.min(1, state.scroll.progress);

      // Apply to panorama
      if (els.panorama) {
        els.panorama.style.transform = `translateX(${translateX}vw) scale(${zoom})`;
      }

      // Globe parallax (subtle)
      if (els.globe) {
        const gx = (offsetX - 0.5) * 15;
        const gy = (offsetY - 0.5) * 10;
        els.globe.style.transform = `translate(calc(-50% + ${gx}px), calc(-50% + ${gy}px))`;
      }

      // Hotspot parallax (subtle counter-movement for depth)
      const hotspots = document.querySelectorAll('.hero360__hotspot');
      hotspots.forEach((hs) => {
        const depth = parseFloat(hs.dataset.depth || 1);
        const hx = (offsetX - 0.5) * 8 * depth;
        const hy = (offsetY - 0.5) * 5 * depth;
        hs.style.setProperty('--parallax-x', `${hx}px`);
        hs.style.setProperty('--parallax-y', `${hy}px`);
      });
    }
  }

  // ============================================================
  // 5. HOTSPOTS
  // ============================================================
  class Hotspots {
    constructor() {
      this.activeTooltip = null;
      this.platforms = HERO360_CONFIG.platforms;
    }

    init() {
      this.render();
      this.bindEvents();
    }

    render() {
      if (!els.hotspotsContainer) return;

      this.platforms.forEach((p) => {
        const hotspot = document.createElement('div');
        hotspot.className = `hero360__hotspot${p.isCenter ? ' hero360__hotspot--center' : ''}`;
        hotspot.dataset.id = p.id;
        hotspot.dataset.url = p.url;
        hotspot.dataset.depth = p.isCenter ? 0.5 : 1.2;
        hotspot.style.left = `${p.x}%`;
        hotspot.style.top = `${p.y}%`;

        const btn = document.createElement('div');
        btn.className = 'hero360__hotspot-btn';
        btn.textContent = p.name;

        const dot = document.createElement('div');
        dot.className = 'hero360__hotspot-dot';

        hotspot.appendChild(btn);
        hotspot.appendChild(dot);
        els.hotspotsContainer.appendChild(hotspot);
      });
    }

    bindEvents() {
      const hotspots = document.querySelectorAll('.hero360__hotspot');
      hotspots.forEach((hs) => {
        hs.addEventListener('mouseenter', () => {
          const id = hs.dataset.id;
          const platform = this.platforms.find((p) => p.id === id);
          if (platform) this.showTooltip(hs, platform);
        });

        hs.addEventListener('mouseleave', () => {
          this.hideTooltip();
        });

        hs.addEventListener('click', (e) => {
          e.stopPropagation();
          const url = hs.dataset.url;
          this.animateClick(hs, url);
        });

        // Touch support
        hs.addEventListener('touchstart', (e) => {
          const id = hs.dataset.id;
          const platform = this.platforms.find((p) => p.id === id);
          if (platform) {
            e.preventDefault();
            // Close any open tooltip
            if (this.activeTooltip && this.activeTooltip !== hs) {
              this.hideTooltip();
            }
            if (this.activeTooltip === hs) {
              this.hideTooltip();
              // On second tap, navigate
              const url = hs.dataset.url;
              window.open(url, '_blank');
            } else {
              this.showTooltip(hs, platform);
            }
          }
        }, { passive: false });
      });
    }

    showTooltip(element, platform) {
      if (!els.tooltip) return;
      this.activeTooltip = element;

      els.tooltip.querySelector('.hero360__tooltip-title').textContent = platform.name;
      els.tooltip.querySelector('.hero360__tooltip-name').textContent = platform.fullName;
      els.tooltip.querySelector('.hero360__tooltip-desc').textContent = platform.description;
      els.tooltip.querySelector('.hero360__tooltip-btn').href = platform.url;

      // Position tooltip near hotspot
      const rect = element.getBoundingClientRect();
      const rootRect = els.root.getBoundingClientRect();
      let top = rect.top - rootRect.top - 10;
      let left = rect.left - rootRect.left + rect.width / 2;

      // Ensure tooltip stays within bounds
      const tooltipWidth = 260;
      if (left + tooltipWidth / 2 > rootRect.width) {
        left = rootRect.width - tooltipWidth / 2 - 10;
      } else if (left - tooltipWidth / 2 < 10) {
        left = tooltipWidth / 2 + 10;
      }

      // If hotspot is in top half, show tooltip below; otherwise above
      const isTopHalf = (rect.top - rootRect.top) < rootRect.height / 2;
      if (isTopHalf) {
        top = rect.bottom - rootRect.top + 10;
      } else {
        top = rect.top - rootRect.top - els.tooltip.offsetHeight - 10;
      }

      els.tooltip.style.left = `${left}px`;
      els.tooltip.style.top = `${top}px`;
      els.tooltip.classList.add('hero360__tooltip--visible');
    }

    hideTooltip() {
      if (!els.tooltip) return;
      this.activeTooltip = null;
      els.tooltip.classList.remove('hero360__tooltip--visible');
    }

    animateClick(element, url) {
      // Visual feedback on click, then navigate directly
      const btn = element.querySelector('.hero360__hotspot-btn');
      if (btn && gsap) {
        gsap.to(btn, {
          scale: 0.95,
          duration: 0.1,
          ease: 'power2.out',
          onComplete: () => {
            gsap.to(btn, {
              scale: 1.05,
              duration: 0.1,
              ease: 'power2.in',
              onComplete: () => {
                gsap.to(btn, { scale: 1, duration: 0.1 });
              },
            });
          },
        });
      }
      // Navigate immediately (not inside callback) to avoid popup blockers
      window.location.href = url;
    }
  }

  // ============================================================
  // 6. PARTICLES — Canvas-based particle system
  // ============================================================
  class Particles {
    constructor() {
      this.canvas = els.canvas;
      this.ctx = null;
      this.particles = [];
      this.networkLines = [];
      this.width = 0;
      this.height = 0;
      this.mouseX = 0;
      this.mouseY = 0;
      this.PARTICLE_COUNT = 80;
      this.CONNECTION_DIST = 140;
    }

    init() {
      if (!this.canvas) return;
      this.ctx = this.canvas.getContext('2d');
      this.resize();
      this.createParticles();
      this.bindEvents();
    }

    resize() {
      this.width = window.innerWidth;
      this.height = window.innerHeight;
      this.canvas.width = this.width;
      this.canvas.height = this.height;
    }

    bindEvents() {
      window.addEventListener('resize', () => this.resize());

      if (!state.isMobile) {
        document.addEventListener('mousemove', (e) => {
          this.mouseX = e.clientX;
          this.mouseY = e.clientY;
        });
      }
    }

    createParticles() {
      this.particles = [];
      for (let i = 0; i < this.PARTICLE_COUNT; i++) {
        this.particles.push({
          x: Math.random() * this.width,
          y: Math.random() * this.height,
          vx: (Math.random() - 0.5) * 0.6,
          vy: (Math.random() - 0.5) * 0.6,
          size: Math.random() * 2 + 1,
          opacity: Math.random() * 0.5 + 0.2,
          pulse: Math.random() * Math.PI * 2,
        });
      }
    }

    draw() {
      if (!this.ctx) return;
      const ctx = this.ctx;

      ctx.clearRect(0, 0, this.width, this.height);

      // Update and draw particles
      this.particles.forEach((p, i) => {
        p.pulse += 0.02;

        // Slight attraction to mouse
        const dx = this.mouseX - p.x;
        const dy = this.mouseY - p.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 200 && dist > 0) {
          const force = 0.0003;
          p.vx += dx * force;
          p.vy += dy * force;
        }

        // Damping
        p.vx *= 0.99;
        p.vy *= 0.99;

        p.x += p.vx;
        p.y += p.vy;

        // Wrap around
        if (p.x < 0) p.x = this.width;
        if (p.x > this.width) p.x = 0;
        if (p.y < 0) p.y = this.height;
        if (p.y > this.height) p.y = 0;

        // Draw particle
        const pulseOpacity = p.opacity + Math.sin(p.pulse) * 0.15;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(0, 180, 255, ${pulseOpacity})`;
        ctx.fill();

        // Glow
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size * 3, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(0, 180, 255, ${pulseOpacity * 0.1})`;
        ctx.fill();
      });

      // Draw network lines
      for (let i = 0; i < this.particles.length; i++) {
        for (let j = i + 1; j < this.particles.length; j++) {
          const a = this.particles[i];
          const b = this.particles[j];
          const dx = a.x - b.x;
          const dy = a.y - b.y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < this.CONNECTION_DIST) {
            const opacity = (1 - dist / this.CONNECTION_DIST) * 0.25;
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.strokeStyle = `rgba(0, 180, 255, ${opacity})`;
            ctx.lineWidth = 0.5;
            ctx.stroke();
          }
        }
      }
    }
  }

  // ============================================================
  // 7. FLOATING ICONS
  // ============================================================
  class FloatingIcons {
    init() {
      if (!els.iconsContainer) return;
      const icons = HERO360_CONFIG.floatingIcons || [];

      icons.forEach((ic, index) => {
        const el = document.createElement('div');
        el.className = 'hero360__icon';
        el.textContent = ic.icon;
        el.style.left = `${ic.x}%`;
        el.style.top = `${ic.y}%`;
        el.style.animationDelay = `${ic.delay || 0}s`;
        el.style.animationDuration = `${4 + Math.random() * 2}s`;
        els.iconsContainer.appendChild(el);
      });
    }
  }

  // ============================================================
  // 8. SCROLL MANAGER
  // ============================================================
  class ScrollManager {
    constructor() {
      this.heroHeight = 0;
    }

    init() {
      this.heroHeight = els.root.offsetHeight;
      window.addEventListener('scroll', () => this.onScroll(), { passive: true });
      window.addEventListener('resize', () => {
        this.heroHeight = els.root.offsetHeight;
      }, { passive: true });
    }

    onScroll() {
      const scrollY = window.scrollY || window.pageYOffset;
      const threshold = HERO360_CONFIG.scrollZoom.threshold;
      state.scroll.target = Math.min(1, scrollY / threshold);

      // Fade out hero content on scroll
      const opacity = Math.max(0, 1 - scrollY / (this.heroHeight * 0.5));
      els.root.style.opacity = opacity;
    }
  }

  // ============================================================
  // 9. MAIN LOOP
  // ============================================================
  class MainLoop {
    constructor(panorama, particles) {
      this.panorama = panorama;
      this.particles = particles;
    }

    start() {
      const loop = () => {
        // Smooth scroll
        state.scroll.progress += (state.scroll.target - state.scroll.progress) * 0.06;

        this.panorama.update();
        this.particles.draw();

        // Apply parallax to hotspots
        const hotspots = document.querySelectorAll('.hero360__hotspot');
        hotspots.forEach((hs) => {
          const px = hs.style.getPropertyValue('--parallax-x') || '0px';
          const py = hs.style.getPropertyValue('--parallax-y') || '0px';
          hs.style.transform = `translate(calc(-50% + ${px}), calc(-50% + ${py}))`;
        });

        state.rafId = requestAnimationFrame(loop);
      };

      loop();
    }
  }

  // ============================================================
  // 10. ENTRY POINT
  // ============================================================
  function init() {
    if (state.initialized) return;
    state.initialized = true;

    detectMobile();
    cacheElements();

    // Guard: old hero section was replaced, skip if root not found
    if (!els.root) {
      console.warn('Hero360: .hero360 section not found. Skipping initialization.');
      return;
    }

    // Wait for GSAP
    if (typeof gsap === 'undefined') {
      console.warn('GSAP not loaded. Some animations may not work.');
    }

    // Init modules
    const panorama = new Panorama();
    const hotspots = new Hotspots();
    const particles = new Particles();
    const icons = new FloatingIcons();
    const scrollManager = new ScrollManager();

    panorama.init();
    hotspots.init();
    particles.init();
    icons.init();
    scrollManager.init();

    // Entry animation
    if (els.root && gsap) {
      gsap.fromTo(els.root,
        { opacity: 0 },
        { opacity: 1, duration: 1.5, ease: 'power2.out' }
      );

      // Stagger hotspot appearance
      const hs = document.querySelectorAll('.hero360__hotspot');
      gsap.fromTo(hs,
        { opacity: 0, y: 20, scale: 0.8 },
        {
          opacity: 1, y: 0, scale: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'back.out(1.7)',
          delay: 0.5,
        }
      );

      // GSAP ScrollTrigger for content section
      if (els.content && gsap.ScrollTrigger) {
        gsap.fromTo(els.content,
          { opacity: 0, y: 60 },
          {
            opacity: 1, y: 0,
            duration: 1.2,
            scrollTrigger: {
              trigger: els.content,
              start: 'top 85%',
              toggleActions: 'play none none reverse',
            },
          }
        );
      }
    }

    // Start main loop
    const loop = new MainLoop(panorama, particles);
    loop.start();

    // Hide loader
    if (els.loader) {
      setTimeout(() => {
        els.loader.classList.add('hero360__loader--hidden');
      }, 600);
    }

    // Handle resize
    window.addEventListener('resize', () => {
      detectMobile();
    }, { passive: true });
  }

  // ============================================================
  // 11. BOOT
  // ============================================================
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();