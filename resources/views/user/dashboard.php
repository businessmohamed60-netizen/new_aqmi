<?php
$title = __('dashboard.title');
$activeSection = 'overview';
$pageTitle = __('dashboard.nav.overview') ?: 'Vue d\'ensemble';
$pageSubtitle = __('dashboard.welcome_sub');
ob_start();
$langCode = $_SESSION['lang'] ?? 'fr';
$levelNameField = $langCode === 'ar' ? 'name_ar' : ($langCode === 'fr' ? 'name_fr' : 'name');
$chartLocale = $langCode === 'ar' ? 'ar' : ($langCode === 'en' ? 'en-US' : 'fr-FR');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
/* ====== Overview-specific styles ====== */
.ud-welcome {
  background: linear-gradient(135deg, rgba(31,111,235,0.08) 0%, rgba(255,253,248,0.94) 45%, #fffdf8 100%);
  backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  padding: 1.75rem 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
}
.ud-welcome::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 60%);
  border-radius: 50%;
  animation: udGlowPulse 6s ease-in-out infinite;
}
@keyframes udGlowPulse {
  0%,100% { opacity: 0.4; transform: scale(0.95); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
.ud-welcome h2 { font-size: 1.5rem; font-weight: 800; color: var(--vx-text-primary); margin-bottom: 0.15rem; letter-spacing: -0.3px; position: relative; z-index: 1; }
.ud-welcome p { color: var(--vx-text-muted); font-size: 0.8rem; position: relative; z-index: 1; }

/* Motivation banner */
.ud-motivation { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border-radius: var(--vx-radius-lg); margin-bottom: 1.5rem; position: relative; overflow: hidden; border: 1px solid; animation: udSlideIn 0.5s ease; }
@keyframes udSlideIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.ud-mot--excellent { background: linear-gradient(135deg, rgba(16,185,129,0.10), rgba(255,253,248,0.95)); border-color: rgba(16,185,129,0.25); }
.ud-mot--good { background: linear-gradient(135deg, rgba(6,182,212,0.10), rgba(255,253,248,0.95)); border-color: rgba(6,182,212,0.25); }
.ud-mot--progress { background: linear-gradient(135deg, rgba(245,158,11,0.10), rgba(255,253,248,0.95)); border-color: rgba(245,158,11,0.25); }
.ud-mot--start { background: linear-gradient(135deg, rgba(99,102,241,0.10), rgba(255,253,248,0.95)); border-color: rgba(99,102,241,0.25); }
.ud-mot-icon { width: 2.75rem; height: 2.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; color: #fff; }
.ud-mot--excellent .ud-mot-icon { background: var(--vx-success-gradient); }
.ud-mot--good .ud-mot-icon { background: var(--vx-info-gradient); }
.ud-mot--progress .ud-mot-icon { background: var(--vx-warning-gradient); }
.ud-mot--start .ud-mot-icon { background: var(--vx-primary-gradient); }
.ud-mot-text h3 { font-size: 0.875rem; font-weight: 800; color: var(--vx-text-primary); margin: 0 0 0.15rem; }
.ud-mot-text p { font-size: 0.75rem; color: var(--vx-text-secondary); margin: 0; line-height: 1.5; }

/* KPI stats */
.ud-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
@media (max-width: 768px) { .ud-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .ud-stats-grid { grid-template-columns: 1fr; } }
.ud-stat-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all var(--vx-transition); min-height: 80px; position: relative; overflow: hidden; }
.ud-stat-card::after { content: ''; position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 70%); opacity: 0; transition: opacity var(--vx-transition); }
.ud-stat-card:hover { transform: translateY(-3px); box-shadow: var(--vx-shadow-lg); border-color: var(--vx-card-border-hover); }
.ud-stat-card:hover::after { opacity: 0.3; }
.ud-stat-icon { width: 2.75rem; height: 2.75rem; border-radius: var(--vx-radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.125rem; flex-shrink: 0; transition: transform var(--vx-transition); }
.ud-stat-card:hover .ud-stat-icon { transform: scale(1.08); }
.ud-stat-value { font-size: 1.5rem; font-weight: 800; color: var(--vx-text-primary); line-height: 1; }
.ud-stat-label { font-size: 0.6875rem; color: var(--vx-text-muted); font-weight: 500; margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.03em; }
.ud-stat-trend { display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.625rem; font-weight: 700; padding: 0.15rem 0.4rem; border-radius: 0.25rem; margin-top: 0.25rem; }
.ud-stat-trend--neutral { background: var(--vx-primary-light); color: var(--vx-primary); }

/* Two-column */
.ud-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
@media (max-width: 768px) { .ud-two-col { grid-template-columns: 1fr; } }

/* Gauge card */
.ud-gauge-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); padding: 1.5rem; text-align: center; }
.ud-gauge-card h3 { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--vx-text-muted); margin: 0 0 1rem; }
.ud-gauge-wrap { position: relative; width: 200px; height: 200px; margin: 0 auto 1rem; }
.ud-gauge-svg { width: 100%; height: 100%; transform: rotate(-90deg); }
.ud-gauge-bg { fill: none; stroke: rgba(99,102,241,0.10); stroke-width: 14; }
.ud-gauge-fill { fill: none; stroke-width: 14; stroke-linecap: round; transition: stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1); }
.ud-gauge-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
.ud-gauge-score { font-size: 2.5rem; font-weight: 900; color: var(--vx-text-primary); line-height: 1; }
.ud-gauge-score small { font-size: 0.875rem; font-weight: 500; color: var(--vx-text-muted); }
.ud-gauge-level-badge { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; font-weight: 600; padding: 0.3rem 0.75rem; border-radius: 0.375rem; margin-top: 0.5rem; }

