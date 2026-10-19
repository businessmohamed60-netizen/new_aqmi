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
// Page privée : jamais mise en cache (navigateur, proxy, cache de page de l'hébergeur)
if (!headers_sent()) {
    header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-LiteSpeed-Cache-Control: no-cache');
}
$langCode = $_SESSION['lang'] ?? 'fr';
$levelNameField = $langCode === 'ar' ? 'name_ar' : ($langCode === 'fr' ? 'name_fr' : 'name');
$chartLocale = $chartLocale ?? ($langCode === 'ar' ? 'ar' : ($langCode === 'en' ? 'en-US' : 'fr-FR'));
$userInitials = strtoupper(mb_substr($user['firstname'] ?? 'U', 0, 1));
$userName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
$userUnreadNotif = 0;
$userUnreadChat = 0;
try { $userUnreadNotif = \App\Models\Notification::unreadCount(Auth::id()); } catch (\Throwable $e) {}
try { $userUnreadChat = \App\Models\ChatConversation::unreadCountForUser(Auth::id()); } catch (\Throwable $e) {}
$navItems = [
    'models'      => ['icon' => 'fa-layer-group',     'label' => __('dashboard.nav.models')      ?: 'Modèles & Analyses', 'href' => '/user/models', 'ic' => 'orange'],
    'assessments' => ['icon' => 'fa-clipboard-list',  'label' => __('dashboard.nav.assessments') ?: 'Évaluations',       'href' => '/assessment/start', 'ic' => 'emerald'],
    'consolidated'=> ['icon' => 'fa-diagram-project', 'label' => __('dashboard.nav.consolidated')?: 'Rapports consolidés','href' => '/user/consolidated', 'ic' => 'pink'],
    'messages'    => ['icon' => 'fa-comment-dots',    'label' => __('dashboard.nav.messages')    ?: 'Messagerie',        'href' => '/user/messages', 'ic' => 'cyan'],
    'profile'     => ['icon' => 'fa-user-gear',        'label' => __('dashboard.nav.profile')     ?: 'Mon profil',        'href' => '/user/profile', 'ic' => 'amber'],
];
?>
<!-- ud-layout : menu superposé v4 (classes nvm-*, !important) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<meta name="csrf-token" content="<?= csrf_token() ?>">
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
}

/* ====== Shell ====== */
:root {
  --ud-sb-w: 270px;
  --ud-topbar-h: 64px;
  --ud-nav-from: #2563EB;
  --ud-nav-via: #4F46E5;
  --ud-nav-to: #7C3AED;
  --ud-nav-ink: #3730D9;
  --ud-nav-glow: rgba(79,70,229,0.35);
  --ud-sb-ease: 0.4s cubic-bezier(0.22, 1, 0.36, 1);
  --ud-sb-gap: 1rem;
}
.ud-shell {
  min-height: 100vh;
  background: var(--vx-body-bg);
  background-image:
    radial-gradient(ellipse 60% 50% at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 80% 100%, rgba(6,182,212,0.04) 0%, transparent 60%);
  background-attachment: fixed;
  font-family: var(--ud-font);
}
.nvm-main {
  margin-left: 0 !important;
  margin-right: 0 !important;
  min-height: 100vh;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

/* ====== Sidebar ====== */
.nvm-sidebar {
  position: fixed !important;
  top: calc(var(--ud-topbar-h) + 0.75rem);
  inset-inline-start: var(--ud-sb-gap);
  width: var(--ud-sb-w);
  max-width: calc(100vw - var(--ud-sb-gap) * 2);
  max-height: calc(100vh - var(--ud-topbar-h) - 1.5rem);
  max-height: calc(100dvh - var(--ud-topbar-h) - 1.5rem);
  border-radius: 1.5rem;
  background: linear-gradient(170deg, var(--ud-nav-from) 0%, var(--ud-nav-via) 55%, var(--ud-nav-to) 100%);
  box-shadow: 0 24px 60px rgba(55,48,217,0.40), 0 2px 8px rgba(15,23,42,0.14);
  z-index: 1000;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  /* fermé : hors écran et inaccessible au clavier */
  transform: translateX(calc(-100% - var(--ud-sb-gap) * 2)) !important;
  opacity: 0 !important;
  visibility: hidden !important;
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.2s ease, visibility 0s linear 0.3s;
}
html[dir="rtl"] .nvm-sidebar { transform: translateX(calc(100% + var(--ud-sb-gap) * 2)) !important; }
.nvm-sidebar.is-open,
html[dir="rtl"] .nvm-sidebar.is-open {
  transform: none !important;
  opacity: 1 !important;
  visibility: visible !important;
  transition-delay: 0s;
}
.nvm-sidebar::after {
  content: '';
  position: absolute;
  inset: 0 0 auto 0;
  height: 160px;
  background: radial-gradient(120% 100% at 0% 0%, rgba(255,255,255,0.22), transparent 70%);
  pointer-events: none;
}
.nvm-sidebar > * { position: relative; z-index: 1; }

.nvm-nav {
  flex: 0 1 auto;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0.75rem;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.35) rgba(255,255,255,0.08);
}

