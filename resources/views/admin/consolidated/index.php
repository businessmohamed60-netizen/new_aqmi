<?php
/**
 * Rapports consolidés — liste admin (demandes à traiter + rapports certifiés)
 * Variables attendues du contrôleur : $reports, $certified
 */
$title     = 'Rapports consolidés';
$reports   = is_array($reports ?? null) ? $reports : [];
$certified = is_array($certified ?? null) ? $certified : [];
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
$pill = function ($status) use ($statusMap) {
    $m = $statusMap[$status] ?? [(string)$status, 'muted'];
    return '<span class="cr-pill cr-pill-' . $m[1] . '"><i></i>' . e($m[0]) . '</span>';
};
$initials = function ($f, $l) {
    $c = function ($s) { $s = (string)$s; return function_exists('mb_substr') ? mb_strtoupper(mb_substr($s, 0, 1)) : strtoupper(substr($s, 0, 1)); };
    return ($c($f) . $c($l)) ?: '?';
};
$fd = function ($d, $withTime = false) {
    if (empty($d)) return '—';
    $t = strtotime((string)$d);
    return $t ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $t) : '—';
};
$scoreCell = function ($v) use ($tone) {
    $s = max(0, min(100, (float)$v));
    $c = $tone($s);
    return '<div class="cr-score"><b style="color:' . $c . ';">' . round($s) . '</b>'
         . '<span class="cr-score-bar"><i style="width:' . round($s) . '%;background:' . $c . ';"></i></span></div>';
};

