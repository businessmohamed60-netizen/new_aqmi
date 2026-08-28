<?php
/**
 * AQMI Final Report — comprehensive professional report view
 *
 * @var array $reportData  Full payload from AqmiReportService::build()
 * @var int   $assessmentId
 */
$r = $reportData;
$assessment    = $r['assessment'];
$lead          = $r['lead'];
$user          = $r['user'];
$globalScore   = $r['global_score'];
$maturity      = $r['maturity_level'];
$domainScores  = $r['domain_scores'];
$strengths     = $r['strengths'];
$weaknesses    = $r['weaknesses'];
$gaps          = $r['gaps'];
$priorities    = $r['priorities'];
$recommendations = $r['recommendations'];
$actionPlan    = $r['action_plan'];
$execSummary   = $r['executive_summary'];
$benchmark     = $r['benchmark'];
$scoreLevels   = $r['score_levels'];
$reportDate    = $r['report_date'];
$reportRef     = $r['report_ref'];
$evalModel     = $r['evaluation_model'];

$levelName  = $maturity['name_fr'] ?? $maturity['name'] ?? 'Non défini';
$levelColor = $maturity['color'] ?? '#1F6FEB';

$companyName = $lead['company'] ?? 'Entreprise';
$sector      = $lead['sector'] ?? 'Non spécifié';
$country     = $lead['country'] ?? 'Non spécifié';
$fullName    = trim(($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? ''));
if (empty($fullName) && $user) {
    $fullName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
}
$email      = $lead['email'] ?? ($user['email'] ?? '');
$phone      = $lead['phone'] ?? '';
$companySize = $lead['company_size'] ?? '';
$certifications = $lead['certifications'] ?? '';

