<?php
$title = 'Base de Données Prospects';
ob_start();
$totalPages = max(1, ceil($total / $perPage));
$avgScore = 0;
$scoredLeads = array_filter($leads, fn($l) => isset($l['total_score']));
if (count($scoredLeads) > 0) {
    $avgScore = round(array_sum(array_column($scoredLeads, 'total_score')) / count($scoredLeads));
}
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  .leads-page { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }

  /* ── Page Header ── */
  .leads-page-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
  }
  .leads-page-header h4 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 800; font-size: 1.5rem; color: var(--auto-text-primary);
    margin: 0; letter-spacing: -0.02em;
  }
  .leads-page-header p {
    font-size: 0.82rem; color: var(--auto-text-muted); margin: 0.25rem 0 0;
  }

  /* ── Stats Row ── */
  .leads-stats-row {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;
  }
  .leads-stat-card {
    background: var(--auto-bg-card);
    border: 1px solid var(--auto-border);
    border-radius: var(--auto-radius-md);
    padding: 1.25rem 1.25rem 1.1rem;
    box-shadow: 0 2px 12px rgba(80,64,42,.05);
    transition: var(--auto-transition);
    position: relative; overflow: hidden;
  }
  .leads-stat-card:hover { border-color: var(--auto-cyan-glow); box-shadow: 0 6px 20px rgba(80,64,42,.1); }
  .leads-stat-card .stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; margin-bottom: 0.75rem;
  }
  .leads-stat-card .stat-value {
    font-size: 1.75rem; font-weight: 800; color: var(--auto-text-primary);
    line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.02em;
  }
  .leads-stat-card .stat-label {
    font-size: 0.7rem; color: var(--auto-text-muted); text-transform: uppercase;
    letter-spacing: 0.06em; margin-top: 0.35rem; font-weight: 600;
  }
  .leads-stat-card .stat-accent-bar {
    position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
    border-radius: 0 0 var(--auto-radius-md) var(--auto-radius-md);
  }

  /* ── Filter Bar ── */
  .leads-filter-bar {
    background: var(--auto-bg-card);
    border: 1px solid var(--auto-border);
    border-radius: var(--auto-radius-md);
    padding: 1rem 1.25rem; margin-bottom: 1.25rem;
    box-shadow: 0 2px 10px rgba(80,64,42,.04);
  }
  .leads-filter-bar form {
    display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;
  }
  .leads-filter-bar .search-wrap {
    position: relative; flex: 1; min-width: 200px;
  }
  .leads-filter-bar .search-wrap i {
    position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%);
    color: var(--auto-text-muted); font-size: 0.8rem;
  }
  .leads-filter-bar .search-wrap input {
    padding-left: 2.2rem !important; width: 100%;
  }
  .leads-filter-bar select, .leads-filter-bar input {
    font-size: 0.8rem; padding: 0.5rem 0.85rem;
    border-radius: 8px; border: 1px solid var(--vx-input-border);
    background: var(--vx-input-bg); color: var(--auto-text-primary);
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 500;
    transition: var(--auto-transition);
  }
  .leads-filter-bar select { min-width: 150px; cursor: pointer; }
  .leads-filter-bar select:hover, .leads-filter-bar input:hover { border-color: var(--auto-cyan-glow); }
  .leads-filter-bar select:focus, .leads-filter-bar input:focus {
    border-color: var(--auto-cyan); outline: none;
    box-shadow: 0 0 0 3px var(--auto-cyan-dim);
  }
  .leads-btn {
    padding: 0.5rem 1.1rem; border-radius: 8px; font-size: 0.78rem;
    font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    border: none; cursor: pointer; transition: var(--auto-transition);
    display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
  }
  .leads-btn-primary { background: var(--auto-cyan); color: #fff; }
  .leads-btn-primary:hover { background: #1558c4; transform: translateY(-1px); box-shadow: 0 4px 12px var(--auto-cyan-glow); }
  .leads-btn-ghost { background: transparent; border: 1px solid var(--vx-input-border); color: var(--auto-text-secondary); }
  .leads-btn-ghost:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
  .leads-btn-export {
    background: linear-gradient(135deg, #1a8a4f, #0d6e3e); color: #fff;
    padding: 0.55rem 1.25rem; border-radius: 8px; font-size: 0.78rem;
    font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    border: none; cursor: pointer; transition: var(--auto-transition);
    display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
  }
  .leads-btn-export:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(13,110,62,.3); }

  /* ── Table Card ── */
  .leads-table-card {
    background: var(--auto-bg-card);
    border: 1px solid var(--auto-border);
    border-radius: var(--auto-radius-md);
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(80,64,42,.06);
  }
  .leads-table-card .table-header {
    padding: 1rem 1.5rem; border-bottom: 1px solid var(--auto-border);
    display: flex; justify-content: space-between; align-items: center;
  }
  .leads-table-card .table-header h6 {
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
    font-size: 0.9rem; color: var(--auto-text-primary); margin: 0;
  }
  .leads-table-card .table-header .count-badge {
    font-size: 0.7rem; font-weight: 600; color: var(--auto-cyan);
    background: var(--auto-cyan-dim); padding: 0.2rem 0.6rem; border-radius: 20px;
  }
  .leads-table-wrap { overflow-x: auto; }

  table.leads-table {
    width: 100%; border-collapse: collapse;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  table.leads-table thead th {
    font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: var(--auto-text-muted);
    padding: 0.85rem 1rem; text-align: left;
    border-bottom: 2px solid var(--auto-border); background: rgba(0,0,0,.015);
  }
  table.leads-table tbody tr {
    transition: var(--auto-transition); cursor: pointer;
    border-bottom: 1px solid var(--vx-divider);
  }
  table.leads-table tbody tr:hover { background: rgba(31,111,235,.03); }
  table.leads-table tbody tr:last-child { border-bottom: none; }
  table.leads-table td {
    padding: 0.9rem 1rem; font-size: 0.8rem;
    color: var(--auto-text-secondary); vertical-align: middle;
  }

  .lead-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--auto-cyan), #4f46e5);
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.78rem; font-weight: 700;
    flex-shrink: 0;
  }
  .lead-name-cell { display: flex; align-items: center; gap: 0.7rem; }
  .lead-name-cell .lead-name-text {
    font-weight: 700; color: var(--auto-text-primary); font-size: 0.82rem;
  }
  .lead-name-cell .lead-email-text {
    font-size: 0.7rem; color: var(--auto-cyan); margin-top: 0.1rem;
  }
  .lead-company-text { font-weight: 600; color: var(--auto-text-primary); }
  .lead-sector-badge {
    display: inline-block; padding: 0.2rem 0.6rem; border-radius: 6px;
    font-size: 0.68rem; font-weight: 600;
    background: var(--auto-cyan-dim); color: var(--auto-cyan);
  }
  .lead-score-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.6rem; border-radius: 20px;
    font-size: 0.72rem; font-weight: 700;
  }
  .lead-date-text { font-size: 0.72rem; color: var(--auto-text-muted); }
  .lead-action-btn {
    width: 34px; height: 34px; border-radius: 8px;
    border: 1px solid var(--auto-border); background: transparent;
    color: var(--auto-text-secondary); cursor: pointer;
    transition: var(--auto-transition); display: inline-flex;
    align-items: center; justify-content: center; text-decoration: none;
  }
  .lead-action-btn:hover {
    border-color: var(--auto-cyan); color: var(--auto-cyan);
    background: var(--auto-cyan-dim);
  }

  .leads-empty {
    text-align: center; padding: 3rem 1rem; color: var(--auto-text-muted);
  }
  .leads-empty i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3; }
  .leads-empty p { font-size: 0.85rem; }

  /* ── Pagination ── */
  .leads-pagination { display: flex; justify-content: center; gap: 0.35rem; margin-top: 1.5rem; }
  .leads-pagination a {
    padding: 0.45rem 0.85rem; border-radius: 8px;
    border: 1px solid var(--auto-border); background: transparent;
    color: var(--auto-text-secondary); text-decoration: none;
    font-size: 0.78rem; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    transition: var(--auto-transition);
  }
  .leads-pagination a:hover { border-color: var(--auto-cyan); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
  .leads-pagination a.active { background: var(--auto-cyan); color: #fff; border-color: var(--auto-cyan); }

  @media (max-width: 992px) { .leads-stats-row { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 576px) { .leads-stats-row { grid-template-columns: 1fr; } .leads-page-header { flex-direction: column; } }

  /* ── Suppression d'un prospect ── */
  .lead-actions-cell { display: inline-flex; gap: 0.4rem; align-items: center; justify-content: center; }
  .lead-action-btn.is-danger { color: #dc2626; border-color: rgba(220,38,38,.35); }
  .lead-action-btn.is-danger:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
  .lead-del-dialog { border: none; border-radius: 1rem; padding: 0; width: min(92vw, 420px); box-shadow: 0 30px 80px rgba(15,23,42,.35); font-family: 'Plus Jakarta Sans', sans-serif; }
  .lead-del-dialog::backdrop { background: rgba(15,23,42,.55); backdrop-filter: blur(2px); }
  .lead-del-head { display: flex; align-items: center; gap: 0.75rem; padding: 1.05rem 1.3rem; background: rgba(220,38,38,.08); border-bottom: 1px solid rgba(220,38,38,.2); }
  .lead-del-head i { width: 2.3rem; height: 2.3rem; border-radius: 0.7rem; display: grid; place-items: center; background: #dc2626; color: #fff; }
  .lead-del-head h5 { margin: 0; font-size: 0.98rem; font-weight: 800; color: #991b1b; }
  .lead-del-body { padding: 1.15rem 1.3rem; font-size: 0.82rem; line-height: 1.6; color: #334155; }
  .lead-del-body .lead-del-who { margin: 0.2rem 0 0.7rem; padding: 0.6rem 0.8rem; border-radius: 0.6rem; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; color: #0f172a; }
  .lead-del-body .lead-del-who small { display: block; font-weight: 500; color: #64748b; }
  .lead-del-actions { display: flex; justify-content: flex-end; gap: 0.6rem; padding: 0 1.3rem 1.15rem; }
  .lead-del-actions button { padding: 0.55rem 1.15rem; border-radius: 0.65rem; font: 700 0.78rem 'Plus Jakarta Sans', sans-serif; cursor: pointer; border: 1px solid #cbd5e1; background: #fff; color: #334155; }
  .lead-del-actions .is-danger { background: #dc2626; border-color: #dc2626; color: #fff; }
</style>

<div class="leads-page auto-fade-in">
  <!-- Page Header -->
  <div class="leads-page-header">
    <div>
      <h4>Base de Données Prospects</h4>
      <p>Gérez et consultez l'ensemble de vos prospects qualifiés issus du questionnaire AQMI</p>
    </div>
    <a href="/admin/leads/export" class="leads-btn-export">
      <i class="fas fa-file-export"></i> Exporter en CSV
    </a>
  </div>

  <!-- Stats Summary -->
  <div class="leads-stats-row">
    <div class="leads-stat-card">
      <div class="stat-icon" style="background:var(--auto-cyan-dim);color:var(--auto-cyan);">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-value"><?= $total ?></div>
      <div class="stat-label">Total Prospects</div>
      <div class="stat-accent-bar" style="background:var(--auto-cyan);"></div>
    </div>
    <div class="leads-stat-card">
      <div class="stat-icon" style="background:rgba(26,138,79,.1);color:#1a8a4f;">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="stat-value"><?= $avgScore ?>%</div>
      <div class="stat-label">Score Moyen</div>
      <div class="stat-accent-bar" style="background:#1a8a4f;"></div>
    </div>
    <div class="leads-stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
        <i class="fas fa-building"></i>
      </div>
      <div class="stat-value"><?= count($sectors) ?></div>
      <div class="stat-label">Secteurs</div>
      <div class="stat-accent-bar" style="background:#d97706;"></div>
    </div>
    <div class="leads-stat-card">
      <div class="stat-icon" style="background:rgba(139,92,246,.1);color:#7c3aed;">
        <i class="fas fa-globe"></i>
      </div>
      <div class="stat-value"><?= count($countries) ?></div>
      <div class="stat-label">Pays</div>
      <div class="stat-accent-bar" style="background:#7c3aed;"></div>
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="leads-filter-bar">
    <form method="GET">
      <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="search" placeholder="Rechercher par nom, entreprise, email..." value="<?= e($search) ?>">
      </div>
      <select name="sector">
        <option value="">Tous les secteurs</option>
        <?php foreach ($sectors as $s): ?>
          <option value="<?= e($s['sector']) ?>" <?= $sector === $s['sector'] ? 'selected' : '' ?>><?= e($s['sector']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="country">
        <option value="">Tous les pays</option>
        <?php foreach ($countries as $c): ?>
          <option value="<?= e($c['country']) ?>" <?= $country === $c['country'] ? 'selected' : '' ?>><?= e($c['country']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="leads-btn leads-btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
      <a href="/admin/leads" class="leads-btn leads-btn-ghost"><i class="fas fa-undo"></i> Réinitialiser</a>
    </form>
  </div>

  <!-- Table -->
  <div class="leads-table-card">
    <div class="table-header">
      <h6><i class="fas fa-address-book me-2" style="color:var(--auto-cyan);"></i>Liste des Prospects</h6>
      <span class="count-badge"><?= $total ?> prospect<?= $total > 1 ? 's' : '' ?></span>
    </div>
    <div class="leads-table-wrap">
      <table class="leads-table">
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th>Prospect</th>
            <th>Entreprise</th>
            <th>Secteur</th>
            <th>Pays</th>
            <th>Score AQMI</th>
            <th>Date</th>
            <th style="text-align:center;width:110px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($leads)): ?>
            <?php foreach ($leads as $l): ?>
              <?php
              $initials = strtoupper(substr($l['firstname'] ?? '', 0, 1)) . strtoupper(substr($l['lastname'] ?? '', 0, 1));
              $score = $l['total_score'] ?? null;
              $scoreColor = '#7d8794';
              $scoreBg = 'rgba(125,135,148,.1)';
              if ($score !== null) {
                $s = (float)$score;
                if ($s >= 71) { $scoreColor = '#0d6e3e'; $scoreBg = 'rgba(13,110,62,.1)'; }
                elseif ($s >= 51) { $scoreColor = '#1F6FEB'; $scoreBg = 'rgba(31,111,235,.1)'; }
                elseif ($s >= 31) { $scoreColor = '#d97706'; $scoreBg = 'rgba(217,119,6,.1)'; }
                else { $scoreColor = '#dc2626'; $scoreBg = 'rgba(220,38,38,.1)'; }
              }
              ?>
              <tr onclick="window.location.href='/admin/leads/detail/<?= $l['id'] ?>'">
                <td style="color:var(--auto-text-muted);font-size:0.72rem;font-weight:600;"><?= str_pad($l['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td>
                  <div class="lead-name-cell">
                    <div class="lead-avatar"><?= $initials ?></div>
                    <div>
                      <div class="lead-name-text"><?= e($l['firstname'] . ' ' . $l['lastname']) ?></div>
                      <div class="lead-email-text"><?= e($l['email'] ?? '') ?></div>
                    </div>
                  </div>
                </td>
                <td><span class="lead-company-text"><?= e($l['company'] ?? '-') ?></span></td>
                <td>
                  <?php if ($l['sector']): ?>
                    <span class="lead-sector-badge"><?= e($l['sector']) ?></span>
                  <?php else: ?>
                    <span style="color:var(--auto-text-muted);">—</span>
                  <?php endif; ?>
                </td>
                <td class="lead-date-text"><?= e($l['country'] ?? '—') ?></td>
                <td>
                  <?php if ($score !== null): ?>
                    <span class="lead-score-pill" style="color:<?= $scoreColor ?>;background:<?= $scoreBg ?>;">
                      <i class="fas fa-chart-pie"></i> <?= (float)$score ?>%
                    </span>
                  <?php else: ?>
                    <span style="color:var(--auto-text-muted);font-size:0.72rem;">Non évalué</span>
                  <?php endif; ?>
                </td>
                <td class="lead-date-text"><?= formatDate($l['created_at']) ?></td>
                <td style="text-align:center;">
                  <div class="lead-actions-cell">
                    <a href="/admin/leads/detail/<?= $l['id'] ?>" class="lead-action-btn" title="Voir la fiche technique" onclick="event.stopPropagation();">
                      <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" class="lead-action-btn is-danger js-lead-delete" title="Supprimer ce prospect"
                            data-id="<?= (int)$l['id'] ?>"
                            data-name="<?= e(trim(($l['firstname'] ?? '') . ' ' . ($l['lastname'] ?? ''))) ?>"
                            data-company="<?= e((string)($l['company'] ?? '')) ?>">
                      <i class="fas fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">
                <div class="leads-empty">
                  <i class="fas fa-user-slash"></i>
                  <p>Aucun prospect trouvé. Les prospects apparaîtront ici automatiquement lorsqu'un visiteur complétera le questionnaire AQMI.</p>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <div class="leads-pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>&country=<?= urlencode($country) ?>">
          <i class="fas fa-chevron-left"></i>
        </a>
      <?php endif; ?>
      <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>&country=<?= urlencode($country) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>&country=<?= urlencode($country) ?>">
          <i class="fas fa-chevron-right"></i>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Confirmation de suppression d'un prospect -->
<dialog class="lead-del-dialog" id="leadDelDialog">
  <form method="POST" action="" id="leadDelForm">
    <?= csrf_field() ?>
    <input type="hidden" name="return_to" value="<?= e((string)($_SERVER['REQUEST_URI'] ?? '/admin/leads')) ?>">
    <div class="lead-del-head">
      <i class="fas fa-trash-can"></i>
      <h5>Supprimer ce prospect ?</h5>
    </div>
    <div class="lead-del-body">
      <div class="lead-del-who"><span id="leadDelName"></span><small id="leadDelCompany"></small></div>
      La fiche, ses champs personnalisés et ses documents joints seront supprimés <strong>définitivement</strong>.
      L'évaluation, le score et le compte utilisateur ne sont pas supprimés.
    </div>
    <div class="lead-del-actions">
      <button type="button" id="leadDelCancel">Annuler</button>
      <button type="submit" class="is-danger" id="leadDelSubmit"><i class="fas fa-trash-can"></i> Supprimer</button>
    </div>
  </form>
</dialog>
<script>
(function () {
  var dlg = document.getElementById('leadDelDialog');
  if (!dlg) return;
  var form = document.getElementById('leadDelForm');
  var submit = document.getElementById('leadDelSubmit');
  [].forEach.call(document.querySelectorAll('.js-lead-delete'), function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation(); e.preventDefault();
      form.action = '/admin/leads/delete/' + btn.getAttribute('data-id');
      document.getElementById('leadDelName').textContent = btn.getAttribute('data-name') || 'Prospect';
      document.getElementById('leadDelCompany').textContent = btn.getAttribute('data-company') || '';
      submit.disabled = false;
      submit.innerHTML = '<i class="fas fa-trash-can"></i> Supprimer';
      if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', '');
    });
  });
  document.getElementById('leadDelCancel').addEventListener('click', function () { dlg.close(); });
  dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
  form.addEventListener('submit', function () {
    submit.disabled = true;
    submit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Suppression...';
  });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
