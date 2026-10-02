<?php $title = __('dashboard.title'); $hideLangSwitcher = true; ob_start();
$langCode = $_SESSION['lang'] ?? 'fr';
$levelNameField = $langCode === 'ar' ? 'name_ar' : ($langCode === 'fr' ? 'name_fr' : 'name');
$chartLocale = $langCode === 'ar' ? 'ar' : ($langCode === 'en' ? 'en-US' : 'fr-FR'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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

/* ====== Base & Layout ====== */
.user-dashboard {
  min-height: 100vh;
  background: var(--vx-body-bg);
  background-image:
    radial-gradient(ellipse 60% 50% at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 80% 100%, rgba(6,182,212,0.04) 0%, transparent 60%);
  background-attachment: fixed;
  font-family: var(--ud-font);
  padding-bottom: 5rem;
}

/* ====== Topbar ====== */
.user-topbar {
  background: rgba(255,253,248,0.92);
  backdrop-filter: blur(24px) saturate(1.8);
  -webkit-backdrop-filter: blur(24px) saturate(1.8);
  border-bottom: 1px solid var(--vx-card-border);
  padding: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 12px rgba(80,64,42,0.04);
}
.user-topbar-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.625rem 1.5rem;
}
.user-topbar .brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  font-weight: 800;
  font-size: 1rem;
  color: var(--vx-text-primary);
  letter-spacing: -0.3px;
  text-decoration: none;
}
.user-topbar .brand .brand-icon {
  width: 34px;
  height: 34px;
  background: var(--vx-primary-gradient);
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  color: #fff;
  font-weight: 800;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
  transition: transform 0.3s ease;
}
.user-topbar .brand:hover .brand-icon {
  transform: scale(1.05) rotate(-3deg);
}

/* Topbar right section */
.ud-topbar-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* User chip */
.ud-user-chip {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.3rem 0.5rem 0.3rem 0.35rem;
  border-radius: 2rem;
  background: var(--vx-primary-light);
  border: 1px solid rgba(99,102,241,0.12);
  transition: all var(--vx-transition);
  cursor: default;
}
.ud-user-chip:hover {
  background: rgba(99,102,241,0.10);
  border-color: rgba(99,102,241,0.20);
}
.ud-user-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 800;
  color: #fff;
  flex-shrink: 0;
}
.ud-user-name {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--vx-text-primary);
  white-space: nowrap;
}

/* Language dropdown */
.ud-lang-dropdown {
  position: relative;
}
.ud-lang-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
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
}
.ud-lang-btn:hover {
  background: var(--vx-primary-light);
  border-color: rgba(99,102,241,0.20);
  color: var(--vx-primary);
}
.ud-lang-btn i {
  font-size: 0.7rem;
}
.ud-lang-current {
  font-weight: 800;
  color: var(--vx-primary);
}
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
  opacity: 0;
  visibility: hidden;
  transform: translateY(-6px);
  transition: all 0.2s ease;
  z-index: 200;
}
.ud-lang-dropdown.is-open .ud-lang-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}
.ud-lang-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.65rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--vx-text-secondary);
  text-decoration: none;
  transition: all 0.15s ease;
}
.ud-lang-item:hover {
  background: var(--vx-primary-light);
  color: var(--vx-primary);
}
.ud-lang-item.is-active {
  background: var(--vx-primary-light);
  color: var(--vx-primary);
  font-weight: 700;
}
.ud-lang-item .ud-lang-flag {
  font-size: 0.65rem;
  font-weight: 800;
  width: 22px;
  height: 22px;
  border-radius: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--vx-primary-light);
  color: var(--vx-primary);
}
.ud-lang-item.is-active .ud-lang-flag {
  background: var(--vx-primary);
  color: #fff;
}

/* Divider */
.ud-topbar-divider {
  width: 1px;
  height: 28px;
  background: var(--vx-card-border);
  flex-shrink: 0;
}

/* Logout button */
.ud-logout-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.35rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
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
.ud-logout-btn i {
  font-size: 0.8rem;
}

/* Responsive topbar */
@media (max-width: 768px) {
  .user-topbar-inner { padding: 0.5rem 1rem; }
  .ud-user-name { display: none; }
  .ud-user-chip { padding: 0.25rem; }
  .ud-topbar-divider { display: none; }
  .ud-logout-btn span { display: none; }
  .ud-logout-btn { padding: 0.35rem 0.5rem; }
}

/* ====== Content ====== */
.user-content {
  max-width: 1140px;
  margin: 0 auto;
  padding: 2rem;
}

