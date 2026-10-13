<?php
$title = __('dashboard.models.title');
$activeSection = 'models';
$pageTitle = __('dashboard.nav.models') ?: 'Modèles & Analyses';
$pageSubtitle = __('dashboard.models.subtitle');
ob_start();
$langCode = $_SESSION['lang'] ?? 'fr';
$levelNameField = $langCode === 'ar' ? 'name_ar' : ($langCode === 'fr' ? 'name_fr' : 'name');
$chartLocale = $langCode === 'ar' ? 'ar' : ($langCode === 'en' ? 'en-US' : 'fr-FR');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
/* ====== Models page specific ====== */
.ud-models-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}
.ud-models-count {
  font-size: 0.8rem;
  color: var(--vx-text-muted);
}
.ud-models-count strong { color: var(--vx-text-primary); font-weight: 800; }

/* Filter chips */
.ud-filter-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.ud-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.75rem;
  border-radius: 2rem;
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--vx-text-secondary);
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  cursor: pointer;
  transition: all var(--vx-transition);
  font-family: var(--ud-font);
}
.ud-chip:hover { border-color: var(--vx-card-border-hover); color: var(--vx-text-primary); }
.ud-chip.is-active { background: var(--vx-primary); color: #fff; border-color: var(--vx-primary); box-shadow: 0 4px 12px var(--vx-primary-glow); }
.ud-chip-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Model cards grid */
.ud-models-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; margin-bottom: 2rem; }
@media (max-width: 768px) { .ud-models-grid { grid-template-columns: 1fr; } }

.ud-model-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  box-shadow: var(--vx-shadow-md);
  overflow: hidden;
  transition: all var(--vx-transition);
  position: relative;
}
.ud-model-card:hover { transform: translateY(-4px); box-shadow: var(--vx-shadow-lg); border-color: var(--vx-card-border-hover); }

