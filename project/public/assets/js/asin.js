/* ═══════════════════════════════════════════════════════════════════
   ASIN — Automotive Supplier Intelligence Network
   Premium Animations
   ═══════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  const APP = {};

  // ─── DOM Cache ──────────────────────────────────────────────────
  APP.cache = {};

  function cacheDOM() {
    APP.cache.page = document.querySelector('.asin-page');
    if (!APP.cache.page) return false;
    APP.cache.hero = APP.cache.page.querySelector('.asin-hero');
    APP.cache.canvas = APP.cache.page.querySelector('.asin-hero-canvas');
    APP.cache.sections = APP.cache.page.querySelectorAll('.asin-section');
    APP.cache.dashboardValues = APP.cache.page.querySelectorAll('.asin-dashboard-card-value');
    APP.cache.rfqSuppliers = APP.cache.page.querySelectorAll('.asin-rfq-supplier');
    APP.cache.whyCards = APP.cache.page.querySelectorAll('.asin-why-card');
    APP.cache.productCards = APP.cache.page.querySelectorAll('.asin-product-card');
    APP.cache.passportItems = APP.cache.page.querySelectorAll('.asin-passport-item');
    APP.cache.marketplaceDots = APP.cache.page.querySelectorAll('.asin-marketplace-dot');
    return true;
  }

  // ─── Hero Globe Canvas ──────────────────────────────────────────
  APP.globe = {
    particles: [],
    connections: [],
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

    // Create particles
    const count = 200;
    const radius = Math.min(canvas.width, canvas.height) * 0.2;

    APP.globe.particles = [];
    for (let i = 0; i < count; i++) {
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(2 * Math.random() - 1);
      APP.globe.particles.push({
        theta: theta,
        phi: phi,
        x: 0, y: 0, z: 0,
        size: 1 + Math.random() * 2,
        color: Math.random() > 0.7 ? '#06b6d4' : '#2563eb',
        speed: 0.5 + Math.random() * 0.5
      });
    }

    // Create connections
    APP.globe.connections = [];
    for (let i = 0; i < 30; i++) {
      const p1 = Math.floor(Math.random() * count);
      let p2 = Math.floor(Math.random() * count);
      if (p2 === p1) p2 = (p1 + 1) % count;
      APP.globe.connections.push({ p1: p1, p2: p2 });
    }

    function project3D(x, y, z) {
      const fl = 300;
      const scale = fl / (fl + z + radius);
      return {
        x: x * scale + canvas.width / 2,
        y: y * scale + canvas.height / 2,
        scale: scale
      };
    }

    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const rotX = APP.globe.mouseY * 0.005;
      const rotY = APP.globe.rotation + APP.globe.mouseX * 0.005;

      // Update particle positions
      const positions = [];
      APP.globe.particles.forEach(function(p) {
        const theta = p.theta + rotY;
        const phi = p.phi + rotX;
        const x = radius * Math.sin(phi) * Math.cos(theta);
        const y = radius * Math.cos(phi);
        const z = radius * Math.sin(phi) * Math.sin(theta);
        positions.push({ x: x, y: y, z: z, color: p.color, size: p.size });
      });

      // Sort by z for depth
      const sorted = positions.map(function(p, i) { return { idx: i, z: p.z }; });
      sorted.sort(function(a, b) { return a.z - b.z; });

      // Draw connections (behind)
      ctx.lineWidth = 0.5;
      APP.globe.connections.forEach(function(conn) {
        const p1 = positions[conn.p1];
        const p2 = positions[conn.p2];
        const proj1 = project3D(p1.x, p1.y, p1.z);
        const proj2 = project3D(p2.x, p2.y, p2.z);
        const alpha = Math.max(0, 0.15 * (1 - Math.abs(p1.z - p2.z) / (radius * 2)));
        ctx.strokeStyle = 'rgba(37,99,235,' + alpha + ')';
        ctx.beginPath();
        ctx.moveTo(proj1.x, proj1.y);
        ctx.lineTo(proj2.x, proj2.y);
        ctx.stroke();
      });

      // Draw particles (front to back)
      sorted.forEach(function(s) {
        const p = positions[s.idx];
        const proj = project3D(p.x, p.y, p.z);
        const alpha = 0.3 + 0.7 * (p.z + radius) / (radius * 2);
        const size = p.size * proj.scale;
        if (size < 0.5) return;
        ctx.beginPath();
        ctx.arc(proj.x, proj.y, size, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.globalAlpha = alpha;
        ctx.fill();
        ctx.globalAlpha = 1;

        // Glow for some
        if (size > 1.5) {
          ctx.beginPath();
          ctx.arc(proj.x, proj.y, size * 3, 0, Math.PI * 2);
          ctx.fillStyle = p.color;
          ctx.globalAlpha = 0.05;
          ctx.fill();
          ctx.globalAlpha = 1;
        }
      });

      APP.globe.rotation += 0.003;
      APP.globe.animId = requestAnimationFrame(draw);
    }

    draw();

    // Mouse tracking
    document.addEventListener('mousemove', function(e) {
      const rect = canvas.getBoundingClientRect();
      APP.globe.mouseX = (e.clientX - rect.left - rect.width / 2) / rect.width;
      APP.globe.mouseY = (e.clientY - rect.top - rect.height / 2) / rect.height;
    });
  };

  // ─── Hero Particles (2D overlay) ────────────────────────────────
  APP.heroParticles = [];

  APP.initHeroParticles = function() {
    const container = APP.cache.hero;
    if (!container) return;
    const count = 40;

    for (let i = 0; i < count; i++) {
      const el = document.createElement('div');
      el.style.cssText =
        'position:fixed;pointer-events:none;width:' + (1 + Math.random() * 2) + 'px;' +
        'height:' + (1 + Math.random() * 2) + 'px;' +
        'background:' + (Math.random() > 0.5 ? '#2563eb' : '#06b6d4') + ';' +
        'border-radius:50%;opacity:0;z-index:1;';
      document.body.appendChild(el);
      APP.heroParticles.push({
        el: el,
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        vx: (Math.random() - 0.5) * 0.3,
        vy: -0.1 - Math.random() * 0.2,
        size: 1 + Math.random() * 2,
        alpha: 0.1 + Math.random() * 0.3
      });
    }

    function animateParticles() {
      APP.heroParticles.forEach(function(p) {
        p.x += p.vx;
        p.y += p.vy;
        if (p.y < -10) {
          p.y = window.innerHeight + 10;
          p.x = Math.random() * window.innerWidth;
        }
        if (p.x < -10 || p.x > window.innerWidth + 10) {
          p.x = Math.random() * window.innerWidth;
        }
        p.el.style.transform = 'translate(' + p.x + 'px,' + p.y + 'px)';
        p.el.style.opacity = p.alpha * (0.5 + 0.5 * Math.sin(Date.now() * 0.001 + p.x));
      });
      requestAnimationFrame(animateParticles);
    }
    animateParticles();
  };

  // ─── GSAP ScrollTrigger Animations ──────────────────────────────
  APP.initGSAP = function() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      var checkGSAP = setInterval(function() {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
          clearInterval(checkGSAP);
          APP.setupScrollTriggers();
        }
      }, 200);
      setTimeout(function() { clearInterval(checkGSAP); }, 10000);
      return;
    }
    APP.setupScrollTriggers();
  };

  APP.setupScrollTriggers = function() {
    // Ensure GSAP and ScrollTrigger are registered
    if (gsap.ScrollTrigger) {
      gsap.registerPlugin(ScrollTrigger);
    }

    // ─── Hero Parallax ──────────────────────────────────────────
    gsap.to('.asin-hero-content', {
      y: 80,
      ease: 'none',
      scrollTrigger: {
        trigger: '.asin-hero',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
      }
    });

    gsap.to('.asin-hero-canvas', {
      scale: 1.1,
      ease: 'none',
      scrollTrigger: {
        trigger: '.asin-hero',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
      }
    });

    // ─── Passport Items ─────────────────────────────────────────
    APP.cache.passportItems.forEach(function(item, i) {
      gsap.fromTo(item,
        { opacity: 0, y: 20 },
        {
          opacity: 1, y: 0,
          duration: 0.6,
          delay: i * 0.08,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: '.asin-passport-card',
            start: 'top 70%',
            end: 'top 30%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Product Cards ──────────────────────────────────────────
    APP.cache.productCards.forEach(function(card, i) {
      gsap.fromTo(card,
        { opacity: 0, y: 30, scale: 0.95 },
        {
          opacity: 1, y: 0, scale: 1,
          duration: 0.6,
          delay: i * 0.1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: '.asin-products-grid',
            start: 'top 75%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── RFQ Suppliers ──────────────────────────────────────────
    APP.cache.rfqSuppliers.forEach(function(supplier, i) {
      ScrollTrigger.create({
        trigger: '.asin-rfq-demo',
        start: 'top 70%',
        onEnter: function() {
          setTimeout(function() {
            supplier.classList.add('visible');
          }, i * 300);
        },
        once: true
      });
    });

    // ─── Dashboard Values ───────────────────────────────────────
    APP.cache.dashboardValues.forEach(function(el) {
      var text = el.textContent;
      var num = parseFloat(text);
      if (isNaN(num)) return;
      var suffix = text.replace(/[\d.,]/g, '');
      gsap.fromTo(el,
        { textContent: 0 },
        {
          textContent: num,
          duration: 2,
          ease: 'power2.out',
          snap: { textContent: 1 },
          scrollTrigger: {
            trigger: el.closest('.asin-dashboard-card'),
            start: 'top 80%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Why Cards ──────────────────────────────────────────────
    APP.cache.whyCards.forEach(function(card, i) {
      gsap.fromTo(card,
        { opacity: 0, y: 30 },
        {
          opacity: 1, y: 0,
          duration: 0.6,
          delay: i * 0.1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: '.asin-why-grid',
            start: 'top 75%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Marketplace Dots ───────────────────────────────────────
    APP.cache.marketplaceDots.forEach(function(dot, i) {
      gsap.fromTo(dot,
        { opacity: 0, scale: 0 },
        {
          opacity: 1, scale: 1,
          duration: 0.4,
          delay: i * 0.1,
          ease: 'back.out(2)',
          scrollTrigger: {
            trigger: '.asin-marketplace-visual',
            start: 'top 70%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Supply Chain SVG animation ─────────────────────────────
    const svgLines = document.querySelectorAll('.asin-supplychain-visual .sc-line');
    svgLines.forEach(function(line, i) {
      gsap.fromTo(line,
        { strokeDashoffset: 1000 },
        {
          strokeDashoffset: 0,
          duration: 1.5,
          delay: i * 0.3,
          ease: 'power2.inOut',
          scrollTrigger: {
            trigger: '.asin-supplychain-visual',
            start: 'top 70%',
            end: 'top 40%',
            scrub: 0.5
          }
        }
      );
    });

    // ─── Section fade-ins ───────────────────────────────────────
    document.querySelectorAll('.asin-section-fade').forEach(function(el) {
      gsap.fromTo(el,
        { opacity: 0, y: 40 },
        {
          opacity: 1, y: 0,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 80%',
            end: 'top 50%',
            scrub: 0.5
          }
        }
      );
    });

    ScrollTrigger.refresh();
  };

  // ─── Marketplace Tooltip ────────────────────────────────────────
  APP.initMarketplace = function() {
    // Create tooltip
    var tooltip = document.createElement('div');
    tooltip.className = 'asin-marketplace-tooltip';
    document.querySelector('.asin-marketplace-visual')?.appendChild(tooltip);

    // Add dots
    var visual = document.querySelector('.asin-marketplace-visual');
    if (!visual) return;

    var countries = [
      { name: 'ALPHA Industries', country: 'France', score: '87%', x: '52%', y: '32%' },
      { name: 'BETA Forge', country: 'Allemagne', score: '92%', x: '55%', y: '38%' },
      { name: 'GAMMA Precision', country: 'Italie', score: '78%', x: '58%', y: '42%' },
      { name: 'DELTA Parts', country: 'Espagne', score: '71%', x: '48%', y: '44%' },
      { name: 'EPSILON Tech', country: 'Japon', score: '94%', x: '82%', y: '30%' },
      { name: 'ZETA Manufacturing', country: 'Corée', score: '89%', x: '80%', y: '36%' },
      { name: 'ETA Automotive', country: 'États-Unis', score: '85%', x: '22%', y: '30%' },
      { name: 'THETA Components', country: 'Mexique', score: '73%', x: '16%', y: '48%' },
      { name: 'IOTA Foundry', country: 'Brésil', score: '68%', x: '32%', y: '65%' },
      { name: 'KAPTA Mold', country: 'Maroc', score: '76%', x: '46%', y: '55%' },
      { name: 'LAMBDA Casting', country: 'Tunisie', score: '72%', x: '52%', y: '52%' },
      { name: 'OMEGA Steel', country: 'Chine', score: '81%', x: '72%', y: '34%' },
      { name: 'SIGMA Alu', country: 'Inde', score: '77%', x: '68%', y: '46%' },
      { name: 'TAU Precision', country: 'Turquie', score: '79%', x: '60%', y: '42%' },
    ];

    countries.forEach(function(c) {
      var dot = document.createElement('div');
      dot.className = 'asin-marketplace-dot';
      dot.style.left = c.x;
      dot.style.top = c.y;
      dot.style.background = c.score >= 85 ? '#10b981' : c.score >= 75 ? '#2563eb' : '#f59e0b';
      dot.dataset.name = c.name;
      dot.dataset.country = c.country;
      dot.dataset.score = c.score;
      visual.appendChild(dot);

      dot.addEventListener('mouseenter', function(e) {
        tooltip.innerHTML =
          '<strong>' + c.name + '</strong>' +
          '<span>' + c.country + '</span>' +
          '<span class="asin-marketplace-tooltip-score">AQMI ' + c.score + '</span>';
        tooltip.classList.add('visible');
        var rect = visual.getBoundingClientRect();
        var dotRect = dot.getBoundingClientRect();
        tooltip.style.left = (dotRect.left - rect.left + 20) + 'px';
        tooltip.style.top = (dotRect.top - rect.top - 10) + 'px';
      });

      dot.addEventListener('mouseleave', function() {
        tooltip.classList.remove('visible');
      });
    });
  };

  // ─── Init ───────────────────────────────────────────────────────
  APP.init = function() {
    if (!cacheDOM()) return;
    APP.initGlobe();
    APP.initHeroParticles();
    APP.initMarketplace();
    APP.initGSAP();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', APP.init);
  } else {
    APP.init();
  }

})();