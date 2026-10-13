<?php
/**
 * Shared sidebar dashboard layout for the user area.
 *
 * Variables expected:
 *   $user           - current user array (from Auth::user())
 *   $activeSection  - one of: 'overview', 'models', 'assessments', 'consolidated'
 *   $content        - rendered page content (from ob_get_clean() in the view)
 *   $title          - page title
 *   $extraStyles    - (optional) additional CSS
 *   $extraScripts   - (optional) additional JS
 *   $chartLocale    - (optional) locale string for chart.js
 */
$langCode = $_SESSION['lang'] ?? 'fr';
$levelNameField = $langCode === 'ar' ? 'name_ar' : ($langCode === 'fr' ? 'name_fr' : 'name');
$chartLocale = $chartLocale ?? ($langCode === 'ar' ? 'ar' : ($langCode === 'en' ? 'en-US' : 'fr-FR'));
$userInitials = strtoupper(mb_substr($user['firstname'] ?? 'U', 0, 1));
$userName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
$navItems = [
    'overview'    => ['icon' => 'fa-gauge-high',      'label' => __('dashboard.nav.overview')    ?: 'Vue d\'ensemble', 'href' => '/user/dashboard'],
    'models'      => ['icon' => 'fa-layer-group',     'label' => __('dashboard.nav.models')      ?: 'Modèles & Analyses', 'href' => '/user/models'],
    'assessments' => ['icon' => 'fa-clipboard-list',  'label' => __('dashboard.nav.assessments') ?: 'Évaluations',       'href' => '/assessment/start'],
    'consolidated'=> ['icon' => 'fa-diagram-project', 'label' => __('dashboard.nav.consolidated')?: 'Rapports consolidés','href' => '/user/consolidated'],
];
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
  --vx-primary: #6366f1;
  --vx-primary-dark: #5558e3;
  --vx-primary-light: rgba(99,102,241,0.12);
  --vx-primary-glow: rgba(99,102,241,0.25);
  --vx-primary-gradient: linear-gradient(135deg, #6366f1, #818cf8);
  --vx-success: #10b981;
  --vx-success-light: rgba(16,185,129,0.12);
  --vx-success-gradient: linear-gradient(135deg, #10b981, #34d399);
  --vx-danger: #ef4444;
  --vx-danger-light: rgba(239,68,68,0.12);
  --vx-danger-gradient: linear-gradient(135deg, #ef4444, #f87171);
  --vx-warning: #f59e0b;
  --vx-warning-light: rgba(245,158,11,0.12);
  --vx-warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
  --vx-info: #06b6d4;
  --vx-info-light: rgba(6,182,212,0.12);
  --vx-info-gradient: linear-gradient(135deg, #06b6d4, #22d3ee);
  --vx-body-bg: #f5f3ed;
  --vx-card-bg: #fffdf8;
  --vx-card-border: #e7e1d7;
  --vx-card-border-hover: #cfc3b3;
  --vx-divider: #eee9e1;
  --vx-text-primary: #17212b;
  --vx-text-secondary: #475569;
  --vx-text-muted: #7d8794;
  --vx-radius-sm: 0.375rem;
  --vx-radius-md: 0.625rem;
  --vx-radius-lg: 0.875rem;
  --vx-radius-xl: 1.25rem;
  --vx-shadow-md: 0 4px 24px rgba(80,64,42,0.07);
  --vx-shadow-lg: 0 12px 48px rgba(80,64,42,0.11);
  --vx-transition: 0.25s ease;
  --ud-font: 'Manrope', 'Inter', system-ui, sans-serif;
  --ud-sidebar-w: 256px;
}

/* ====== Shell ====== */
.ud-shell {
  display: flex;
  min-height: 100vh;
  background: var(--vx-body-bg);
  background-image:
    radial-gradient(ellipse 60% 50% at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 80% 100%, rgba(6,182,212,0.04) 0%, transparent 60%);
  background-attachment: fixed;
  font-family: var(--ud-font);
}

/* ====== Sidebar ====== */
.ud-sidebar {
  width: var(--ud-sidebar-w);
  flex-shrink: 0;
  background: var(--vx-card-bg);
  border-right: 1px solid var(--vx-card-border);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  z-index: 200;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.ud-sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 1.25rem 1.5rem;
  font-weight: 800;
  font-size: 1rem;
  color: var(--vx-text-primary);
  letter-spacing: -0.3px;
  text-decoration: none;
  border-bottom: 1px solid var(--vx-divider);
}
.ud-sidebar-brand .brand-icon {
  width: 34px; height: 34px;
  background: var(--vx-primary-gradient);
  border-radius: 0.5rem;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.85rem; color: #fff; font-weight: 800;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
  transition: transform 0.3s ease;
}
.ud-sidebar-brand:hover .brand-icon { transform: scale(1.05) rotate(-3deg); }

.ud-sidebar-nav {
  flex: 1;
  padding: 1rem 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  overflow-y: auto;
}
.ud-nav-section-label {
  font-size: 0.6rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--vx-text-muted);
  padding: 0.75rem 0.75rem 0.35rem;
}
.ud-nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.85rem;
  border-radius: var(--vx-radius-md);
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--vx-text-secondary);
  text-decoration: none;
  transition: all var(--vx-transition);
  position: relative;
  white-space: nowrap;
}
.ud-nav-item i { font-size: 0.9rem; width: 20px; text-align: center; flex-shrink: 0; }
.ud-nav-item:hover {
  background: var(--vx-primary-light);
  color: var(--vx-primary);
}
.ud-nav-item.is-active {
  background: var(--vx-primary-light);
  color: var(--vx-primary);
}
.ud-nav-item.is-active::before {
  content: '';
  position: absolute;
  left: 0; top: 50%;
  transform: translateY(-50%);
  width: 4px; height: 60%;
  background: var(--vx-primary);
  border-radius: 0 4px 4px 0;
}
.ud-nav-item .ud-nav-badge {
  margin-left: auto;
  font-size: 0.6rem;
  font-weight: 700;
  padding: 0.1rem 0.4rem;
  border-radius: 0.25rem;
  background: var(--vx-primary-light);
  color: var(--vx-primary);
}

