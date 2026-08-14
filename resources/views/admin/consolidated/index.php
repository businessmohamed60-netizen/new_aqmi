<?php $title = 'Rapports Consolidés'; ob_start(); ?>
<div class="nova-card">
  <div class="nova-card-header">
    <h3><i class="fas fa-layer-group me-2"></i>Rapports Consolidés - Demandes en attente</h3>
  </div>
  <div class="nova-card-body">
    <?php if (empty($reports)): ?>
      <div class="nova-empty-state">
        <i class="fas fa-inbox"></i>
        <p>Aucune demande de rapport consolidé en attente.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Utilisateur</th>
              <th>Score</th>
              <th>Niveau</th>
              <th>Statut</th>
              <th>Demandé le</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reports as $r): ?>
              <tr>
                <td><strong><?= e($r['title']) ?></strong></td>
                <td><?= e($r['firstname'] . ' ' . $r['lastname']) ?><br><small class="text-muted"><?= e($r['email']) ?></small></td>
                <td><span class="badge bg-primary"><?= round($r['consolidated_score'] ?? 0) ?>/100</span></td>
                <td><?= e($r['maturity_level'] ?? '-') ?></td>
                <td>
                  <?php
                  $statusLabels = [
                    'certification_requested' => ['warning', 'En attente'],
                    'under_review' => ['info', 'En examen'],
                  ];
                  $sl = $statusLabels[$r['status']] ?? ['secondary', $r['status']];
                  ?>
                  <span class="badge bg-<?= $sl[0] ?>"><?= $sl[1] ?></span>
                </td>
                <td><?= $r['certification_requested_at'] ? date('d/m/Y H:i', strtotime($r['certification_requested_at'])) : '-' ?></td>
                <td>
                  <a href="/admin/consolidated/<?= $r['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Examiner
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="nova-card mt-4">
  <div class="nova-card-header">
    <h3><i class="fas fa-certificate me-2"></i>Rapports Consolidés Certifiés</h3>
  </div>
  <div class="nova-card-body">
    <?php if (empty($certified)): ?>
      <div class="nova-empty-state">
        <i class="fas fa-check-circle"></i>
        <p>Aucun rapport consolidé certifié pour le moment.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Numéro</th>
              <th>Titre</th>
              <th>Utilisateur</th>
              <th>Score</th>
              <th>Niveau</th>
              <th>Certifié le</th>
              <th>Validé par</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($certified as $r): ?>
              <tr>
                <td><code><?= e($r['report_number'] ?? '-') ?></code></td>
                <td><strong><?= e($r['title']) ?></strong></td>
                <td><?= e($r['firstname'] . ' ' . $r['lastname']) ?></td>
                <td><span class="badge bg-success"><?= round($r['consolidated_score'] ?? 0) ?>/100</span></td>
                <td><?= e($r['maturity_level'] ?? '-') ?></td>
                <td><?= $r['certified_at'] ? date('d/m/Y', strtotime($r['certified_at'])) : '-' ?></td>
                <td><?= e($r['validated_by'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
