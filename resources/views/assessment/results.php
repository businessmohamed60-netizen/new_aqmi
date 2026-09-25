<?php
$lang = $_SESSION['lang'] ?? 'fr';
$isRtl = $lang === 'ar';
$title = __('results.report_title');
$globalScore = $analysis['global_score'];
$level = $analysis['maturity_level'] ?? null;
$levelName = $level ? ($isRtl && !empty($level['name_ar']) ? $level['name_ar'] : (!empty($level['name_fr']) ? $level['name_fr'] : ($level['name'] ?? ''))) : '';
$levelColor = $level['color'] ?? '#1F6FEB';
$lead = \App\Models\Lead::findByAssessment($assessment['id']);
$currentUser = $assessment['user_id'] ? \App\Models\User::find($assessment['user_id']) : null;
$companyName = $lead['company'] ?? 'Entreprise';
$sector = $lead['sector'] ?? 'Industrie Automobile';
$fullName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
if (empty(trim($fullName)) && $currentUser) {
    $fullName = ($currentUser['firstname'] ?? '') . ' ' . ($currentUser['lastname'] ?? '');
}
$email = $lead['email'] ?? ($currentUser['email'] ?? '');
$phone = $lead['phone'] ?? '';
$country = $lead['country'] ?? '';

$reportStatus = $report['status'] ?? null;
$hasNoRequest = $reportStatus === null;
$isCertRequested = $reportStatus === 'certification_requested';
$isUnderReview = $reportStatus === 'under_review';
$isApproved = $reportStatus === 'approved';
$isCertified = $reportStatus === 'certified';
$isRejected = $reportStatus === 'rejected';

$domainScores = $analysis['domain_scores'];
$domainCount = count($domainScores);

// Compute optimal column count for balanced grid rows.
// Tries column counts 2–6 and picks the one where the last row
// is closest to full (minimises orphan cards). Tie → more columns.
$optimalCols = 2;
$bestScore = PHP_INT_MAX;
for ($c = 2; $c <= min(6, max(2, $domainCount)); $c++) {
    $rem = $domainCount % $c;
    $score = $rem === 0 ? 0 : ($c - $rem);
    if ($score < $bestScore || ($score === $bestScore && $c > $optimalCols)) {
        $bestScore = $score;
        $optimalCols = $c;
    }
}
if ($domainCount <= 1) $optimalCols = 1;
$recommendations = $recommendations ?? [];
$strengths = $analysis['strengths'] ?? [];
$weaknesses = $analysis['weaknesses'] ?? [];
$benchmark = $analysis['benchmark'] ?? ['global_avg' => 0, 'domain_avgs' => []];
$hasBenchmark = $benchmark['global_avg'] > 0;

$maturityInfo = [
    ['max' => 30, 'label' => __('maturity.beginner'), 'color' => '#6c757d', 'desc' => __('maturity.beginner_desc')],
    ['max' => 50, 'label' => __('maturity.developing'), 'color' => '#fd7e14', 'desc' => __('maturity.developing_desc')],
    ['max' => 70, 'label' => __('maturity.structured'), 'color' => '#1a56db', 'desc' => __('maturity.structured_desc')],
    ['max' => 85, 'label' => __('maturity.performing'), 'color' => '#059669', 'desc' => __('maturity.performing_desc')],
    ['max' => 100, 'label' => __('maturity.excellence'), 'color' => '#d97706', 'desc' => __('maturity.excellence_desc')],
];
$currentLevel = null;
foreach ($maturityInfo as $m) { if ($globalScore <= $m['max']) { $currentLevel = $m; break; } }
if (!$currentLevel) $currentLevel = $maturityInfo[count($maturityInfo)-1];

if ($level) {
    $currentLevel = [
        'max' => (float)($level['max_percent'] ?? 100),
        'label' => $isRtl && !empty($level['name_ar']) ? $level['name_ar'] : (!empty($level['name_fr']) ? $level['name_fr'] : $level['name']),
        'color' => $level['color'] ?? $currentLevel['color'],
        'desc' => $currentLevel['desc'],
    ];
}