/* Sidebar footer */
.ud-sidebar-foot {
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--vx-divider);
}
.ud-sidebar-user {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 0.75rem;
}
.ud-sidebar-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex; align-items: center; justify-content: center;
  font-size: 0.8rem; font-weight: 800; color: #fff;
  flex-shrink: 0;
  box-shadow: 0 4px 12px var(--vx-primary-glow);
}
.ud-sidebar-user-info {
  flex: 1; min-width: 0;
}
.ud-sidebar-user-name {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--vx-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ud-sidebar-user-role {
  font-size: 0.62rem;
  color: var(--vx-text-muted);
}
.ud-lang-dropdown { position: relative; }
.ud-lang-btn {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.35rem 0.6rem;
  border-radius: 0.5rem;
  background: transparent;
  border: 1px solid var(--vx-card-border);
  cursor: pointer;
  font-family: var(--ud-font);
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--vx-text-secondary);
  transition: all var(--vx-transition);
  width: 100%;
  justify-content: center;
  margin-bottom: 0.5rem;
}
.ud-lang-btn:hover { background: var(--vx-primary-light); border-color: rgba(99,102,241,0.20); color: var(--vx-primary); }
.ud-lang-current { font-weight: 800; color: var(--vx-primary); }
.ud-lang-menu {
  position: absolute;
  bottom: calc(100% + 0.4rem);
  left: 0; right: 0;
  min-width: 140px;
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md);
  box-shadow: var(--vx-shadow-lg);
  padding: 0.3rem;
  opacity: 0; visibility: hidden;
  transform: translateY(6px);
  transition: all 0.2s ease;
  z-index: 300;
}
.ud-lang-dropdown.is-open .ud-lang-menu { opacity: 1; visibility: visible; transform: translateY(0); }
.ud-lang-item {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.45rem 0.65rem;
  border-radius: 0.375rem;
  font-size: 0.75rem; font-weight: 500;
  color: var(--vx-text-secondary);
  text-decoration: none;
  transition: all 0.15s ease;
}
.ud-lang-item:hover { background: var(--vx-primary-light); color: var(--vx-primary); }
.ud-lang-item.is-active { background: var(--vx-primary-light); color: var(--vx-primary); font-weight: 700; }
.ud-lang-flag {
  font-size: 0.6rem; font-weight: 800;
  width: 22px; height: 22px;
  border-radius: 0.25rem;
  display: flex; align-items: center; justify-content: center;
  background: var(--vx-primary-light); color: var(--vx-primary);
}
.ud-lang-item.is-active .ud-lang-flag { background: var(--vx-primary); color: #fff; }

.ud-logout-btn {
  display: flex; align-items: center; justify-content: center; gap: 0.4rem;
  width: 100%;
  padding: 0.45rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem; font-weight: 600;
  font-family: var(--ud-font);
  color: var(--vx-danger);
  background: var(--vx-danger-light);
  border: 1px solid rgba(239,68,68,0.15);
  text-decoration: none;
  transition: all var(--vx-transition);
  cursor: pointer;
}
.ud-logout-btn:hover {
  background: rgba(239,68,68,0.15);
  border-color: rgba(239,68,68,0.30);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239,68,68,0.15);
  color: var(--vx-danger);
}

