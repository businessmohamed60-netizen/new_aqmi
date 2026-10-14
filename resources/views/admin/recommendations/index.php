<?php
$title = 'Gestion des Recommandations';
ob_start();

$total   = count($recommendations);
$active  = count(array_filter($recommendations, fn($r) => !empty($r['is_active'])));
$byPrio  = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];
foreach ($recommendations as $r) {
    if (isset($byPrio[$r['priority']])) $byPrio[$r['priority']]++;
}
$prioLabels = ['critical' => 'Critique', 'high' => 'Haute', 'medium' => 'Moyenne', 'low' => 'Basse'];
$prioIcons  = ['critical' => 'fa-circle-exclamation', 'high' => 'fa-bolt', 'medium' => 'fa-arrow-right', 'low' => 'fa-check'];
$opSymbols  = ['<' => '<', '>' => '>', '<=' => '≤', '>=' => '≥', '==' => '='];
?>
<style>
.rec-wrap {
  /* Palette claire : fond pastel + texte foncé de la même teinte */
  --rec-red-bg:#FDECEE;    --rec-red-fg:#B4233A;    --rec-red-bd:#F6C4CB;
  --rec-amber-bg:#FFF3D6;  --rec-amber-fg:#8A5A00;  --rec-amber-bd:#F5DA9B;
  --rec-blue-bg:#E6F1FB;   --rec-blue-fg:#1F5F99;   --rec-blue-bd:#BBD8F2;
  --rec-green-bg:#E3F6EB;  --rec-green-fg:#17744A;  --rec-green-bd:#B5E3C9;
  --rec-violet-bg:#EFEAFB; --rec-violet-fg:#5B3FB0; --rec-violet-bd:#D3C8F2;
  --rec-rose-bg:#FCE8F1;   --rec-rose-fg:#A8346B;   --rec-rose-bd:#F4C3DA;
  --rec-gray-bg:#EEF1F4;   --rec-gray-fg:#5B6670;   --rec-gray-bd:#D9DEE3;
}
.rec-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.75rem; margin-bottom:1.25rem; }
.rec-title { color:var(--auto-text-primary); font-weight:700; font-size:1rem; margin:0; display:flex; align-items:center; gap:.5rem; }
.rec-title i { color:var(--auto-cyan); }
.rec-title small { color:var(--auto-text-muted); font-weight:500; font-size:.72rem; }

/* Barre d'outils */
.rec-toolbar { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; padding:.85rem 1rem; border-bottom:1px solid var(--auto-border); }
.rec-search { position:relative; flex:1 1 220px; max-width:320px; }
.rec-search i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:var(--auto-text-muted); font-size:.72rem; }
.rec-search input {
  width:100%; padding:.45rem .7rem .45rem 2rem; font-size:.76rem; border-radius:var(--auto-radius-sm);
  border:1px solid var(--auto-border); background:transparent; color:var(--auto-text-primary);
}
.rec-search input:focus { outline:2px solid var(--auto-cyan); outline-offset:1px; }
.rec-filters { display:flex; flex-wrap:wrap; gap:.35rem; }
.rec-pill {
  display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .65rem; font-size:.68rem; font-weight:600;
  border-radius:999px; border:1px solid var(--rec-gray-bd); background:var(--rec-gray-bg); color:var(--rec-gray-fg);
  cursor:pointer; transition:transform .15s ease, box-shadow .15s ease;
}
.rec-pill b { font-weight:700; }
.rec-pill:hover { transform:translateY(-1px); }
.rec-pill:focus-visible { outline:2px solid var(--auto-cyan); outline-offset:2px; }
.rec-pill[data-filter="critical"] { background:var(--rec-red-bg);   color:var(--rec-red-fg);   border-color:var(--rec-red-bd); }
.rec-pill[data-filter="high"]     { background:var(--rec-amber-bg); color:var(--rec-amber-fg); border-color:var(--rec-amber-bd); }
.rec-pill[data-filter="medium"]   { background:var(--rec-blue-bg);  color:var(--rec-blue-fg);  border-color:var(--rec-blue-bd); }
.rec-pill[data-filter="low"]      { background:var(--rec-green-bg); color:var(--rec-green-fg); border-color:var(--rec-green-bd); }
.rec-pill.is-on { box-shadow:0 0 0 2px currentColor; }
.rec-status-select {
  padding:.4rem .6rem; font-size:.72rem; border-radius:var(--auto-radius-sm);
  border:1px solid var(--auto-border); background:transparent; color:var(--auto-text-secondary);
}
.rec-count { margin-left:auto; font-size:.7rem; color:var(--auto-text-muted); }