$nReview  = count(array_filter($reports, function ($r) { return ($r['status'] ?? '') === 'under_review'; }));
$nWaiting = count($reports) - $nReview;
$nCert    = count($certified);
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
    padding-bottom: 6rem; /* évite que le bas de page soit masqué par le gabarit */
  }
  .cr-page *, .cr-page *::before, .cr-page *::after { box-sizing: border-box; }
  .cr-page a:focus-visible, .cr-page input:focus-visible { outline: 2px solid var(--cr-accent); outline-offset: 2px; }

  /* En-tête */
  .cr-head { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.4rem; }
  .cr-head h1 { margin: 0; font-size: 1.6rem; font-weight: 800; letter-spacing: -.025em; }
  .cr-head p { margin: .3rem 0 0; font-size: .85rem; color: var(--cr-muted); max-width: 52ch; }
  .cr-search { position: relative; min-width: 260px; }
  .cr-search i { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); color: var(--cr-muted); font-size: .8rem; pointer-events: none; }
  .cr-search input { width: 100%; padding: .6rem .9rem .6rem 2.3rem; border-radius: 10px; border: 1px solid var(--cr-line); background: var(--cr-card); color: var(--cr-ink); font: 500 .82rem 'Plus Jakarta Sans', sans-serif; }
  .cr-search input:focus { border-color: var(--cr-accent); outline: none; box-shadow: 0 0 0 3px var(--cr-accent-soft); }

  /* Synthèse */
  .cr-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 1.4rem; }
  .cr-stat { display: flex; align-items: center; gap: .9rem; padding: 1rem 1.15rem; background: var(--cr-card); border: 1px solid var(--cr-line); border-radius: 14px; }
  .cr-stat-n { font-size: 1.9rem; font-weight: 800; letter-spacing: -.03em; line-height: 1; min-width: 2ch; }
  .cr-stat-t { font-size: .8rem; font-weight: 700; line-height: 1.25; }
  .cr-stat-t small { display: block; margin-top: .15rem; font-weight: 500; color: var(--cr-muted); font-size: .72rem; }
  .cr-stat.is-warn .cr-stat-n { color: #b45309; }
  .cr-stat.is-info .cr-stat-n { color: #0369a1; }
  .cr-stat.is-ok   .cr-stat-n { color: #047857; }

  /* Sections */
  .cr-section { background: var(--cr-card); border: 1px solid var(--cr-line); border-radius: 14px; margin-bottom: 1.4rem; overflow: hidden; }
  .cr-section-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 1.3rem; border-bottom: 1px solid var(--cr-line); }
  .cr-section-head h2 { margin: 0; font-size: 1rem; font-weight: 800; display: flex; align-items: center; gap: .6rem; }
  .cr-section-head h2 i { display: inline-grid; place-items: center; width: 28px; height: 28px; border-radius: 8px; background: var(--cr-accent-soft); color: var(--cr-accent); font-size: .78rem; }
  .cr-count { font-size: .72rem; font-weight: 700; color: var(--cr-muted); background: rgba(100,116,139,.1); padding: .2rem .65rem; border-radius: 999px; }

  /* Tableau */
  .cr-wrap { overflow-x: auto; }
  .cr-table { width: 100%; border-collapse: collapse; min-width: 760px; }
  .cr-table th { padding: .7rem 1rem; text-align: left; font-size: .74rem; font-weight: 700; color: var(--cr-muted); background: rgba(100,116,139,.05); border-bottom: 1px solid var(--cr-line); white-space: nowrap; }
  .cr-table td { padding: .85rem 1rem; font-size: .82rem; border-bottom: 1px solid var(--cr-line); vertical-align: middle; }
  .cr-table tbody tr:last-child td { border-bottom: 0; }
  .cr-table tbody tr.is-link { cursor: pointer; }
  .cr-table tbody tr:hover { background: var(--cr-accent-soft); }
  .cr-title { font-weight: 700; }
  .cr-user { display: flex; align-items: center; gap: .65rem; }
  .cr-av { flex: 0 0 auto; width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; font-size: .72rem; font-weight: 800; color: #fff; background: linear-gradient(135deg, var(--cr-accent), #4f46e5); }
  .cr-user b { display: block; font-size: .82rem; font-weight: 700; }
  .cr-user small { color: var(--cr-muted); font-size: .72rem; }
  .cr-muted { color: var(--cr-muted); }
  .cr-ref { font-family: ui-monospace, 'SFMono-Regular', Menlo, monospace; font-size: .76rem; font-weight: 600; padding: .15rem .5rem; border-radius: 6px; background: rgba(100,116,139,.1); white-space: nowrap; }

  .cr-score { display: flex; align-items: center; gap: .6rem; min-width: 110px; }
  .cr-score b { font-size: .9rem; font-weight: 800; min-width: 2.2ch; text-align: right; font-variant-numeric: tabular-nums; }
  .cr-score-bar { flex: 1; height: 6px; border-radius: 999px; background: rgba(100,116,139,.15); overflow: hidden; }
  .cr-score-bar i { display: block; height: 100%; border-radius: 999px; }

  .cr-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .22rem .65rem; border-radius: 999px; font-size: .72rem; font-weight: 700; white-space: nowrap; }
  .cr-pill i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
  .cr-pill-warn  { background: rgba(245,158,11,.14); color: #b45309; }
  .cr-pill-info  { background: rgba(14,165,233,.13); color: #0369a1; }
  .cr-pill-ok    { background: rgba(16,185,129,.13); color: #047857; }
  .cr-pill-bad   { background: rgba(220,38,38,.12); color: #b91c1c; }
  .cr-pill-muted { background: rgba(100,116,139,.13); color: #475569; }

  .cr-btn { display: inline-flex; align-items: center; gap: .45rem; padding: .48rem .95rem; border-radius: 9px; font: 700 .76rem 'Plus Jakarta Sans', sans-serif; text-decoration: none; border: 1px solid transparent; cursor: pointer; white-space: nowrap; }
  .cr-btn-primary { background: var(--cr-accent); color: #fff; }
  .cr-btn-primary:hover { filter: brightness(1.08); color: #fff; }
  .cr-btn-ghost { background: transparent; color: var(--cr-ink); border-color: var(--cr-line); }
  .cr-btn-ghost:hover { border-color: var(--cr-accent); color: var(--cr-accent); }

  /* États vides */
  .cr-empty { display: flex; flex-direction: column; align-items: center; text-align: center; gap: .35rem; padding: 2.6rem 1rem; color: var(--cr-muted); }
  .cr-empty i { font-size: 1.8rem; opacity: .35; margin-bottom: .4rem; }
  .cr-empty b { color: var(--cr-ink); font-size: .9rem; }
  .cr-empty span { font-size: .8rem; max-width: 46ch; }
  .cr-nomatch { display: none; padding: 1.6rem 1rem; text-align: center; font-size: .82rem; color: var(--cr-muted); }

  @media (max-width: 760px) {
    .cr-stats { grid-template-columns: 1fr; }
    .cr-search { min-width: 0; width: 100%; }
  }
</style>

<div class="cr-page" id="crPage">

  <header class="cr-head">
    <div>
      <h1>Rapports consolidés</h1>
      <p>Examinez les demandes de certification et retrouvez les rapports déjà certifiés.</p>
    </div>
    <label class="cr-search">
      <i class="fas fa-search"></i>
      <input type="search" id="crSearch" placeholder="Rechercher un rapport, un nom, un e-mail…" aria-label="Rechercher dans les rapports">
    </label>
  </header>

  <div class="cr-stats">
    <div class="cr-stat is-warn"><div class="cr-stat-n"><?= (int)$nWaiting ?></div><div class="cr-stat-t">En attente<small>Demandes à examiner</small></div></div>
    <div class="cr-stat is-info"><div class="cr-stat-n"><?= (int)$nReview ?></div><div class="cr-stat-t">En examen<small>Analyse en cours</small></div></div>
    <div class="cr-stat is-ok"><div class="cr-stat-n"><?= (int)$nCert ?></div><div class="cr-stat-t">Certifiés<small>Rapports délivrés</small></div></div>
  </div>

  <!-- Demandes en attente -->
  <section class="cr-section" aria-labelledby="crPendingTitle">
    <div class="cr-section-head">
      <h2 id="crPendingTitle"><i class="fas fa-layer-group"></i> Demandes à traiter</h2>
      <span class="cr-count"><?= count($reports) ?> demande<?= count($reports) > 1 ? 's' : '' ?></span>
    </div>
    <?php if (empty($reports)): ?>
      <div class="cr-empty">
        <i class="fas fa-inbox"></i>
        <b>Aucune demande en attente</b>
        <span>Les nouvelles demandes de certification apparaîtront ici dès qu'un utilisateur les enverra.</span>
      </div>
    <?php else: ?>
      <div class="cr-wrap">
        <table class="cr-table" data-filterable>
          <thead>
            <tr>
              <th>Rapport</th>
              <th>Demandeur</th>
              <th>Score</th>
              <th>Niveau</th>
              <th>Statut</th>
              <th>Demandé le</th>
              <th><span class="visually-hidden" style="position:absolute;left:-9999px;">Action</span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reports as $r):
              $fn = (string)($r['firstname'] ?? ''); $ln = (string)($r['lastname'] ?? '');
              $href = '/admin/consolidated/' . (int)($r['id'] ?? 0);
            ?>
              <tr class="is-link" data-href="<?= e($href) ?>">
                <td class="cr-title"><?= e((string)($r['title'] ?? '—')) ?></td>
                <td>
                  <div class="cr-user">
                    <span class="cr-av"><?= e($initials($fn, $ln)) ?></span>
                    <span><b><?= e(trim($fn . ' ' . $ln)) ?></b><small><?= e((string)($r['email'] ?? '')) ?></small></span>
                  </div>
                </td>
                <td><?= $scoreCell($r['consolidated_score'] ?? 0) ?></td>
                <td><?= e((string)($r['maturity_level'] ?? '—')) ?></td>
                <td><?= $pill($r['status'] ?? '') ?></td>
                <td class="cr-muted"><?= e($fd($r['certification_requested_at'] ?? null, true)) ?></td>
                <td style="text-align:right;"><a href="<?= e($href) ?>" class="cr-btn cr-btn-primary"><i class="fas fa-eye"></i> Examiner</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="cr-nomatch">Aucune demande ne correspond à votre recherche.</div>
    <?php endif; ?>
  </section>

  <!-- Rapports certifiés -->
  <section class="cr-section" aria-labelledby="crCertTitle">
    <div class="cr-section-head">
      <h2 id="crCertTitle"><i class="fas fa-certificate"></i> Rapports certifiés</h2>
      <span class="cr-count"><?= count($certified) ?> rapport<?= count($certified) > 1 ? 's' : '' ?></span>
    </div>
    <?php if (empty($certified)): ?>
      <div class="cr-empty">
        <i class="fas fa-circle-check"></i>
        <b>Aucun rapport certifié pour le moment</b>
        <span>Un rapport apparaît ici dès que vous le certifiez depuis sa page d'examen.</span>
      </div>
    <?php else: ?>
      <div class="cr-wrap">
        <table class="cr-table" data-filterable>
          <thead>
            <tr>
              <th>Numéro</th>
              <th>Rapport</th>
              <th>Demandeur</th>
              <th>Score</th>
              <th>Niveau</th>
              <th>Certifié le</th>
              <th>Validé par</th>
              <th><span class="visually-hidden" style="position:absolute;left:-9999px;">Action</span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($certified as $r):
              $fn = (string)($r['firstname'] ?? ''); $ln = (string)($r['lastname'] ?? '');
              $href = isset($r['id']) ? '/admin/consolidated/' . (int)$r['id'] : '';
            ?>
              <tr<?= $href !== '' ? ' class="is-link" data-href="' . e($href) . '"' : '' ?>>
                <td><span class="cr-ref"><?= e((string)($r['report_number'] ?? '—')) ?></span></td>
                <td class="cr-title"><?= e((string)($r['title'] ?? '—')) ?></td>
                <td>
                  <div class="cr-user">
                    <span class="cr-av"><?= e($initials($fn, $ln)) ?></span>
                    <span><b><?= e(trim($fn . ' ' . $ln)) ?></b><?php if (!empty($r['email'])): ?><small><?= e((string)$r['email']) ?></small><?php endif; ?></span>
                  </div>
                </td>
                <td><?= $scoreCell($r['consolidated_score'] ?? 0) ?></td>
                <td><?= e((string)($r['maturity_level'] ?? '—')) ?></td>
                <td class="cr-muted"><?= e($fd($r['certified_at'] ?? null)) ?></td>
                <td><?= e((string)($r['validated_by'] ?? '—')) ?></td>
                <td style="text-align:right;"><?php if ($href !== ''): ?><a href="<?= e($href) ?>" class="cr-btn cr-btn-ghost">Voir</a><?php endif; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="cr-nomatch">Aucun rapport certifié ne correspond à votre recherche.</div>
    <?php endif; ?>
  </section>

</div>

<script>
(function () {
  var root = document.getElementById('crPage');
  if (!root) return;

  /* Ligne cliquable (sans gêner les liens et boutons) */
  [].forEach.call(root.querySelectorAll('tr[data-href]'), function (tr) {
    tr.addEventListener('click', function (e) {
      if (e.target.closest('a, button, input, select, textarea')) return;
      window.location.href = tr.getAttribute('data-href');
    });
  });

  /* Recherche instantanée dans les deux tableaux */
  var input = document.getElementById('crSearch');
  if (!input) return;
  input.addEventListener('input', function () {
    var q = input.value.trim().toLowerCase();
    [].forEach.call(root.querySelectorAll('table[data-filterable]'), function (table) {
      var shown = 0;
      [].forEach.call(table.tBodies[0].rows, function (row) {
        var ok = q === '' || row.textContent.toLowerCase().indexOf(q) !== -1;
        row.style.display = ok ? '' : 'none';
        if (ok) shown++;
      });
      var msg = table.parentNode.nextElementSibling;
      if (msg && msg.classList.contains('cr-nomatch')) msg.style.display = (shown === 0) ? 'block' : 'none';
    });
  });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