.ud-model-card-top {
  display: flex; align-items: center; gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--vx-divider);
  position: relative;
}
.ud-model-card-top::before {
  content: ''; position: absolute; top: 0; left: 0;
  width: 4px; height: 100%;
}
.ud-model-icon {
  width: 2.25rem; height: 2.25rem;
  border-radius: var(--vx-radius-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; flex-shrink: 0; color: #fff;
  box-shadow: 0 4px 14px rgba(0,0,0,0.10);
}
.ud-model-title { flex: 1; min-width: 0; }
.ud-model-title h4 { font-size: 0.85rem; font-weight: 800; color: var(--vx-text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.15px; }
.ud-model-title .ud-model-meta { font-size: 0.65rem; color: var(--vx-text-muted); margin-top: 0.15rem; }
.ud-model-level-badge {
  display: inline-flex; align-items: center; gap: 0.25rem;
  font-size: 0.625rem; font-weight: 700;
  padding: 0.25rem 0.55rem; border-radius: 0.3rem;
  white-space: nowrap; flex-shrink: 0;
}

.ud-model-card-body {
  display: grid;
  grid-template-columns: 160px 1fr;
  gap: 1rem;
  padding: 1.25rem;
  align-items: center;
}
@media (max-width: 480px) { .ud-model-card-body { grid-template-columns: 1fr; text-align: center; } }

.ud-model-radar-wrap { position: relative; width: 160px; height: 160px; margin: 0 auto; }
.ud-model-radar-wrap canvas { max-width: 160px; max-height: 160px; }

.ud-model-detail { display: flex; flex-direction: column; gap: 0.75rem; }
.ud-model-kpis { display: flex; gap: 0.5rem; }
.ud-model-kpi {
  flex: 1; text-align: center;
  padding: 0.5rem 0.4rem;
  border-radius: var(--vx-radius-md);
  background: rgba(99,102,241,0.04);
  border: 1px solid rgba(99,102,241,0.06);
}
.ud-model-kpi-label { font-size: 0.55rem; color: var(--vx-text-muted); text-transform: uppercase; letter-spacing: 0.03em; font-weight: 600; }
.ud-model-kpi-value { font-size: 1.05rem; font-weight: 800; color: var(--vx-text-primary); line-height: 1.1; margin-top: 0.15rem; }
.ud-model-kpi-value small { font-size: 0.55rem; font-weight: 500; color: var(--vx-text-muted); }

.ud-model-domains { display: flex; flex-direction: column; gap: 0.35rem; }
.ud-domain-row { display: flex; align-items: center; gap: 0.5rem; }
.ud-domain-name { font-size: 0.625rem; color: var(--vx-text-secondary); font-weight: 600; width: 90px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ud-domain-bar { flex: 1; height: 5px; border-radius: 3px; background: rgba(99,102,241,0.08); overflow: hidden; }
.ud-domain-bar-fill { height: 100%; border-radius: 3px; transition: width 0.9s cubic-bezier(0.4,0,0.2,1); }
.ud-domain-pct { font-size: 0.58rem; font-weight: 700; color: var(--vx-text-muted); width: 30px; text-align: right; flex-shrink: 0; }

.ud-model-card-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.75rem 1.25rem;
  border-top: 1px solid var(--vx-divider);
  background: rgba(99,102,241,0.02);
}
.ud-model-footer-info { font-size: 0.65rem; color: var(--vx-text-muted); display: flex; align-items: center; gap: 0.35rem; }
.ud-model-footer-info .ud-model-count { font-weight: 700; color: var(--vx-text-secondary); }
.ud-model-actions { display: flex; gap: 0.4rem; }
.ud-model-btn {
  display: inline-flex; align-items: center; gap: 0.25rem;
  padding: 0.3rem 0.65rem;
  border-radius: var(--vx-radius-sm);
  font-size: 0.65rem; font-weight: 700;
  font-family: var(--ud-font);
  text-decoration: none; transition: all var(--vx-transition); cursor: pointer; border: none;
}
.ud-model-btn--primary { color: #fff; }
.ud-model-btn--primary:hover { transform: translateY(-1px); filter: brightness(1.1); color: #fff; }
.ud-model-btn--outline { background: transparent; color: var(--vx-text-secondary); border: 1px solid var(--vx-card-border); }
.ud-model-btn--outline:hover { background: rgba(99,102,241,0.06); border-color: var(--vx-card-border-hover); color: var(--vx-text-primary); }

.ud-model-no-score { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 160px; gap: 0.5rem; }
.ud-model-no-score i { font-size: 1.5rem; color: var(--vx-text-muted); opacity: 0.4; }
.ud-model-no-score span { font-size: 0.65rem; color: var(--vx-text-muted); text-align: center; }

/* Comparison chart */
.ud-compare-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
  margin-bottom: 2rem;
}
.ud-compare-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
.ud-compare-header h3 { font-size: 0.8125rem; font-weight: 700; color: var(--vx-text-primary); margin: 0; }
.ud-compare-container { position: relative; height: 300px; }

/* Empty state */
.ud-models-empty {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  padding: 2.5rem 1.5rem;
  text-align: center;
}
.ud-models-empty i { font-size: 2rem; color: var(--vx-text-muted); opacity: 0.4; margin-bottom: 0.75rem; display: block; }
.ud-models-empty p { font-size: 0.8rem; color: var(--vx-text-muted); margin: 0 0 1rem; }

/* Buttons */
.nova-btn { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; border-radius: var(--vx-radius-sm); font-size: 0.8125rem; font-weight: 600; text-decoration: none; transition: all var(--vx-transition); cursor: pointer; border: none; font-family: var(--ud-font); }
.nova-btn-primary { background: var(--vx-primary-gradient); color: #fff; box-shadow: 0 4px 16px var(--vx-primary-glow); }
.nova-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px var(--vx-primary-glow); filter: brightness(1.1); color: #fff; }
.nova-btn-outline { background: transparent; color: var(--vx-text-secondary); border: 1px solid var(--vx-card-border); }
.nova-btn-outline:hover { background: rgba(99,102,241,0.06); border-color: var(--vx-card-border-hover); color: var(--vx-text-primary); transform: translateY(-1px); }
</style>

<!-- Toolbar -->
<div class="ud-models-toolbar">
  <div class="ud-models-count">
    <strong><?= count($modelStats) ?></strong> <?= __('dashboard.models.subtitle') ?>
  </div>
  <div class="ud-filter-chips">
    <button class="ud-chip is-active" data-filter="all"><?= __('dashboard.models.filter_all') ?: 'Tous' ?></button>
    <button class="ud-chip" data-filter="completed"><?= __('dashboard.models.filter_completed') ?: 'Terminés' ?></button>
    <button class="ud-chip" data-filter="pending"><?= __('dashboard.models.filter_pending') ?: 'En cours' ?></button>
  </div>
</div>

<?php if (!empty($modelStats)): ?>
  <!-- Comparison Radar Chart -->
  <?php
    $compareModels = [];
    foreach ($modelStats as $ms) {
      if ($ms['latest_score'] !== null && !empty($ms['chart_values'])) {
        $compareModels[] = [
          'name' => $ms['display_name'] ?: $ms['name'],
          'labels' => $ms['chart_labels'],
          'values' => $ms['chart_values'],
          'color' => $ms['color'],
        ];
      }
    }
  ?>
  <?php if (count($compareModels) >= 2): ?>
  <div class="ud-compare-card">
    <div class="ud-compare-header">
      <h3><i class="fas fa-chart-radar" style="color:var(--vx-primary);margin-right:0.5rem;"></i><?= __('dashboard.models.comparison') ?: 'Comparaison des modèles' ?></h3>
      <span style="font-size:0.68rem;color:var(--vx-text-muted);"><?= __('dashboard.models.comparison_sub') ?: 'Scores par domaine' ?></span>
    </div>
    <div class="ud-compare-container">
      <canvas id="udCompareChart"></canvas>
    </div>
  </div>
  <?php endif; ?>

  <!-- Model Cards -->
  <div class="ud-models-grid" id="udModelsGrid">
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
      $isCompleted = $hasScore;
    ?>
      <div class="ud-model-card" data-status="<?= $isCompleted ? 'completed' : 'pending' ?>">
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

        <div class="ud-model-card-body">
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

          <div class="ud-model-detail">
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
            <?php elseif ($hasScore): ?>
              <div style="font-size:0.65rem;color:var(--vx-text-muted);text-align:center;padding:0.5rem;">
                <?= __('dashboard.models.no_domains') ?: 'Aucun score par domaine disponible' ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

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
    <a href="/assessment/start" class="nova-btn nova-btn-primary">
      <i class="fas fa-plus" style="margin-right:0.25rem;"></i><?= __('dashboard.assessments.start') ?>
    </a>
  </div>
<?php endif; ?>

<!-- Filter Script -->
<script>
(function() {
  var chips = document.querySelectorAll('.ud-chip[data-filter]');
  var cards = document.querySelectorAll('#udModelsGrid .ud-model-card');
  chips.forEach(function(chip) {
    chip.addEventListener('click', function() {
      chips.forEach(function(c) { c.classList.remove('is-active'); });
      chip.classList.add('is-active');
      var filter = chip.getAttribute('data-filter');
      cards.forEach(function(card) {
        var status = card.getAttribute('data-status');
        if (filter === 'all' || status === filter) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
})();
</script>

<!-- Comparison Radar Chart Script -->
<?php if (!empty($compareModels) && count($compareModels) >= 2): ?>
<script>
(function() {
  var canvas = document.getElementById('udCompareChart');
  if (!canvas) return;
  var models = <?= json_encode($compareModels, JSON_UNESCAPED_UNICODE) ?>;
  var labels = models[0].labels;
  var datasets = models.map(function(m, i) {
    return {
      label: m.name,
      data: m.values,
      backgroundColor: m.color + '15',
      borderColor: m.color,
      borderWidth: 2,
      pointBackgroundColor: m.color,
      pointBorderColor: '#fff',
      pointBorderWidth: 1.5,
      pointRadius: 3,
      pointHoverRadius: 5,
    };
  });
  new Chart(canvas, {
    type: 'radar',
    data: { labels: labels, datasets: datasets },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { family: 'Manrope', size: 10, weight: '600' }, color: '#475569', padding: 12, boxWidth: 12, boxHeight: 12 }
        },
        tooltip: {
          backgroundColor: 'rgba(23,33,43,0.95)', titleColor: '#fff', bodyColor: '#fff',
          titleFont: { family: 'Manrope', size: 11, weight: '700' },
          bodyFont: { family: 'Manrope', size: 11, weight: '600' },
          padding: 10, cornerRadius: 8,
          callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.parsed.r + '%'; } }
        }
      },
      scales: {
        r: {
          min: 0, max: 100, beginAtZero: true,
          angleLines: { color: 'rgba(80,64,42,0.08)' },
          grid: { color: 'rgba(80,64,42,0.06)' },
          pointLabels: { font: { family: 'Manrope', size: 8, weight: '600' }, color: '#7d8794' },
          ticks: { display: false, stepSize: 25 }
        }
      },
      animation: { duration: 1000, easing: 'easeOutQuart' }
    }
  });
})();
</script>
<?php endif; ?>

<!-- Per-Model Radar Charts -->
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
  var modelStats = <?= json_encode($radarModels, JSON_UNESCAPED_UNICODE) ?>;
  modelStats.forEach(function(ms) {
    var canvas = document.getElementById('modelRadar_' + ms.id);
    if (!canvas) return;
    var color = ms.color || '#1a56db';
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
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(23,33,43,0.95)', titleColor: '#fff', bodyColor: '#fff',
            titleFont: { family: 'Manrope', size: 10, weight: '700' },
            bodyFont: { family: 'Manrope', size: 11, weight: '600' },
            padding: 8, cornerRadius: 6, displayColors: false,
            callbacks: { label: function(ctx) { return ctx.parsed.r + '%'; } }
          }
        },
        scales: {
          r: {
            min: 0, max: 100, beginAtZero: true,
            angleLines: { color: 'rgba(80,64,42,0.08)' },
            grid: { color: 'rgba(80,64,42,0.06)' },
            pointLabels: { font: { family: 'Manrope', size: 7, weight: '600' }, color: '#7d8794' },
            ticks: { display: false, stepSize: 25 }
          }
        },
        animation: { duration: 900, easing: 'easeOutQuart' }
      }
    });
  });
})();
</script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