$circumference = 2 * pi() * 70;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Final AQMI — <?= e($companyName) ?></title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/aqmi-final-report.css">
</head>
<body>
<div class="aqmi-final-report">

    <!-- Toolbar -->
    <div class="aqmi-fr-toolbar">
        <div class="aqmi-fr-toolbar-brand">
            <span class="brand-badge">AQMI</span>
            <span class="brand-sub">Final Report</span>
        </div>
        <div class="aqmi-fr-toolbar-actions">
            <button class="aqmi-fr-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <a href="/assessment/<?= $assessmentId ?>/results" class="aqmi-fr-btn">
                <i class="fas fa-arrow-left"></i> Retour aux résultats
            </a>
            <a href="/" class="aqmi-fr-btn">
                <i class="fas fa-home"></i> Accueil
            </a>
        </div>
    </div>

    <div class="aqmi-fr-container">

        <!-- ============================== COVER ============================== -->
        <div class="aqmi-fr-cover aqmi-fr-animate">
            <div class="aqmi-fr-cover-content">
                <div class="aqmi-fr-cover-header">
                    <div class="aqmi-fr-cover-brand">
                        <div class="brand-name">AQMI</div>
                        <div class="brand-tagline">Automotive Quality Maturity Index</div>
                    </div>
                    <div class="aqmi-fr-cover-ref">
                        <div class="ref-label">Référence</div>
                        <div class="ref-number"><?= e($reportRef) ?></div>
                        <div class="ref-date"><?= e($reportDate) ?></div>
                    </div>
                </div>

                <h1 class="aqmi-fr-cover-title">Rapport d'Évaluation de Maturité Qualité</h1>
                <p class="aqmi-fr-cover-subtitle">
                    Évaluation complète selon le référentiel
                    <?= $evalModel ? e($evalModel['name_fr'] ?: $evalModel['name']) : 'AQMI Standard' ?>
                </p>

                <div class="aqmi-fr-cover-grid">
                    <!-- Company Info -->
                    <div class="aqmi-fr-cover-company">
                        <div class="label">Dossier Entreprise</div>
                        <div class="company-name"><?= e($companyName) ?></div>
                        <div class="company-meta">
                            <span class="meta-label">Secteur</span>
                            <span class="meta-value"><?= e($sector) ?></span>
                            <span class="meta-label">Pays</span>
                            <span class="meta-value"><?= e($country) ?></span>
                            <span class="meta-label">Évalué par</span>
                            <span class="meta-value"><?= e($fullName) ?></span>
                            <?php if ($email): ?>
                            <span class="meta-label">Email</span>
                            <span class="meta-value"><?= e($email) ?></span>
                            <?php endif; ?>
                            <?php if ($phone): ?>
                            <span class="meta-label">Téléphone</span>
                            <span class="meta-value"><?= e($phone) ?></span>
                            <?php endif; ?>
                            <?php if ($companySize): ?>
                            <span class="meta-label">Taille</span>
                            <span class="meta-value"><?= e($companySize) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="aqmi-fr-cover-score">
                        <div class="label">Score AQMI Global</div>
                        <div class="aqmi-fr-gauge">
                            <svg viewBox="0 0 160 160">
                                <?php
                                $gaugeColor = $levelColor;
                                $gaugePct = max(0, min(100, $globalScore));
                                $gaugeOffset = $circumference * (1 - $gaugePct / 100);
                                // Ticks
                                for ($t = 0; $t < 100; $t += 10):
                                    $angle = (2 * pi() * $t / 100) - (pi() / 2);
                                    $x1 = 80 + 62 * cos($angle); $y1 = 80 + 62 * sin($angle);
                                    $x2 = 80 + 70 * cos($angle); $y2 = 80 + 70 * sin($angle);
                                ?>
                                <line x1="<?= round($x1,1) ?>" y1="<?= round($y1,1) ?>" x2="<?= round($x2,1) ?>" y2="<?= round($y2,1) ?>" stroke="rgba(255,255,255,0.08)" stroke-width="1.5"/>
                                <?php endfor; ?>
                                <circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="10"/>
                                <circle cx="80" cy="80" r="70" fill="none" stroke="<?= $gaugeColor ?>" stroke-width="10"
                                    stroke-dasharray="<?= round($circumference,2) ?>" stroke-dashoffset="<?= round($gaugeOffset,2) ?>"
                                    stroke-linecap="round" transform="rotate(-90 80 80)"/>
                            </svg>
                            <div class="aqmi-fr-gauge-value">
                                <div class="num" style="color:<?= $gaugeColor ?>;" id="gaugeScore"><?= round($globalScore) ?>%</div>
                                <div class="lbl">Maturité</div>
                            </div>
                        </div>
                        <div class="aqmi-fr-level-badge" style="background:<?= $levelColor ?>1a;border:1px solid <?= $levelColor ?>40;color:<?= $levelColor ?>;">
                            <i class="fas fa-crown" style="font-size:0.6rem;"></i>
                            Niveau <?= e($levelName) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================== EXECUTIVE SUMMARY ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(31,111,235,0.15);color:#1f6feb;">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Synthèse Exécutive</div>
                    <div class="aqmi-fr-section-subtitle">Vue d'ensemble des résultats de l'évaluation</div>
                </div>
            </div>
            <div class="aqmi-fr-exec-summary">
                <p><?= $execSummary['text'] ?></p>
                <div class="aqmi-fr-exec-stats">
                    <div class="aqmi-fr-exec-stat">
                        <div class="stat-value" style="color:<?= $levelColor ?>;"><?= round($globalScore) ?>%</div>
                        <div class="stat-label">Score Global</div>
                    </div>
                    <div class="aqmi-fr-exec-stat">
                        <div class="stat-value" style="color:#2EC4B6;"><?= $execSummary['strong_count'] ?></div>
                        <div class="stat-label">Domaines Solides</div>
                    </div>
                    <div class="aqmi-fr-exec-stat">
                        <div class="stat-value" style="color:#E5484D;"><?= $execSummary['weak_count'] ?></div>
                        <div class="stat-label">Domaines Faibles</div>
                    </div>
                    <div class="aqmi-fr-exec-stat">
                        <div class="stat-value" style="color:#9d8fd1;"><?= $execSummary['domain_count'] ?></div>
                        <div class="stat-label">Domaines Évalués</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================== DOMAIN SCORES + RADAR ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(46,196,182,0.15);color:#2EC4B6;">
                    <i class="fas fa-chart-radar"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Résultats par Domaine</div>
                    <div class="aqmi-fr-section-subtitle">Scores par pilier et profil de maturité</div>
                </div>
            </div>
            <div class="aqmi-fr-split">
                <div class="aqmi-fr-card">
                    <div class="aqmi-fr-chart-container">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
                <div class="aqmi-fr-card">
                    <div class="aqmi-fr-chart-container">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
            <div style="height:0.75rem;"></div>
            <div class="aqmi-fr-card">
                <div class="aqmi-fr-domain-grid">
                    <?php foreach ($domainScores as $ds):
                        $pct = round($ds['percent_score']);
                        $c = $pct >= 70 ? '#2EC4B6' : ($pct >= 50 ? '#9d8fd1' : '#E5484D');
                    ?>
                    <div class="aqmi-fr-domain-item">
                        <div class="aqmi-fr-domain-score" style="color:<?= $c ?>;"><?= $pct ?>%</div>
                        <div class="aqmi-fr-domain-name"><?= e($ds['domain_name_fr'] ?: $ds['domain_name']) ?></div>
                        <div class="aqmi-fr-domain-bar">
                            <div class="aqmi-fr-domain-bar-fill" style="width:<?= $pct ?>%;background:<?= $c ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Score Levels Legend -->
            <?php if (!empty($scoreLevels)): ?>
            <div class="aqmi-fr-level-legend">
                <?php foreach ($scoreLevels as $lvl): ?>
                <span class="aqmi-fr-level-chip">
                    <span class="dot" style="background:<?= $lvl['color'] ?>;"></span>
                    <?= e($lvl['name_fr'] ?: $lvl['name']) ?>
                    (<?= round($lvl['min_percent']) ?>–<?= round($lvl['max_percent']) ?>%)
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ============================== STRENGTHS & GAPS ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(46,196,182,0.15);color:#2EC4B6;">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Points Forts & Écarts</div>
                    <div class="aqmi-fr-section-subtitle">Domaines de maîtrise et axes d'amélioration</div>
                </div>
            </div>
            <div class="aqmi-fr-split">
                <!-- Strengths -->
                <div class="aqmi-fr-card aqmi-fr-strength-card">
                    <div class="aqmi-fr-card-title" style="color:#2EC4B6;">
                        <i class="fas fa-circle-check"></i> Points Forts
                    </div>
                    <?php foreach ($strengths as $s):
                        $pct = round($s['percent_score']);
                        $c = $pct >= 70 ? '#2EC4B6' : '#9d8fd1';
                    ?>
                    <div class="aqmi-fr-list-item">
                        <span><?= e($s['domain_name_fr'] ?: $s['domain_name']) ?></span>
                        <span class="item-score" style="color:<?= $c ?>;"><?= $pct ?>%</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($strengths)): ?>
                    <div class="aqmi-fr-list-item" style="color:rgba(255,255,255,0.3);">Aucun point fort identifié</div>
                    <?php endif; ?>
                </div>
                <!-- Weaknesses -->
                <div class="aqmi-fr-card aqmi-fr-weakness-card">
                    <div class="aqmi-fr-card-title" style="color:#E5484D;">
                        <i class="fas fa-triangle-exclamation"></i> Axes d'Amélioration
                    </div>
                    <?php foreach ($weaknesses as $w):
                        $pct = round($w['percent_score']);
                        $c = $pct >= 50 ? '#9d8fd1' : '#E5484D';
                    ?>
                    <div class="aqmi-fr-list-item">
                        <span><?= e($w['domain_name_fr'] ?: $w['domain_name']) ?></span>
                        <span class="item-score" style="color:<?= $c ?>;"><?= $pct ?>%</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($weaknesses)): ?>
                    <div class="aqmi-fr-list-item" style="color:rgba(255,255,255,0.3);">Aucun axe d'amélioration critique</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================== GAPS DETAIL ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(229,72,77,0.15);color:#E5484D;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Analyse des Écarts</div>
                    <div class="aqmi-fr-section-subtitle">Distance par rapport au niveau d'excellence (100%)</div>
                </div>
            </div>
            <div class="aqmi-fr-card">
                <?php foreach ($gaps as $g):
                    $sevColor = $g['severity'] === 'critical' ? '#E5484D' : ($g['severity'] === 'high' ? '#9d8fd1' : ($g['severity'] === 'medium' ? '#1f6feb' : '#2EC4B6'));
                    $sevLabel = $g['severity'] === 'critical' ? 'Critique' : ($g['severity'] === 'high' ? 'Élevé' : ($g['severity'] === 'medium' ? 'Moyen' : 'Faible'));
                    $gapPct = $g['score'];
                ?>
                <div class="aqmi-fr-gap-item">
                    <div class="aqmi-fr-gap-domain"><?= e($g['domain_name_fr'] ?? $g['domain_name']) ?></div>
                    <div class="aqmi-fr-gap-bar">
                        <div class="aqmi-fr-gap-bar-fill" style="width:<?= $gapPct ?>%;background:<?= $sevColor ?>;"></div>
                    </div>
                    <div class="aqmi-fr-gap-score"><?= round($g['score']) ?>%</div>
                    <div class="aqmi-fr-gap-value" style="color:<?= $sevColor ?>;">-<?= round($g['gap']) ?>%</div>
                    <span class="aqmi-fr-gap-severity" style="background:<?= $sevColor ?>15;color:<?= $sevColor ?>;"><?= $sevLabel ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ============================== PRIORITIES ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(157,143,209,0.15);color:#9d8fd1;">
                    <i class="fas fa-flag"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Priorités d'Amélioration</div>
                    <div class="aqmi-fr-section-subtitle">Domaines nécessitant une action immédiate</div>
                </div>
            </div>
            <div class="aqmi-fr-priority-grid">
                <?php foreach ($priorities as $p):
                    $priColor = $p['priority'] === 'critical' ? '#E5484D' : ($p['priority'] === 'high' ? '#9d8fd1' : '#1f6feb');
                    $priLabel = $p['priority'] === 'critical' ? 'Critique' : ($p['priority'] === 'high' ? 'Haute' : 'Moyenne');
                ?>
                <div class="aqmi-fr-priority-item" style="border-left-color:<?= $priColor ?>;">
                    <div class="aqmi-fr-priority-domain"><?= e($p['domain_name_fr'] ?? $p['domain_name']) ?></div>
                    <div class="aqmi-fr-priority-meta">
                        <span class="aqmi-fr-priority-score" style="color:<?= $priColor ?>;"><?= round($p['score']) ?>%</span>
                        <span>Écart: <?= round($p['gap']) ?>%</span>
                    </div>
                    <div style="margin-top:0.4rem;">
                        <span class="aqmi-fr-priority-tag" style="background:<?= $priColor ?>15;color:<?= $priColor ?>;"><?= $priLabel ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($priorities)): ?>
                <div style="color:rgba(255,255,255,0.3);font-size:0.78rem;padding:1rem;">Aucune priorité critique identifiée.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ============================== RECOMMENDATIONS ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(31,111,235,0.15);color:#1f6feb;">
                    <i class="fas fa-list-check"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Recommandations</div>
                    <div class="aqmi-fr-section-subtitle">Actions stratégiques par niveau de priorité</div>
                </div>
            </div>
            <div class="aqmi-fr-card">
                <?php if (!empty($recommendations)): ?>
                    <?php foreach ($recommendations as $rec):
                        $p = $rec['priority'] ?? 'medium';
                        $icon = $p === 'critical' ? 'fa-circle-exclamation' : ($p === 'high' ? 'fa-bolt' : ($p === 'medium' ? 'fa-arrow-right' : 'fa-check'));
                        $color = $p === 'critical' ? '#E5484D' : ($p === 'high' ? '#9d8fd1' : ($p === 'medium' ? '#1f6feb' : 'rgba(255,255,255,0.4)'));
                        $label = $p === 'critical' ? 'Critique' : ($p === 'high' ? 'Haute' : ($p === 'medium' ? 'Moyenne' : 'Basse'));
                    ?>
                    <div class="aqmi-fr-reco-item">
                        <div class="aqmi-fr-reco-badge" style="background:<?= $color ?>15;color:<?= $color ?>;">
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="aqmi-fr-reco-text"><?= e($rec['text'] ?? '') ?></div>
                        <span class="aqmi-fr-reco-tag" style="background:<?= $color ?>15;color:<?= $color ?>;"><?= $label ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center;padding:1.5rem;color:rgba(255,255,255,0.3);font-size:0.78rem;">
                        <i class="fas fa-check-circle" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;color:#2EC4B6;"></i>
                        Aucune recommandation générée. Votre progression est sur la bonne voie.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ============================== ACTION PLAN ============================== -->
        <div class="aqmi-fr-section aqmi-fr-animate">
            <div class="aqmi-fr-section-header">
                <div class="aqmi-fr-section-icon" style="background:rgba(46,196,182,0.15);color:#2EC4B6;">
                    <i class="fas fa-route"></i>
                </div>
                <div>
                    <div class="aqmi-fr-section-title">Plan d'Action</div>
                    <div class="aqmi-fr-section-subtitle">Feuille de route en trois phases temporelles</div>
                </div>
            </div>
            <div class="aqmi-fr-action-grid">
                <div class="aqmi-fr-action-phase" style="border-top-color:#E5484D;">
                    <div class="aqmi-fr-action-phase-tag">Phase 1</div>
                    <div class="aqmi-fr-action-phase-title">Court Terme</div>
                    <div class="aqmi-fr-action-phase-duration"><i class="far fa-clock"></i> 0–3 mois</div>
                    <ul class="aqmi-fr-action-list">
                        <?php foreach ($actionPlan['short_term']['items'] as $item): ?>
                        <li><?= e($item['text']) ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($actionPlan['short_term']['items'])): ?>
                        <li style="color:rgba(255,255,255,0.25);">Aucune action critique immédiate</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="aqmi-fr-action-phase" style="border-top-color:#9d8fd1;">
                    <div class="aqmi-fr-action-phase-tag">Phase 2</div>
                    <div class="aqmi-fr-action-phase-title">Moyen Terme</div>
                    <div class="aqmi-fr-action-phase-duration"><i class="far fa-clock"></i> 3–9 mois</div>
                    <ul class="aqmi-fr-action-list">
                        <?php foreach ($actionPlan['medium_term']['items'] as $item): ?>
                        <li><?= e($item['text']) ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($actionPlan['medium_term']['items'])): ?>
                        <li style="color:rgba(255,255,255,0.25);">Aucune action à moyen terme</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="aqmi-fr-action-phase" style="border-top-color:#2EC4B6;">
                    <div class="aqmi-fr-action-phase-tag">Phase 3</div>
                    <div class="aqmi-fr-action-phase-title">Long Terme</div>
                    <div class="aqmi-fr-action-phase-duration"><i class="far fa-clock"></i> 9–24 mois</div>
                    <ul class="aqmi-fr-action-list">
                        <?php foreach ($actionPlan['long_term']['items'] as $item): ?>
                        <li><?= e($item['text']) ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($actionPlan['long_term']['items'])): ?>
                        <li style="color:rgba(255,255,255,0.25);">Aucune action à long terme</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <!-- Summary table -->
            <table class="aqmi-fr-action-summary">
                <thead>
                    <tr>
                        <th>Priorité</th>
                        <th class="center">Nombre</th>
                        <th>Délai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="color:#E5484D;font-weight:bold;">●</span> Critique</td>
                        <td class="center"><?= $actionPlan['counts']['critical'] ?></td>
                        <td>0–3 mois</td>
                    </tr>
                    <tr>
                        <td><span style="color:#9d8fd1;font-weight:bold;">●</span> Haute</td>
                        <td class="center"><?= $actionPlan['counts']['high'] ?></td>
                        <td>3–6 mois</td>
                    </tr>
                    <tr>
                        <td><span style="color:#1f6feb;font-weight:bold;">●</span> Moyenne</td>
                        <td class="center"><?= $actionPlan['counts']['medium'] ?></td>
                        <td>6–12 mois</td>
                    </tr>
                    <tr>
                        <td><span style="color:rgba(255,255,255,0.4);font-weight:bold;">●</span> Basse</td>
                        <td class="center"><?= $actionPlan['counts']['low'] ?></td>
                        <td>12+ mois</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td class="center"><?= $actionPlan['counts']['total'] ?></td>
                        <td>—</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="aqmi-fr-footer">
            <div class="aqmi-fr-footer-brand">AQMI — Automotive Quality Maturity Index</div>
            <div class="aqmi-fr-footer-copy">
                Document confidentiel généré le <?= e($reportDate) ?> — AQMI &copy; <?= date('Y') ?> by NOVAQYS — Destiné à <?= e($companyName) ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
