<?php
$title = 'Gestion des Questions';
ob_start();
$typeLabels = [
    'rating_scale' => ['label' => 'Notation 1-5', 'color' => '#00d4ff'],
    'yes_no' => ['label' => 'Oui/Non', 'color' => '#00f5a0'],
    'multiple_choice' => ['label' => 'Choix multiple', 'color' => '#7c3aed'],
    'text_input' => ['label' => 'Texte libre', 'color' => '#ff8c00'],
    'numeric' => ['label' => 'Numérique', 'color' => '#ff3366'],
    'date_input' => ['label' => 'Date', 'color' => '#0891b2'],
];
$evalModels = [];
try {
    $evalModels = \App\Models\EvaluationModel::allActive();
    $evalModelsById = [];
    foreach ($evalModels as $em) $evalModelsById[$em['id']] = $em['name_fr'] ?: $em['name'];
} catch (\Exception $e) { $evalModelsById = []; }
?>
<style>
.auto-questions-header { margin-bottom: 1.5rem; }
.auto-questions-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-questions-header .auto-input,
.auto-questions-header .auto-select { font-size: 0.75rem; padding: 0.35rem 0.7rem; }
.auto-questions-header .auto-select { min-width: 140px; }
.auto-table .q-title-cell { color: var(--auto-text-primary); font-weight: 500; font-size: 0.78rem; }
.auto-table .q-title-cell small { color: var(--auto-text-muted); font-size: 0.65rem; }
.auto-table .q-title-cell .model-badge { font-size: 0.6rem; color: var(--auto-purple); }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-green); border-color: var(--auto-green); }
.auto-table .btn-action {
  width: 28px; height: 28px;
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
  font-size: 0.7rem;
}
.auto-table .btn-action:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-table .btn-action-danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
.auto-table .btn-action-duplicate:hover { border-color: var(--auto-purple-glow); color: var(--auto-purple); background: rgba(124,58,237,0.08); }
.auto-q-info-box {
  margin-top: 1.25rem;
  padding: 0.85rem 1rem;
  background: rgba(0,245,160,0.06);
  border: 1px solid rgba(0,245,160,0.15);
  border-radius: var(--auto-radius-sm);
}
.auto-q-info-box small { color: var(--auto-green); font-size: 0.7rem; font-weight: 500; }
.auto-q-info-box .type-chip {
  display: inline-block;
  padding: 0.15rem 0.5rem;
  border-radius: 100px;
  font-size: 0.6rem;
  font-weight: 600;
  margin: 0 0.25rem;
}
/* Modal dark */
.modal-content { background: var(--auto-bg-card-solid); border: 1px solid var(--auto-border); }
.modal-header { border-bottom-color: var(--auto-border); }
.modal-header h5 { color: var(--auto-text-primary); }
.modal-footer { border-top-color: var(--auto-border); }
.btn-close { filter: invert(0.8); }
@media (max-width: 768px) { .auto-questions-wrap { overflow-x: auto; } }
</style>

