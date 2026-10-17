<?php
/**
 * Rapport consolidé — examen et certification (admin)
 * Variables attendues du contrôleur : $report, $user, $items, $domainBreakdown
 */
$title  = 'Rapport consolidé';
$report = is_array($report ?? null) ? $report : [];
$user   = is_array($user ?? null) ? $user : [];
$items  = is_array($items ?? null) ? $items : [];
$domainBreakdown = is_array($domainBreakdown ?? null) ? $domainBreakdown : [];

$methodLabels = [
    'weighted_average' => 'Moyenne pondérée',
    'domain_fusion'    => 'Fusion par domaine',
    'best_of'          => 'Meilleure performance',
    'latest_only'      => 'Dernière évaluation',
    'maturity_gap'     => 'Indice d\'écart de maturité',
    'consensus'        => 'Score de consensus',
];
$methodCode = (string)($report['calculation_method'] ?? 'weighted_average');
$methodLabel = $methodLabels[$methodCode] ?? ucfirst(str_replace('_', ' ', $methodCode));
ob_start();

/* ───────────── Aides d'affichage ───────────── */
$tone = function ($s) {
    if ($s >= 86) return '#d97706';
    if ($s >= 71) return '#059669';
    if ($s >= 51) return '#1a56db';
    if ($s >= 31) return '#fd7e14';
    return '#6c757d';
};
$statusMap = [
    'draft'                   => ['Brouillon', 'muted'],
    'certification_requested' => ['En attente', 'warn'],
    'under_review'            => ['En examen', 'info'],
    'approved'                => ['Approuvé', 'info'],
    'rejected'                => ['Rejeté', 'bad'],
    'certified'               => ['Certifié', 'ok'],
];
$status = (string)($report['status'] ?? '');
$sm     = $statusMap[$status] ?? [$status, 'muted'];
$fd = function ($d, $withTime = false) {
    if (empty($d)) return null;
    $t = strtotime((string)$d);
    return $t ? date($withTime ? 'd/m/Y à H:i' : 'd/m/Y', $t) : null;
};
$csrf = function_exists('csrf_field') ? csrf_field() : '';

$reportId = (int)($report['id'] ?? 0);
$score    = max(0, min(100, (float)($report['consolidated_score'] ?? 0)));
$scoreCol = $tone($score);
$circ     = 2 * M_PI * 52;
$offset   = $circ * (1 - $score / 100);

$fullName = trim((string)($user['firstname'] ?? '') . ' ' . (string)($user['lastname'] ?? ''));
$firstChar = function ($s) { $s = (string)$s; return function_exists('mb_substr') ? mb_strtoupper(mb_substr($s, 0, 1)) : strtoupper(substr($s, 0, 1)); };
$initials = ($firstChar($user['firstname'] ?? '') . $firstChar($user['lastname'] ?? '')) ?: '?';

$canReview = in_array($status, ['certification_requested', 'under_review', 'approved'], true);
$hasSaved  = !empty($report['aqmi_level_assigned']) || !empty($report['observations']) || !empty($report['action_plan']) || !empty($report['admin_comment']);

