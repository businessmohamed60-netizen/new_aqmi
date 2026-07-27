<?php
$title = "Modèles d'Évaluation";
ob_start();
?>
<style>
.auto-model-header { margin-bottom: 1.5rem; }
.auto-model-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-model-header p { color: var(--auto-text-muted); font-size: 0.75rem; margin-bottom: 0; margin-top: 0.15rem; }
.auto-model-card {
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  transition: var(--auto-transition);
  height: 100%;
}
.auto-model-card:hover { border-color: var(--auto-border-glow); box-shadow: var(--auto-shadow-glow); transform: translateY(-2px); }
.auto-model-card .model-icon {
  width: 48px; height: 48px;
  border-radius: var(--auto-radius-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem; flex-shrink: 0;
}
.auto-model-card h6 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.85rem; margin-bottom: 0.2rem; }
.auto-model-card .model-desc { color: var(--auto-text-muted); font-size: 0.7rem; }
.auto-model-card .model-stat { text-align: center; flex: 1; }
.auto-model-card .model-stat .num { font-weight: 700; font-size: 1.1rem; color: var(--auto-text-primary); font-family: var(--auto-font-mono); }
.auto-model-card .model-stat .lbl { font-size: 0.6rem; color: var(--auto-text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
.auto-model-card .card-actions { border-top: 1px solid var(--auto-border); margin: 0.75rem -1.25rem -1.25rem; padding: 0.75rem 1.25rem; display: flex; gap: 0.5rem; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.auto-model-empty {
  text-align: center; padding: 3rem 1rem;
  background: var(--auto-bg-card);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
}
.auto-model-empty i { font-size: 2.5rem; color: var(--auto-text-muted); margin-bottom: 1rem; }
.auto-model-empty h5 { color: var(--auto-text-primary); font-weight: 600; }
.auto-model-empty p { color: var(--auto-text-muted); font-size: 0.8rem; }
.auto-model-info {
  margin-top: 1.25rem;
  padding: 0.75rem 1rem;
  background: rgba(0,245,160,0.06);
  border: 1px solid rgba(0,245,160,0.15);
  border-radius: var(--auto-radius-sm);
}
.auto-model-info small { color: var(--auto-green); font-size: 0.7rem; font-weight: 500; }
</style>

<div class="auto-fade-in">
  <div class="auto-model-header d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
    <div>
      <h5><i class="fas fa-layer-group me-2" style="color:var(--auto-cyan);"></i>Modèles d'Évaluation</h5>
      <p>Créez et gérez plusieurs modèles d'évaluation</p>
    </div>
    <a href="/admin/evaluation-models/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouveau modèle</a>
  </div>

  <div class="row g-3">
    <?php if (!empty($models)): ?>
      <?php foreach ($models as $m): ?>
        <div class="col-md-6 col-lg-4">
          <div class="auto-model-card p-4">
            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="model-icon" style="background:<?= $m['color'] ?>15;color:<?= $m['color'] ?>;">
                <i class="fas <?= e($m['icon'] ?: 'fa-clipboard-check') ?>"></i>
              </div>
              <div style="flex:1;">
                <h6><?= e($m['name_fr'] ?: $m['name']) ?></h6>
                <?php if ($m['description_fr']): ?>
                  <div class="model-desc"><?= e(truncate($m['description_fr'], 100)) ?></div>
                <?php endif; ?>
              </div>
              <div class="form-check form-switch" style="padding-left:2.5rem;flex-shrink:0;">
                <input class="form-check-input toggle-model" type="checkbox" data-id="<?= $m['id'] ?>" <?= $m['is_active'] ? 'checked' : '' ?>>
              </div>
            </div>
            <div style="display:flex;gap:1.5rem;padding:1rem 0;border-top:1px solid var(--auto-border);border-bottom:1px solid var(--auto-border);margin-bottom:0.75rem;">
              <div class="model-stat"><div class="num"><?= $m['domains_count'] ?></div><div class="lbl">Domaines</div></div>
              <div class="model-stat"><div class="num"><?= $m['questions_count'] ?></div><div class="lbl">Questions</div></div>
            </div>
            <div style="display:flex;gap:0.5rem;">
              <a href="/admin/evaluation-models/edit/<?= $m['id'] ?>" class="auto-btn auto-btn-outline auto-btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-edit me-1"></i>Configurer</a>
              <a href="/admin/evaluation-models/delete/<?= $m['id'] ?>" class="auto-btn auto-btn-outline auto-btn-sm" style="flex:0;justify-content:center;" data-confirm="Supprimer ce modèle d'évaluation ?"><i class="fas fa-trash"></i></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="auto-model-empty">
          <i class="fas fa-layer-group"></i>
          <h5>Aucun modèle d'évaluation</h5>
          <p>Créez votre premier modèle d'évaluation pour structurer vos questionnaires.</p>
          <a href="/admin/evaluation-models/create" class="auto-btn auto-btn-primary mt-2"><i class="fas fa-plus me-1"></i>Créer un modèle</a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="auto-model-info">
    <div class="d-flex align-items-center gap-2">
      <i class="fas fa-info-circle"></i>
      <small>
        <strong>Conseil :</strong> Créez différents modèles d'évaluation pour couvrir plusieurs aspects (Qualité, Lean, Industrie 4.0, Sécurité). Chaque modèle peut avoir ses propres domaines et questions.
      </small>
    </div>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).on('change', '.toggle-model', function() {
    $.post('/admin/questions/toggle/' + $(this).data('id'), {});
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>