/* Maturity ladder */
.ud-maturity-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); padding: 1.5rem; }
.ud-maturity-card h3 { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--vx-text-muted); margin: 0 0 1.25rem; }
.ud-maturity-ladder { display: flex; flex-direction: column; gap: 0.5rem; }
.ud-maturity-step { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.75rem; border-radius: var(--vx-radius-md); border: 1px solid transparent; transition: all var(--vx-transition); }
.ud-maturity-step:hover { background: rgba(99,102,241,0.03); }
.ud-maturity-step.is-current { background: rgba(99,102,241,0.06); border-color: rgba(99,102,241,0.20); box-shadow: 0 2px 12px rgba(99,102,241,0.08); }
.ud-maturity-dot { width: 0.75rem; height: 0.75rem; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(255,255,255,0.8); }
.ud-maturity-step.is-current .ud-maturity-dot { animation: udDotPulse 2s ease-in-out infinite; }
@keyframes udDotPulse {
  0%,100% { box-shadow: 0 0 0 3px rgba(255,255,255,0.8), 0 0 0 0 rgba(99,102,241,0.3); }
  50% { box-shadow: 0 0 0 3px rgba(255,255,255,0.8), 0 0 0 8px rgba(99,102,241,0); }
}
.ud-maturity-info { flex: 1; display: flex; justify-content: space-between; align-items: center; }
.ud-maturity-name { font-size: 0.8125rem; font-weight: 700; color: var(--vx-text-primary); }
.ud-maturity-range { font-size: 0.625rem; color: var(--vx-text-muted); font-weight: 600; }
.ud-maturity-check { color: var(--vx-success); font-size: 0.875rem; }

/* Chart card */
.ud-chart-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); padding: 1.5rem; margin-bottom: 1.5rem; }
.ud-chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
.ud-chart-header h3 { font-size: 0.8125rem; font-weight: 700; color: var(--vx-text-primary); margin: 0; }
.ud-chart-sub { font-size: 0.6875rem; color: var(--vx-text-muted); }
.ud-chart-container { position: relative; height: 220px; }

/* Assessment list */
.ud-assessment-card { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); box-shadow: var(--vx-shadow-md); }
.ud-assessment-item { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--vx-divider); transition: all var(--vx-transition); }
.ud-assessment-item:last-child { border-bottom: none; }
.ud-assessment-item:hover { background: rgba(99,102,241,0.03); }
.ud-assessment-company { font-size: 0.8125rem; font-weight: 600; color: var(--vx-text-primary); }
.ud-assessment-meta { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem; font-size: 0.6875rem; color: var(--vx-text-muted); flex-wrap: wrap; }
.ud-mini-bar { width: 80px; height: 6px; border-radius: 3px; background: rgba(99,102,241,0.10); overflow: hidden; position: relative; }
.ud-mini-bar-fill { height: 100%; border-radius: 3px; transition: width 1s ease; }

/* Empty state */
.ud-empty-state { text-align: center; padding: 3rem 1rem; }
.ud-empty-icon { width: 64px; height: 64px; border-radius: 50%; background: var(--vx-primary-light); border: 1px solid rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: var(--vx-primary); font-size: 1.5rem; animation: udEmptyPulse 3s ease-in-out infinite; }
@keyframes udEmptyPulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
  50% { box-shadow: 0 0 24px 4px rgba(99,102,241,0.15); }
}
.ud-empty-state p { color: var(--vx-text-muted); font-size: 0.85rem; margin-bottom: 1rem; }