/* Palette des icônes */
.nvm-link { --ic-a: #94A3B8; --ic-b: #64748B; }
.nvm-link[data-ic="orange"]  { --ic-a: #FDBA74; --ic-b: #F97316; }
.nvm-link[data-ic="pink"]    { --ic-a: #F9A8D4; --ic-b: #EC4899; }
.nvm-link[data-ic="emerald"] { --ic-a: #6EE7B7; --ic-b: #10B981; }
.nvm-link[data-ic="amber"]   { --ic-a: #FCD34D; --ic-b: #F59E0B; }
.nvm-link[data-ic="cyan"]    { --ic-a: #67E8F9; --ic-b: #06B6D4; }
.nvm-link[data-ic="teal"]    { --ic-a: #5EEAD4; --ic-b: #0D9488; }
.nvm-link[data-ic="lime"]    { --ic-a: #BEF264; --ic-b: #65A30D; }
.nvm-link[data-ic="rose"]    { --ic-a: #FDA4AF; --ic-b: #F43F5E; }
.nvm-link[data-ic="fuchsia"] { --ic-a: #F0ABFC; --ic-b: #C026D3; }
.nvm-link[data-ic="red"]     { --ic-a: #FCA5A5; --ic-b: #EF4444; }

.nvm-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  min-height: 2.75rem;
  margin-bottom: 0.25rem;
  padding: 0.6rem 0.85rem;
  border-radius: 0.85rem;
  box-sizing: border-box;
  position: relative;
  overflow: hidden;
  white-space: nowrap;
  color: rgba(255,255,255,0.86);
  font-size: 0.8125rem;
  font-weight: 600;
  text-decoration: none;
  transition: background var(--vx-transition), color var(--vx-transition), transform var(--vx-transition), box-shadow var(--vx-transition);
}
.nvm-link i {
  width: 2.1rem; height: 2.1rem;
  display: inline-flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  border-radius: 0.65rem;
  background: linear-gradient(135deg, var(--ic-a), var(--ic-b));
  color: #ffffff;
  font-size: 0.9rem;
  line-height: 1;
  box-shadow: 0 4px 10px rgba(15,23,42,0.25), inset 0 1px 0 rgba(255,255,255,0.4);
  transition: transform var(--vx-transition);
}
.nvm-label {
  flex: 1 1 auto;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.3;
}
.nvm-link:hover {
  background: rgba(255,255,255,0.14);
  color: #ffffff;
  transform: translateX(3px);
}
html[dir="rtl"] .nvm-link:hover { transform: translateX(-3px); }
.nvm-link:hover i { transform: scale(1.08) rotate(-4deg); }

/* Lien actif : pilule dégradée, pastille blanche */
.nvm-link.is-active {
  background: linear-gradient(135deg, var(--ud-nav-from), var(--ud-nav-to));
  color: #ffffff;
  font-weight: 700;
  box-shadow: 0 10px 24px rgba(15,23,42,0.28), 0 0 0 1px rgba(255,255,255,0.12) inset;
}
.nvm-link.is-active i {
  background: #ffffff;
  color: var(--ic-b);
  box-shadow: 0 4px 12px rgba(15,23,42,0.25);
}
.nvm-link:focus-visible { outline: 2px solid #ffffff; outline-offset: 2px; }

/* Pastilles de compteur */
.nvm-badge {
  margin-inline-start: auto;
  min-width: 1.25rem;
  padding: 0.1rem 0.45rem;
  border-radius: 999px;
  background: rgba(255,255,255,0.24);
  color: #ffffff;
  font-size: 0.65rem; font-weight: 700;
  line-height: 1.2;
  text-align: center;
}
.nvm-badge.is-alert { background: var(--vx-danger); }

.nvm-footer {
  border-top: 1px solid rgba(255,255,255,0.16);
  padding: 0.75rem 0.75rem 0.5rem;
  flex-shrink: 0;
}

.nvm-overlay {
  position: fixed !important;
  inset: var(--ud-topbar-h) 0 0 0;
  background: rgba(15,23,42,0.28);
  -webkit-backdrop-filter: blur(2px);
  backdrop-filter: blur(2px);
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.25s ease, visibility 0s linear 0.25s;
}
.nvm-overlay.is-open {
  opacity: 1;
  visibility: visible;
  transition-delay: 0s;
}

/* ====== Top bar ====== */
.ud-topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  height: var(--ud-topbar-h);
  background: rgba(255,253,248,0.88);
  backdrop-filter: blur(24px) saturate(1.8);
  -webkit-backdrop-filter: blur(24px) saturate(1.8);
  box-shadow: 0 4px 20px rgba(79,70,229,0.08);
  padding: 0 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}
.ud-topbar::after {
  content: '';
  position: absolute;
  inset-inline: 0;
  bottom: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--ud-nav-from), var(--ud-nav-via), var(--ud-nav-to));
}
.ud-topbar-left { display: flex; align-items: center; gap: 0.75rem; }
.ud-topbar-spacer { flex: 1; }
.ud-topbar-toggle {
  width: 2.25rem; height: 2.25rem;
  display: flex; align-items: center; justify-content: center;
  border-radius: var(--vx-radius-md);
  border: 1px solid var(--vx-card-border);
  background: transparent;
  color: var(--vx-text-secondary);
  font-size: 0.875rem;
  cursor: pointer;
  transition: all var(--vx-transition);
}
.ud-topbar-toggle:hover {
  background: rgba(79,70,229,0.10);
  color: var(--ud-nav-ink);
  border-color: rgba(79,70,229,0.2);
}

/* Right side: user + lang + logout */
.ud-topbar-right {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-shrink: 0;
}
.ud-topbar-user {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.ud-topbar-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex; align-items: center; justify-content: center;
  font-size: 0.75rem; font-weight: 800; color: #fff;
  flex-shrink: 0;
  box-shadow: 0 4px 12px var(--vx-primary-glow);
}
.ud-topbar-user-name {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--vx-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}
.ud-topbar-user-role {
  font-size: 0.62rem;
  color: var(--vx-text-muted);
}

/* Language dropdown */
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
  white-space: nowrap;
}
.ud-lang-btn:hover { background: var(--vx-primary-light); border-color: rgba(99,102,241,0.20); color: var(--vx-primary); }
.ud-lang-current { font-weight: 800; color: var(--vx-primary); }
.ud-lang-menu {
  position: absolute;
  top: calc(100% + 0.4rem);
  right: 0;
  min-width: 140px;
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md);
  box-shadow: var(--vx-shadow-lg);
  padding: 0.3rem;
  opacity: 0; visibility: hidden;
  transform: translateY(-6px);
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

/* Page title row below topbar */
.ud-page-title-bar {
  box-sizing: border-box;
  padding: 1rem 1.5rem 0;
  max-width: 1140px;
  width: 100%;
  margin: 0 auto;
}
.ud-page-title {
  font-size: 1rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  letter-spacing: -0.2px;
}
.ud-page-subtitle {
  font-size: 0.7rem;
  color: var(--vx-text-muted);
  font-weight: 500;
}

/* Bouton menu + logo (le menu est replié par défaut) */
.ud-topbar-toggle {
  width: auto;
  padding: 0 0.85rem;
  gap: 0.5rem;
  font-family: var(--ud-font);
  font-size: 0.78rem;
  font-weight: 700;
}
.ud-topbar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  color: var(--vx-text-primary);
  font-size: 0.95rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  text-decoration: none;
  white-space: nowrap;
}
.ud-topbar-brand-icon {
  width: 2rem; height: 2rem;
  display: flex; align-items: center; justify-content: center;
  border-radius: 0.6rem;
  background: linear-gradient(135deg, var(--ud-nav-from), var(--ud-nav-to));
  color: #ffffff;
  font-size: 0.8rem; font-weight: 800;
  box-shadow: 0 4px 12px var(--ud-nav-glow);
}
@media (max-width: 480px) {
  .ud-topbar-toggle-label, .ud-topbar-brand-text { display: none; }
  .ud-topbar-toggle { padding: 0; width: 2.25rem; }
}

/* ====== Main content ====== */
.ud-content {
  box-sizing: border-box;
  flex: 1;
  padding: 1.5rem 2rem 2rem;
  max-width: 1140px;
  width: 100%;
  margin: 0 auto;
}

/* ====== Responsive ====== */
@media (max-width: 992px) {
  .ud-topbar { padding: 0 1rem; }
}
@media (max-width: 768px) {
  .ud-topbar-user-name, .ud-topbar-user-role { display: none; }
  .ud-topbar-user { gap: 0; }
  .ud-content { padding: 1rem; }
  .ud-page-title-bar { padding: 0.75rem 1rem 0; }
  .ud-lang-btn span:not(.ud-lang-current) { display: none; }
}
@media (prefers-reduced-motion: reduce) {
  .nvm-sidebar, .nvm-overlay { transition: none; }
  .nvm-link:hover { transform: none; }
}

/* Notification bell (user dashboard) */
.ud-bell-dropdown { position: relative; }
.ud-bell-btn {
  position: relative; background: transparent; border: 1px solid var(--vx-card-border);
  cursor: pointer; width: 36px; height: 36px; border-radius: 0.5rem;
  display: flex; align-items: center; justify-content: center;
  color: var(--vx-text-secondary); transition: all var(--vx-transition);
  font-size: 0.95rem;
}
.ud-bell-btn:hover { background: var(--vx-primary-light); border-color: rgba(99,102,241,0.2); color: var(--vx-primary); }
.ud-bell-badge {
  position: absolute; top: -3px; right: -3px; min-width: 17px; height: 17px;
  border-radius: 9px; background: var(--vx-danger); color: #fff;
  font-size: 0.6rem; font-weight: 700; display: flex; align-items: center;
  justify-content: center; padding: 0 4px; line-height: 1; border: 2px solid var(--vx-card-bg);
}
.ud-bell-menu {
  position: absolute; top: calc(100% + 0.5rem); right: 0; width: 340px;
  background: var(--vx-card-bg); border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-lg);
  opacity: 0; visibility: hidden; transform: translateY(-8px);
  transition: all 0.2s ease; z-index: 400; max-height: 420px; display: flex; flex-direction: column;
}
.ud-bell-dropdown.is-open .ud-bell-menu { opacity: 1; visibility: visible; transform: translateY(0); }
.ud-bell-header {
  padding: 0.85rem 1rem; border-bottom: 1px solid var(--vx-card-border);
  display: flex; align-items: center; justify-content: space-between;
  font-weight: 700; font-size: 0.82rem;
}
.ud-bell-markall { font-size: 0.68rem; color: var(--vx-primary); text-decoration: none; font-weight: 600; }
.ud-bell-markall:hover { text-decoration: underline; }
.ud-bell-list { overflow-y: auto; flex: 1; }
.ud-bell-empty { padding: 2rem 1rem; text-align: center; color: var(--vx-text-muted); font-size: 0.8rem; }
.ud-bell-item {
  display: flex; gap: 0.65rem; padding: 0.75rem 1rem; text-decoration: none;
  border-bottom: 1px solid var(--vx-divider); transition: background 0.15s ease;
}
.ud-bell-item:hover { background: var(--vx-primary-light); }
.ud-bell-item .notif-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--vx-text-muted); flex-shrink: 0; margin-top: 5px; }
.ud-bell-item.unread .notif-dot { background: var(--vx-primary); }
.ud-bell-item .notif-content { flex: 1; min-width: 0; }
.ud-bell-item .notif-title { font-size: 0.78rem; font-weight: 600; color: var(--vx-text-primary); }
.ud-bell-item .notif-body { font-size: 0.72rem; color: var(--vx-text-muted); margin-top: 2px; }
.ud-bell-item .notif-time { font-size: 0.62rem; color: var(--vx-text-muted); margin-top: 4px; }
@media (max-width: 480px) {
  .ud-bell-menu { width: calc(100vw - 2rem); right: -0.5rem; }
}
</style>
<?= $extraStyles ?? '' ?>

<div class="ud-shell">
  <!-- Sidebar -->
  <aside class="nvm-sidebar" id="nvmSidebar" aria-label="Navigation">
    <nav class="nvm-nav">
      <?php foreach ($navItems as $key => $item): $isActive = ($activeSection ?? '') === $key; ?>
        <a href="<?= $item['href'] ?>"
           class="nvm-link<?= $isActive ? ' is-active' : '' ?>"
           data-ic="<?= $item['ic'] ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
          <i class="fas <?= $item['icon'] ?>"></i>
          <span class="nvm-label"><?= e($item['label']) ?></span>
          <?php if ($key === 'assessments' && !empty($totalAssessments)): ?>
            <span class="nvm-badge"><?= (int)$totalAssessments ?></span>
          <?php endif; ?>
          <?php if ($key === 'messages' && $userUnreadChat > 0): ?>
            <span class="nvm-badge is-alert"><?= (int)$userUnreadChat ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="nvm-footer">
      <a href="/" class="nvm-link" data-ic="teal">
        <i class="fas fa-globe"></i>
        <span class="nvm-label"><?= e(__('dashboard.nav.site') ?: 'Site') ?></span>
      </a>
      <a href="/logout" class="nvm-link" data-ic="red">
        <i class="fas fa-arrow-right-from-bracket"></i>
        <span class="nvm-label"><?= e(__('dashboard.logout')) ?></span>
      </a>
    </div>
  </aside>
  <div class="nvm-overlay" id="nvmOverlay"></div>

  <div class="nvm-main">
    <!-- Top bar -->
    <header class="ud-topbar">
      <div class="ud-topbar-left">
        <button type="button" class="ud-topbar-toggle" id="nvmToggle" aria-label="Menu" aria-expanded="false" aria-controls="nvmSidebar">
          <i class="fas fa-bars"></i>
          <span class="ud-topbar-toggle-label">Menu</span>
        </button>
        <a href="/user/models" class="ud-topbar-brand">
          <span class="ud-topbar-brand-icon">N</span>
          <span class="ud-topbar-brand-text"><?= e(__('dashboard.brand')) ?></span>
        </a>
        <span style="font-size:0.6rem;font-weight:800;padding:0.1rem 0.4rem;border-radius:999px;background:#10b981;color:#fff;">v4</span><!-- repère temporaire : à supprimer -->
      </div>
      <div class="ud-topbar-spacer"></div>

    <div class="ud-topbar-right">
      <!-- Notification Bell -->
      <div class="ud-bell-dropdown" id="udBellDropdown">
        <button class="ud-bell-btn" id="udBellBtn" type="button" aria-label="Notifications">
          <i class="fas fa-bell"></i>
          <?php if ($userUnreadNotif > 0): ?>
          <span class="ud-bell-badge" id="udBellBadge"><?= $userUnreadNotif > 99 ? '99+' : (int)$userUnreadNotif ?></span>
          <?php else: ?>
          <span class="ud-bell-badge" id="udBellBadge" style="display:none;">0</span>
          <?php endif; ?>
        </button>
        <div class="ud-bell-menu" id="udBellMenu">
          <div class="ud-bell-header">
            <span>Notifications</span>
            <a href="javascript:void(0)" id="udBellMarkAll" class="ud-bell-markall">Tout marquer lu</a>
          </div>
          <div class="ud-bell-list" id="udBellList">
            <div class="ud-bell-empty">Aucune notification</div>
          </div>
        </div>
      </div>

      <div class="ud-topbar-user">
        <div class="ud-topbar-avatar"><?= $userInitials ?></div>
        <div>
          <div class="ud-topbar-user-name"><?= e($userName) ?></div>
          <div class="ud-topbar-user-role"><?= e(__('dashboard.nav.client') ?: 'Client') ?></div>
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
    </div>
    </header>

    <!-- Page title -->
    <?php if (!empty($pageTitle) || !empty($title)): ?>
      <div class="ud-page-title-bar">
        <div class="ud-page-title"><?= e($pageTitle ?? $title ?? '') ?></div>
        <?php if (!empty($pageSubtitle)): ?>
          <div class="ud-page-subtitle"><?= e($pageSubtitle) ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="ud-content">
      <?= $content ?? '' ?>
    </div>
  </div>
</div>

<?= $extraScripts ?? '' ?>
<script>
(function() {
  var sidebar = document.getElementById('nvmSidebar');
  var toggle = document.getElementById('nvmToggle');
  var overlay = document.getElementById('nvmOverlay');
  if (!sidebar || !toggle) return;

  // Repli automatique uniquement avec une souris (sur tactile : toucher à côté du menu)
  var canHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
  var hovered = false;
  var timer = null;

  function isOpen() { return sidebar.classList.contains('is-open'); }
  function clearTimer() { if (timer) { clearTimeout(timer); timer = null; } }

  function armAutoClose(delay) {
    if (!canHover) return;
    clearTimer();
    timer = setTimeout(function() {
      if (hovered || sidebar.matches(':focus-within')) return;
      setOpen(false);
    }, delay);
  }

  function setOpen(open) {
    clearTimer();
    sidebar.classList.toggle('is-open', open);
    if (overlay) overlay.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) armAutoClose(3000); // se replie si la souris n'y entre pas
  }

  toggle.addEventListener('click', function() { setOpen(!isOpen()); });
  if (overlay) overlay.addEventListener('click', function() { setOpen(false); });
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') setOpen(false); });

  sidebar.addEventListener('mouseenter', function() { hovered = true; clearTimer(); });
  sidebar.addEventListener('mouseleave', function() { hovered = false; if (isOpen()) armAutoClose(600); });

  // Clic sur un lien : le menu se referme tout de suite
  sidebar.querySelectorAll('.nvm-link').forEach(function(link) {
    link.addEventListener('click', function() { setOpen(false); });
  });
})();
</script>
<script>
(function() {
  var dropdown = document.getElementById('udLangDropdown');
  var btn = document.getElementById('udLangBtn');
  if (dropdown && btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); dropdown.classList.toggle('is-open'); });
    document.addEventListener('click', function(e) { if (!dropdown.contains(e.target)) dropdown.classList.remove('is-open'); });
  }

  // Notification bell
  var bellDropdown = document.getElementById('udBellDropdown');
  var bellBtn = document.getElementById('udBellBtn');
  if (bellBtn && bellDropdown) {
    bellBtn.addEventListener('click', function(e) { e.stopPropagation(); bellDropdown.classList.toggle('is-open'); });
    document.addEventListener('click', function(e) { if (!bellDropdown.contains(e.target)) bellDropdown.classList.remove('is-open'); });
  }

  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
  var csrfToken = csrfMeta ? csrfMeta.content : '';

  function pollNotifs() {
    fetch('/api/notifications/poll', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        var badge = document.getElementById('udBellBadge');
        var list = document.getElementById('udBellList');
        if (!badge || !list) return;
        if (data.unread > 0) {
          badge.textContent = data.unread > 99 ? '99+' : data.unread;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
        if (data.html && data.html.trim()) {
          list.innerHTML = data.html;
          list.querySelectorAll('.notif-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
              e.preventDefault();
              var id = this.getAttribute('data-id');
              fetch('/api/notifications/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: '_csrf_token=' + csrfToken + '&id=' + id
              }).then(function() { window.location.href = item.getAttribute('href'); });
            });
          });
        } else {
          list.innerHTML = '<div class="ud-bell-empty">Aucune notification</div>';
        }
      })
      .catch(function() {});
  }

  var markAll = document.getElementById('udBellMarkAll');
  if (markAll) {
    markAll.addEventListener('click', function(e) {
      e.preventDefault();
      fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: '_csrf_token=' + csrfToken
      }).then(function() { pollNotifs(); });
    });
  }

  pollNotifs();
  setInterval(pollNotifs, 30000);
})();
</script>