/* ====== Welcome / Motivation Banner ====== */
.user-welcome {
  background: linear-gradient(135deg, rgba(31,111,235,0.08) 0%, rgba(255,253,248,0.94) 45%, #fffdf8 100%);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  padding: 1.75rem 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
}
.user-welcome::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 60%);
  border-radius: 50%;
  animation: userGlowPulse 6s ease-in-out infinite;
}
@keyframes userGlowPulse {
  0%, 100% { opacity: 0.4; transform: scale(0.95); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
.user-welcome h2 {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin-bottom: 0.15rem;
  letter-spacing: -0.3px;
  position: relative;
  z-index: 1;
}
.user-welcome p {
  color: var(--vx-text-muted);
  font-size: 0.8rem;
  position: relative;
  z-index: 1;
}

/* ====== Motivation Banner ====== */
.ud-motivation {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border-radius: var(--vx-radius-lg);
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
  border: 1px solid;
  animation: udSlideIn 0.5s ease;
}
@keyframes udSlideIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.ud-motivation.ud-mot--excellent {
  background: linear-gradient(135deg, rgba(16,185,129,0.10), rgba(255,253,248,0.95));
  border-color: rgba(16,185,129,0.25);
}
.ud-motivation.ud-mot--good {
  background: linear-gradient(135deg, rgba(6,182,212,0.10), rgba(255,253,248,0.95));
  border-color: rgba(6,182,212,0.25);
}
.ud-motivation.ud-mot--progress {
  background: linear-gradient(135deg, rgba(245,158,11,0.10), rgba(255,253,248,0.95));
  border-color: rgba(245,158,11,0.25);
}
.ud-motivation.ud-mot--start {
  background: linear-gradient(135deg, rgba(99,102,241,0.10), rgba(255,253,248,0.95));
  border-color: rgba(99,102,241,0.25);
}
.ud-mot-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
  color: #fff;
}
.ud-mot--excellent .ud-mot-icon { background: var(--vx-success-gradient); }
.ud-mot--good .ud-mot-icon { background: var(--vx-info-gradient); }
.ud-mot--progress .ud-mot-icon { background: var(--vx-warning-gradient); }
.ud-mot--start .ud-mot-icon { background: var(--vx-primary-gradient); }
.ud-mot-text h3 {
  font-size: 0.875rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin: 0 0 0.15rem;
}
.ud-mot-text p {
  font-size: 0.75rem;
  color: var(--vx-text-secondary);
  margin: 0;
  line-height: 1.5;
}

/* ====== KPI Stat Cards ====== */
.ud-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 768px) {
  .ud-stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .ud-stats-grid { grid-template-columns: 1fr; }
}
.user-stat-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  padding: 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all var(--vx-transition);
  min-height: 80px;
  position: relative;
  overflow: hidden;
  cursor: default;
}
.user-stat-card::after {
  content: '';
  position: absolute;
  top: 0; right: 0;
  width: 60px; height: 60px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 70%);
  opacity: 0;
  transition: opacity var(--vx-transition);
}
.user-stat-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--vx-shadow-lg);
  border-color: var(--vx-card-border-hover);
}
.user-stat-card:hover::after {
  opacity: 0.3;
}
.user-stat-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: var(--vx-radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.125rem;
  flex-shrink: 0;
  transition: transform var(--vx-transition);
}
.user-stat-card:hover .user-stat-icon {
  transform: scale(1.08);
}
.user-stat-value {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  line-height: 1;
  font-family: var(--ud-font);
}
.user-stat-label {
  font-size: 0.6875rem;
  color: var(--vx-text-muted);
  font-weight: 500;
  margin-top: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.ud-stat-trend {
  display: inline-flex;
  align-items: center;
  gap: 0.2rem;
  font-size: 0.625rem;
  font-weight: 700;
  padding: 0.15rem 0.4rem;
  border-radius: 0.25rem;
  margin-top: 0.25rem;
}
.ud-stat-trend--up { background: var(--vx-success-light); color: var(--vx-success); }
.ud-stat-trend--down { background: var(--vx-danger-light); color: var(--vx-danger); }
.ud-stat-trend--neutral { background: var(--vx-primary-light); color: var(--vx-primary); }

/* ====== Two-column Layout ====== */
.ud-two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 768px) {
  .ud-two-col { grid-template-columns: 1fr; }
}

/* ====== Score Gauge Card ====== */
.ud-gauge-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
  text-align: center;
}
.ud-gauge-card h3 {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--vx-text-muted);
  margin: 0 0 1rem;
}
.ud-gauge-wrap {
  position: relative;
  width: 200px;
  height: 200px;
  margin: 0 auto 1rem;
}
.ud-gauge-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}
.ud-gauge-bg {
  fill: none;
  stroke: rgba(99,102,241,0.10);
  stroke-width: 14;
}
.ud-gauge-fill {
  fill: none;
  stroke-width: 14;
  stroke-linecap: round;
  transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.ud-gauge-center {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}
.ud-gauge-score {
  font-size: 2.5rem;
  font-weight: 900;
  color: var(--vx-text-primary);
  line-height: 1;
  font-family: var(--ud-font);
}
.ud-gauge-score small {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--vx-text-muted);
}
.ud-gauge-level {
  font-size: 0.75rem;
  font-weight: 700;
  margin-top: 0.35rem;
  padding: 0.2rem 0.6rem;
  border-radius: 1rem;
  display: inline-block;
}
.ud-gauge-level-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.3rem 0.75rem;
  border-radius: 0.375rem;
  margin-top: 0.5rem;
}

/* ====== Maturity Ladder ====== */
.ud-maturity-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
}
.ud-maturity-card h3 {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--vx-text-muted);
  margin: 0 0 1.25rem;
}
.ud-maturity-ladder {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.ud-maturity-step {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 0.75rem;
  border-radius: var(--vx-radius-md);
  border: 1px solid transparent;
  transition: all var(--vx-transition);
  position: relative;
}
.ud-maturity-step:hover {
  background: rgba(99,102,241,0.03);
}
.ud-maturity-step.is-current {
  background: rgba(99,102,241,0.06);
  border-color: rgba(99,102,241,0.20);
  box-shadow: 0 2px 12px rgba(99,102,241,0.08);
}
.ud-maturity-dot {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 50%;
  flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(255,255,255,0.8);
}
.ud-maturity-step.is-current .ud-maturity-dot {
  animation: udDotPulse 2s ease-in-out infinite;
}
@keyframes udDotPulse {
  0%, 100% { box-shadow: 0 0 0 3px rgba(255,255,255,0.8), 0 0 0 0 rgba(99,102,241,0.3); }
  50% { box-shadow: 0 0 0 3px rgba(255,255,255,0.8), 0 0 0 8px rgba(99,102,241,0); }
}
.ud-maturity-info {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.ud-maturity-name {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--vx-text-primary);
}
.ud-maturity-range {
  font-size: 0.625rem;
  color: var(--vx-text-muted);
  font-weight: 600;
}
.ud-maturity-check {
  color: var(--vx-success);
  font-size: 0.875rem;
}

/* ====== Progress Chart Card ====== */
.ud-chart-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}
.ud-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}
.ud-chart-header h3 {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--vx-text-primary);
  margin: 0;
}
.ud-chart-header .ud-chart-sub {
  font-size: 0.6875rem;
  color: var(--vx-text-muted);
}
.ud-chart-container {
  position: relative;
  height: 220px;
}
.ud-chart-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 220px;
  color: var(--vx-text-muted);
  font-size: 0.8rem;
}

/* ====== Per-Model Score Cards Section ====== */
.ud-models-section {
  margin-bottom: 1.5rem;
}
.ud-models-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}
.ud-models-header h3 {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin: 0;
  letter-spacing: -0.2px;
}
.ud-models-header h3 i {
  color: var(--vx-primary);
  margin-right: 0.4rem;
}
.ud-models-header .ud-models-sub {
  font-size: 0.68rem;
  color: var(--vx-text-muted);
}

.ud-models-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
}
@media (max-width: 768px) {
  .ud-models-grid { grid-template-columns: 1fr; }
}

/* ====== Model Score Card ====== */
.ud-model-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  box-shadow: var(--vx-shadow-md);
  overflow: hidden;
  transition: all var(--vx-transition);
  position: relative;
}
.ud-model-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--vx-shadow-lg);
  border-color: var(--vx-card-border-hover);
}