/* Buttons */
.nova-btn { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; border-radius: var(--vx-radius-sm); font-size: 0.8125rem; font-weight: 600; text-decoration: none; transition: all var(--vx-transition); cursor: pointer; border: none; font-family: var(--ud-font); }
.nova-btn-primary { background: var(--vx-primary-gradient); color: #fff; box-shadow: 0 4px 16px var(--vx-primary-glow); }
.nova-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px var(--vx-primary-glow); filter: brightness(1.1); color: #fff; }
.nova-btn-outline { background: transparent; color: var(--vx-text-secondary); border: 1px solid var(--vx-card-border); }
.nova-btn-outline:hover { background: rgba(99,102,241,0.06); border-color: var(--vx-card-border-hover); color: var(--vx-text-primary); transform: translateY(-1px); }

/* Card header */
.ud-card-header { background: transparent; border-bottom: 1px solid var(--vx-divider); padding: 0.875rem 1.25rem; font-size: 0.8125rem; font-weight: 600; color: var(--vx-text-primary); display: flex; align-items: center; justify-content: space-between; }
.badge { font-size: 0.625rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: var(--vx-radius-sm); }

/* Quick models preview */
.ud-models-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.ud-model-mini { background: var(--vx-card-bg); border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-lg); padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: all var(--vx-transition); text-decoration: none; }
.ud-model-mini:hover { transform: translateY(-3px); box-shadow: var(--vx-shadow-md); border-color: var(--vx-card-border-hover); }
.ud-model-mini-icon { width: 2.25rem; height: 2.25rem; border-radius: var(--vx-radius-md); display: flex; align-items: center; justify-content: center; font-size: 0.95rem; color: #fff; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.ud-model-mini-info { flex: 1; min-width: 0; }
.ud-model-mini-name { font-size: 0.78rem; font-weight: 700; color: var(--vx-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ud-model-mini-score { font-size: 0.68rem; color: var(--vx-text-muted); margin-top: 0.1rem; }
.ud-model-mini-score strong { color: var(--vx-text-primary); font-weight: 800; }

@media (max-width: 768px) {
  .ud-welcome { padding: 1.25rem; }
  .ud-welcome h2 { font-size: 1.15rem; }
  .ud-welcome p { font-size: 0.72rem; }
  .ud-assessment-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
  .ud-gauge-wrap { width: 170px; height: 170px; }
  .ud-gauge-score { font-size: 2rem; }
}
</style>

<!-- Welcome -->
<div class="ud-welcome">
  <h2><?= __('dashboard.welcome', ['name' => e($user['firstname'] ?? '')]) ?></h2>
  <p><?= __('dashboard.welcome_sub') ?></p>
  <?php if (isset($assessmentLimit) && $assessmentLimit !== null): ?>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:<?= $assessmentsRemaining > 0 ? 'var(--vx-text-secondary)' : 'var(--vx-danger)' ?>;font-weight:600;position:relative;z-index:1;">
      <i class="fas fa-chart-bar" style="margin-right:0.25rem;"></i>
      <?= $assessmentsRemaining > 0
        ? "Évaluations restantes : {$assessmentsRemaining} / {$assessmentLimit}"
        : "Limite atteinte ({$assessmentLimit} évaluation(s)). Contactez l'administrateur." ?>
    </p>
  <?php endif; ?>
</div>

<!-- Motivation Banner -->
<?php
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
  <div class="ud-stat-card">
    <div class="ud-stat-icon" style="background:var(--vx-primary-light);color:var(--vx-primary);border:1px solid rgba(99,102,241,0.15);">
      <i class="fas fa-clipboard-list"></i>
    </div>
    <div>
      <div class="ud-stat-value"><?= $totalAssessments ?></div>
      <div class="ud-stat-label"><?= __('dashboard.stat.total') ?></div>
    </div>
  </div>
  <div class="ud-stat-card">
    <div class="ud-stat-icon" style="background:var(--vx-success-light);color:var(--vx-success);border:1px solid rgba(16,185,129,0.15);">
      <i class="fas fa-check-circle"></i>
    </div>
    <div>
      <div class="ud-stat-value" style="color:var(--vx-success);"><?= $completedCount ?></div>
      <div class="ud-stat-label"><?= __('dashboard.stat.completed') ?></div>
      <?php if ($totalAssessments > 0): ?>
        <span class="ud-stat-trend ud-stat-trend--neutral"><?= $completionRate ?>%</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="ud-stat-card">
    <div class="ud-stat-icon" style="background:var(--vx-info-light);color:var(--vx-info);border:1px solid rgba(6,182,212,0.15);">
      <i class="fas fa-chart-line"></i>
    </div>
    <div>
      <div class="ud-stat-value" style="color:var(--vx-info);"><?= $bestScore !== null ? $bestScore : '—' ?></div>
      <div class="ud-stat-label"><?= __('dashboard.stat.best') ?></div>
    </div>
  </div>
  <div class="ud-stat-card">
    <div class="ud-stat-icon" style="background:var(--vx-warning-light);color:var(--vx-warning);border:1px solid rgba(245,158,11,0.15);">
      <i class="fas fa-hourglass-half"></i>
    </div>
    <div>
      <div class="ud-stat-value" style="color:var(--vx-warning);"><?= $totalAssessments - $completedCount ?></div>
      <div class="ud-stat-label"><?= __('dashboard.stat.inprogress') ?></div>
    </div>
  </div>
</div>

<!-- Quick Models Preview -->
<?php if (!empty($modelStats)): ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
  <h3 style="font-size:0.95rem;font-weight:800;color:var(--vx-text-primary);margin:0;">
    <i class="fas fa-layer-group" style="color:var(--vx-primary);margin-right:0.4rem;"></i>
    <?= __('dashboard.models.title') ?>
  </h3>
  <a href="/user/models" class="nova-btn nova-btn-outline" style="padding:0.3rem 0.7rem;font-size:0.72rem;">
    <?= __('dashboard.models.view_all') ?: 'Voir tout' ?> <i class="fas fa-arrow-right" style="margin-left:0.25rem;"></i>
  </a>
</div>
<div class="ud-models-preview">
  <?php foreach (array_slice($modelStats, 0, 4) as $ms):
    $mColor = $ms['color'] ?: '#1a56db';
    $hasScore = $ms['latest_score'] !== null;
  ?>
    <a href="/user/models" class="ud-model-mini">
      <div class="ud-model-mini-icon" style="background:<?= htmlspecialchars($mColor) ?>;">
        <i class="fas <?= htmlspecialchars($ms['icon'] ?: 'fa-clipboard-check') ?>"></i>
      </div>
      <div class="ud-model-mini-info">
        <div class="ud-model-mini-name"><?= htmlspecialchars($ms['display_name'] ?: $ms['name']) ?></div>
        <div class="ud-model-mini-score">
          <?= $hasScore ? '<strong>' . round($ms['latest_score']) . '/100</strong>' : ($ms['completed'] . ' / ' . $ms['total'] . ' ' . __('dashboard.models.assessments')) ?>
        </div>
      </div>
    </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

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

<!-- Assessment List -->
<div class="ud-assessment-card">
  <div class="ud-card-header">
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
      <div class="ud-assessment-item">
        <div style="flex:1;">
          <div class="ud-assessment-company"><?= e($a['company'] ?? ($a['lead_firstname'] ?? '') . ' ' . ($a['lead_lastname'] ?? '')) ?></div>
          <div class="ud-assessment-meta">
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
    <div class="ud-empty-state">
      <div class="ud-empty-icon"><i class="fas fa-clipboard-list"></i></div>
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

<!-- Score Chart Script -->
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
        label: 'Score', data: scores,
        borderColor: '#6366f1', backgroundColor: gradient,
        borderWidth: 2.5, fill: true, tension: 0.35,
        pointBackgroundColor: '#6366f1', pointBorderColor: '#fff',
        pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7,
        pointHoverBackgroundColor: '#5558e3',
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(23,33,43,0.95)', titleColor: '#fff', bodyColor: '#fff',
          titleFont: { family: 'Manrope', size: 12, weight: '700' },
          bodyFont: { family: 'Manrope', size: 13, weight: '600' },
          padding: 12, cornerRadius: 8, displayColors: false,
          callbacks: { label: function(context) { return 'Score: ' + context.parsed.y + '/100'; } }
        }
      },
      scales: {
        y: { beginAtZero: true, max: 100, grid: { color: 'rgba(80,64,42,0.06)' },
             ticks: { font: { family: 'Manrope', size: 10 }, color: '#7d8794', stepSize: 25, callback: function(v) { return v + '%'; } } },
        x: { grid: { display: false }, ticks: { font: { family: 'Manrope', size: 10 }, color: '#7d8794' } }
      },
      animation: { duration: 1200, easing: 'easeOutQuart' }
    }
  });
})();
</script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
