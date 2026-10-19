/* ═══════════════════════════════════════════════════════════════════
   NOVAQYS Admin — Vuexy-Inspired JavaScript
   Sidebar Toggle, User Dropdown, Responsive
   ═══════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  // ─── Sidebar Toggle ───────────────────────────────────────────
  const sidebar = document.getElementById('novaSidebar');
  const toggleBtn = document.getElementById('novaSidebarToggle');

  if (sidebar && toggleBtn) {
    // Desktop: toggle collapsed state
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      // Save preference
      try {
        localStorage.setItem('nova_sidebar_collapsed', sidebar.classList.contains('collapsed'));
      } catch(e) {}
    });

    // Restore saved state
    try {
      const saved = localStorage.getItem('nova_sidebar_collapsed');
      if (saved === 'true') {
        sidebar.classList.add('collapsed');
      }
    } catch(e) {}
  }

  // ─── Mobile Sidebar ───────────────────────────────────────────
  const mobileToggle = document.getElementById('novaMobileToggle');
  const sidebarOverlay = document.getElementById('novaSidebarOverlay');

  function openMobileSidebar() {
    if (sidebar) sidebar.classList.add('mobile-open');
    if (sidebarOverlay) sidebarOverlay.classList.add('mobile-open');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileSidebar() {
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (sidebarOverlay) sidebarOverlay.classList.remove('mobile-open');
    document.body.style.overflow = '';
  }

  if (mobileToggle) {
    mobileToggle.addEventListener('click', openMobileSidebar);
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeMobileSidebar);
  }

  // ─── User Dropdown ────────────────────────────────────────────
  const userDropdown = document.getElementById('novaUserDropdown');
  if (userDropdown) {
    const toggle = userDropdown.querySelector('.nova-user-btn');

    // Toggle on click
    toggle.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
    });

    // Close on outside click
    document.addEventListener('click', function() {
      userDropdown.classList.remove('open');
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        userDropdown.classList.remove('open');
      }
    });
  }

  // ─── Auto-close sidebar on route change (mobile) ──────────────
  document.querySelectorAll('.nova-sidebar-link').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 992) {
        closeMobileSidebar();
      }
    });
  });

  // ─── Liens du menu : un seul lien actif à la fois ─────────────
  const sidebarLinks = Array.prototype.slice.call(document.querySelectorAll('.nova-sidebar-link'));

  function cleanPath(p) {
    return (p || '').replace(/\/+$/, '') || '/';
  }

  function fixActiveLink() {
    const here = cleanPath(location.pathname);
    const currentParams = new URLSearchParams(location.search);
    let best = null;
    let bestScore = -1;

    sidebarLinks.forEach(function(link) {
      const href = link.getAttribute('href');
      if (!href || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) return;

      let url;
      try { url = new URL(href, location.href); } catch (e) { return; }
      if (url.origin !== location.origin) return;

      // Correspondance sur des segments entiers : /report ne correspond pas à /report-studio
      const p = cleanPath(url.pathname);
      const matches = here === p || (p !== '/' && here.indexOf(p + '/') === 0);
      if (!matches) return;

      // Si le lien porte des paramètres (?tab=…), ils doivent tous correspondre
      let paramCount = 0;
      let paramsOk = true;
      url.searchParams.forEach(function(value, key) {
        paramCount++;
        if (currentParams.get(key) !== value) paramsOk = false;
      });
      if (!paramsOk) return;

      // Le lien le plus précis gagne
      const score = p.length * 10 + paramCount;
      if (score > bestScore) { best = link; bestScore = score; }
    });

    // Aucune correspondance : on garde, parmi les liens déjà actifs, le plus précis
    if (!best) {
      sidebarLinks.forEach(function(link) {
        if (!link.classList.contains('active')) return;
        const len = (link.getAttribute('href') || '').length;
        if (len > bestScore) { best = link; bestScore = len; }
      });
    }
    if (!best) return;

    sidebarLinks.forEach(function(link) {
      const on = (link === best);
      link.classList.toggle('active', on);
      if (on) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
  }
  fixActiveLink();

  // ─── Icônes colorées (couleur choisie selon le libellé / l'URL) ─
  const ICON_RULES = [
    ['red',     /logout|d[ée]connex|quitter|sortie/],
    ['pink',    /studio|design|mod[èe]le|template/],
    ['emerald', /report|rapport|export|stat|analy/],
    ['orange',  /dashboard|tableau|accueil|home/],
    ['amber',   /assess|[ée]valuat|audit|diagnos/],
    ['cyan',    /user|utilisateur|client|compan|entreprise|team|[ée]quipe/],
    ['teal',    /setting|param[èe]tre|config/],
    ['lime',    /billing|factur|paiement|abonnement/],
    ['rose',    /\blogs?\b|journal|alert|notif/],
    ['fuchsia', /\bai\b|\bia\b|assistant|chat/]
  ];
  const ICON_FALLBACK = ['orange', 'pink', 'emerald', 'amber', 'cyan', 'teal', 'lime', 'rose', 'fuchsia'];

  function pickIconColor(text) {
    for (let i = 0; i < ICON_RULES.length; i++) {
      if (ICON_RULES[i][1].test(text)) return ICON_RULES[i][0];
    }
    let h = 0;
    for (let j = 0; j < text.length; j++) h = (h * 31 + text.charCodeAt(j)) >>> 0;
    return ICON_FALLBACK[h % ICON_FALLBACK.length];
  }

  sidebarLinks.forEach(function(link) {
    if (link.hasAttribute('data-ic')) return; // couleur définie à la main : on respecte
    const label = (link.textContent || '').trim();
    const text = ((link.getAttribute('href') || '') + ' ' + label).toLowerCase();
    link.setAttribute('data-ic', pickIconColor(text));
  });

  // ─── Menu replié : infobulle avec le nom du lien ──────────────
  function syncTitles() {
    const collapsed = sidebar && sidebar.classList.contains('collapsed') && window.innerWidth > 992;
    sidebarLinks.forEach(function(link) {
      const label = link.querySelector('span');
      const text = label ? label.textContent.trim() : '';
      if (collapsed && text) link.setAttribute('title', text);
      else link.removeAttribute('title');
    });
  }
  syncTitles();
  if (sidebar && window.MutationObserver) {
    new MutationObserver(syncTitles).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
  }
  window.addEventListener('resize', syncTitles);

  // ─── Scroll sidebar to active link on page load ───────────────
  function scrollSidebarToActive() {
    const nav = document.querySelector('.nova-sidebar-nav');
    if (!nav) return;
    const active = nav.querySelector('.nova-sidebar-link.active');
    if (!active) return;
    // Prefer native scrollIntoView (centers the item), with manual fallback.
    try {
      active.scrollIntoView({ block: 'center', behavior: 'smooth' });
    } catch (e) {
      const navRect = nav.getBoundingClientRect();
      const activeRect = active.getBoundingClientRect();
      nav.scrollTop = (activeRect.top - navRect.top) - (navRect.height - activeRect.height) / 2 + nav.scrollTop;
    }
  }
  // Run after DOM is ready, after load, and after a short delay to be safe.
  scrollSidebarToActive();
  window.addEventListener('load', function() {
    setTimeout(scrollSidebarToActive, 50);
  });
  setTimeout(scrollSidebarToActive, 150);

  // ─── Handle window resize ─────────────────────────────────────
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 992) {
        closeMobileSidebar();
      }
    }, 100);
  });

  console.log('NOVAQYS Admin initialized');
})();