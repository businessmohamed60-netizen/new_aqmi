<?php
$title = 'Rapport AQMI Premium';
$globalScore = $analysis['global_score'];
$level = $analysis['maturity_level'] ?? null;
$levelName = $level['name_fr'] ?? $level['name'] ?? 'Non défini';
$levelColor = $level['color'] ?? '#3b82f6';
$lead = \App\Models\Lead::findByAssessment($assessment['id']);
$companyName = $lead['company'] ?? 'Entreprise';
$sector = $lead['sector'] ?? 'Industrie Automobile';
$fullName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
$email = $lead['email'] ?? '';
$phone = $lead['phone'] ?? '';
$country = $lead['country'] ?? '';

$reportStatus = $report['status'] ?? null;
$isReportValidated = $reportStatus === 'validated';
$isReportPending = $reportStatus === 'pending';
$isReportRejected = $reportStatus === 'rejected';
$noReportRequested = $report === null;

$domainScores = $analysis['domain_scores'];
$domainCount = count($domainScores);
$recommendations = $recommendations ?? [];
$strengths = $analysis['strengths'] ?? [];
$weaknesses = $analysis['weaknesses'] ?? [];
$benchmark = $analysis['benchmark'] ?? ['global_avg' => 0, 'domain_avgs' => []];
$hasBenchmark = $benchmark['global_avg'] > 0;

$maturityInfo = [
    ['max' => 20, 'label' => 'Initial', 'color' => '#ef4444', 'desc' => 'Processus non structurés'],
    ['max' => 40, 'label' => 'En Développement', 'color' => '#f59e0b', 'desc' => 'Processus en construction'],
    ['max' => 60, 'label' => 'Structuré', 'color' => '#3b82f6', 'desc' => 'Processus définis et documentés'],
    ['max' => 80, 'label' => 'Avancé', 'color' => '#06b6d4', 'desc' => 'Processus maîtrisés et mesurés'],
    ['max' => 100, 'label' => 'Optimisé', 'color' => '#22c55e', 'desc' => 'Excellence opérationnelle'],
];
$currentLevel = null;
foreach ($maturityInfo as $m) { if ($globalScore <= $m['max']) { $currentLevel = $m; break; } }
if (!$currentLevel) $currentLevel = $maturityInfo[count($maturityInfo)-1];

$projected = [
    ['label' => "Aujourd'hui", 'score' => $globalScore],
    ['label' => '6 Mois', 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.35))],
    ['label' => '12 Mois', 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.60))],
    ['label' => '24 Mois', 'score' => min(100, round($globalScore + (100 - $globalScore) * 0.82))],
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

