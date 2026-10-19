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

    /* ─── Hero parallax (subtle, no fade-out) ──────────────────────── */
    const hero = document.querySelector('.hero');
    const heroContent = document.querySelector('.hero-content');
    if (hero && heroContent) {
      window.addEventListener('scroll', function() {
        const rect = hero.getBoundingClientRect();
        const progress = Math.max(0, Math.min(1, -rect.top / (rect.height * 0.5)));
        if (progress > 0) {
          heroContent.style.transform = 'translateY(' + (progress * 12) + 'px)';
        } else {
          heroContent.style.transform = '';
        }
      }, { passive: true });
    }
  });

})();
