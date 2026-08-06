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