$defaultShort = ['Établir un système de management qualité (QMS)', 'Former les équipes aux exigences qualité', 'Mettre en place des indicateurs de performance (KPI)'];
$defaultMedium = ['Développer un système d\'audit interne structuré', 'Mettre en place un processus CAPA', 'Déployer la gestion des risques fournisseurs'];
$defaultLong = ['Viser la certification IATF 16949', 'Développer une culture d\'amélioration continue', 'Automatiser les processus qualité'];

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
          <div class="lbl">Score Global</div>
        </div>
      </div>
      <h1 class="aqmi-results-title">Rapport de Maturité Qualité</h1>
      <p class="aqmi-results-subtitle"><?= e($companyName) ?> — Évaluation complète de votre système qualité selon les exigences IATF 16949</p>
      <div class="aqmi-results-level" style="background:<?= $currentLevel['color'] ?>0d;border:1px solid <?= $currentLevel['color'] ?>20;color:<?= $currentLevel['color'] ?>;">
        <i class="fas fa-crown" style="font-size:0.55rem;"></i> Niveau <?= e($currentLevel['label']) ?>
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
        <div style="width:6px;height:6px;border-radius:50%;background:var(--aqmi-success);box-shadow:0 0 8px rgba(34,197,94,0.5);"></div>
        <span style="font-size:0.65rem;font-weight:500;color:var(--aqmi-text-tertiary);">Rapport du <?= date('d/m/Y') ?></span>
      </div>
    </div>

    <!-- Domain Scores -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-accent);"></div>
        <div>
          <h3>Scores par Domaine</h3>
          <p>Répartition détaillée par pilier de performance qualité</p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-domain-grid">
          <?php foreach ($domainScores as $i => $ds):
            $pct = round($ds['percent_score']);
            $c = $pct >= 70 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
          ?>
            <div class="aqmi-domain-item">
              <div class="aqmi-domain-score" style="color:<?= $c ?>;"><?= $pct ?>%</div>
              <div class="aqmi-domain-name"><?= e($ds['domain_name_fr'] ?: $ds['domain_name']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Benchmark -->
    <div class="aqmi-results-section">
      <div class="aqmi-results-section-title">
        <div class="marker" style="background:var(--aqmi-info);"></div>
        <div>
          <h3>Comparaison &amp; Benchmark</h3>
          <p>Positionnement par rapport à la moyenne du marché</p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-split-grid">
          <div>
            <h4 style="font-size:0.75rem;font-weight:700;color:var(--aqmi-text);margin:0 0 0.75rem;display:flex;align-items:center;gap:0.4rem;">
              <i class="fas fa-chart-simple" style="color:var(--aqmi-accent);"></i> Mes scores
            </h4>
            <?php foreach ($domainScores as $ds):
              $pct = round($ds['percent_score']);
              $c = $pct >= 70 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            ?>
              <div style="margin-bottom:0.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.65rem;margin-bottom:0.2rem;">
                  <span style="color:var(--aqmi-text-secondary);font-weight:500;"><?= e($ds['domain_name_fr'] ?: $ds['domain_name']) ?></span>
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
              <i class="fas fa-building-columns" style="color:var(--aqmi-info);"></i> Benchmark marché
            </h4>
            <?php if ($hasBenchmark):
              foreach ($domainScores as $ds):
                $db = $benchmark['domain_avgs'][$ds['domain_id']] ?? 0;
                $diff = round($ds['percent_score'] - $db);
            ?>
              <div style="margin-bottom:0.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.65rem;margin-bottom:0.2rem;">
                  <span style="color:var(--aqmi-text-secondary);font-weight:500;"><?= e($ds['domain_name_fr'] ?: $ds['domain_name']) ?></span>
                  <span style="font-weight:700;color:<?= $diff >= 0 ? '#22c55e' : '#ef4444' ?>;">
                    <?= round($db) ?>% <?php if ($diff != 0): ?><small>(<?= $diff >= 0 ? '+' : '' ?><?= $diff ?>)</small><?php endif; ?>
                  </span>
                </div>
                <div style="height:4px;background:rgba(255,255,255,0.04);border-radius:3px;overflow:hidden;">
                  <div style="height:100%;width:<?= $db ?>%;background:var(--aqmi-accent);opacity:0.4;border-radius:3px;transition:width 1.2s ease-out;"></div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php else: ?>
              <p style="color:var(--aqmi-text-tertiary);font-size:0.78rem;text-align:center;padding:1.5rem 0;">Données benchmark insuffisantes.</p>
            <?php endif; ?>
          </div>
        </div>
        <!-- Leader Comparison -->
        <div style="margin-top:1rem;background:rgba(255,255,255,0.02);border:1px solid var(--aqmi-border);border-radius:var(--aqmi-radius-md);padding:0.85rem 1rem;display:flex;align-items:center;gap:0.75rem;">
          <div style="text-align:center;flex-shrink:0;">
            <div style="font-size:0.55rem;color:var(--aqmi-text-tertiary);text-transform:uppercase;font-weight:600;">Leader</div>
            <div style="font-size:1.1rem;font-weight:800;color:var(--aqmi-accent);">88%</div>
          </div>
          <div style="flex:1;height:24px;background:rgba(255,255,255,0.04);border-radius:12px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;height:100%;width:88%;background:var(--aqmi-accent);opacity:0.15;border-radius:12px;"></div>
            <div class="aqmi-bmk-fill" style="position:absolute;top:0;left:0;height:100%;width:<?= $globalScore ?>%;background:linear-gradient(90deg,<?= $currentLevel['color'] ?>,<?= $currentLevel['color'] ?>bb);border-radius:12px;z-index:2;transition:width 1.2s ease-out;"></div>
            <div style="position:absolute;top:50%;transform:translateY(-50%);font-size:0.55rem;font-weight:700;color:#fff;z-index:3;padding:0 0.5rem;white-space:nowrap;left:<?= min(85, max(2, $globalScore - 6)) ?>%;"><?= e(mb_substr($companyName, 0, 14)) ?> (<?= $globalScore ?>%)</div>
          </div>
          <div style="text-align:center;flex-shrink:0;">
            <div style="font-size:0.55rem;color:var(--aqmi-text-tertiary);text-transform:uppercase;font-weight:600;">Votre score</div>
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
          <h3>Projection de Maturité</h3>
          <p>Trajectoire estimée selon la roadmap proposée</p>
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
            <h3 style="font-size:0.85rem;">Points Forts</h3>
          </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
          <?php $advs = array_map(fn($s) => $s['domain_name_fr'] ?: $s['domain_name'], $strengths);
          if (count($advs) < 3) $advs = array_merge($advs, ['Processus structurés', 'Conformité réglementaire']);
          foreach ($advs as $a): ?>
            <span style="display:inline-flex;align-items:center;gap:0.3rem;background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.12);color:var(--aqmi-success);padding:0.35rem 0.75rem;border-radius:var(--aqmi-radius-sm);font-size:0.68rem;font-weight:600;">
              <i class="fas fa-check-circle" style="font-size:0.55rem;"></i> <?= e($a) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="aqmi-results-card" style="margin-bottom:0;">
        <div class="aqmi-results-section-title" style="margin-bottom:0.75rem;">
          <div class="marker" style="background:var(--aqmi-warning);"></div>
          <div>
            <h3 style="font-size:0.85rem;">Axes de Progrès</h3>
          </div>
        </div>
        <?php
        $opps = [];
        foreach ($weaknesses as $w) {
          $opps[] = ['domain' => $w['domain_name_fr'] ?: $w['domain_name'], 'potential' => min(100, round($w['percent_score'] + 40)), 'invest' => $w['percent_score'] < 30 ? '500K' : ($w['percent_score'] < 50 ? '350K' : '180K')];
        }
        if (count($opps) < 3) $opps = array_merge($opps, [['domain'=>'Digitalisation','potential'=>85,'invest'=>'250K'],['domain'=>'Formation Continue','potential'=>80,'invest'=>'120K']]);
        foreach (array_slice($opps, 0, 4) as $o): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--aqmi-border);">
            <div>
              <div style="font-size:0.72rem;font-weight:600;color:var(--aqmi-text);"><?= e($o['domain']) ?></div>
              <div style="font-size:0.6rem;color:var(--aqmi-text-tertiary);">Invest. <?= e($o['invest']) ?> K</div>
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
          <h3>Roadmap d'Amélioration</h3>
          <p>Plan d'action progressif en trois phases</p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <div class="aqmi-roadmap">
          <div class="aqmi-rm-phase aqmi-rm-short">
            <div class="aqmi-rm-tag">Phase 1</div>
            <div class="aqmi-rm-title">Court Terme</div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> 0–3 mois</div>
            <ul class="aqmi-rm-list">
              <?php foreach (($shortTerm ?: $defaultShort) as $k => $r): if ($k >= 3) break; ?>
                <li><?= e(is_string($r) ? $r : ($r['text'] ?? '')) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="aqmi-rm-phase aqmi-rm-mid">
            <div class="aqmi-rm-tag">Phase 2</div>
            <div class="aqmi-rm-title">Moyen Terme</div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> 3–9 mois</div>
            <ul class="aqmi-rm-list">
              <?php foreach (($mediumTerm ?: $defaultMedium) as $k => $r): if ($k >= 3) break; ?>
                <li><?= e(is_string($r) ? $r : ($r['text'] ?? '')) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="aqmi-rm-phase aqmi-rm-long">
            <div class="aqmi-rm-tag">Phase 3</div>
            <div class="aqmi-rm-title">Long Terme</div>
            <div class="aqmi-rm-dur"><i class="far fa-clock"></i> 9–24 mois</div>
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
          <h3>Recommandations Prioritaires</h3>
          <p>Actions clés structurées par niveau de priorité</p>
        </div>
      </div>
      <div class="aqmi-results-card">
        <?php if (!empty($recommendations)): ?>
          <?php foreach ($recommendations as $r):
            $p = $r['priority'] ?? 'medium';
            $cls = $p === 'critical' ? 'r0' : ($p === 'high' ? 'r1' : ($p === 'medium' ? 'r2' : 'r3'));
            $ico = $p === 'critical' ? 'fa-circle-exclamation' : ($p === 'high' ? 'fa-bolt' : ($p === 'medium' ? 'fa-arrow-right' : 'fa-check'));
            $lbl = $p === 'critical' ? 'Critique' : ($p === 'high' ? 'Haute' : ($p === 'medium' ? 'Moyenne' : 'Basse'));
            $bg = $p === 'critical' ? '#ef4444' : ($p === 'high' ? '#f59e0b' : ($p === 'medium' ? '#3b82f6' : 'var(--aqmi-text-tertiary)'));
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
            <p style="color:var(--aqmi-text-tertiary);font-size:0.78rem;margin:0;">Aucune recommandation générée. Votre progression est sur la bonne voie.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Actions -->
    <div class="aqmi-results-actions">
      <?php if ($noReportRequested): ?>
        <a href="/assessment/<?= $assessment['id'] ?>/lead-form" class="btn btn-primary" style="background:linear-gradient(135deg,var(--aqmi-accent),var(--aqmi-purple));border:none;box-shadow:0 4px 20px var(--aqmi-accent-glow);">
          <i class="fas fa-file-export"></i> Demander un rapport certifié
        </a>
      <?php elseif ($isReportPending): ?>
        <span class="btn" style="pointer-events:none;opacity:0.75;">
          <i class="fas fa-clock"></i> Demande transmise — en attente de validation
        </span>
      <?php elseif ($isReportRejected): ?>
        <form method="GET" action="/assessment/<?= $assessment['id'] ?>/request-report" style="display:inline;">
          <button type="submit" class="btn" style="background:var(--aqmi-warning);border-color:var(--aqmi-warning);color:#fff;">
            <i class="fas fa-redo"></i> Renvoyer la demande
          </button>
        </form>
      <?php else: ?>
        <a href="/report/<?= $assessment['id'] ?>/download" class="btn btn-primary">
          <i class="fas fa-file-pdf"></i> Télécharger le rapport officiel
        </a>
      <?php endif; ?>
      <button onclick="window.print()" class="btn">
        <i class="fas fa-print"></i> Imprimer
      </button>
      <a href="/assessment/start" class="btn">
        <i class="fas fa-redo"></i> Nouvelle évaluation
      </a>
      <a href="/" class="btn">
        <i class="fas fa-home"></i> Accueil
      </a>
    </div>

    <div style="text-align:center;padding:1.5rem 0 0;margin-top:2rem;border-top:1px solid var(--aqmi-border);">
      <small style="font-size:0.62rem;color:var(--aqmi-text-tertiary);">
        AQMI &copy; <?= date('Y') ?> — Automotive Quality Maturity Index — Document confidentiel généré le <?= date('d/m/Y') ?>
      </small>
    </div>
  </div>
</div>

<?php
$projLabelsJson = json_encode(array_map(fn($p) => $p['label'], $projected));
$projScoresJson = json_encode(array_map(fn($p) => $p['score'], $projected));

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
          label: 'Score projeté',
          data: {$projScoresJson},
          borderColor: '{$currentLevel['color']}',
          backgroundColor: function(ctx) {
            var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
            g.addColorStop(0, '{$currentLevel['color']}18');
            g.addColorStop(1, '{$currentLevel['color']}02');
            return g;
          },
          fill: true, tension: 0.45,
          pointBackgroundColor: ['#f59e0b', '{$currentLevel['color']}', '#06b6d4', '#22c55e'],
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
})();
</script>
SCRIPT;

$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>