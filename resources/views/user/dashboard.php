<?php $title = 'Tableau de bord - Espace Client'; ob_start(); ?>
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
  background: rgba(255,253,248,0.94);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--vx-card-border);
  padding: 0.75rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
}
.user-topbar .brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 800;
  font-size: 1.05rem;
  color: var(--vx-text-primary);
  letter-spacing: -0.3px;
}
.user-topbar .brand .brand-icon {
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
  transition: transform 0.3s ease;
}
.user-topbar .brand:hover .brand-icon {
  transform: scale(1.05) rotate(-3deg);
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
  .user-topbar { padding: 0.5rem 1rem; }
  .user-content { padding: 1rem; }
  .user-welcome { padding: 1.25rem; }
  .user-welcome h2 { font-size: 1.15rem; }
  .user-welcome p { font-size: 0.72rem; }
  .user-assessment-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
  .ud-gauge-wrap { width: 170px; height: 170px; }
  .ud-gauge-score { font-size: 2rem; }
}
</style>

<div class="user-dashboard">
  <!-- Topbar -->
  <div class="user-topbar">
    <div class="brand">
      <span class="brand-icon">N</span>
      <span>NOVAQYS</span>
    </div>
    <div class="d-flex align-items-center" style="gap:1rem;">
      <span class="d-md-inline" style="font-size:0.75rem;color:var(--vx-text-secondary);">
        <i class="fas fa-user" style="color:var(--vx-primary);margin-right:0.25rem;"></i><?= e($user['firstname'] ?? '') ?> <?= e($user['lastname'] ?? '') ?>
      </span>
      <a href="/logout" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
        <i class="fas fa-sign-out-alt" style="margin-right:0.25rem;"></i>Déconnexion
      </a>
    </div>
  </div>

  <!-- Content -->
  <div class="user-content">
    <!-- Welcome -->
    <div class="user-welcome">
      <h2>Bonjour, <?= e($user['firstname'] ?? '') ?></h2>
      <p>Bienvenue dans votre espace client NOVAQYS. Suivez votre parcours d'excellence qualité en temps réel.</p>
    </div>

    <?php
      // Motivation message based on latest score
      $motClass = 'ud-mot--start';
      $motIcon = 'fa-rocket';
      $motTitle = 'Lancez votre premier audit';
      $motText = "Vous n'avez pas encore d'évaluation terminée. Commencez votre parcours d'auto-évaluation pour mesurer votre maturité qualité.";
      if ($latestScore !== null) {
          if ($latestScore >= 71) {
              $motClass = 'ud-mot--excellent'; $motIcon = 'fa-trophy';
              $motTitle = 'Excellente performance !';
              $motText = "Votre score de {$latestScore}/100 vous place au niveau « {$maturityLevel['name_fr']} ». Continuez sur cette lancée pour atteindre l'excellence.";
          } elseif ($latestScore >= 51) {
              $motClass = 'ud-mot--good'; $motIcon = 'fa-chart-line';
              $motTitle = 'Bonne progression';
              $motText = "Votre score de {$latestScore}/100 montre une structure qualité solide. Quelques efforts ciblés vous mèneront au niveau supérieur.";
          } elseif ($latestScore >= 31) {
              $motClass = 'ud-mot--progress'; $motIcon = 'fa-seedling';
              $motTitle = 'En plein développement';
              $motText = "Votre score de {$latestScore}/100 indique des fondations en construction. Identifiez vos axes prioritaires pour accélérer votre progression.";
          } else {
              $motClass = 'ud-mot--progress'; $motIcon = 'fa-flag';
              $motTitle = 'Premier pas accompli';
              $motText = "Votre score de {$latestScore}/100 marque le début de votre parcours. Chaque évaluation vous rapproche de l'excellence qualité.";
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
          <div class="user-stat-label">Évaluations totales</div>
        </div>
      </div>
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-success-light);color:var(--vx-success);border:1px solid rgba(16,185,129,0.15);">
          <i class="fas fa-check-circle"></i>
        </div>
        <div>
          <div class="user-stat-value" style="color:var(--vx-success);"><?= $completedCount ?></div>
          <div class="user-stat-label">Terminées</div>
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
          <div class="user-stat-label">Meilleur score</div>
        </div>
      </div>
      <div class="user-stat-card">
        <div class="user-stat-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(245,158,11,0.15);">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div>
          <div class="user-stat-value" style="color:var(--vx-warning);"><?= $totalAssessments - $completedCount ?></div>
          <div class="user-stat-label">En cours</div>
        </div>
      </div>
    </div>

    <!-- Two-column: Gauge + Maturity Ladder -->
    <div class="ud-two-col">
      <!-- Score Gauge -->
      <div class="ud-gauge-card">
        <h3>Score global</h3>
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
                <?= htmlspecialchars($maturityLevel['name_fr'] ?? $maturityLevel['name'] ?? 'N/A') ?>
              </div>
            </div>
          </div>
          <?php if ($progressDelta != 0): ?>
            <div style="font-size:0.72rem;color:var(--vx-text-muted);">
              <?php if ($progressDelta > 0): ?>
                <i class="fas fa-arrow-trend-up" style="color:var(--vx-success);"></i>
                <span style="color:var(--vx-success);font-weight:700;">+<?= $progressDelta ?></span> depuis votre première évaluation
              <?php else: ?>
                <i class="fas fa-arrow-trend-down" style="color:var(--vx-danger);"></i>
                <span style="color:var(--vx-danger);font-weight:700;"><?= $progressDelta ?></span> depuis votre première évaluation
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div style="font-size:0.72rem;color:var(--vx-text-muted);">
              <i class="fas fa-minus" style="color:var(--vx-text-muted);"></i> Score stable
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="ud-gauge-wrap" style="display:flex;align-items:center;justify-content:center;">
            <div class="ud-gauge-center">
              <div class="ud-gauge-score" style="color:var(--vx-text-muted);">—</div>
              <div style="font-size:0.7rem;color:var(--vx-text-muted);margin-top:0.5rem;">Aucun score</div>
            </div>
          </div>
          <div style="font-size:0.72rem;color:var(--vx-text-muted);">Terminez une évaluation pour voir votre score</div>
        <?php endif; ?>
      </div>

      <!-- Maturity Ladder -->
      <div class="ud-maturity-card">
        <h3>Niveaux de maturité</h3>
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
                  <?= htmlspecialchars($sl['name_fr'] ?: $sl['name']) ?>
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
          <h3><i class="fas fa-chart-line" style="color:var(--vx-primary);margin-right:0.5rem;"></i>Évolution de vos scores</h3>
          <span class="ud-chart-sub"><?= count($scoreHistory) ?> évaluation(s) terminée(s)</span>
        </div>
        <div class="ud-chart-container">
          <canvas id="udScoreChart"></canvas>
        </div>
      </div>
    <?php endif; ?>

    <!-- Assessment List -->
    <div class="user-assessment-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-file-alt" style="color:var(--vx-primary);margin-right:0.5rem;"></i>Mes évaluations</span>
        <div style="display:flex;gap:0.5rem;">
          <a href="/user/consolidated" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
            <i class="fas fa-layer-group" style="margin-right:0.25rem;"></i>Consolider
          </a>
          <a href="/assessment/start" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
            <i class="fas fa-plus" style="margin-right:0.25rem;"></i>Nouvelle
          </a>
        </div>
      </div>

      <?php if (!empty($assessments)): ?>
        <?php foreach ($assessments as $a):
          $statusBadge = $a['status'] === 'completed' ? 'var(--vx-success)' : 'var(--vx-warning)';
          $statusBg = $a['status'] === 'completed' ? 'var(--vx-success-light)' : 'var(--vx-warning-light)';
          $statusLabel = $a['status'] === 'completed' ? 'Terminée' : 'En cours';
          $reportBadge = '';
          if ($a['report_status'] === 'certified') $reportBadge = '<span class="badge" style="background:var(--vx-success-light);color:var(--vx-success);"><i class="fas fa-certificate" style="margin-right:0.25rem;"></i>Certifié</span>';
          elseif ($a['report_status'] === 'approved') $reportBadge = '<span class="badge" style="background:var(--vx-info-light);color:var(--vx-info);"><i class="fas fa-thumbs-up" style="margin-right:0.25rem;"></i>Approuvé</span>';
          elseif ($a['report_status'] === 'under_review') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-magnifying-glass" style="margin-right:0.25rem;"></i>En examen</span>';
          elseif ($a['report_status'] === 'certification_requested') $reportBadge = '<span class="badge" style="background:var(--vx-warning-light);color:var(--vx-warning);"><i class="fas fa-hourglass" style="margin-right:0.25rem;"></i>En attente de validation</span>';
          elseif ($a['report_status'] === 'rejected') $reportBadge = '<span class="badge" style="background:var(--vx-danger-light);color:var(--vx-danger);"><i class="fas fa-times-circle" style="margin-right:0.25rem;"></i>Rejeté</span>';
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
                  <span>Score: <strong style="color:<?= $scoreColor ?>;"><?= $scoreVal ?>/100</strong></span>
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
                  <i class="fas fa-file-alt" style="margin-right:0.25rem;"></i>Résultats
                </a>
                <?php if ($a['report_status'] === 'certified'): ?>
                  <a href="/report/<?= $a['id'] ?>/download" class="nova-btn nova-btn-primary" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                    <i class="fas fa-certificate" style="margin-right:0.25rem;"></i>Télécharger mon certificat AQMI
                  </a>
                <?php endif; ?>
              <?php else: ?>
                <a href="/assessment/<?= $a['id'] ?>" class="nova-btn nova-btn-outline" style="padding:0.375rem 0.75rem;font-size:0.75rem;">
                  <i class="fas fa-arrow-right" style="margin-right:0.25rem;"></i>Continuer
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="user-empty-state">
          <div class="user-empty-icon"><i class="fas fa-clipboard-list"></i></div>
          <p>Vous n'avez pas encore d'évaluation.</p>
          <a href="/assessment/start" class="nova-btn nova-btn-primary"><i class="fas fa-plus" style="margin-right:0.25rem;"></i>Commencer</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile Bottom Navigation -->
  <nav style="position:fixed;bottom:0;left:0;right:0;z-index:1050;background:rgba(255,253,248,0.96);border-top:1px solid var(--vx-card-border);display:flex;padding:0.35rem 0;justify-content:space-around;backdrop-filter:blur(12px);" class="d-md-none">
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-primary);padding:0.25rem 0.5rem;gap:0.15rem;" href="/user/dashboard">
      <i class="fas fa-home" style="font-size:0.9rem;"></i><span>Accueil</span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/assessment/start">
      <i class="fas fa-plus-circle" style="font-size:0.9rem;"></i><span>Nouveau</span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-text-muted);padding:0.25rem 0.5rem;gap:0.15rem;" href="/">
      <i class="fas fa-globe" style="font-size:0.9rem;"></i><span>Site</span>
    </a>
    <a class="d-flex" style="flex-direction:column;align-items:center;text-decoration:none;font-size:0.55rem;color:var(--vx-danger);padding:0.25rem 0.5rem;gap:0.15rem;" href="/logout">
      <i class="fas fa-sign-out-alt" style="font-size:0.9rem;"></i><span>Quitter</span>
    </a>
  </nav>
</div>

<?php if (!empty($scoreHistory) && count($scoreHistory) >= 1): ?>
<script>
(function() {
  const ctx = document.getElementById('udScoreChart');
  if (!ctx) return;

  const history = <?= json_encode($scoreHistory) ?>;
  const labels = history.map(function(h) {
    const d = new Date(h.date);
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
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
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
