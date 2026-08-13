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

    /* ─── Hero Slider Carousel ─────────────────────────────────────── */
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-slider-dot');
    var prevBtn = document.getElementById('heroSlidePrev');
    var nextBtn = document.getElementById('heroSlideNext');
    var progress = document.getElementById('heroSliderProgress');

    if (slides.length > 0) {
      var current = 0;
      var autoTimer = null;
      var SLIDE_DURATION = 7000;

      function goTo(index) {
        slides.forEach(function(s, i) { s.classList.toggle('active', i === index); });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === index); });
        current = index;
        resetProgress();
        restartAuto();
      }
      function next() { goTo((current + 1) % slides.length); }
      function prev() { goTo((current - 1 + slides.length) % slides.length); }

      function resetProgress() {
        if (progress) {
          progress.style.transition = 'none';
          progress.style.width = '0%';
          void progress.offsetWidth;
          progress.style.transition = 'width ' + (SLIDE_DURATION / 1000) + 's linear';
          progress.style.width = '100%';
        }
      }
      function restartAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(next, SLIDE_DURATION);
      }

      if (nextBtn) nextBtn.addEventListener('click', next);
      if (prevBtn) prevBtn.addEventListener('click', prev);
      dots.forEach(function(dot) {
        dot.addEventListener('click', function() { goTo(parseInt(dot.dataset.index, 10)); });
      });

      var heroSection = document.querySelector('.hero-slider');
      if (heroSection) {
        heroSection.addEventListener('mouseenter', function() { clearInterval(autoTimer); });
        heroSection.addEventListener('mouseleave', function() { restartAuto(); });
        var touchStartX = 0;
        heroSection.addEventListener('touchstart', function(e) { touchStartX = e.touches[0].clientX; }, { passive: true });
        heroSection.addEventListener('touchend', function(e) {
          var dx = e.changedTouches[0].clientX - touchStartX;
          if (Math.abs(dx) > 50) { if (dx > 0) prev(); else next(); }
        }, { passive: true });
      }

      document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'ArrowRight') next();
      });

      resetProgress();
      restartAuto();
    }
  });

})();