$projected = [
    ['label' => __('results.today'), 'score' => $globalScore],
    ['label' => __('results.months_6'), 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.35))],
    ['label' => __('results.months_12'), 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.60))],
    ['label' => __('results.months_24'), 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.82))],
];

$shortTerm = []; $mediumTerm = []; $longTerm = [];
foreach ($recommendations as $r) {
    $p = $r['priority'] ?? 'medium';
    if ($p === 'critical') $shortTerm[] = $r;
    elseif ($p === 'high') $mediumTerm[] = $r;
    else $longTerm[] = $r;
}
if (empty($shortTerm)) $shortTerm = array_slice($recommendations, 0, 3);
if (empty($mediumTerm) && count($recommendations) > 3) $mediumTerm = array_slice($recommendations, 3, 3);
if (empty($longTerm) && count($recommendations) > 6) $longTerm = array_slice($recommendations, 6, 3);

$defaultShort = [__('results.default_short_1'), __('results.default_short_2'), __('results.default_short_3')];
$defaultMedium = [__('results.default_medium_1'), __('results.default_medium_2'), __('results.default_medium_3')];
$defaultLong = [__('results.default_long_1'), __('results.default_long_2'), __('results.default_long_3')];

// Calculate radius for SVG circle
$circumference = 2 * pi() * 70; // r=70