/* Tableau */
.rec-table tbody tr { transition:background .15s ease; }
.rec-table tbody tr:hover { background:rgba(120,140,170,.07); }
.rec-table td:first-child { box-shadow:inset 3px 0 0 var(--row-accent, transparent); }
.rec-table tr[data-priority="critical"] { --row-accent:#E5677A; }
.rec-table tr[data-priority="high"]     { --row-accent:#E8B53F; }
.rec-table tr[data-priority="medium"]   { --row-accent:#5AA3E0; }
.rec-table tr[data-priority="low"]      { --row-accent:#4FBF8A; }
.rec-table tr.is-inactive .rec-text, .rec-table tr.is-inactive .rec-text-secondary { opacity:.55; }
.rec-id { color:var(--auto-text-muted); font-size:.7rem; font-variant-numeric:tabular-nums; }
.rec-text { color:var(--auto-text-primary); font-weight:500; font-size:.8rem; line-height:1.45; }
.rec-text-secondary { color:var(--auto-text-secondary); font-size:.74rem; line-height:1.4; margin-top:.25rem; }

/* Puces colorées */
.rec-chip {
  display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .55rem; border-radius:999px;
  font-size:.64rem; font-weight:600; border:1px solid transparent; white-space:nowrap;
}
.rec-chip.red    { background:var(--rec-red-bg);    color:var(--rec-red-fg);    border-color:var(--rec-red-bd); }
.rec-chip.amber  { background:var(--rec-amber-bg);  color:var(--rec-amber-fg);  border-color:var(--rec-amber-bd); }
.rec-chip.blue   { background:var(--rec-blue-bg);   color:var(--rec-blue-fg);   border-color:var(--rec-blue-bd); }
.rec-chip.green  { background:var(--rec-green-bg);  color:var(--rec-green-fg);  border-color:var(--rec-green-bd); }
.rec-chip.violet { background:var(--rec-violet-bg); color:var(--rec-violet-fg); border-color:var(--rec-violet-bd); }
.rec-chip.rose   { background:var(--rec-rose-bg);   color:var(--rec-rose-fg);   border-color:var(--rec-rose-bd); }
.rec-chip.gray   { background:var(--rec-gray-bg);   color:var(--rec-gray-fg);   border-color:var(--rec-gray-bd); }

/* Langues : coloré = renseigné, pointillé = manquant */
.rec-langs { display:inline-flex; gap:.25rem; }
.rec-lang { min-width:26px; text-align:center; padding:.15rem .3rem; border-radius:6px; font-size:.6rem; font-weight:700; border:1px dashed var(--rec-gray-bd); color:var(--auto-text-muted); background:transparent; }
.rec-lang.on { border-style:solid; }
.rec-lang.fr.on { background:var(--rec-blue-bg);   color:var(--rec-blue-fg);   border-color:var(--rec-blue-bd); }
.rec-lang.en.on { background:var(--rec-green-bg);  color:var(--rec-green-fg);  border-color:var(--rec-green-bd); }
.rec-lang.ar.on { background:var(--rec-amber-bg);  color:var(--rec-amber-fg);  border-color:var(--rec-amber-bd); }
.rec-lang.es.on { background:var(--rec-rose-bg);   color:var(--rec-rose-fg);   border-color:var(--rec-rose-bd); }
.rec-lang-count { display:block; margin-top:.2rem; font-size:.6rem; color:var(--auto-text-muted); }

/* Condition lisible */
.rec-cond { font-size:.72rem; color:var(--auto-text-secondary); white-space:nowrap; }
.rec-cond code { font-family:var(--auto-font-mono); font-size:.68rem; color:var(--auto-cyan); background:var(--rec-gray-bg); padding:.1rem .35rem; border-radius:5px; }
.rec-cond strong { color:var(--auto-text-primary); }

.rec-btn-action {
  width:30px; height:30px; border-radius:var(--auto-radius-sm); border:1px solid var(--auto-border);
  background:transparent; color:var(--auto-text-secondary); display:inline-flex; align-items:center; justify-content:center;
  text-decoration:none; font-size:.72rem; transition:var(--auto-transition);
}
.rec-btn-action:hover { background:var(--rec-blue-bg); color:var(--rec-blue-fg); border-color:var(--rec-blue-bd); }
.rec-btn-action.danger:hover { background:var(--rec-red-bg); color:var(--rec-red-fg); border-color:var(--rec-red-bd); }
.rec-btn-action:focus-visible { outline:2px solid var(--auto-cyan); outline-offset:2px; }

.rec-empty { text-align:center; padding:2.5rem 1rem; color:var(--auto-text-muted); font-size:.8rem; }
.rec-empty i { display:block; font-size:1.4rem; margin-bottom:.6rem; color:var(--auto-cyan); }
.rec-empty a { color:var(--auto-cyan); font-weight:600; }

@media (max-width:768px) {
  .rec-search { max-width:none; }
  .rec-count { margin-left:0; width:100%; }
}
@media (prefers-reduced-motion:reduce) { .rec-pill, .rec-table tbody tr { transition:none; } }
</style>

<div class="rec-wrap auto-fade-in">
  <div class="rec-header">
    <h5 class="rec-title">
      <i class="fas fa-lightbulb"></i>Règles de recommandation
      <small><?= $total ?> au total · <?= $active ?> active(s)</small>
    </h5>
    <a href="/admin/recommendations/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle règle</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div class="rec-toolbar">
      <label class="rec-search">
        <i class="fas fa-search"></i>
        <input type="search" id="recSearch" placeholder="Rechercher un texte, un champ, un domaine…" aria-label="Rechercher une règle">
      </label>

      <div class="rec-filters" role="group" aria-label="Filtrer par priorité">
        <?php foreach ($byPrio as $p => $n): ?>
          <button type="button" class="rec-pill" data-filter="<?= $p ?>">
            <i class="fas <?= $prioIcons[$p] ?>"></i><?= $prioLabels[$p] ?> <b><?= $n ?></b>
          </button>
        <?php endforeach; ?>
      </div>

      <select id="recStatus" class="rec-status-select" aria-label="Filtrer par statut">
        <option value="">Tous les statuts</option>
        <option value="1">Actives</option>
        <option value="0">Inactives</option>
      </select>

      <span class="rec-count" id="recCount" aria-live="polite"></span>
    </div>

    <div style="overflow-x:auto;">
      <table class="auto-table rec-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Recommandation</th>
            <th>Langues</th>
            <th>Domaine</th>
            <th>Condition</th>
            <th>Priorité</th>
            <th class="text-center">Statut</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="recBody">
          <?php if (!empty($recommendations)): ?>
            <?php foreach ($recommendations as $r): ?>
              <?php
                $hasFr = !empty($r['recommendation_text_fr']);
                $hasEn = !empty($r['recommendation_text']);
                $hasAr = !empty($r['recommendation_text_ar']);
                $hasEs = !empty($r['recommendation_text_es']);
                $langCount = (int)$hasFr + (int)$hasEn + (int)$hasAr + (int)$hasEs;

                $primary   = $r['recommendation_text_fr'] ?: $r['recommendation_text'];
                $secondary = ($hasFr && $hasEn) ? $r['recommendation_text'] : '';
                $domain    = $r['domain_name_fr'] ?: ($r['domain_name'] ?: 'Global');
                $isGlobal  = $domain === 'Global';
                $prio      = $r['priority'];
                $pColor    = ['critical' => 'red', 'high' => 'amber', 'medium' => 'blue', 'low' => 'green'][$prio] ?? 'gray';
                $op        = $opSymbols[$r['condition_operator'] ?? ''] ?? ($r['condition_operator'] ?? '–');
                $isActive  = !empty($r['is_active']);
                $haystack  = mb_strtolower(implode(' ', [$primary, $secondary, $domain, $r['condition_field'] ?? '']));
              ?>
              <tr data-priority="<?= e($prio) ?>"
                  data-active="<?= $isActive ? 1 : 0 ?>"
                  data-search="<?= e($haystack) ?>"
                  class="<?= $isActive ? '' : 'is-inactive' ?>">
                <td class="rec-id"><?= (int)$r['id'] ?></td>
                <td style="min-width:300px;max-width:460px;">
                  <div class="rec-text"><?= e(truncate($primary, 110)) ?></div>
                  <?php if ($secondary): ?>
                    <div class="rec-text-secondary"><?= e(truncate($secondary, 110)) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="rec-langs" title="<?= $langCount ?>/4 langues renseignées">
                    <span class="rec-lang fr <?= $hasFr ? 'on' : '' ?>" title="Français">FR</span>
                    <span class="rec-lang en <?= $hasEn ? 'on' : '' ?>" title="English">EN</span>
                    <span class="rec-lang ar <?= $hasAr ? 'on' : '' ?>" title="العربية">AR</span>
                    <span class="rec-lang es <?= $hasEs ? 'on' : '' ?>" title="Español">ES</span>
                  </div>
                  <span class="rec-lang-count"><?= $langCount ?>/4</span>
                </td>
                <td><span class="rec-chip <?= $isGlobal ? 'gray' : 'violet' ?>"><i class="fas <?= $isGlobal ? 'fa-globe' : 'fa-layer-group' ?>"></i><?= e($domain) ?></span></td>
                <td>
                  <span class="rec-cond">
                    <code><?= e($r['condition_field'] ?? '–') ?></code>
                    <?= e($op) ?>
                    <strong><?= e($r['condition_value'] ?? '–') ?> %</strong>
                  </span>
                </td>
                <td><span class="rec-chip <?= $pColor ?>"><i class="fas <?= $prioIcons[$prio] ?? 'fa-circle' ?>"></i><?= e($prioLabels[$prio] ?? ucfirst($prio)) ?></span></td>
                <td class="text-center">
                  <span class="rec-chip <?= $isActive ? 'green' : 'gray' ?>">
                    <i class="fas <?= $isActive ? 'fa-circle-check' : 'fa-circle-pause' ?>"></i><?= $isActive ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td class="text-center">
                  <div style="display:flex;justify-content:center;gap:.3rem;">
                    <a href="/admin/recommendations/edit/<?= (int)$r['id'] ?>" class="rec-btn-action" title="Modifier" aria-label="Modifier la règle <?= (int)$r['id'] ?>"><i class="fas fa-pen"></i></a>
                    <a href="/admin/recommendations/delete/<?= (int)$r['id'] ?>" class="rec-btn-action danger" data-confirm="Supprimer cette règle ?" title="Supprimer" aria-label="Supprimer la règle <?= (int)$r['id'] ?>"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8">
              <div class="rec-empty">
                <i class="fas fa-lightbulb"></i>
                Aucune règle pour le moment.<br>
                <a href="/admin/recommendations/create">Créer la première règle</a>
              </div>
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="rec-empty" id="recNoMatch" hidden>
        <i class="fas fa-filter-circle-xmark"></i>Aucune règle ne correspond à ces filtres.
      </div>
    </div>
  </div>
</div>
<?php
$extraScripts = <<<'SCRIPTS'
<script>
(function () {
  var rows = Array.prototype.slice.call(document.querySelectorAll('#recBody tr[data-priority]'));
  if (!rows.length) return;
  var search = document.getElementById('recSearch');
  var status = document.getElementById('recStatus');
  var count  = document.getElementById('recCount');
  var empty  = document.getElementById('recNoMatch');
  var pills  = document.querySelectorAll('.rec-pill');
  var activePrio = '';

  function apply() {
    var q = search.value.trim().toLowerCase(), s = status.value, shown = 0;
    rows.forEach(function (tr) {
      var ok = (!activePrio || tr.dataset.priority === activePrio)
            && (s === '' || tr.dataset.active === s)
            && (!q || tr.dataset.search.indexOf(q) !== -1);
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    count.textContent = shown + ' / ' + rows.length + ' affichée(s)';
    empty.hidden = shown !== 0;
  }

  pills.forEach(function (p) {
    p.addEventListener('click', function () {
      var f = p.dataset.filter;
      activePrio = (activePrio === f) ? '' : f;
      pills.forEach(function (x) { x.classList.toggle('is-on', x.dataset.filter === activePrio); });
      apply();
    });
  });
  search.addEventListener('input', apply);
  status.addEventListener('change', apply);
  apply();
})();
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