/* ====== Main area ====== */
.ud-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.ud-topbar {
  background: rgba(255,253,248,0.92);
  backdrop-filter: blur(24px) saturate(1.8);
  -webkit-backdrop-filter: blur(24px) saturate(1.8);
  border-bottom: 1px solid var(--vx-card-border);
  padding: 0.625rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 12px rgba(80,64,42,0.04);
  min-height: 56px;
}
.ud-topbar-title {
  font-size: 1rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  letter-spacing: -0.2px;
}
.ud-topbar-sub {
  font-size: 0.7rem;
  color: var(--vx-text-muted);
  font-weight: 500;
}
.ud-topbar-right { display: flex; align-items: center; gap: 0.65rem; }
.ud-menu-toggle {
  display: none;
  background: transparent;
  border: 1px solid var(--vx-card-border);
  border-radius: 0.5rem;
  padding: 0.4rem 0.55rem;
  cursor: pointer;
  color: var(--vx-text-secondary);
  font-size: 0.9rem;
  transition: all var(--vx-transition);
}
.ud-menu-toggle:hover { background: var(--vx-primary-light); color: var(--vx-primary); }

.ud-content {
  flex: 1;
  padding: 2rem;
  max-width: 1140px;
  width: 100%;
  margin: 0 auto;
}

/* ====== Backdrop for mobile sidebar ====== */
.ud-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(23,33,43,0.4);
  z-index: 190;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.ud-backdrop.is-visible { display: block; opacity: 1; }

/* ====== Responsive ====== */
@media (max-width: 1024px) {
  .ud-sidebar {
    position: fixed;
    top: 0; left: 0;
    transform: translateX(-100%);
    box-shadow: var(--vx-shadow-lg);
  }
  .ud-sidebar.is-open { transform: translateX(0); }
  .ud-menu-toggle { display: inline-flex; }
}
@media (max-width: 768px) {
  .ud-content { padding: 1rem; }
  .ud-topbar { padding: 0.5rem 1rem; }
}

/* ====== Mobile bottom nav ====== */
.ud-bottom-nav {
  display: none;
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 1050;
  background: rgba(255,253,248,0.96);
  border-top: 1px solid var(--vx-card-border);
  padding: 0.35rem 0;
  justify-content: space-around;
  backdrop-filter: blur(12px);
}
.ud-bottom-nav a {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  font-size: 0.55rem;
  padding: 0.25rem 0.5rem;
  gap: 0.15rem;
}
.ud-bottom-nav a i { font-size: 0.9rem; }
@media (max-width: 768px) {
  .ud-bottom-nav { display: flex; }
  .ud-content { padding-bottom: 5rem; }
}
</style>

