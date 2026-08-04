<?php
$title = 'Gestion des Leads';
ob_start();
$totalPages = max(1, ceil($total / $perPage));
?>
<style>
.auto-leads-header { margin-bottom: 1.5rem; }
.auto-leads-header .auto-input,
.auto-leads-header .auto-select { font-size: 0.75rem; padding: 0.35rem 0.7rem; }
.auto-leads-header .auto-select { min-width: 140px; }
.auto-table .lead-name { color: var(--auto-text-primary); font-weight: 600; font-size: 0.78rem; }
.auto-table .lead-email { color: var(--auto-cyan); font-size: 0.7rem; text-decoration: none; }
.auto-table .lead-email:hover { text-decoration: underline; }
.auto-table .btn-action {
  width: 30px; height: 30px;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 0.75rem;
}
.auto-table .btn-action:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-pagination { display:flex;justify-content:center;gap:0.35rem;margin-top:1rem; }
.auto-pagination a {
  padding:0.35rem 0.7rem; border-radius:var(--auto-radius-sm);
  border:1px solid var(--auto-border); background:transparent;
  color:var(--auto-text-secondary); text-decoration:none;
  font-size:0.75rem; transition:var(--auto-transition);
}
.auto-pagination a:hover { border-color:var(--auto-cyan-glow); color:var(--auto-cyan); background:var(--auto-cyan-dim); }
.auto-pagination a.active { background:var(--auto-cyan); color:#080c18; border-color:var(--auto-cyan); font-weight:700; }
@media (max-width: 768px) { .auto-leads-wrap { overflow-x: auto; } .auto-leads-header { flex-direction: column; align-items: stretch; } }
</style>

<div class="auto-leads-wrap auto-fade-in">
  <div class="auto-leads-header d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
      <input type="text" name="search" class="auto-input" placeholder="Rechercher..." value="<?= e($search) ?>" style="width:180px;">
      <select name="sector" class="auto-select">
        <option value="">Tous secteurs</option>
        <?php foreach ($sectors as $s): ?>
          <option value="<?= e($s['sector']) ?>" <?= $sector === $s['sector'] ? 'selected' : '' ?>><?= e($s['sector']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="country" class="auto-select">
        <option value="">Tous pays</option>
        <?php foreach ($countries as $c): ?>
          <option value="<?= e($c['country']) ?>" <?= $country === $c['country'] ? 'selected' : '' ?>><?= e($c['country']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="auto-btn auto-btn-secondary auto-btn-sm"><i class="fas fa-search"></i></button>
      <a href="/admin/leads" class="auto-btn auto-btn-secondary auto-btn-sm"><i class="fas fa-times"></i></a>
    </form>
    <a href="/admin/leads/export" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-file-export me-1"></i>Export CSV</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr><th>#</th><th>Nom</th><th>Entreprise</th><th>Secteur</th><th>Email</th><th>Pays</th><th>Score</th><th>Date</th><th class="text-center">Actions</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($leads)): ?>
            <?php foreach ($leads as $l): ?>
              <tr>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $l['id'] ?></td>
                <td><span class="lead-name"><?= e($l['firstname'] . ' ' . $l['lastname']) ?></span></td>
                <td style="color:var(--auto-text-secondary);font-size:0.78rem;"><?= e($l['company']) ?></td>
                <td><span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($l['sector'] ?? '-') ?></span></td>
                <td><a href="mailto:<?= e($l['email']) ?>" class="lead-email"><?= e($l['email']) ?></a></td>
                <td style="color:var(--auto-text-muted);font-size:0.75rem;"><?= e($l['country'] ?? '-') ?></td>
                <td><span class="auto-badge auto-badge-green" style="font-size:0.6rem;"><?= e($l['total_score'] ?? '-') ?>%</span></td>
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= formatDate($l['created_at']) ?></td>
                <td class="text-center">
                  <a href="/admin/leads/detail/<?= $l['id'] ?>" class="btn-action" title="Voir la fiche"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="9" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucun lead trouvé</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="auto-pagination">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>&country=<?= urlencode($country) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>