/* Card header strip */
.ud-model-card-top {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--vx-divider);
  position: relative;
}
.ud-model-card-top::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px; height: 100%;
  border-radius: 0;
}
.ud-model-icon {
  width: 2.25rem;
  height: 2.25rem;
  border-radius: var(--vx-radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
  color: #fff;
  box-shadow: 0 4px 14px rgba(0,0,0,0.10);
}
.ud-model-title {
  flex: 1;
  min-width: 0;
}
.ud-model-title h4 {
  font-size: 0.85rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  letter-spacing: -0.15px;
}
.ud-model-title .ud-model-meta {
  font-size: 0.65rem;
  color: var(--vx-text-muted);
  margin-top: 0.15rem;
}
.ud-model-level-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.625rem;
  font-weight: 700;
  padding: 0.25rem 0.55rem;
  border-radius: 0.3rem;
  white-space: nowrap;
  flex-shrink: 0;
}

/* Card body */
.ud-model-card-body {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 1rem;
  padding: 1.25rem;
  align-items: center;
}
@media (max-width: 480px) {
  .ud-model-card-body {
    grid-template-columns: 1fr;
    text-align: center;
  }
}

/* Radar chart wrapper */
.ud-model-radar-wrap {
  position: relative;
  width: 140px;
  height: 140px;
  margin: 0 auto;
}
.ud-model-radar-wrap canvas {
  max-width: 140px;
  max-height: 140px;
}

/* Right side: mini KPIs + domain bars */
.ud-model-detail {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
.ud-model-kpis {
  display: flex;
  gap: 0.5rem;
}
.ud-model-kpi {
  flex: 1;
  text-align: center;
  padding: 0.5rem 0.4rem;
  border-radius: var(--vx-radius-md);
  background: rgba(99,102,241,0.04);
  border: 1px solid rgba(99,102,241,0.06);
}
.ud-model-kpi-label {
  font-size: 0.55rem;
  color: var(--vx-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.03em;
  font-weight: 600;
}
.ud-model-kpi-value {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  line-height: 1.1;
  margin-top: 0.15rem;
}
.ud-model-kpi-value small {
  font-size: 0.55rem;
  font-weight: 500;
  color: var(--vx-text-muted);
}

/* Domain bars */
.ud-model-domains {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}
.ud-domain-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.ud-domain-name {
  font-size: 0.625rem;
  color: var(--vx-text-secondary);
  font-weight: 600;
  width: 90px;
  flex-shrink: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ud-domain-bar {
  flex: 1;
  height: 5px;
  border-radius: 3px;
  background: rgba(99,102,241,0.08);
  overflow: hidden;
}
.ud-domain-bar-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.9s cubic-bezier(0.4,0,0.2,1);
}
.ud-domain-pct {
  font-size: 0.58rem;
  font-weight: 700;
  color: var(--vx-text-muted);
  width: 30px;
  text-align: right;
  flex-shrink: 0;
}

/* Card footer */
.ud-model-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1.25rem;
  border-top: 1px solid var(--vx-divider);
  background: rgba(99,102,241,0.02);
}
.ud-model-footer-info {
  font-size: 0.65rem;
  color: var(--vx-text-muted);
  display: flex;
  align-items: center;
  gap: 0.35rem;
}
.ud-model-footer-info .ud-model-count {
  font-weight: 700;
  color: var(--vx-text-secondary);
}
.ud-model-actions {
  display: flex;
  gap: 0.4rem;
}
.ud-model-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.3rem 0.65rem;
  border-radius: var(--vx-radius-sm);
  font-size: 0.65rem;
  font-weight: 700;
  font-family: var(--ud-font);
  text-decoration: none;
  transition: all var(--vx-transition);
  cursor: pointer;
  border: none;
}
.ud-model-btn--primary {
  color: #fff;
}
.ud-model-btn--primary:hover {
  transform: translateY(-1px);
  filter: brightness(1.1);
  color: #fff;
}
.ud-model-btn--outline {
  background: transparent;
  color: var(--vx-text-secondary);
  border: 1px solid var(--vx-card-border);
}
.ud-model-btn--outline:hover {
  background: rgba(99,102,241,0.06);
  border-color: var(--vx-card-border-hover);
  color: var(--vx-text-primary);
}

/* No-score placeholder in model card */
.ud-model-no-score {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 140px;
  gap: 0.5rem;
}
.ud-model-no-score i {
  font-size: 1.5rem;
  color: var(--vx-text-muted);
  opacity: 0.4;
}
.ud-model-no-score span {
  font-size: 0.65rem;
  color: var(--vx-text-muted);
  text-align: center;
}

/* Models empty state */
.ud-models-empty {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  padding: 2.5rem 1.5rem;
  text-align: center;
}
.ud-models-empty i {
  font-size: 2rem;
  color: var(--vx-text-muted);
  opacity: 0.4;
  margin-bottom: 0.75rem;
  display: block;
}
.ud-models-empty p {
  font-size: 0.8rem;
  color: var(--vx-text-muted);
  margin: 0;
}

/* ====== Assessment List ====== */
.user-assessment-card {
  background: var(--vx-card-bg);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
}
.user-assessment-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.875rem 1.25rem;
  border-bottom: 1px solid var(--vx-divider);
  transition: all var(--vx-transition);
}
.user-assessment-item:last-child {
  border-bottom: none;
}
.user-assessment-item:hover {
  background: rgba(99,102,241,0.03);
}
.user-assessment-company {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--vx-text-primary);
}
.user-assessment-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.25rem;
  font-size: 0.6875rem;
  color: var(--vx-text-muted);
  flex-wrap: wrap;
}

/* ====== Mini Score Bar (in assessment items) ====== */
.ud-mini-bar {
  width: 80px;
  height: 6px;
  border-radius: 3px;
  background: rgba(99,102,241,0.10);
  overflow: hidden;
  position: relative;
}
.ud-mini-bar-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 1s ease;
}

/* ====== Empty State ====== */
.user-empty-state {
  text-align: center;
  padding: 3rem 1rem;
}
.user-empty-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--vx-primary-light);
  border: 1px solid rgba(99,102,241,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: var(--vx-primary);
  font-size: 1.5rem;
  animation: userEmptyPulse 3s ease-in-out infinite;
}
@keyframes userEmptyPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
  50% { box-shadow: 0 0 24px 4px rgba(99,102,241,0.15); }
}
.user-empty-state p {
  color: var(--vx-text-muted);
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

/* ====== Buttons ====== */
.nova-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 1rem;
  border-radius: var(--vx-radius-sm);
  font-size: 0.8125rem;
  font-weight: 600;
  text-decoration: none;
  transition: all var(--vx-transition);
  cursor: pointer;
  border: none;
  font-family: var(--ud-font);
}
.nova-btn-primary {
  background: var(--vx-primary-gradient);
  color: #fff;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
}
.nova-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 24px var(--vx-primary-glow);
  filter: brightness(1.1);
  color: #fff;
}
.nova-btn-outline {
  background: rgba(255,255,255,0.03);
  color: var(--vx-text-secondary);
  border: 1px solid var(--vx-card-border);
}
.nova-btn-outline:hover {
  background: rgba(99,102,241,0.06);
  border-color: var(--vx-card-border-hover);
  color: var(--vx-text-primary);
  transform: translateY(-1px);
}