// Chronologie : une vraie séquence d'étapes datées
$steps = [
    ['Rapport créé',            $fd($report['created_at'] ?? null), null],
    ['Certification demandée',  $fd($report['certification_requested_at'] ?? null, true), null],
    ['Certifié',                $fd($report['certified_at'] ?? null, true), !empty($report['certified_at']) && !empty($report['validated_by']) ? 'Validé par ' . (string)$report['validated_by'] : null],
];
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  .cr-page {
    --cr-ink: var(--auto-text-primary, #0f172a);
    --cr-muted: var(--auto-text-muted, #64748b);
    --cr-line: var(--auto-border, #e2e8f0);
    --cr-card: var(--auto-bg-card, #fff);
    --cr-accent: var(--auto-cyan, #1F6FEB);
    --cr-accent-soft: var(--auto-cyan-dim, rgba(31,111,235,.09));
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; color: var(--cr-ink);
  }
  .cr-page *, .cr-page *::before, .cr-page *::after { box-sizing: border-box; }
  .cr-page a:focus-visible, .cr-page button:focus-visible, .cr-page input:focus-visible, .cr-page textarea:focus-visible { outline: 2px solid var(--cr-accent); outline-offset: 2px; }

  .cr-back { display: inline-flex; align-items: center; gap: .45rem; margin-bottom: 1rem; font-size: .8rem; font-weight: 700; color: var(--cr-muted); text-decoration: none; }
  .cr-back:hover { color: var(--cr-accent); }

  /* En-tête */
  .cr-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.4rem; }
  .cr-head h1 { margin: 0 0 .6rem; font-size: 1.6rem; font-weight: 800; letter-spacing: -.025em; line-height: 1.2; }
  .cr-who { display: flex; align-items: center; gap: .65rem; font-size: .84rem; }
  .cr-av { flex: 0 0 auto; width: 36px; height: 36px; border-radius: 11px; display: grid; place-items: center; font-size: .75rem; font-weight: 800; color: #fff; background: linear-gradient(135deg, var(--cr-accent), #4f46e5); }
  .cr-who b { display: block; font-weight: 700; }
  .cr-who small { color: var(--cr-muted); font-size: .74rem; }
  .cr-head-side { display: flex; flex-direction: column; align-items: flex-end; gap: .5rem; }
  .cr-ref { font-family: ui-monospace, 'SFMono-Regular', Menlo, monospace; font-size: .78rem; font-weight: 600; padding: .2rem .55rem; border-radius: 6px; background: rgba(100,116,139,.1); }

  .cr-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .28rem .75rem; border-radius: 999px; font-size: .76rem; font-weight: 700; white-space: nowrap; }
  .cr-pill i { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
  .cr-pill-warn  { background: rgba(245,158,11,.14); color: #b45309; }
  .cr-pill-info  { background: rgba(14,165,233,.13); color: #0369a1; }
  .cr-pill-ok    { background: rgba(16,185,129,.13); color: #047857; }
  .cr-pill-bad   { background: rgba(220,38,38,.12); color: #b91c1c; }
  .cr-pill-muted { background: rgba(100,116,139,.13); color: #475569; }

  /* Grille */
  .cr-grid { display: grid; grid-template-columns: 290px minmax(0, 1fr); gap: 16px; align-items: start; }
  .cr-side { display: grid; gap: 16px; position: sticky; top: 1rem; }
  .cr-main { display: grid; gap: 16px; min-width: 0; }

  .cr-card { background: var(--cr-card); border: 1px solid var(--cr-line); border-radius: 14px; overflow: hidden; }
  .cr-card-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: .95rem 1.25rem; border-bottom: 1px solid var(--cr-line); }
  .cr-card-head h2 { margin: 0; font-size: .95rem; font-weight: 800; display: flex; align-items: center; gap: .6rem; }
  .cr-card-head h2 i { display: inline-grid; place-items: center; width: 27px; height: 27px; border-radius: 8px; background: var(--cr-accent-soft); color: var(--cr-accent); font-size: .75rem; }
  .cr-card-body { padding: 1.15rem 1.25rem; }
  .cr-count { font-size: .72rem; font-weight: 700; color: var(--cr-muted); background: rgba(100,116,139,.1); padding: .2rem .65rem; border-radius: 999px; }

  /* Score : l'élément mis en avant */
  .cr-score-card { text-align: center; padding: 1.4rem 1.25rem 1.25rem; }
  .cr-ring { position: relative; width: 168px; height: 168px; margin: 0 auto .9rem; }
  .cr-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); display: block; }
  .cr-ring .bg { fill: none; stroke: rgba(100,116,139,.15); stroke-width: 9; }
  .cr-ring .fg { fill: none; stroke-width: 9; stroke-linecap: round; animation: crRing .9s cubic-bezier(.2,.7,.2,1) both; }
  @keyframes crRing { from { stroke-dashoffset: var(--cr-c); } }
  .cr-ring-num { position: absolute; inset: 0; display: grid; place-content: center; text-align: center; }
  .cr-ring-num b { font-size: 3rem; font-weight: 800; letter-spacing: -.04em; line-height: 1; }
  .cr-ring-num span { font-size: .78rem; font-weight: 600; color: var(--cr-muted); margin-top: .2rem; }
  .cr-level { font-size: 1rem; font-weight: 800; }
  .cr-level-sub { font-size: .76rem; color: var(--cr-muted); margin-top: .15rem; }
  .cr-assigned { margin-top: 1rem; padding: .7rem .85rem; border-radius: 10px; background: var(--cr-accent-soft); text-align: left; }
  .cr-assigned small { display: block; font-size: .72rem; color: var(--cr-muted); font-weight: 600; margin-bottom: .1rem; }
  .cr-assigned b { font-size: .86rem; font-weight: 800; color: var(--cr-accent); }

  /* Chronologie */
  .cr-tl { list-style: none; margin: 0; padding: 0; }
  .cr-tl li { position: relative; padding: 0 0 1.1rem 1.6rem; font-size: .82rem; }
  .cr-tl li:last-child { padding-bottom: 0; }
  .cr-tl li::before { content: ''; position: absolute; left: 4px; top: .35rem; bottom: -.35rem; width: 2px; background: var(--cr-line); }
  .cr-tl li:last-child::before { display: none; }
  .cr-tl li::after { content: ''; position: absolute; left: 0; top: .3rem; width: 10px; height: 10px; border-radius: 50%; background: var(--cr-card); border: 2px solid #cbd5e1; }
  .cr-tl li.is-done::after { background: var(--cr-accent); border-color: var(--cr-accent); }
  .cr-tl li.is-last-done::after { box-shadow: 0 0 0 4px var(--cr-accent-soft); }
  .cr-tl b { display: block; font-weight: 700; }
  .cr-tl span { color: var(--cr-muted); font-size: .76rem; }
  .cr-tl li:not(.is-done) b { color: var(--cr-muted); font-weight: 600; }

  /* Évaluations */
  .cr-wrap { overflow-x: auto; }
  .cr-table { width: 100%; border-collapse: collapse; min-width: 560px; }
  .cr-table th { padding: .65rem 1rem; text-align: left; font-size: .74rem; font-weight: 700; color: var(--cr-muted); background: rgba(100,116,139,.05); border-bottom: 1px solid var(--cr-line); white-space: nowrap; }
  .cr-table td { padding: .8rem 1rem; font-size: .82rem; border-bottom: 1px solid var(--cr-line); vertical-align: middle; }
  .cr-table tbody tr:last-child td { border-bottom: 0; }
  .cr-table tbody tr:hover { background: var(--cr-accent-soft); }
  .cr-score { display: flex; align-items: center; gap: .6rem; min-width: 120px; }
  .cr-score b { font-size: .88rem; font-weight: 800; min-width: 2.2ch; text-align: right; font-variant-numeric: tabular-nums; }
  .cr-score-bar { flex: 1; height: 6px; border-radius: 999px; background: rgba(100,116,139,.15); overflow: hidden; }
  .cr-score-bar i { display: block; height: 100%; border-radius: 999px; }
  .cr-muted { color: var(--cr-muted); }
  .cr-empty { padding: 2rem 1rem; text-align: center; color: var(--cr-muted); font-size: .82rem; }

  /* Formulaire */
  .cr-field { margin-bottom: 1rem; }
  .cr-field label { display: block; margin-bottom: .35rem; font-size: .78rem; font-weight: 700; }
  .cr-field label small { font-weight: 500; color: var(--cr-muted); margin-left: .35rem; }
  .cr-field input, .cr-field textarea { width: 100%; padding: .65rem .85rem; border-radius: 10px; border: 1px solid var(--vx-input-border, var(--cr-line)); background: var(--vx-input-bg, var(--cr-card)); color: var(--cr-ink); font: 500 .85rem/1.5 'Plus Jakarta Sans', sans-serif; resize: vertical; }
  .cr-field input:focus, .cr-field textarea:focus { border-color: var(--cr-accent); outline: none; box-shadow: 0 0 0 3px var(--cr-accent-soft); }
  .cr-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; }

  .cr-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .6rem 1.15rem; border-radius: 10px; font: 700 .8rem 'Plus Jakarta Sans', sans-serif; text-decoration: none; border: 1px solid transparent; cursor: pointer; white-space: nowrap; }
  .cr-btn-primary { background: var(--cr-accent); color: #fff; }
  .cr-btn-primary:hover { filter: brightness(1.08); color: #fff; }
  .cr-btn-ok { background: #059669; color: #fff; }
  .cr-btn-ok:hover { filter: brightness(1.08); }
  .cr-btn-ghost { background: transparent; color: var(--cr-ink); border-color: var(--cr-line); }
  .cr-btn-ghost:hover { border-color: var(--cr-accent); color: var(--cr-accent); }
  .cr-btn-info { background: rgba(14,165,233,.1); color: #0369a1; border-color: rgba(14,165,233,.3); }
  .cr-btn-info:hover { background: rgba(14,165,233,.18); }
  .cr-btn-bad { background: transparent; color: #b91c1c; border-color: rgba(220,38,38,.35); }
  .cr-btn-bad:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
  .cr-hint { margin: 0 0 .9rem; font-size: .78rem; color: var(--cr-muted); }
  .cr-decide form { margin: 0; }

  /* Analyse enregistrée */
  .cr-note + .cr-note { margin-top: 1.1rem; padding-top: 1.1rem; border-top: 1px solid var(--cr-line); }
  .cr-note h3 { margin: 0 0 .35rem; font-size: .8rem; font-weight: 800; display: flex; align-items: center; gap: .5rem; }
  .cr-note h3 em { font-style: normal; font-weight: 600; font-size: .7rem; color: var(--cr-muted); background: rgba(100,116,139,.12); padding: .05rem .5rem; border-radius: 999px; }
  .cr-note p { margin: 0; font-size: .86rem; line-height: 1.65; max-width: 75ch; }

  @media (max-width: 920px) {
    .cr-grid { grid-template-columns: 1fr; }
    .cr-side { position: static; }
    .cr-head-side { align-items: flex-start; }
  }
  @media (prefers-reduced-motion: reduce) { .cr-ring .fg { animation: none; } }
</style>

<div class="cr-page">

  <a href="/admin/consolidated" class="cr-back"><i class="fas fa-arrow-left"></i> Rapports consolidés</a>

  <header class="cr-head">
    <div>
      <h1><?= e((string)($report['title'] ?? 'Rapport consolidé')) ?></h1>
      <div class="cr-who">
        <span class="cr-av"><?= e($initials) ?></span>
        <span><b><?= e($fullName !== '' ? $fullName : '—') ?></b><small><?= e((string)($user['email'] ?? '')) ?></small></span>
      </div>
    </div>
    <div class="cr-head-side">
      <span class="cr-pill cr-pill-<?= e($sm[1]) ?>"><i></i><?= e($sm[0]) ?></span>
      <?php if (!empty($report['report_number'])): ?><span class="cr-ref"><?= e((string)$report['report_number']) ?></span><?php endif; ?>
    </div>
  </header>

  <div class="cr-grid">

    <!-- Colonne de gauche : score et chronologie -->
    <aside class="cr-side">
      <section class="cr-card cr-score-card" aria-label="Score consolidé">
        <div class="cr-ring">
          <svg viewBox="0 0 120 120" aria-hidden="true">
            <circle class="bg" cx="60" cy="60" r="52"/>
            <circle class="fg" cx="60" cy="60" r="52" stroke="<?= $scoreCol ?>" stroke-dasharray="<?= round($circ, 2) ?>" stroke-dashoffset="<?= round($offset, 2) ?>" style="--cr-c:<?= round($circ, 2) ?>;"/>
          </svg>
          <div class="cr-ring-num"><b style="color:<?= $scoreCol ?>;"><?= round($score) ?></b><span>sur 100</span></div>
        </div>
        <div class="cr-level"><?= e((string)($report['maturity_level'] ?? '—')) ?></div>
        <div class="cr-level-sub">Niveau de maturité calculé</div>
        <div style="margin-top:.85rem;padding:.6rem .8rem;border-radius:10px;background:rgba(100,116,139,.07);text-align:left;">
          <small style="display:block;font-size:.72rem;color:var(--cr-muted);font-weight:600;margin-bottom:.15rem;">Méthode de calcul</small>
          <b style="font-size:.82rem;font-weight:800;color:var(--cr-ink);"><?= e($methodLabel) ?></b>
        </div>
        <?php if (!empty($report['aqmi_level_assigned'])): ?>
          <div class="cr-assigned"><small>Niveau AQMI attribué</small><b><?= e((string)$report['aqmi_level_assigned']) ?></b></div>
        <?php endif; ?>
      </section>

      <section class="cr-card">
        <div class="cr-card-head"><h2><i class="fas fa-clock-rotate-left"></i> Chronologie</h2></div>
        <div class="cr-card-body">
          <?php
            $lastDone = -1;
            foreach ($steps as $i => $st) { if ($st[1] !== null) $lastDone = $i; }
          ?>
          <ol class="cr-tl">
            <?php foreach ($steps as $i => $st): $done = $st[1] !== null; ?>
              <li class="<?= $done ? 'is-done' : '' ?><?= $i === $lastDone ? ' is-last-done' : '' ?>">
                <b><?= e($st[0]) ?></b>
                <span><?= $done ? e($st[1]) : 'Pas encore' ?><?= $st[2] ? ' · ' . e($st[2]) : '' ?></span>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      </section>
    </aside>

    <!-- Colonne principale -->
    <div class="cr-main">

      <section class="cr-card">
        <div class="cr-card-head">
          <h2><i class="fas fa-list"></i> Évaluations incluses</h2>
          <span class="cr-count"><?= count($items) ?></span>
        </div>
        <?php if (empty($items)): ?>
          <div class="cr-empty">Aucune évaluation n'est rattachée à ce rapport.</div>
        <?php else: ?>
          <div class="cr-wrap">
            <table class="cr-table">
              <thead>
                <tr><th>Modèle</th><th>Entreprise</th><th>Score</th><th>Poids</th><th>Niveau</th><th>Date</th></tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item):
                  $s = max(0, min(100, (float)($item['score'] ?? 0))); $c = $tone($s);
                ?>
                  <tr>
                    <td><strong><?= e((string)($item['model_name'] ?? '—')) ?></strong></td>
                    <td><?= e((string)($item['company'] ?? '—')) ?></td>
                    <td><div class="cr-score"><b style="color:<?= $c ?>;"><?= round($s) ?></b><span class="cr-score-bar"><i style="width:<?= round($s) ?>%;background:<?= $c ?>;"></i></span></div></td>
                    <td><strong style="font-variant-numeric:tabular-nums;",><?= e(isset($item['weight']) ? rtrim(rtrim(number_format((float)$item['weight'], 2), '0'), '.') : '1') ?></strong></td>
                    <td><?= e((string)($item['maturity_level'] ?? '—')) ?></td>
                    <td class="cr-muted"><?= e($fd($item['created_at'] ?? null) ?? '—') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <?php if (!empty($domainBreakdown)): ?>
      <section class="cr-card">
        <div class="cr-card-head">
          <h2><i class="fas fa-layer-group"></i> Découpage par domaine consolidé</h2>
          <span class="cr-count"><?= count($domainBreakdown) ?></span>
        </div>
        <div class="cr-card-body">
          <?php foreach ($domainBreakdown as $db):
            $ds = max(0, min(100, (float)($db['fused_score'] ?? 0))); $dc = $tone($ds);
          ?>
            <div style="margin-bottom:.85rem;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
                <span style="font-size:.82rem;font-weight:700;">
                  <?php if (!empty($db['icon'])): ?><i class="fas <?= e($db['icon']) ?>" style="color:var(--cr-accent);margin-right:.35rem;"></i><?php endif; ?>
                  <?= e((string)($db['domain_label'] ?? '—')) ?>
                  <?php if ((int)($db['contributions'] ?? 1) > 1): ?>
                    <span style="font-size:.68rem;color:var(--cr-muted);margin-left:.3rem;"><?= (int)$db['contributions'] ?> modèles</span>
                  <?php endif; ?>
                </span>
                <span style="font-size:.85rem;font-weight:800;color:<?= $dc ?>;font-variant-numeric:tabular-nums;"><?= round($ds) ?>/100</span>
              </div>
              <div class="cr-score-bar" style="height:7px;"><i style="width:<?= round($ds) ?>%;background:<?= $dc ?>;"></i></div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <?php if ($canReview): ?>
      <section class="cr-card">
        <div class="cr-card-head"><h2><i class="fas fa-pen-to-square"></i> Analyse</h2></div>
        <div class="cr-card-body">
          <form action="/admin/consolidated/<?= $reportId ?>/review" method="POST">
            <?= $csrf ?>
            <div class="cr-field">
              <label for="crLevel">Niveau AQMI attribué</label>
              <input type="text" id="crLevel" name="aqmi_level_assigned" value="<?= e((string)($report['aqmi_level_assigned'] ?? '')) ?>" placeholder="Ex : Niveau 3 - Performant">
            </div>
            <div class="cr-field">
              <label for="crObs">Observations</label>
              <textarea id="crObs" name="observations" rows="4" placeholder="Votre lecture de l'évaluation globale…"><?= e((string)($report['observations'] ?? '')) ?></textarea>
            </div>
            <div class="cr-field">
              <label for="crPlan">Plan d'action</label>
              <textarea id="crPlan" name="action_plan" rows="4" placeholder="Actions recommandées pour progresser…"><?= e((string)($report['action_plan'] ?? '')) ?></textarea>
            </div>
            <div class="cr-field">
              <label for="crCom">Commentaire interne <small>non destiné à l'utilisateur</small></label>
              <textarea id="crCom" name="admin_comment" rows="2" placeholder="Note réservée à l'équipe…"><?= e((string)($report['admin_comment'] ?? '')) ?></textarea>
            </div>
            <div class="cr-actions">
              <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-floppy-disk"></i> Enregistrer l'analyse</button>
            </div>
          </form>
        </div>
      </section>

      <section class="cr-card cr-decide">
        <div class="cr-card-head"><h2><i class="fas fa-gavel"></i> Décision</h2></div>
        <div class="cr-card-body">
          <p class="cr-hint">Enregistrez d'abord l'analyse : les champs non enregistrés ne sont pas pris en compte par la décision.</p>
          <div class="cr-actions">
            <form action="/admin/consolidated/<?= $reportId ?>/approve" method="POST">
              <?= $csrf ?>
              <button type="submit" class="cr-btn cr-btn-info"><i class="fas fa-thumbs-up"></i> Approuver</button>
            </form>
            <form action="/admin/consolidated/<?= $reportId ?>/certify" method="POST" onsubmit="return confirm('Certifier ce rapport ?');">
              <?= $csrf ?>
              <button type="submit" class="cr-btn cr-btn-ok"><i class="fas fa-certificate"></i> Certifier</button>
            </form>
            <form action="/admin/consolidated/<?= $reportId ?>/reject" method="POST" onsubmit="return confirm('Rejeter cette demande ?');">
              <?= $csrf ?>
              <button type="submit" class="cr-btn cr-btn-bad"><i class="fas fa-xmark"></i> Rejeter</button>
            </form>
          </div>
        </div>
      </section>

      <?php elseif ($hasSaved): ?>
      <section class="cr-card">
        <div class="cr-card-head"><h2><i class="fas fa-clipboard-check"></i> Analyse enregistrée</h2></div>
        <div class="cr-card-body">
          <?php if (!empty($report['observations'])): ?>
            <div class="cr-note"><h3>Observations</h3><p><?= nl2br(e((string)$report['observations'])) ?></p></div>
          <?php endif; ?>
          <?php if (!empty($report['action_plan'])): ?>
            <div class="cr-note"><h3>Plan d'action</h3><p><?= nl2br(e((string)$report['action_plan'])) ?></p></div>
          <?php endif; ?>
          <?php if (!empty($report['admin_comment'])): ?>
            <div class="cr-note"><h3>Commentaire <em>interne</em></h3><p><?= nl2br(e((string)$report['admin_comment'])) ?></p></div>
          <?php endif; ?>
          <?php if (empty($report['observations']) && empty($report['action_plan']) && empty($report['admin_comment'])): ?>
            <div class="cr-note"><p class="cr-muted">Seul le niveau AQMI attribué a été renseigné.</p></div>
          <?php endif; ?>
        </div>
      </section>
      <?php endif; ?>

    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