<div class="auto-questions-wrap auto-fade-in">
  <div class="auto-questions-header d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <input type="text" id="tableSearch" class="auto-input" placeholder="Rechercher..." style="width:200px;">
      <select id="filterType" class="auto-select">
        <option value="">Tous types</option>
        <option value="rating_scale">Notation 1-5</option>
        <option value="yes_no">Oui/Non</option>
        <option value="multiple_choice">Choix multiple</option>
        <option value="text_input">Texte libre</option>
        <option value="numeric">Numérique</option>
      </select>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="/admin/questions/export" class="auto-btn auto-btn-secondary auto-btn-sm"><i class="fas fa-file-export me-1"></i>Export</a>
      <button type="button" class="auto-btn auto-btn-secondary auto-btn-sm" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-file-import me-1"></i>Import</button>
      <a href="/admin/questions/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouvelle question</a>
    </div>
  </div>

  <!-- Import Modal -->
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="/admin/questions/import" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="modal-header"><h5 class="modal-title">Importer des questions (CSV)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="auto-label">Fichier CSV</label>
              <input type="file" name="import_file" class="auto-input" accept=".csv" required>
            </div>
            <small style="color:var(--auto-text-muted);font-size:0.7rem;">Format: Domaine, Question, Description, Poids, Ordre</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="auto-btn auto-btn-secondary auto-btn-sm" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="auto-btn auto-btn-primary auto-btn-sm">Importer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table" id="questionsTable">
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Question</th>
            <th>Type</th>
            <th>Domaine</th>
            <th class="text-center">Poids</th>
            <th class="text-center">Requis</th>
            <th class="text-center">Actif</th>
            <th class="text-center" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
              <?php
              $qt = $q['question_type'] ?? 'rating_scale';
              $tc = $typeLabels[$qt]['color'] ?? '#6b7280';
              $tl = $typeLabels[$qt]['label'] ?? $qt;
              $modelName = isset($q['model_id']) && isset($evalModelsById[$q['model_id']]) ? $evalModelsById[$q['model_id']] : '';
              ?>
              <tr data-type="<?= e($qt) ?>">
                <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $q['id'] ?></td>
                <td>
                  <div class="q-title-cell"><?= e($q['title_fr'] ?: $q['title']) ?></div>
                  <?php if ($modelName): ?>
                    <small class="model-badge"><i class="fas fa-layer-group me-1" style="font-size:0.55rem;"></i><?= e($modelName) ?></small>
                  <?php endif; ?>
                  <?php if ($q['description_fr']): ?>
                    <br><small style="color:var(--auto-text-muted);font-size:0.65rem;"><?= e(truncate($q['description_fr'], 60)) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="auto-badge" style="background:<?= $tc ?>18;border-color:<?= $tc ?>35;color:<?= $tc ?>;"><?= $tl ?></span>
                </td>
                <td><span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($q['domain_name_fr'] ?: $q['domain_name']) ?></span></td>
                <td class="text-center" style="color:var(--auto-text-secondary);font-family:var(--auto-font-mono);font-size:0.75rem;"><?= e($q['weight']) ?></td>
                <td class="text-center">
                  <?php if ($q['is_required'] ?? 1): ?>
                    <i class="fas fa-check-circle" style="color:var(--auto-green);"></i>
                  <?php else: ?>
                    <i class="fas fa-times-circle" style="color:var(--auto-text-muted);"></i>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="form-check form-switch d-inline-block" style="padding-left:2.5rem;">
                    <input class="form-check-input toggle-status" type="checkbox" data-id="<?= $q['id'] ?>" data-url="/admin/questions/toggle/<?= $q['id'] ?>" <?= $q['is_active'] ? 'checked' : '' ?>>
                  </div>
                </td>
                <td class="text-center">
                  <div style="display:flex;justify-content:center;gap:0.3rem;">
                    <a href="/admin/questions/edit/<?= $q['id'] ?>" class="btn-action" title="Modifier"><i class="fas fa-edit"></i></a>
                    <a href="/admin/questions/duplicate/<?= $q['id'] ?>" class="btn-action btn-action-duplicate" title="Dupliquer"><i class="fas fa-copy"></i></a>
                    <a href="/admin/questions/delete/<?= $q['id'] ?>" class="btn-action btn-action-danger" data-confirm="Supprimer cette question ?" title="Supprimer"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucune question trouvée</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="auto-q-info-box">
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <i class="fas fa-info-circle" style="color:var(--auto-green);"></i>
      <small>
        <strong>Types de questions disponibles :</strong>
        <span class="type-chip" style="background:#00d4ff18;color:#00d4ff;">Notation 1-5</span> Évaluation par niveau de maturité
        <span class="type-chip" style="background:#00f5a018;color:#00f5a0;">Oui/Non</span> Réponse binaire
        <span class="type-chip" style="background:#7c3aed18;color:#7c3aed;">Choix multiple</span> Options prédéfinies
        <span class="type-chip" style="background:#ff8c0018;color:#ff8c00;">Texte libre</span> Réponse ouverte
        <span class="type-chip" style="background:#ff336618;color:#ff3366;">Numérique</span> Valeur chiffrée
      </small>
    </div>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).ready(function() {
    $('#filterType').on('change', function() {
        var type = $(this).val();
        $('#questionsTable tbody tr[data-type]').each(function() {
            if (!type || $(this).data('type') === type) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).on('change', '.toggle-status', function() {
        var cb = $(this);
        var url = cb.data('url');
        $.post(url).fail(function() { location.reload(); });
    });
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>