/* ====== Badges ====== */
.badge {
  font-size: 0.625rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: var(--vx-radius-sm);
}

/* ====== Card Header ====== */
.card-header {
  background: transparent;
  border-bottom: 1px solid var(--vx-divider);
  padding: 0.875rem 1.25rem;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--vx-text-primary);
}

/* ====== Utility ====== */
.d-flex { display: flex; }
.flex-wrap { flex-wrap: wrap; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-center { justify-content: center; }
.text-decoration-none { text-decoration: none; }
.d-none { display: none; }
@media (min-width: 768px) { .d-md-none { display: none !important; } .d-md-inline { display: inline !important; } }

/* ====== Responsive ====== */
@media (max-width: 768px) {
  .user-content { padding: 1rem; }
  .user-welcome { padding: 1.25rem; }
  .user-welcome h2 { font-size: 1.15rem; }
  .user-welcome p { font-size: 0.72rem; }
  .user-assessment-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
  .ud-gauge-wrap { width: 170px; height: 170px; }
  .ud-gauge-score { font-size: 2rem; }
}

/* ====== Excellence Footer ====== */
.ud-excellence-footer {
  margin-top: 2.5rem;
  border-top: 1px solid var(--vx-card-border);
  background: linear-gradient(180deg, transparent 0%, rgba(99,102,241,0.03) 100%);
  padding: 2rem 2rem 1.5rem;
}
.ud-excellence-inner {
  max-width: 1140px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
}
.ud-excellence-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.ud-excellence-mark {
  width: 32px;
  height: 32px;
  background: var(--vx-primary-gradient);
  border-radius: var(--vx-radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #fff;
  font-weight: 800;
  box-shadow: 0 4px 16px var(--vx-primary-glow);
  flex-shrink: 0;
}
.ud-excellence-name {
  font-size: 0.85rem;
  font-weight: 800;
  color: var(--vx-text-primary);
  letter-spacing: -0.2px;
}
.ud-excellence-tag {
  font-size: 0.65rem;
  color: var(--vx-text-muted);
  letter-spacing: 0.03em;
}
.ud-excellence-slogan {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--vx-text-secondary);
  font-style: italic;
  max-width: 480px;
}
.ud-excellence-icon {
  color: var(--vx-warning);
  font-size: 1rem;
  flex-shrink: 0;
}
.ud-excellence-links {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.72rem;
}
.ud-excellence-links a {
  color: var(--vx-text-muted);
  text-decoration: none;
  transition: color var(--vx-transition);
}
.ud-excellence-links a:hover {
  color: var(--vx-primary);
}
.ud-excellence-dot {
  color: var(--vx-text-muted);
}
.ud-excellence-copy {
  text-align: center;
  font-size: 0.68rem;
  color: var(--vx-text-muted);
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid var(--vx-divider);
}
@media (max-width: 768px) {
  .ud-excellence-inner { flex-direction: column; text-align: center; }
  .ud-excellence-slogan { font-size: 0.75rem; }
}
</style>

<div class="user-dashboard">
  <!-- Topbar -->
  <div class="user-topbar">
    <div class="user-topbar-inner">
      <a href="/user/dashboard" class="brand" style="text-decoration:none;">
        <span class="brand-icon">N</span>
        <span><?= __('dashboard.brand') ?></span>
      </a>
      <div class="ud-topbar-right">
        <div class="ud-user-chip">
          <span class="ud-user-avatar"><?= strtoupper(mb_substr($user['firstname'] ?? 'U', 0, 1)) ?></span>
          <span class="ud-user-name"><?= e($user['firstname'] ?? '') ?> <?= e($user['lastname'] ?? '') ?></span>
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
        <div class="ud-topbar-divider"></div>
        <a href="/logout" class="ud-logout-btn" title="<?= __('dashboard.logout') ?>">
          <i class="fas fa-arrow-right-from-bracket"></i>
          <span><?= __('dashboard.logout') ?></span>
        </a>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="user-content">
    <!-- Welcome -->
    <div class="user-welcome">
      <h2><?= __('dashboard.welcome', ['name' => e($user['firstname'] ?? '')]) ?></h2>
      <p><?= __('dashboard.welcome_sub') ?></p>
      <?php if (isset($assessmentLimit) && $assessmentLimit !== null): ?>
        <p style="margin-top:0.5rem;font-size:0.75rem;color:<?= $assessmentsRemaining > 0 ? 'var(--vx-text-secondary)' : 'var(--vx-danger)' ?>;font-weight:600;">
          <i class="fas fa-chart-bar" style="margin-right:0.25rem;"></i>
          <?= $assessmentsRemaining > 0
            ? "Évaluations restantes : {$assessmentsRemaining} / {$assessmentLimit}"
            : "Limite atteinte ({$assessmentLimit} évaluation(s)). Contactez l'administrateur." ?>
        </p>
      <?php endif; ?>
    </div>

    <?php
      // Motivation message based on latest score
      $motClass = 'ud-mot--start';
      $motIcon = 'fa-rocket';
      $motTitle = __('dashboard.mot.start.title');
      $motText = __('dashboard.mot.start.text');
      if ($latestScore !== null) {
          $levelName = $maturityLevel[$levelNameField] ?? $maturityLevel['name'] ?? 'N/A';
          if ($latestScore >= 71) {
              $motClass = 'ud-mot--excellent'; $motIcon = 'fa-trophy';
              $motTitle = __('dashboard.mot.excellent.title');
              $motText = __('dashboard.mot.excellent.text', ['score' => $latestScore, 'level' => $levelName]);
          } elseif ($latestScore >= 51) {
              $motClass = 'ud-mot--good'; $motIcon = 'fa-chart-line';
              $motTitle = __('dashboard.mot.good.title');
              $motText = __('dashboard.mot.good.text', ['score' => $latestScore]);
          } elseif ($latestScore >= 31) {
              $motClass = 'ud-mot--progress'; $motIcon = 'fa-seedling';
              $motTitle = __('dashboard.mot.progress.title');
              $motText = __('dashboard.mot.progress.text', ['score' => $latestScore]);
          } else {
              $motClass = 'ud-mot--progress'; $motIcon = 'fa-flag';
              $motTitle = __('dashboard.mot.first.title');
              $motText = __('dashboard.mot.first.text', ['score' => $latestScore]);
          }
      }
    ?>
    <div class="ud-motivation <?= $motClass ?>">
      <div class="ud-mot-icon"><i class="fas <?= htmlspecialchars($motIcon) ?>"></i></div>
      <div class="ud-mot-text">
        <h3><?= htmlspecialchars($motTitle) ?></h3>
        <p><?= htmlspecialchars($motText) ?></p>
      </div>
    </div>

    <!-- KPI Stats -->
    <div class="ud-stats-grid">
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-primary-light);color:var(--vx-primary);border:1px solid rgba(99,102,241,0.15);">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div>
          <div class="user-stat-value"><?= $totalAssessments ?></div>
          <div class="user-stat-label"><?= __('dashboard.stat.total') ?></div>
        </div>
      </div>
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-success-light);color:var(--vx-success);border:1px solid rgba(16,185,129,0.15);">
          <i class="fas fa-check-circle"></i>
        </div>
        <div>
          <div class="user-stat-value" style="color:var(--vx-success);"><?= $completedCount ?></div>
          <div class="user-stat-label"><?= __('dashboard.stat.completed') ?></div>
          <?php if ($totalAssessments > 0): ?>
            <span class="ud-stat-trend ud-stat-trend--neutral"><?= $completionRate ?>%</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-info-light);color:var(--vx-info);border:1px solid rgba(6,182,212,0.15);">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <div class="user-stat-value" style="color:var(--vx-info);">
            <?= $bestScore !== null ? $bestScore : '—' ?>
          </div>
          <div class="user-stat-label"><?= __('dashboard.stat.best') ?></div>
        </div>
      </div>
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(245,158,11,0.15);">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div>
          <div class="user-stat-value" style="color:var(--vx-warning);"><?= $totalAssessments - $completedCount ?></div>
          <div class="user-stat-label"><?= __('dashboard.stat.inprogress') ?></div>
        </div>
      </div>
    </div>

    <!-- Two-column: Gauge + Maturity Ladder -->
    <div class="ud-two-col">
      <!-- Score Gauge -->
      <div class="ud-gauge-card">
        <h3><?= __('dashboard.gauge.title') ?></h3>
        <?php if ($latestScore !== null): ?>
          <?php
            $gaugeColor = $maturityLevel['color'] ?? '#6366f1';
            $circumference = 2 * M_PI * 80;
            $gaugePct = min($latestScore, 100);
            $gaugeOffset = $circumference - ($gaugePct / 100) * $circumference * 0.75;
            $gaugeCirc = $circumference * 0.75;
          ?>
          <div class="ud-gauge-wrap">
            <svg class="ud-gauge-svg" viewBox="0 0 200 200">
              <circle class="ud-gauge-bg" cx="100" cy="100" r="80"
                stroke-dasharray="<?= $gaugeCirc ?> <?= $circumference ?>"
                stroke-dashoffset="0"
                transform="rotate(135 100 100)"
                style="stroke-linecap:round;" />
              <circle class="ud-gauge-fill" cx="100" cy="100" r="80"
                stroke="<?= htmlspecialchars($gaugeColor) ?>"
                stroke-dasharray="<?= $gaugeCirc ?> <?= $circumference ?>"
                stroke-dashoffset="<?= $gaugeOffset ?>"
                transform="rotate(135 100 100)" />
            </svg>
            <div class="ud-gauge-center">
              <div class="ud-gauge-score"><?= round($latestScore) ?><small>/100</small></div>
              <div class="ud-gauge-level-badge" style="background:<?= htmlspecialchars($gaugeColor) ?>20;color:<?= htmlspecialchars($gaugeColor) ?>;">
                <i class="fas <?= htmlspecialchars($maturityLevel['icon'] ?? 'fa-chart-bar') ?>"></i>
                <?= htmlspecialchars($maturityLevel[$levelNameField] ?? $maturityLevel['name'] ?? 'N/A') ?>
              </div>
            </div>
          </div>
          <?php if ($progressDelta != 0): ?>
            <div style="font-size:0.72rem;color:var(--vx-text-muted);">
              <?php if ($progressDelta > 0): ?>
                <i class="fas fa-arrow-trend-up" style="color:var(--vx-success);"></i>
                <span style="color:var(--vx-success);font-weight:700;">+<?= $progressDelta ?></span> <?= __('dashboard.gauge.progress_since') ?>
              <?php else: ?>
                <i class="fas fa-arrow-trend-down" style="color:var(--vx-danger);"></i>
                <span style="color:var(--vx-danger);font-weight:700;"><?= $progressDelta ?></span> <?= __('dashboard.gauge.progress_since') ?>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div style="font-size:0.72rem;color:var(--vx-text-muted);">
              <i class="fas fa-minus" style="color:var(--vx-text-muted);"></i> <?= __('dashboard.gauge.stable') ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="ud-gauge-wrap" style="display:flex;align-items:center;justify-content:center;">
            <div class="ud-gauge-center">
              <div class="ud-gauge-score" style="color:var(--vx-text-muted);">—</div>
              <div style="font-size:0.7rem;color:var(--vx-text-muted);margin-top:0.5rem;"><?= __('dashboard.gauge.no_score') ?></div>
            </div>
          </div>
          <div style="font-size:0.72rem;color:var(--vx-text-muted);"><?= __('dashboard.gauge.no_score_hint') ?></div>
        <?php endif; ?>
      </div>

      <!-- Maturity Ladder -->
      <div class="ud-maturity-card">
        <h3><?= __('dashboard.maturity.title') ?></h3>
        <div class="ud-maturity-ladder">
          <?php
            $currentLevelId = $maturityLevel['id'] ?? null;
            $reachedCurrent = false;
            foreach ($scoreLevels as $sl):
              $isCurrent = ($currentLevelId !== null && $sl['id'] == $currentLevelId);
              if ($isCurrent) $reachedCurrent = true;
              $isPast = ($currentLevelId !== null && !$isCurrent && !$reachedCurrent);
          ?>
            <div class="ud-maturity-step <?= $isCurrent ? 'is-current' : '' ?>">
              <div class="ud-maturity-dot" style="background:<?= htmlspecialchars($sl['color']) ?>;opacity:<?= $isPast ? '1' : ($isCurrent ? '1' : '0.3') ?>;"></div>
              <div class="ud-maturity-info">
                <span class="ud-maturity-name" style="opacity:<?= $isPast || $isCurrent ? '1' : '0.5' ?>;">
                  <?= htmlspecialchars($sl[$levelNameField] ?: $sl['name']) ?>
                </span>
                <span class="ud-maturity-range"><?= round($sl['min_percent']) ?>–<?= round($sl['max_percent']) ?>%</span>
              </div>
              <?php if ($isPast): ?>
                <i class="fas fa-check ud-maturity-check"></i>
              <?php elseif ($isCurrent): ?>
                <i class="fas fa-circle-dot" style="color:<?= htmlspecialchars($sl['color']) ?>;font-size:0.75rem;"></i>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Score Progress Chart -->
    <?php if (!empty($scoreHistory) && count($scoreHistory) >= 1): ?>
      <div class="ud-chart-card">
        <div class="ud-chart-header">
          <h3><i class="fas fa-chart-line" style="color:var(--vx-primary);margin-right:0.5rem;"></i><?= __('dashboard.chart.title') ?></h3>
          <span class="ud-chart-sub"><?= __('dashboard.chart.subtitle', ['count' => count($scoreHistory)]) ?></span>
        </div>
        <div class="ud-chart-container">
          <canvas id="udScoreChart"></canvas>
        </div>
      </div>
    <?php endif; ?>

    <!-- ====== Per-Model Score Cards ====== -->
    <div class="ud-models-section">
      <div class="ud-models-header">
        <h3><i class="fas fa-layer-group"></i><?= __('dashboard.models.title') ?></h3>
        <span class="ud-models-sub"><?= __('dashboard.models.subtitle') ?></span>
      </div>

      <?php if (!empty($modelStats)): ?>
        <div class="ud-models-grid">
          <?php foreach ($modelStats as $idx => $ms):
            $mColor = $ms['color'] ?: '#1a56db';
            $mLevelName = '';
            $mLevelColor = $mColor;
            if ($ms['maturity_level']) {
              $mLevelName = $ms['maturity_level'][$levelNameField] ?? $ms['maturity_level']['name'] ?? '';
              $mLevelColor = $ms['maturity_level']['color'] ?? $mColor;
            }
            $hasScore = $ms['latest_score'] !== null;
            $radarId = 'modelRadar_' . $ms['id'];
          ?>
            <div class="ud-model-card">
              <!-- Card Top: Model name + level badge -->
              <div class="ud-model-card-top" style="border-left:4px solid <?= htmlspecialchars($mColor) ?>;">
                <div class="ud-model-icon" style="background:<?= htmlspecialchars($mColor) ?>;">
                  <i class="fas <?= htmlspecialchars($ms['icon'] ?: 'fa-clipboard-check') ?>"></i>
                </div>
                <div class="ud-model-title">
                  <h4><?= htmlspecialchars($ms['display_name'] ?: $ms['name']) ?></h4>
                  <div class="ud-model-meta">
                    <?= $ms['completed'] ?> <?= __('dashboard.models.completed') ?> / <?= $ms['total'] ?> <?= __('dashboard.models.assessments') ?>
                  </div>
                </div>
                <?php if ($hasScore && $mLevelName): ?>
                  <span class="ud-model-level-badge" style="background:<?= htmlspecialchars($mLevelColor) ?>20;color:<?= htmlspecialchars($mLevelColor) ?>;">
                    <i class="fas fa-medal" style="font-size:0.55rem;"></i>
                    <?= htmlspecialchars($mLevelName) ?>
                  </span>
                <?php endif; ?>
              </div>

              <!-- Card Body: Radar + KPIs + Domain bars -->
              <div class="ud-model-card-body">
                <!-- Radar Chart -->
                <div class="ud-model-radar-wrap">
                  <?php if ($hasScore): ?>
                    <canvas id="<?= $radarId ?>"></canvas>
                  <?php else: ?>
                    <div class="ud-model-no-score">
                      <i class="fas fa-chart-radar"></i>
                      <span><?= __('dashboard.models.no_score') ?></span>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Detail: KPIs + Domain bars -->
                <div class="ud-model-detail">
                  <!-- Mini KPIs -->
                  <div class="ud-model-kpis">
                    <div class="ud-model-kpi">
                      <div class="ud-model-kpi-label"><?= __('dashboard.models.latest') ?></div>
                      <div class="ud-model-kpi-value" style="color:<?= htmlspecialchars($mLevelColor) ?>;">
                        <?= $hasScore ? round($ms['latest_score']) : '—' ?><small>/100</small>
                      </div>
                    </div>
                    <div class="ud-model-kpi">
                      <div class="ud-model-kpi-label"><?= __('dashboard.models.best') ?></div>
                      <div class="ud-model-kpi-value">
                        <?= $ms['best_score'] !== null ? round($ms['best_score']) : '—' ?><small>/100</small>
                      </div>
                    </div>
                    <div class="ud-model-kpi">
                      <div class="ud-model-kpi-label"><?= __('dashboard.models.avg') ?></div>
                      <div class="ud-model-kpi-value">
                        <?= $ms['avg_score'] !== null ? round($ms['avg_score']) : '—' ?><small>/100</small>
                      </div>
                    </div>
                  </div>

                  <!-- Domain Score Bars -->
                  <?php if (!empty($ms['domain_scores'])): ?>
                    <div class="ud-model-domains">
                      <?php foreach ($ms['domain_scores'] as $ds):
                        $dsColor = $ds['level']['color'] ?? $mColor;
                        $dsPct = round($ds['percent_score']);
                        $dsLabel = $ds['domain_label'] ?: ($ds['domain_name_fr'] ?: $ds['domain_name']);
                      ?>
                        <div class="ud-domain-row">
                          <span class="ud-domain-name" title="<?= htmlspecialchars($dsLabel) ?>"><?= htmlspecialchars($dsLabel) ?></span>
                          <div class="ud-domain-bar">
                            <div class="ud-domain-bar-fill" style="width:<?= $dsPct ?>%;background:<?= htmlspecialchars($dsColor) ?>;"></div>
                          </div>
                          <span class="ud-domain-pct"><?= $dsPct ?>%</span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Card Footer -->
              <div class="ud-model-card-footer">
                <div class="ud-model-footer-info">
                  <i class="fas fa-chart-pie" style="font-size:0.6rem;"></i>
                  <span class="ud-model-count"><?= $ms['completed'] ?></span> / <?= $ms['total'] ?> <?= __('dashboard.models.assessments') ?>
                </div>
                <div class="ud-model-actions">
                  <?php if ($hasScore && $ms['latest_assessment_id']): ?>
                    <a href="/assessment/<?= $ms['latest_assessment_id'] ?>/results" class="ud-model-btn ud-model-btn--primary" style="background:<?= htmlspecialchars($mColor) ?>;">
                      <i class="fas fa-file-alt" style="font-size:0.6rem;"></i><?= __('dashboard.models.view_results') ?>
                    </a>
                  <?php else: ?>
                    <a href="/assessment/start" class="ud-model-btn ud-model-btn--outline">
                      <i class="fas fa-play" style="font-size:0.6rem;"></i><?= __('dashboard.models.start') ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ud-models-empty">
          <i class="fas fa-layer-group"></i>
          <p><?= __('dashboard.models.empty') ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Assessment List -->
    <div class="user-assessment-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-file-alt" style="color:var(--vx-primary);margin-right:0.5rem;"></i><?= __('dashboard.assessments.title') ?></span>
        <div style="display:flex;gap:0.5rem;">
          <a href="/user/consolidated" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
            <i class="fas fa-layer-group" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.consolidate') ?>
          </a>
          <a href="/assessment/start" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;<?= isset($assessmentsRemaining) && $assessmentsRemaining === 0 ? 'pointer-events:none;opacity:0.5;' : '' ?>">
            <i class="fas fa-plus" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.new') ?>
            <?php if (isset($assessmentLimit) && $assessmentLimit !== null): ?>
              <span style="margin-left:0.35rem;font-size:0.65rem;opacity:0.85;">(<?= $assessmentsUsed ?>/<?= $assessmentLimit ?>)</span>
            <?php endif; ?>
          </a>
        </div>
      </div>

      <?php if (!empty($assessments)): ?>
        <?php foreach ($assessments as $a):
          $statusBadge = $a['status'] === 'completed' ? 'var(--vx-success)' : 'var(--vx-warning)';
          $statusBg = $a['status'] === 'completed' ? 'var(--vx-success-light)' : 'var(--vx-warning-light)';
          $statusLabel = $a['status'] === 'completed' ? __('dashboard.assessments.completed') : __('dashboard.assessments.in_progress');
          $reportBadge = '';
          if ($a['report_status'] === 'certified') $reportBadge = '<span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);"><i class="fas fa-certificate" style="margin-right:0.25rem;"></i>' . __('dashboard.report.certified') . '</span>';
          elseif ($a['report_status'] === 'approved') $reportBadge = '<span class="badge" style="background:var(--vx-info-light);color:var(--vx-info);"><i class="fas fa-thumbs-up" style="margin-right:0.25rem;"></i>' . __('dashboard.report.approved') . '</span>';
          elseif ($a['report_status'] === 'under_review') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-magnifying-glass" style="margin-right:0.25rem;"></i>' . __('dashboard.report.under_review') . '</span>';
          elseif ($a['report_status'] === 'certification_requested') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-hourglass" style="margin-right:0.25rem;"></i>' . __('dashboard.report.certification_requested') . '</span>';
          elseif ($a['report_status'] === 'rejected') $reportBadge = '<span class="badge" style="background:var(--vx-danger-light);color:var(--vx-danger);"><i class="fas fa-times-circle" style="margin-right:0.25rem;"></i>' . __('dashboard.report.rejected') . '</span>';
          $scoreVal = $a['total_score'] !== null ? round((float)$a['total_score']) : null;
          $scoreColor = '#6366f1';
          if ($scoreVal !== null) {
              if ($scoreVal >= 86) $scoreColor = '#d97706';
              elseif ($scoreVal >= 71) $scoreColor = '#059669';
              elseif ($scoreVal >= 51) $scoreColor = '#1a56db';
              elseif ($scoreVal >= 31) $scoreColor = '#fd7e14';
              else $scoreColor = '#6c757d';
          }
        ?>
          <div class="user-assessment-item">
            <div style="flex:1;">
              <div class="user-assessment-company"><?= e($a['company'] ?? ($a['lead_firstname'] ?? '') . ' ' . ($a['lead_lastname'] ?? '')) ?></div>
              <div class="user-assessment-meta">
                <span class="badge" style="background:<?= $statusBg ?>;color:<?= $statusBadge ?>;"><?= $statusLabel ?></span>
                <?php if ($a['total_score'] !== null): ?>
                  <span><?= __('dashboard.assessments.score') ?>: <strong style="color:<?= $scoreColor ?>;"><?= $scoreVal ?>/100</strong></span>
                  <div class="ud-mini-bar">
                    <div class="ud-mini-bar-fill" style="width:<?= $scoreVal ?>%;background:<?= $scoreColor ?>;"></div>
                  </div>
                <?php endif; ?>
                <span><?= date('d/m/Y', strtotime($a['created_at'])) ?></span>
                <?= $reportBadge ?>
              </div>
            </div>
            <div style="display:flex;gap:0.5rem;">
              <?php if ($a['status'] === 'completed'): ?>
                <a href="/assessment/<?= $a['id'] ?>/results" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-file-alt" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.results') ?>
                </a>
                <?php if ($a['report_status'] === 'certified'): ?>
                  <a href="/report/<?= $a['id'] ?>/download" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                    <i class="fas fa-certificate" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.download_cert') ?>
                  </a>
                <?php endif; ?>
              <?php else: ?>
                <a href="/assessment/<?= $a['id'] ?>" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-arrow-right" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.continue') ?>
                </a>
                <form method="POST" action="/assessment/<?= $a['id'] ?>/cancel" style="display:inline;" onsubmit="return confirm('Supprimer cette évaluation ? Cette action est irréversible.');">
                  <button type="submit" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;color:var(--vx-danger);border-color:var(--vx-danger);">
                    <i class="fas fa-trash" style="margin-right:0.25rem;"></i>Supprimer
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="user-empty-state">
          <div class="user-empty-icon"><i class="fas fa-clipboard-list"></i></div>
          <p><?= __('dashboard.assessments.empty') ?></p>
          <a href="/assessment/start" class="nova-btn nova-btn-primary" <?= isset($assessmentsRemaining) && $assessmentsRemaining === 0 ? 'style="pointer-events:none;opacity:0.5;"' : '' ?>><i class="fas fa-plus" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.start') ?></a>
          <?php if (isset($assessmentLimit) && $assessmentLimit !== null && $assessmentsRemaining === 0): ?>
            <p style="margin-top:0.75rem;font-size:0.75rem;color:var(--vx-danger);font-weight:600;">
              <i class="fas fa-lock" style="margin-right:0.25rem;"></i>Vous avez atteint votre limite de <?= $assessmentLimit ?> évaluation(s).
            </p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- AQMI Excellence Footer -->
  <footer class="ud-excellence-footer">
    <div class="ud-excellence-inner">
      <div class="ud-excellence-brand">
        <span class="ud-excellence-mark">N</span>
        <div>
          <div class="ud-excellence-name">NOVAQYS · AQMI</div>
          <div class="ud-excellence-tag">Automotive Quality Maturity Index</div>
        </div>
      </div>
      <div class="ud-excellence-slogan">
        <i class="fas fa-medal ud-excellence-icon"></i>
        <span>L'excellence qualité n'est pas une destination, c'est chaque évaluation qui vous y mène.</span>
      </div>
      <div class="ud-excellence-links">
        <a href="/cgu">CGU</a>
        <span class="ud-excellence-dot">·</span>
        <a href="/privacy">Protection des données</a>
      </div>
    </div>
    <div class="ud-excellence-copy">
      &copy; <?= date('Y') ?> NOVAQYS. Tous droits réservés.
    </div>
  </footer>

  <!-- Mobile Bottom Navigation -->
  <nav style="position:fixed;bottom:0;left:0;right:0;z-index:1050;background:rgba(255,253,248,0.96);border-top:1px solid var(--vx-card-border);display:flex;padding:0.35rem 0;justify-content:space-around;backdrop-filter:blur(12px);" class="d-md-none">
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-primary);padding:0.25rem 0.5rem;gap:0.15rem;" href="/user/dashboard">
      <i class="fas fa-home" style="font-size:0.9rem;"></i><span><?= __('dashboard.nav.home') ?></span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/assessment/start">
      <i class="fas fa-plus-circle" style="font-size:0.9rem;"></i><span><?= __('dashboard.nav.new') ?></span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/">
      <i class="fas fa-globe" style="font-size:0.9rem;"></i><span><?= __('dashboard.nav.site') ?></span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-danger);padding:0.25rem 0.5rem;gap:0.15rem;" href="/logout">
      <i class="fas fa-sign-out-alt" style="font-size:0.9rem;"></i><span><?= __('dashboard.nav.quit') ?></span>
    </a>
  </nav>