ob_start();
?>
<div class="aqmi-results">
  <div class="aqmi-results-inner">
    <!-- Header with Score -->
    <div class="aqmi-results-header">
      <div class="aqmi-results-score-ring" id="scoreRing">
        <svg viewBox="0 0 160 160">
          <circle class="bg" cx="80" cy="80" r="70"/>
          <circle class="fg" id="scoreCircle" cx="80" cy="80" r="70"
            stroke-dasharray="<?= $circumference ?>" stroke-dashoffset="<?= $circumference ?>"
            style="stroke:<?= $currentLevel['color'] ?>;"/>
        </svg>
        <div class="aqmi-results-score-value">
          <div class="num" id="scoreValue" style="color:<?= $currentLevel['color'] ?>;">0%</div>
          <div class="lbl"><?= __('results.global_score') ?></div>
        </div>
      </div>
      <h1 class="aqmi-results-title"><?= __('results.report_title') ?></h1>
      <p class="aqmi-results-subtitle"><?= e($companyName) ?> <?= __('results.report_subtitle') ?></p>
      <div class="aqmi-results-level" style="background:<?= $currentLevel['color'] ?>0d;border:1px solid <?= $currentLevel['color'] ?>20;color:<?= $currentLevel['color'] ?>;">
        <i class="fas fa-crown" style="font-size:0.55rem;"></i> <?= __('results.level_label') ?> <?= e($currentLevel['label']) ?>
      </div>
    </div>

    <!-- Cover Info -->
    <div class="aqmi-results-card" style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:1rem;">
        <div style="width:44px;height:44px;background:rgba(255,255,255,0.03);border:1px solid var(--aqmi-border);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1rem;color:var(--aqmi-text-secondary);">
          <i class="fas fa-industry"></i>
        </div>
        <div>
          <div style="font-size:1.1rem;font-weight:700;color:var(--aqmi-text);"><?= e($companyName) ?></div>
          <div style="font-size:0.72rem;color:var(--aqmi-text-tertiary);display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:0.15rem;">
            <span><i class="fas fa-tag"></i> <?= e($sector) ?></span>
            <span><i class="fas fa-user"></i> <?= e($fullName) ?></span>
            <?php if ($email): ?><span><i class="fas fa-envelope"></i> <?= e($email) ?></span><?php endif; ?>
          </div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.02);border:1px solid var(--aqmi-border);border-radius:100px;padding:0.35rem 0.85rem 0.35rem 0.65rem;">
        <div style="width:6px;height:6px;border-radius:50%;background:var(--aqmi-success);box-shadow:0 0 8px rgba(46,196,182,0.5);"></div>
        <span style="font-size:0.65rem;font-weight:500;color:var(--aqmi-text-tertiary);"><?= __('results.report_date') ?> <?= date('d/m/Y') ?></span>
      </div>
    </div>

    <!-- Domain Scores -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-accent);"></div>
        <div>
          <h3><?= __('results.section_domains') ?></h3>
          <p><?= __('results.section_domains_desc') ?></p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-domain-grid" style="grid-template-columns:repeat(<?= $optimalCols ?>, 1fr);--cols:<?= $optimalCols ?>;">
          <?php foreach ($domainScores as $i => $ds):
            $pct = round($ds['percent_score']);
            $c = $pct >= 70 ? '#2EC4B6' : ($pct >= 50 ? '#9d8fd1' : '#E5484D');
          ?>
            <div class="aqmi-domain-item">
              <div class="aqmi-domain-score" style="color:<?= $c ?>;"><?= $pct ?>%</div>
              <div class="aqmi-domain-name"><?= e($ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name']) ?></div>
              <div class="aqmi-domain-bar">
                <div class="aqmi-domain-bar-fill" style="width:<?= $pct ?>%;background:<?= $c ?>;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="aqmi-split-grid" style="margin-top:1rem;">
        <div class="aqmi-results-card">
          <h4 style="font-size:0.75rem;font-weight:700;color:var(--aqmi-text);margin:0 0 0.75rem;display:flex;align-items:center;gap:0.4rem;">
            <i class="fas fa-chart-area" style="color:var(--aqmi-accent);"></i> <?= __('results.radar_view') ?>
          </h4>
          <div style="position:relative;height:280px;">
            <canvas id="domainRadarChart"></canvas>
          </div>
        </div>
        <div class="aqmi-results-card">
          <h4 style="font-size:0.75rem;font-weight:700;color:var(--aqmi-text);margin:0 0 0.75rem;display:flex;align-items:center;gap:0.4rem;">
            <i class="fas fa-chart-column" style="color:var(--aqmi-accent);"></i> <?= __('results.bar_compare') ?>
          </h4>
          <div style="position:relative;height:280px;">
            <canvas id="domainBarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Benchmark -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-info);"></div>
        <div>
          <h3><?= __('results.section_benchmark') ?></h3>
          <p><?= __('results.section_benchmark_desc') ?></p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-split-grid">
          <div>
            <h4 style="font-size:0.75rem;font-weight:700;color:var(--aqmi-text);margin:0 0 0.75rem;display:flex;align-items:center;gap:0.4rem;">
              <i class="fas fa-chart-simple" style="color:var(--aqmi-accent);"></i> <?= __('results.my_scores') ?>
            </h4>
            <?php foreach ($domainScores as $ds):
              $pct = round($ds['percent_score']);
              $c = $pct >= 70 ? '#2EC4B6' : ($pct >= 50 ? '#9d8fd1' : '#E5484D');
            ?>
              <div style="margin-bottom:0.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.65rem;margin-bottom:0.2rem;">
                  <span style="color:var(--aqmi-text-secondary);font-weight:500;"><?= e($ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name']) ?></span>
                  <span style="font-weight:700;color:<?= $c ?>;"><?= $pct ?>%</span>
                </div>
                <div style="height:4px;background:rgba(255,255,255,0.04);border-radius:3px;overflow:hidden;">
                  <div class="aqmi-bmk-fill" style="height:100%;width:<?= $pct ?>%;background:<?= $c ?>;border-radius:3px;transition:width 1.2s ease-out;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div>
            <h4 style="font-size:0.75rem;font-weight:700;color:var(--aqmi-text);margin:0 0 0.75rem;display:flex;align-items:center;gap:0.4rem;">
              <i class="fas fa-building-columns" style="color:var(--aqmi-info);"></i> <?= __('results.market_benchmark') ?>
            </h4>
            <?php if ($hasBenchmark):
              foreach ($domainScores as $ds):
                $db = $benchmark['domain_avgs'][$ds['domain_id']] ?? 0;
                $diff = round($ds['percent_score'] - $db);
            ?>
              <div style="margin-bottom:0.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.65rem;margin-bottom:0.2rem;">
                  <span style="color:var(--aqmi-text-secondary);font-weight:500;"><?= e($ds['domain_label'] ?? $ds['domain_name_fr'] ?? $ds['domain_name']) ?></span>
                  <span style="font-weight:700;color:<?= $diff >= 0 ? '#2EC4B6' : '#E5484D' ?>;">
                    <?= round($db) ?>% <?php if ($diff != 0): ?><small>(<?= $diff >= 0 ? '+' : '' ?><?= $diff ?>)</small><?php endif; ?>
                  </span>
                </div>
                <div style="height:4px;background:rgba(255,255,255,0.04);border-radius:3px;overflow:hidden;">
                  <div style="height:100%;width:<?= $db ?>%;background:var(--aqmi-accent);opacity:0.4;border-radius:3px;transition:width 1.2s ease-out;"></div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php else: ?>
              <p style="color:var(--aqmi-text-tertiary);font-size:0.78rem;text-align:center;padding:1.5rem 0;"><?= __('results.benchmark_insufficient') ?></p>
            <?php endif; ?>
          </div>
        </div>
        <!-- Leader Comparison -->
        <div style="margin-top:1rem;background:rgba(255,255,255,0.02);border:1px solid var(--aqmi-border);border-radius:var(--aqmi-radius-md);padding:0.85rem 1rem;display:flex;align-items:center;gap:0.75rem;">
          <div style="text-align:center;flex-shrink:0;">
            <div style="font-size:0.55rem;color:var(--aqmi-text-tertiary);text-transform:uppercase;font-weight:600;"><?= __('results.leader') ?></div>
            <div style="font-size:1.1rem;font-weight:800;color:var(--aqmi-accent);">88%</div>
          </div>
          <div style="flex:1;height:24px;background:rgba(255,255,255,0.04);border-radius:12px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;height:100%;width:88%;background:var(--aqmi-accent);opacity:0.15;border-radius:12px;"></div>
            <div class="aqmi-bmk-fill" style="position:absolute;top:0;left:0;height:100%;width:<?= $globalScore ?>%;background:linear-gradient(90deg,<?= $currentLevel['color'] ?>,<?= $currentLevel['color'] ?>bb);border-radius:12px;z-index:2;transition:width 1.2s ease-out;"></div>
            <div style="position:absolute;top:50%;transform:translateY(-50%);font-size:0.55rem;font-weight:700;color:#fff;z-index:3;padding:0 0.5rem;white-space:nowrap;left:<?= min(85, max(2, $globalScore - 6)) ?>%;"><?= e(mb_substr($companyName, 0, 14)) ?> (<?= $globalScore ?>%)</div>
          </div>
          <div style="text-align:center;flex-shrink:0;">
            <div style="font-size:0.55rem;color:var(--aqmi-text-tertiary);text-transform:uppercase;font-weight:600;"><?= __('results.your_score') ?></div>
            <div style="font-size:1.1rem;font-weight:800;color:<?= $currentLevel['color'] ?>;"><?= $globalScore ?>%</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Projection -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-success);"></div>
        <div>
          <h3><?= __('results.section_projection') ?></h3>
          <p><?= __('results.section_projection_desc') ?></p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div style="position:relative;height:220px;">
          <canvas id="projectionChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Strengths & Opportunities -->
    <div class="aqmi-split-grid" style="margin-bottom:1.5rem;">
      <div class="aqmi-results-card" style="margin-bottom:0;">
        <div class="aqmi-results-section-title" style="margin-bottom:0.75rem;">
          <div class="marker" style="background:var(--aqmi-success);"></div>
          <div>
            <h3 style="font-size:0.85rem;"><?= __('results.section_strengths') ?></h3>
          </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
          <?php $advs = array_map(fn($s) => $s['domain_label'] ?? $s['domain_name_fr'] ?? $s['domain_name'], $strengths);
          if (count($advs) < 3) $advs = array_merge($advs, [__('results.default_strength_1'), __('results.default_strength_2')]);
          foreach ($advs as $a): ?>
            <span style="display:inline-flex;align-items:center;gap:0.3rem;background:rgba(46,196,182,0.06);border:1px solid rgba(46,196,182,0.12);color:var(--aqmi-success);padding:0.35rem 0.75rem;border-radius:var(--aqmi-radius-sm);font-size:0.68rem;font-weight:600;">
              <i class="fas fa-check-circle" style="font-size:0.55rem;"></i> <?= e($a) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="aqmi-results-card" style="margin-bottom:0;">
        <div class="aqmi-results-section-title" style="margin-bottom:0.75rem;">
          <div class="marker" style="background:var(--aqmi-warning);"></div>
          <div>
            <h3 style="font-size:0.85rem;"><?= __('results.section_weaknesses') ?></h3>
          </div>
        </div>
        <?php
        $opps = [];
        foreach ($weaknesses as $w) {
          $opps[] = ['domain' => $w['domain_label'] ?? $w['domain_name_fr'] ?? $w['domain_name'], 'potential' => min(100, round($w['percent_score'] + 40)), 'invest' => $w['percent_score'] < 30 ? '500K' : ($w['percent_score'] < 50 ? '350K' : '180K')];
        }
        if (count($opps) < 3) $opps = array_merge($opps, [['domain'=>__('results.default_weakness_1'),'potential'=>85,'invest'=>'250K'],['domain'=>__('results.default_weakness_2'),'potential'=>80,'invest'=>'120K']]);
        foreach (array_slice($opps, 0, 4) as $o): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--aqmi-border);">
            <div>
              <div style="font-size:0.72rem;font-weight:600;color:var(--aqmi-text);"><?= e($o['domain']) ?></div>
              <div style="font-size:0.6rem;color:var(--aqmi-text-tertiary);"><?= __('results.invest_label') ?> <?= e($o['invest']) ?> K</div>
            </div>
            <div style="font-size:0.78rem;font-weight:700;color:var(--aqmi-success);">+<?= $o['potential'] ?>%</div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Roadmap -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-warning);"></div>
        <div>
          <h3><?= __('results.section_roadmap') ?></h3>
          <p><?= __('results.section_roadmap_desc') ?></p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-roadmap">
          <div class="aqmi-rm-phase aqmi-rm-short">
            <div class="aqmi-rm-tag"><?= __('results.phase') ?> 1</div>
            <div class="aqmi-rm-title"><?= __('results.short_term') ?></div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> <?= __('results.short_term_dur') ?></div>
            <ul class="aqmi-rm-list">
              <?php foreach (($shortTerm ?: $defaultShort) as $k => $r): if ($k >= 3) break; ?>
                <li><?= e(is_string($r) ? $r : ($r['text'] ?? '')) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="aqmi-rm-phase aqmi-rm-mid">
            <div class="aqmi-rm-tag"><?= __('results.phase') ?> 2</div>
            <div class="aqmi-rm-title"><?= __('results.medium_term') ?></div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> <?= __('results.medium_term_dur') ?></div>
            <ul class="aqmi-rm-list">
              <?php foreach (($mediumTerm ?: $defaultMedium) as $k => $r): if ($k >= 3) break; ?>
                <li><?= e(is_string($r) ? $r : ($r['text'] ?? '')) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="aqmi-rm-phase aqmi-rm-long">
            <div class="aqmi-rm-tag"><?= __('results.phase') ?> 3</div>
            <div class="aqmi-rm-title"><?= __('results.long_term') ?></div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> <?= __('results.long_term_dur') ?></div>
            <ul class="aqmi-rm-list">
              <?php foreach (($longTerm ?: $defaultLong) as $k => $r): if ($k >= 3) break; ?>
                <li><?= e(is_string($r) ? $r : ($r['text'] ?? '')) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-danger);"></div>
        <div>
          <h3><?= __('results.section_recommendations') ?></h3>
          <p><?= __('results.section_recommendations_desc') ?></p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <?php if (!empty($recommendations)): ?>
          <?php foreach ($recommendations as $r):
            $p = $r['priority'] ?? 'medium';
            $cls = $p === 'critical' ? 'r0' : ($p === 'high' ? 'r1' : ($p === 'medium' ? 'r2' : 'r3'));
            $ico = $p === 'critical' ? 'fa-circle-exclamation' : ($p === 'high' ? 'fa-bolt' : ($p === 'medium' ? 'fa-arrow-right' : 'fa-check'));
            $lbl = $p === 'critical' ? __('results.priority_critical') : ($p === 'high' ? __('results.priority_high') : ($p === 'medium' ? __('results.priority_medium') : __('results.priority_low')));
            $bg = $p === 'critical' ? '#E5484D' : ($p === 'high' ? '#9d8fd1' : ($p === 'medium' ? '#1F6FEB' : 'var(--aqmi-text-tertiary)'));
          ?>
            <div class="aqmi-rec-item">
              <div class="aqmi-rec-badge" style="background:<?= $bg ?>;"><i class="fas <?= $ico ?>"></i></div>
              <div class="aqmi-rec-text"><?= e($r['text']) ?></div>
              <div class="aqmi-rec-tag" style="color:<?= $bg ?>;"><?= $lbl ?></div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;padding:1.25rem;">
            <i class="fas fa-check-circle" style="font-size:1.25rem;color:var(--aqmi-success);display:block;margin-bottom:0.4rem;"></i>
            <p style="color:var(--aqmi-text-tertiary);font-size:0.78rem;margin:0;"><?= __('results.no_recommendations') ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Actions -->
    <div class="aqmi-results-actions">
      <a href="/assessment/<?= $assessment['id'] ?>/report" class="btn btn-primary" style="background:linear-gradient(135deg,var(--aqmi-accent),var(--aqmi-warning));border:none;box-shadow:0 4px 20px var(--aqmi-accent-glow);">
        <i class="fas fa-file-contract"></i> <?= __('results.full_report') ?>
      </a>
      <?php if ($hasNoRequest): ?>
        <a href="/assessment/<?= $assessment['id'] ?>/lead" class="btn btn-primary" style="background:linear-gradient(135deg,var(--aqmi-accent),var(--aqmi-warning));border:none;box-shadow:0 4px 20px var(--aqmi-accent-glow);">
          <i class="fas fa-certificate"></i> <?= __('results.request_certified') ?>
        </a>
      <?php elseif ($isCertRequested || $isUnderReview): ?>
        <span class="btn" style="background:rgba(157,143,209,0.08);border-color:var(--aqmi-warning);color:var(--aqmi-warning);cursor:default;">
          <i class="fas fa-hourglass-half"></i> <?= __('results.pending_validation') ?>
        </span>
      <?php elseif ($isApproved): ?>
        <span class="btn" style="background:rgba(31,111,235,0.08);border-color:#1F6FEB;color:#1F6FEB;cursor:default;">
          <i class="fas fa-cog fa-spin"></i> <?= __('results.approved_generating') ?>
        </span>
      <?php elseif ($isRejected): ?>
        <form method="GET" action="/assessment/<?= $assessment['id'] ?>/request-report" style="display:inline;">
          <button type="submit" class="btn" style="background:var(--aqmi-warning);border-color:var(--aqmi-warning);color:#fff;">
            <i class="fas fa-redo"></i> <?= __('results.resend_request') ?>
          </button>
        </form>
      <?php elseif ($isCertified): ?>
        <a href="/report/<?= $assessment['id'] ?>/download" class="btn btn-primary">
          <i class="fas fa-file-circle-check"></i> <?= __('results.download_certified') ?>
        </a>
      <?php endif; ?>

      <button onclick="window.print()" class="btn" style="border-color:var(--aqmi-accent);color:var(--aqmi-accent);">
        <i class="fas fa-print"></i> <?= __('results.print_summary') ?>
      </button>
      <a href="/assessment/start" class="btn">
        <i class="fas fa-redo"></i> <?= __('results.new_assessment') ?>
      </a>
      <a href="/" class="btn">
        <i class="fas fa-home"></i> <?= __('results.home') ?>
      </a>
      <a href="/logout" class="btn" style="border-color:var(--aqmi-danger);color:var(--aqmi-danger);">
        <i class="fas fa-sign-out-alt"></i> <?= __('results.logout') ?>
      </a>
    </div>

    <div style="text-align:center;padding:1.5rem 0 0;margin-top:2rem;border-top:1px solid var(--aqmi-border);">
      <small style="font-size:0.62rem;color:var(--aqmi-text-tertiary);">
        <?= __('results.confidential_doc') ?> <?= date('d/m/Y') ?>
      </small>
    </div>
  </div>
