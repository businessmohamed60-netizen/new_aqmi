/* ═══════════════════════════════════════════════════════════════════
   NOVAQYS V2 — Landing Page JavaScript
   Scroll animations, navigation, interactive effects
   ═══════════════════════════════════════════════════════════════════ */
(function() {
  'use strict';

  /* ─── Navbar scroll effect ────────────────────────────────────── */
  const navbar = document.querySelector('.nav-header');
  if (navbar) {
    const onScroll = () => {
      navbar.classList.toggle('scrolled', window.scrollY > 30);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ─── Mobile menu toggle ──────────────────────────────────────── */
  const toggleBtn = document.getElementById('navToggle');
  const mobileMenu = document.getElementById('navMobile');
  if (toggleBtn && mobileMenu) {
    toggleBtn.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      const icon = toggleBtn.querySelector('i');
      if (icon) {
        icon.className = isOpen ? 'fas fa-xmark' : 'fas fa-bars';
      }
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });
  }

  /* ─── Intersection Observer for animations ────────────────────── */
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -80px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;

        if (el.classList.contains('fade-up')) {
          el.classList.add('visible');
        }

        if (el.classList.contains('fade-in')) {
          el.classList.add('visible');
        }

        if (el.classList.contains('scale-in')) {
          el.classList.add('visible');
        }

        if (el.classList.contains('stagger-children')) {
          el.classList.add('visible');
          const children = el.querySelectorAll(':scope > *');
          children.forEach((child, i) => {
            child.style.transitionDelay = (i * 0.06) + 's';
          });
        }

        if (el.classList.contains('observe-once') || el.dataset.observeOnce !== undefined) {
          observer.unobserve(el);
        }
      }
    });
  }, observerOptions);

  /* ─── Register elements ───────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function() {
    // Observe all animated elements
    document.querySelectorAll('.fade-up:not(.visible)').forEach(el => {
      if (el.getBoundingClientRect().top < window.innerHeight) {
        setTimeout(() => { el.classList.add('visible'); }, 100);
      } else {
        observer.observe(el);
      }
    });

    document.querySelectorAll('.fade-in:not(.visible)').forEach(el => {
      if (el.getBoundingClientRect().top < window.innerHeight) {
        el.classList.add('visible');
      } else {
        observer.observe(el);
      }
    });

    document.querySelectorAll('.scale-in:not(.visible)').forEach(el => {
      if (el.getBoundingClientRect().top < window.innerHeight) {
        el.classList.add('visible');
      } else {
        observer.observe(el);
      }
    });

    // Stagger children
    document.querySelectorAll('.stagger-children:not(.visible)').forEach(el => {
      if (el.getBoundingClientRect().top < window.innerHeight) {
        el.classList.add('visible');
        const children = el.querySelectorAll(':scope > *');
        children.forEach((child, i) => {
          child.style.transitionDelay = (i * 0.06) + 's';
        });
      } else {
        observer.observe(el);
      }
    });

    /* ─── Hero parallax ───────────────────────────────────────────── */
    const hero = document.querySelector('.hero');
    const heroContent = document.querySelector('.hero-content');
    if (hero && heroContent) {
      window.addEventListener('scroll', function() {
        const rect = hero.getBoundingClientRect();
        const progress = Math.max(0, Math.min(1, -rect.top / (rect.height * 0.5)));
        if (progress > 0) {
          heroContent.style.transform = 'translateY(' + (progress * 20) + 'px)';
          heroContent.style.opacity = Math.max(0, 1 - (progress * 1.5));
        } else {
          heroContent.style.transform = '';
          heroContent.style.opacity = '';
        }
      }, { passive: true });
    }

    /* ─── 360° Interactive Ecosystem ───────────────────────────────── */
    const container = document.querySelector('.hero-360-container');
    const image = container ? container.querySelector('.hero-360-image') : null;
    const panel = document.getElementById('hero360Panel');
    const panelIcon = document.getElementById('hero360PanelIcon');
    const panelTitle = document.getElementById('hero360PanelTitle');
    const panelDesc = document.getElementById('hero360PanelDesc');
    const links = container ? container.querySelectorAll('.hero-360-link') : [];

    if (container && image && panel) {
      let mouseX = 0, mouseY = 0;
      let currentX = 0, currentY = 0;

      // ── Mouse parallax ──
      container.addEventListener('mousemove', function(e) {
        const rect = container.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width;
        const y = (e.clientY - rect.top) / rect.height;
        mouseX = (x - 0.5) * 2;
        mouseY = (y - 0.5) * 2;
      });

      container.addEventListener('mouseleave', function() {
        mouseX = 0;
        mouseY = 0;
      });

      // Smooth parallax animation loop
      function animateParallax() {
        currentX += (mouseX - currentX) * 0.08;
        currentY += (mouseY - currentY) * 0.08;
        image.style.transform = 'translate(' + (currentX * 1.2) + '%, ' + (currentY * 0.8) + '%) scale(1.03)';
        requestAnimationFrame(animateParallax);
      }
      animateParallax();

      // ── Info panel on hover ──
      links.forEach(function(link) {
        link.addEventListener('mouseenter', function() {
          var platform = link.dataset.platform || 'Plateforme';
          var desc = link.dataset.desc || '';
          var color = link.dataset.color || '#7367f0';
          var iconEl = link.querySelector('i');

          panelTitle.textContent = platform;
          panelDesc.textContent = desc;

          if (iconEl) {
            panelIcon.innerHTML = iconEl.outerHTML;
          }
          panelIcon.style.background = color + '20';
          panelIcon.style.color = color;

          var arrow = panel.querySelector('.hero-360-panel-arrow');
          if (arrow) {
            arrow.style.background = color + '20';
            arrow.style.borderColor = color + '40';
            arrow.style.color = color;
          }

          link.style.borderColor = color;
          link.style.boxShadow = '0 0 40px ' + color + '66, 0 8px 30px rgba(0,0,0,0.5)';
          panel.classList.add('active');
        });

        link.addEventListener('mouseleave', function() {
          link.style.borderColor = '';
          link.style.boxShadow = '';
          panel.classList.remove('active');
        });

        // ── Smooth scroll on click ──
        link.addEventListener('click', function(e) {
          var href = link.getAttribute('href');
          if (href && href.charAt(0) === '#') {
            e.preventDefault();
            var target = document.querySelector(href);
            if (target) {
              panel.classList.remove('active');
              var header = document.querySelector('.nav-header');
              var offset = header ? header.offsetHeight : 0;
              var targetPos = target.getBoundingClientRect().top + window.scrollY - offset - 20;
              window.scrollTo({ top: targetPos, behavior: 'smooth' });
            }
          }
        });
      });
    }
  });

})();