</div>

<script>
(function() {
  var dropdown = document.getElementById('udLangDropdown');
  var btn = document.getElementById('udLangBtn');
  if (!dropdown || !btn) return;
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    dropdown.classList.toggle('is-open');
  });
  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) dropdown.classList.remove('is-open');
  });
})();
</script>

<?php if (!empty($scoreHistory) && count($scoreHistory) >= 1): ?>
<script>
(function() {
  const ctx = document.getElementById('udScoreChart');
  if (!ctx) return;

  const history = <?= json_encode($scoreHistory, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) ?: '[]' ?>;
  const labels = history.map(function(h) {
    const d = new Date(h.date);
    return d.toLocaleDateString('<?= $chartLocale ?>', { day: '2-digit', month: 'short' });
  });
  const scores = history.map(function(h) { return h.score; });

  const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
  gradient.addColorStop(0, 'rgba(99,102,241,0.25)');
  gradient.addColorStop(1, 'rgba(99,102,241,0.0)');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Score',
        data: scores,
        borderColor: '#6366f1',
        backgroundColor: gradient,
        borderWidth: 2.5,
        fill: true,
        tension: 0.35,
        pointBackgroundColor: '#6366f1',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointHoverBackgroundColor: '#5558e3',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(23,33,43,0.95)',
          titleColor: '#fff',
          bodyColor: '#fff',
          titleFont: { family: 'Manrope', size: 12, weight: '700' },
          bodyFont: { family: 'Manrope', size: 13, weight: '600' },
          padding: 12,
          cornerRadius: 8,
          displayColors: false,
          callbacks: {
            label: function(context) {
              return 'Score: ' + context.parsed.y + '/100';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          grid: { color: 'rgba(80,64,42,0.06)' },
          ticks: {
            font: { family: 'Manrope', size: 10 },
            color: '#7d8794',
            stepSize: 25,
            callback: function(v) { return v + '%'; }
          }
        },
        x: {
          grid: { display: false },
          ticks: {
            font: { family: 'Manrope', size: 10 },
            color: '#7d8794',
          }
        }
      },
      animation: {
        duration: 1200,
        easing: 'easeOutQuart'
      }
    }
  });
})();
</script>
<?php endif; ?>

