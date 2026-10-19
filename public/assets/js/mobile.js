/* ── AQMI Mobile Interactions ── */

document.addEventListener('DOMContentLoaded', function() {

  // Admin sidebar hamburger toggle
  const sidebarToggles = document.querySelectorAll('.mobile-toggle, .mobile-sidebar-overlay');
  const sidebar = document.querySelector('.admin-sidebar');
  const overlay = document.querySelector('.mobile-sidebar-overlay');

  if (sidebarToggles.length && sidebar) {
    sidebarToggles.forEach(el => {
      el.addEventListener('click', function(e) {
        if (this.classList.contains('mobile-sidebar-overlay') && !this.classList.contains('show')) return;
        sidebar.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
        document.querySelectorAll('.mobile-toggle').forEach(t => t.classList.toggle('active'));
      });
    });
  }

  // Close sidebar on nav link click (mobile)
  if (sidebar) {
    sidebar.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          sidebar.classList.remove('show');
          if (overlay) overlay.classList.remove('show');
          document.querySelectorAll('.mobile-toggle').forEach(t => t.classList.remove('active'));
        }
      });
    });
  }

  // Questionnaire mobile domain sheet toggle
  const domainTrigger = document.querySelector('.mobile-domain-trigger');
  const domainSheet = document.querySelector('.q-domain-sidebar');
  if (domainTrigger && domainSheet) {
    domainTrigger.addEventListener('click', function() {
      domainSheet.classList.toggle('show');
    });
    // Close sheet on backdrop click
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768 &&
          domainSheet.classList.contains('show') &&
          !domainSheet.contains(e.target) &&
          !domainTrigger.contains(e.target)) {
        domainSheet.classList.remove('show');
      }
    });
    // Close sheet when domain item clicked
    domainSheet.querySelectorAll('.q-domain-item').forEach(item => {
      item.addEventListener('click', function() {
        domainSheet.classList.remove('show');
      });
    });
  }

  // Responsive tables → card list auto toggle
  function handleResponsiveTables() {
    document.querySelectorAll('.table-responsive').forEach(tableWrap => {
      const cardList = tableWrap.querySelector('.mobile-card-list');
      if (window.innerWidth <= 576 && cardList) {
        const table = tableWrap.querySelector('.data-table');
        if (table) { table.style.display = 'none'; }
        cardList.style.display = 'block';
      } else if (cardList) {
        const table = tableWrap.querySelector('.data-table');
        if (table) { table.style.display = ''; }
        cardList.style.display = '';
      }
    });
  }
  handleResponsiveTables();
  window.addEventListener('resize', handleResponsiveTables);

  // Safe area padding for bottom nav
  const bottomNav = document.querySelector('.mobile-bottom-nav');
  if (bottomNav) {
    const adminContent = document.querySelector('.admin-content');
    if (adminContent && window.innerWidth <= 768) {
      adminContent.style.paddingBottom = '4rem';
    }
  }
});