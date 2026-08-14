<?php $title = 'Détail Rapport Consolidé'; ob_start(); ?>
<div class="nova-card">
  <div class="nova-card-header">
    <h3><i class="fas fa-layer-group me-2"></i><?= e($report['title']) ?></h3>
  </div>
  <div class="nova-card-body">
    <div class="row mb-4">
      <div class="col-md-6">
        <p><strong>Utilisateur:</strong> <?= e($user['firstname'] . ' ' . $user['lastname']) ?> (<?= e($user['email']) ?>)</p>
        <p><strong>Score consolidé:</strong> <span class="badge bg-primary"><?= round($report['consolidated_score'] ?? 0) ?>/100</span></p>
        <p><strong>Niveau de maturité:</strong> <?= e($report['maturity_level'] ?? '-') ?></p>
        <p><strong>Statut:</strong>
          <?php
          $statusLabels = [
            'draft' => ['secondary', 'Brouillon'],
            'certification_requested' => ['warning', 'En attente'],
            'under_review' => ['info', 'En examen'],
            'approved' => ['info', 'Approuvé'],
            'rejected' => ['danger', 'Rejeté'],
            'certified' => ['success', 'Certifié'],
          ];
          $sl = $statusLabels[$report['status']] ?? ['secondary', $report['status']];
          ?>
          <span class="badge bg-<?= $sl[0] ?>"><?= $sl[1] ?></span>
        </p>
        <?php if (!empty($report['report_number'])): ?>
          <p><strong>Numéro:</strong> <code><?= e($report['report_number']) ?></code></p>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <p><strong>Créé le:</strong> <?= date('d/m/Y', strtotime($report['created_at'])) ?></p>
        <p><strong>Demande envoyée le:</strong> <?= $report['certification_requested_at'] ? date('d/m/Y H:i', strtotime($report['certification_requested_at'])) : '-' ?></p>
        <?php if ($report['certified_at']): ?>
          <p><strong>Certifié le:</strong> <?= date('d/m/Y H:i', strtotime($report['certified_at'])) ?></p>
          <p><strong>Validé par:</strong> <?= e($report['validated_by'] ?? '-') ?></p>
        <?php endif; ?>
      </div>
    </div>

    <h5 class="mb-3"><i class="fas fa-list me-2"></i>Évaluations incluses (<?= count($items) ?>)</h5>
    <div class="table-responsive mb-4">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Modèle</th>
            <th>Entreprise</th>
            <th>Score</th>
            <th>Niveau</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= e($item['model_name'] ?? '-') ?></td>
              <td><?= e($item['company'] ?? '-') ?></td>
              <td><strong><?= round($item['score'] ?? 0) ?>/100</strong></td>
              <td><?= e($item['maturity_level'] ?? '-') ?></td>
              <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if (in_array($report['status'], ['certification_requested', 'under_review', 'approved'])): ?>
      <div class="nova-card mt-4">
        <div class="nova-card-header">
          <h5><i class="fas fa-edit me-2"></i>Analyse et certification</h5>
        </div>
        <div class="nova-card-body">
          <form action="/admin/consolidated/<?= $report['id'] ?>/review" method="POST" class="mb-3">
            <div class="mb-3">
              <label class="form-label">Niveau AQMI attribué</label>
              <input type="text" name="aqmi_level_assigned" class="form-control" value="<?= e($report['aqmi_level_assigned'] ?? '') ?>" placeholder="Ex: Niveau 3 - Performant">
            </div>
            <div class="mb-3">
              <label class="form-label">Observations</label>
              <textarea name="observations" class="form-control" rows="3" placeholder="Observations sur l'évaluation globale..."><?= e($report['observations'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Plan d'action</label>
              <textarea name="action_plan" class="form-control" rows="3" placeholder="Plan d'action recommandé..."><?= e($report['action_plan'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Commentaire interne</label>
              <textarea name="admin_comment" class="form-control" rows="2" placeholder="Commentaire..."><?= e($report['admin_comment'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-save"></i> Enregistrer l'analyse
            </button>
          </form>

          <div class="d-flex gap-2 flex-wrap mt-3">
            <form action="/admin/consolidated/<?= $report['id'] ?>/approve" method="POST">
              <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-thumbs-up"></i> Approuver</button>
            </form>
            <form action="/admin/consolidated/<?= $report['id'] ?>/certify" method="POST">
              <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-certificate"></i> Certifier</button>
            </form>
            <form action="/admin/consolidated/<?= $report['id'] ?>/reject" method="POST" onsubmit="return confirm('Rejeter cette demande ?');">
              <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Rejeter</button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($report['admin_comment']) || !empty($report['observations']) || !empty($report['action_plan'])): ?>
      <div class="nova-card mt-4">
        <div class="nova-card-header">
          <h5><i class="fas fa-clipboard me-2"></i>Analyse enregistrée</h5>
        </div>
        <div class="nova-card-body">
          <?php if (!empty($report['aqmi_level_assigned'])): ?>
            <p><strong>Niveau AQMI:</strong> <?= e($report['aqmi_level_assigned']) ?></p>
          <?php endif; ?>
          <?php if (!empty($report['observations'])): ?>
            <p><strong>Observations:</strong><br><?= nl2br(e($report['observations'])) ?></p>
          <?php endif; ?>
          <?php if (!empty($report['action_plan'])): ?>
            <p><strong>Plan d'action:</strong><br><?= nl2br(e($report['action_plan'])) ?></p>
          <?php endif; ?>
          <?php if (!empty($report['admin_comment'])): ?>
            <p><strong>Commentaire:</strong><br><?= nl2br(e($report['admin_comment'])) ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<a href="/admin/consolidated" class="btn btn-outline-secondary btn-sm mt-3">
  <i class="fas fa-arrow-left"></i> Retour à la liste
</a>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