<!-- ====== Per-Model Radar Charts ====== -->
<?php
  $radarModels = [];
  foreach ($modelStats as $m) {
    if ($m['latest_score'] !== null && !empty($m['chart_labels'])) {
      $radarModels[] = [
        'id' => $m['id'],
        'labels' => $m['chart_labels'],
        'values' => $m['chart_values'],
        'color' => $m['color'],
      ];
    }
  }
?>
<?php if (!empty($radarModels)): ?>
<script>
(function() {
  const modelStats = <?= json_encode($radarModels, JSON_UNESCAPED_UNICODE) ?: '[]' ?>;

  const chartLocale = '<?= $chartLocale ?>';

  modelStats.forEach(function(ms) {
    const canvas = document.getElementById('modelRadar_' + ms.id);
    if (!canvas) return;

    const color = ms.color || '#1a56db';

    new Chart(canvas, {
      type: 'radar',
      data: {
        labels: ms.labels,
        datasets: [{
          data: ms.values,
          backgroundColor: color + '25',
          borderColor: color,
          borderWidth: 2,
          pointBackgroundColor: color,
          pointBorderColor: '#fff',
          pointBorderWidth: 1.5,
          pointRadius: 3,
          pointHoverRadius: 5,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(23,33,43,0.95)',
            titleColor: '#fff',
            bodyColor: '#fff',
            titleFont: { family: 'Manrope', size: 10, weight: '700' },
            bodyFont: { family: 'Manrope', size: 11, weight: '600' },
            padding: 8,
            cornerRadius: 6,
            displayColors: false,
            callbacks: {
              label: function(ctx) {
                return ctx.parsed.r + '%';
              }
            }
          }
        },
        scales: {
          r: {
            min: 0,
            max: 100,
            beginAtZero: true,
            angleLines: { color: 'rgba(80,64,42,0.08)' },
            grid: { color: 'rgba(80,64,42,0.06)' },
            pointLabels: {
              font: { family: 'Manrope', size: 7, weight: '600' },
              color: '#7d8794',
            },
            ticks: {
              display: false,
              stepSize: 25,
            }
          }
        },
        animation: {
          duration: 900,
          easing: 'easeOutQuart'
        }
      }
    });
  });
})();
</script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