(function() {
    var finalScore = <?= $globalScore ?>;
    var scoreColor = '<?= $levelColor ?>';

    // Domain labels and scores
    var domainLabels = <?= json_encode(array_map(fn($d) => $d['domain_name_fr'] ?: $d['domain_name'], $domainScores)) ?>;
    var domainScoresArr = <?= json_encode(array_map(fn($d) => round($d['percent_score']), $domainScores)) ?>;
    var benchmarkData = <?= json_encode(array_map(fn($d) => $benchmark['domain_avgs'][$d['domain_id']] ?? null, $domainScores)) ?>;

    // Radar chart
    var radarEl = document.getElementById('radarChart');
    if (radarEl && window.Chart) {
        new Chart(radarEl, {
            type: 'radar',
            data: {
                labels: domainLabels,
                datasets: [{
                    label: 'Score (%)',
                    data: domainScoresArr,
                    borderColor: scoreColor,
                    backgroundColor: scoreColor + '22',
                    pointBackgroundColor: scoreColor,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0, max: 100,
                        ticks: { stepSize: 25, display: false },
                        grid: { color: 'rgba(255,255,255,0.06)' },
                        angleLines: { color: 'rgba(255,255,255,0.06)' },
                        pointLabels: { color: 'rgba(255,255,255,0.5)', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Bar chart — scores vs benchmark
    var barEl = document.getElementById('barChart');
    if (barEl && window.Chart) {
        var datasets = [{
            label: 'Mes scores',
            data: domainScoresArr,
            backgroundColor: scoreColor,
            borderRadius: 4
        }];
        if (benchmarkData.some(function(v) { return v !== null; })) {
            datasets.push({
                label: 'Benchmark marché',
                data: benchmarkData,
                backgroundColor: 'rgba(255,255,255,0.12)',
                borderRadius: 4
            });
        }
        new Chart(barEl, {
            type: 'bar',
            data: { labels: domainLabels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: datasets.length > 1,
                        labels: { color: 'rgba(255,255,255,0.4)', font: { size: 10 } }
                    }
                },
                scales: {
                    y: {
                        min: 0, max: 100,
                        ticks: { color: 'rgba(255,255,255,0.3)', callback: function(v) { return v + '%'; } },
                        grid: { color: 'rgba(255,255,255,0.04)' }
                    },
                    x: {
                        ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 9 }, maxRotation: 45, minRotation: 0 },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Animate gauge score
    var gaugeEl = document.getElementById('gaugeScore');
    if (gaugeEl && window.gsap) {
        var scoreObj = { val: 0 };
        gsap.to(scoreObj, {
            val: finalScore,
            duration: 2,
            ease: 'power3.out',
            onUpdate: function() {
                gaugeEl.textContent = Math.round(scoreObj.val) + '%';
            }
        });
    }

    // Animate domain bars
    setTimeout(function() {
        document.querySelectorAll('.aqmi-fr-domain-bar-fill').forEach(function(el) {
            var w = el.style.width;
            el.style.width = '0%';
            setTimeout(function() { el.style.width = w; }, 100);
        });
    }, 300);
})();
</script>
</body>
</html>