<div class="ud-shell">
  <!-- Sidebar -->
  <aside class="ud-sidebar" id="udSidebar">
    <a href="/user/dashboard" class="ud-sidebar-brand">
      <span class="brand-icon">N</span>
      <span><?= e(__('dashboard.brand')) ?></span>
    </a>
    <nav class="ud-sidebar-nav">
      <div class="ud-nav-section-label"><?= e(__('dashboard.nav.menu') ?: 'Menu') ?></div>
      <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= $item['href'] ?>" class="ud-nav-item <?= ($activeSection ?? '') === $key ? 'is-active' : '' ?>">
          <i class="fas <?= $item['icon'] ?>"></i>
          <span><?= e($item['label']) ?></span>
          <?php if ($key === 'assessments' && !empty($totalAssessments)): ?>
            <span class="ud-nav-badge"><?= $totalAssessments ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="ud-sidebar-foot">
      <div class="ud-sidebar-user">
        <div class="ud-sidebar-avatar"><?= $userInitials ?></div>
        <div class="ud-sidebar-user-info">
          <div class="ud-sidebar-user-name"><?= e($userName) ?></div>
          <div class="ud-sidebar-user-role"><?= e(__('dashboard.nav.client') ?: 'Client') ?></div>
        </div>
      </div>
      <div class="ud-lang-dropdown" id="udLangDropdown">
        <button class="ud-lang-btn" id="udLangBtn" type="button">
          <i class="fas fa-globe"></i>
          <span class="ud-lang-current"><?= strtoupper($langCode) ?></span>
          <i class="fas fa-chevron-down" style="font-size:0.6rem;"></i>
        </button>
        <div class="ud-lang-menu">
          <?php foreach (['fr'=>'Français','en'=>'English','ar'=>'العربية','es'=>'Español'] as $l => $lName): ?>
            <a href="/lang/<?= $l ?>" class="ud-lang-item <?= $langCode===$l ? 'is-active' : '' ?>">
              <span class="ud-lang-flag"><?= strtoupper($l) ?></span>
              <span><?= $lName ?></span>
              <?php if ($langCode===$l): ?>
                <i class="fas fa-check" style="margin-left:auto;font-size:0.65rem;color:var(--vx-primary);"></i>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="/logout" class="ud-logout-btn">
        <i class="fas fa-arrow-right-from-bracket"></i>
        <span><?= e(__('dashboard.logout')) ?></span>
      </a>
    </div>
  </aside>

  <!-- Backdrop -->
  <div class="ud-backdrop" id="udBackdrop"></div>

  <!-- Main -->
  <div class="ud-main">
    <!-- Topbar -->
    <header class="ud-topbar">
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <button class="ud-menu-toggle" id="udMenuToggle" type="button" aria-label="Menu">
          <i class="fas fa-bars"></i>
        </button>
        <div>
          <div class="ud-topbar-title"><?= e($pageTitle ?? $title ?? '') ?></div>
          <?php if (!empty($pageSubtitle)): ?>
            <div class="ud-topbar-sub"><?= e($pageSubtitle) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="ud-topbar-right">
        <?php if (!empty($topbarActions)) echo $topbarActions; ?>
      </div>
    </header>

    <!-- Content -->
    <div class="ud-content">
      <?= $content ?? '' ?>
    </div>
  </div>
</div>

<!-- Mobile bottom nav -->
<nav class="ud-bottom-nav">
  <a href="/user/dashboard" style="color:<?= ($activeSection??'')==='overview'?'var(--vx-primary)':'var(--vx-text-muted)' ?>">
    <i class="fas fa-gauge-high"></i><span><?= e(__('dashboard.nav.home') ?: 'Accueil') ?></span>
  </a>
  <a href="/user/models" style="color:<?= ($activeSection??'')==='models'?'var(--vx-primary)':'var(--vx-text-muted)' ?>">
    <i class="fas fa-layer-group"></i><span><?= e(__('dashboard.nav.models') ?: 'Modèles') ?></span>
  </a>
  <a href="/assessment/start" style="color:var(--vx-text-muted);">
    <i class="fas fa-plus-circle"></i><span><?= e(__('dashboard.nav.new') ?: 'Nouveau') ?></span>
  </a>
  <a href="/" style="color:var(--vx-text-muted);">
    <i class="fas fa-globe"></i><span><?= e(__('dashboard.nav.site') ?: 'Site') ?></span>
  </a>
  <a href="/logout" style="color:var(--vx-danger);">
    <i class="fas fa-sign-out-alt"></i><span><?= e(__('dashboard.nav.quit') ?: 'Quitter') ?></span>
  </a>
</nav>

<script>
(function() {
  var sidebar = document.getElementById('udSidebar');
  var backdrop = document.getElementById('udBackdrop');
  var toggle = document.getElementById('udMenuToggle');
  if (!sidebar || !toggle) return;
  function openSidebar() { sidebar.classList.add('is-open'); backdrop.classList.add('is-visible'); }
  function closeSidebar() { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-visible'); }
  toggle.addEventListener('click', function() {
    if (sidebar.classList.contains('is-open')) { closeSidebar(); } else { openSidebar(); }
  });
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  var dropdown = document.getElementById('udLangDropdown');
  var btn = document.getElementById('udLangBtn');
  if (dropdown && btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); dropdown.classList.toggle('is-open'); });
    document.addEventListener('click', function(e) { if (!dropdown.contains(e.target)) dropdown.classList.remove('is-open'); });
  }
})();
</script>