</div>

<?php
$projLabelsJson = json_encode(array_map(fn($p) => $p['label'], $projected));
$projScoresJson = json_encode(array_map(fn($p) => $p['score'], $projected));

$domainLabelsJson = json_encode(array_map(fn($d) => $d['domain_label'] ?? $d['domain_name_fr'] ?? $d['domain_name'], $domainScores));
$domainScoresJson = json_encode(array_map(fn($d) => round($d['percent_score']), $domainScores));
$domainBenchmarkJson = json_encode(array_map(
    fn($d) => $benchmark['domain_avgs'][$d['domain_id']] ?? null,
    $domainScores
));

$extraStyles = '<link rel="stylesheet" href="/css/aqmi-results-print.css">';

$extraScripts = <<<SCRIPT
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
(function() {
  var finalScore = {$globalScore};
  var scoreColor = '{$currentLevel['color']}';
  var circumference = {$circumference};

  // Animate score counter
  var scoreObj = { val: 0 };
  gsap.to(scoreObj, {
    val: finalScore,
    duration: 2.5,
    ease: 'power3.out',
    onUpdate: function() {
      var v = Math.round(scoreObj.val);
      document.getElementById('scoreValue').textContent = v + '%';
      var offset = circumference - (v / 100) * circumference;
      document.getElementById('scoreCircle').style.strokeDashoffset = offset;
    }
  });

  // Animate benchmark bars
  setTimeout(function() {
    document.querySelectorAll('.aqmi-bmk-fill').forEach(function(el) {
      var w = el.style.width;
      el.style.width = '0%';
      setTimeout(function() { el.style.width = w; }, 100);
    });
  }, 500);

  // Projection chart
  var p = document.getElementById('projectionChart');
  if (p) {
    new Chart(p, {
      type: 'line',
      data: {
        labels: {$projLabelsJson},
        datasets: [{
          label: '<?= __('results.projected_score') ?>',
          data: {$projScoresJson},
          borderColor: '{$currentLevel['color']}',
          backgroundColor: function(ctx) {
            var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
            g.addColorStop(0, '{$currentLevel['color']}18');
            g.addColorStop(1, '{$currentLevel['color']}02');
            return g;
          },
          fill: true, tension: 0.45,
          pointBackgroundColor: ['#9d8fd1', '{$currentLevel['color']}', '#2EC4B6', '#2EC4B6'],
          pointRadius: [8, 6, 6, 8],
          pointHoverRadius: [12, 10, 10, 12],
          borderWidth: 3
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(ctx) { return 'Score: ' + ctx.raw + '%'; } } } },
        scales: {
          y: { min: 0, max: 100, ticks: { stepSize: 20, callback: function(v) { return v + '%'; }, color: 'var(--aqmi-text-tertiary)' }, grid: { color: 'rgba(255,255,255,0.04)' } },
          x: { ticks: { color: 'var(--aqmi-text-tertiary)' }, grid: { display: false } }
        }
      }
    });
  }
  // Radar chart — vue d'ensemble par domaine
  var radarEl = document.getElementById('domainRadarChart');
  if (radarEl) {
    new Chart(radarEl, {
      type: 'radar',
      data: {
        labels: {$domainLabelsJson},
        datasets: [{
          label: '<?= __('results.score_label') ?> (%)',
          data: {$domainScoresJson},
          borderColor: '{$currentLevel['color']}',
          backgroundColor: '{$currentLevel['color']}22',
          pointBackgroundColor: '{$currentLevel['color']}',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          r: {
            min: 0, max: 100, ticks: { stepSize: 25, display: false },
            grid: { color: 'rgba(255,255,255,0.06)' },
            angleLines: { color: 'rgba(255,255,255,0.06)' },
            pointLabels: { color: 'var(--aqmi-text-tertiary)', font: { size: 10 } }
          }
        }
      }
    });
  }

  // Bar chart — mes scores vs benchmark marché
  var barEl = document.getElementById('domainBarChart');
  if (barEl) {
    var barDatasets = [{
      label: '<?= __('results.my_scores') ?>',
      data: {$domainScoresJson},
      backgroundColor: '{$currentLevel['color']}',
      borderRadius: 4
    }];
    var benchmarkData = {$domainBenchmarkJson};
    if (benchmarkData.some(function(v) { return v !== null; })) {
      barDatasets.push({
        label: '<?= __('results.market_benchmark') ?>',
        data: benchmarkData,
        backgroundColor: 'rgba(255,255,255,0.15)',
        borderRadius: 4
      });
    }
    new Chart(barEl, {
      type: 'bar',
      data: { labels: {$domainLabelsJson}, datasets: barDatasets },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: barDatasets.length > 1, labels: { color: 'var(--aqmi-text-tertiary)', font: { size: 10 } } } },
        scales: {
          y: { min: 0, max: 100, ticks: { color: 'var(--aqmi-text-tertiary)', callback: function(v) { return v + '%'; } }, grid: { color: 'rgba(255,255,255,0.04)' } },
          x: { ticks: { color: 'var(--aqmi-text-tertiary)', font: { size: 9 } }, grid: { display: false } }
        }
      }
    });
  }

  // Le redimensionnement d'un <canvas> Chart.js est asynchrone
  // (requestAnimationFrame) : selon le navigateur, l'impression peut se
  // déclencher avant que le graphique ait fini de se redessiner. Comme le
  // CSS d'impression ne modifie plus la taille des graphiques (on garde
  // exactement les mêmes dimensions qu'à l'écran), un simple redessin
  // immédiat (sans animation) suffit : pas besoin de remplacer le canvas
  // par une image, ce qui évitera aussi tout souci d'icône "image cassée"
  // si le navigateur bloque les data-URI en impression.
  function prepareChartsForPrint() {
    if (!window.Chart || !Chart.instances) return;
    Object.values(Chart.instances).forEach(function(c) {
      try {
        c.resize();
        c.update('none');
      } catch (e) {
        // On ignore silencieusement : au pire le graphique garde son
        // rendu actuel, jamais d'icône cassée.
      }
    });
  }

  window.addEventListener('beforeprint', prepareChartsForPrint);
  if (window.matchMedia) {
    window.matchMedia('print').addEventListener('change', function(e) {
      if (e.matches) prepareChartsForPrint();
    });
  }
})();
</script>
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>