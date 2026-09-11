<?php
$currentLang = $_SESSION['lang'] ?? 'fr';
$title = $currentLang === 'en' ? 'Evaluation Models' : ($currentLang === 'ar' ? 'نماذج التقييم' : "Modèles d'Évaluation");
ob_start();
?>
<style>
/* ── Language switcher ── */
.ev-lang-switch {
  display: inline-flex;
  gap: 2px;
  background: var(--auto-bg-card-solid);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-sm);
  padding: 3px;
}
.ev-lang-switch a {
  padding: 0.3rem 0.7rem;
  font-size: 0.7rem;
  font-weight: 600;
  border-radius: 5px;
  color: var(--auto-text-muted);
  text-decoration: none;
  transition: var(--auto-transition);
  letter-spacing: 0.3px;
}
.ev-lang-switch a:hover { color: var(--auto-text-primary); background: var(--auto-border); }
.ev-lang-switch a.active {
  background: var(--auto-cyan);
  color: #fff;
  box-shadow: 0 2px 8px var(--auto-cyan-glow);
}

/* ── Page header ── */
.ev-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.75rem;
}
.ev-header-left h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--auto-text-primary);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.ev-header-left h2 .ev-icon-wrap {
  width: 42px; height: 42px;
  border-radius: var(--auto-radius-md);
  background: linear-gradient(135deg, var(--auto-cyan), var(--auto-cyan-dim));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.1rem;
  box-shadow: 0 4px 14px var(--auto-cyan-glow);
}
.ev-header-left p {
  color: var(--auto-text-muted);
  font-size: 0.82rem;
  margin: 0.35rem 0 0 0.1rem;
}
.ev-header-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

/* ── Stats bar ── */
.ev-stats-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.75rem;
  flex-wrap: wrap;
}
.ev-stat-pill {
  flex: 1;
  min-width: 140px;
  background: var(--auto-bg-card);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-md);
  padding: 0.9rem 1.1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  backdrop-filter: blur(20px);
  transition: var(--auto-transition);
}
.ev-stat-pill:hover { border-color: var(--auto-border-glow); transform: translateY(-1px); }
.ev-stat-pill .pill-icon {
  width: 38px; height: 38px;
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.95rem;
  flex-shrink: 0;
}
.ev-stat-pill .pill-val {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--auto-text-primary);
  font-family: var(--auto-font-mono);
  line-height: 1;
}
.ev-stat-pill .pill-lbl {
  font-size: 0.65rem;
  color: var(--auto-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-top: 0.2rem;
}

/* ── Model cards ── */
.ev-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.25rem; }

.ev-card {
  position: relative;
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  transition: var(--auto-transition);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.ev-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--card-accent, var(--auto-cyan));
  opacity: 0.7;
}
.ev-card:hover {
  border-color: var(--auto-border-glow);
  box-shadow: var(--auto-shadow-glow);
  transform: translateY(-3px);
}

.ev-card-body { padding: 1.35rem 1.35rem 0.85rem; flex: 1; }

.ev-card-top {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  margin-bottom: 1rem;
}
.ev-card-icon {
  width: 52px; height: 52px;
  border-radius: var(--auto-radius-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}
.ev-card-title-row {
  flex: 1;
  min-width: 0;
}
.ev-card-title-row h6 {
  color: var(--auto-text-primary);
  font-weight: 700;
  font-size: 0.92rem;
  margin: 0 0 0.15rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ev-card-title-row .ev-card-desc {
  color: var(--auto-text-muted);
  font-size: 0.75rem;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Toggle switch */
.ev-toggle-wrap { flex-shrink: 0; padding-left: 0.5rem; }
.ev-toggle {
  position: relative;
  width: 40px; height: 22px;
  cursor: pointer;
}
.ev-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
.ev-toggle .slider {
  position: absolute; inset: 0;
  background: var(--auto-border);
  border-radius: 100px;
  transition: var(--auto-transition);
}
.ev-toggle .slider::before {
  content: '';
  position: absolute;
  width: 16px; height: 16px;
  left: 3px; top: 3px;
  background: #fff;
  border-radius: 50%;
  transition: var(--auto-transition);
  box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.ev-toggle input:checked + .slider { background: var(--auto-cyan); }
.ev-toggle input:checked + .slider::before { transform: translateX(18px); }

/* Stats inside card */
.ev-card-stats {
  display: flex;
  gap: 0;
  padding: 0.85rem 0;
  border-top: 1px solid var(--auto-border);
  border-bottom: 1px solid var(--auto-border);
  margin-bottom: 0.85rem;
}
.ev-card-stat {
  flex: 1;
  text-align: center;
}
.ev-card-stat + .ev-card-stat { border-left: 1px solid var(--auto-border); }
.ev-card-stat .num {
  font-weight: 800;
  font-size: 1.15rem;
  color: var(--auto-text-primary);
  font-family: var(--auto-font-mono);
}
.ev-card-stat .lbl {
  font-size: 0.58rem;
  color: var(--auto-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 0.15rem;
}

/* Card actions */
.ev-card-actions {
  display: flex;
  gap: 0.5rem;
  padding: 0.85rem 1.35rem;
  background: rgba(0,0,0,0.02);
  border-top: 1px solid var(--auto-border);
  margin: 0 -1.35rem -0.85rem;
}

/* Status badge */
.ev-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.62rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 100px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}
.ev-status-active { background: rgba(46,196,182,0.12); color: var(--auto-green); }
.ev-status-inactive { background: rgba(125,135,148,0.12); color: var(--auto-text-muted); }

/* Empty state */
.ev-empty {
  text-align: center;
  padding: 4rem 1.5rem;
  background: var(--auto-bg-card);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  backdrop-filter: blur(20px);
}
.ev-empty-icon {
  width: 80px; height: 80px;
  margin: 0 auto 1.25rem;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem;
  background: var(--auto-cyan-dim);
  color: var(--auto-cyan);
}
.ev-empty h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 1.05rem; margin-bottom: 0.4rem; }
.ev-empty p { color: var(--auto-text-muted); font-size: 0.82rem; max-width: 380px; margin: 0 auto 1.5rem; }

/* Info tip */
.ev-tip {
  margin-top: 1.75rem;
  padding: 0.9rem 1.15rem;
  background: var(--auto-cyan-dim);
  border: 1px solid rgba(59,130,246,0.15);
  border-radius: var(--auto-radius-md);
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
}
.ev-tip i { color: var(--auto-cyan); font-size: 1rem; margin-top: 1px; }
.ev-tip small { color: var(--auto-text-secondary); font-size: 0.76rem; line-height: 1.5; }
.ev-tip strong { color: var(--auto-text-primary); }

/* Buttons */
.ev-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.55rem 1.1rem;
  font-size: 0.78rem;
  font-weight: 600;
  border-radius: var(--auto-radius-sm);
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: var(--auto-transition);
  white-space: nowrap;
}
.ev-btn-primary {
  background: var(--auto-cyan);
  color: #fff;
  box-shadow: 0 4px 14px var(--auto-cyan-glow);
}
.ev-btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 6px 20px var(--auto-cyan-glow); }
.ev-btn-outline {
  background: transparent;
  border: 1px solid var(--auto-border);
  color: var(--auto-text-secondary);
}
.ev-btn-outline:hover { border-color: var(--auto-cyan); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.ev-btn-danger {
  background: transparent;
  border: 1px solid var(--auto-border);
  color: var(--auto-red);
  width: 38px;
  padding: 0.55rem 0;
}
.ev-btn-danger:hover { background: rgba(244,63,94,0.08); border-color: var(--auto-red); }
.ev-btn-sm { padding: 0.45rem 0.85rem; font-size: 0.72rem; }

/* Animations */
@keyframes evFadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.ev-card { animation: evFadeUp 0.4s ease both; }
.ev-card:nth-child(2) { animation-delay: 0.06s; }
.ev-card:nth-child(3) { animation-delay: 0.12s; }
.ev-card:nth-child(4) { animation-delay: 0.18s; }
.ev-card:nth-child(5) { animation-delay: 0.24s; }
.ev-card:nth-child(6) { animation-delay: 0.30s; }
.ev-card:nth-child(n+7) { animation-delay: 0.36s; }

@media (max-width: 576px) {
  .ev-header-left h2 { font-size: 1.1rem; }
  .ev-grid { grid-template-columns: 1fr; }
}
</style>

<div class="auto-fade-in">
  <!-- Header -->
  <div class="ev-header">
    <div class="ev-header-left">
      <h2>
        <span class="ev-icon-wrap"><i class="fas fa-layer-group"></i></span>
        <?php if ($currentLang === 'en'): ?>Evaluation Models<?php elseif ($currentLang === 'ar'): ?>نماذج التقييم<?php else: ?>Modèles d'Évaluation<?php endif; ?>
      </h2>
      <p>
        <?php if ($currentLang === 'en'): ?>Create and manage multiple assessment models<?php elseif ($currentLang === 'ar'): ?>أنشئ وأدر عدة نماذج تقييم<?php else: ?>Créez et gérez plusieurs modèles d'évaluation<?php endif; ?>
      </p>
    </div>
    <div class="ev-header-right">
      <!-- Language switcher -->
      <div class="ev-lang-switch">
        <a href="/lang/fr" class="<?= $currentLang === 'fr' ? 'active' : '' ?>">FR</a>
        <a href="/lang/en" class="<?= $currentLang === 'en' ? 'active' : '' ?>">EN</a>
        <a href="/lang/ar" class="<?= $currentLang === 'ar' ? 'active' : '' ?>">AR</a>
      </div>
      <a href="/admin/evaluation-models/create" class="ev-btn ev-btn-primary ev-btn-sm">
        <i class="fas fa-plus"></i>
        <?php if ($currentLang === 'en'): ?>New Model<?php elseif ($currentLang === 'ar'): ?>نموذج جديد<?php else: ?>Nouveau modèle<?php endif; ?>
      </a>
    </div>
  </div>

  <!-- Stats bar -->
  <?php if (!empty($models)): ?>
    <?php
      $totalModels = count($models);
      $activeModels = count(array_filter($models, fn($m) => $m['is_active']));
      $totalDomains = array_sum(array_map(fn($m) => $m['domains_count'], $models));
      $totalQuestions = array_sum(array_map(fn($m) => $m['questions_count'], $models));
    ?>
    <div class="ev-stats-bar">
      <div class="ev-stat-pill">
        <div class="pill-icon" style="background:var(--auto-cyan-dim);color:var(--auto-cyan);"><i class="fas fa-layer-group"></i></div>
        <div>
          <div class="pill-val"><?= $totalModels ?></div>
          <div class="pill-lbl"><?= $currentLang === 'en' ? 'Models' : ($currentLang === 'ar' ? 'النماذج' : 'Modèles') ?></div>
        </div>
      </div>
      <div class="ev-stat-pill">
        <div class="pill-icon" style="background:rgba(46,196,182,0.1);color:var(--auto-green);"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="pill-val"><?= $activeModels ?></div>
          <div class="pill-lbl"><?= $currentLang === 'en' ? 'Active' : ($currentLang === 'ar' ? 'نشط' : 'Actifs') ?></div>
        </div>
      </div>
      <div class="ev-stat-pill">
        <div class="pill-icon" style="background:rgba(157,143,209,0.1);color:var(--auto-purple);"><i class="fas fa-folder"></i></div>
        <div>
          <div class="pill-val"><?= $totalDomains ?></div>
          <div class="pill-lbl"><?= $currentLang === 'en' ? 'Domains' : ($currentLang === 'ar' ? 'المجالات' : 'Domaines') ?></div>
        </div>
      </div>
      <div class="ev-stat-pill">
        <div class="pill-icon" style="background:rgba(245,158,11,0.1);color:var(--auto-yellow);"><i class="fas fa-question-circle"></i></div>
        <div>
          <div class="pill-val"><?= $totalQuestions ?></div>
          <div class="pill-lbl"><?= $currentLang === 'en' ? 'Questions' : ($currentLang === 'ar' ? 'الأسئلة' : 'Questions') ?></div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Cards grid -->
  <div class="ev-grid">
    <?php if (!empty($models)): ?>
      <?php foreach ($models as $m): ?>
        <?php
          $name = $currentLang === 'ar' ? ($m['name_ar'] ?: $m['name_fr'] ?: $m['name']) : ($currentLang === 'en' ? ($m['name'] ?: $m['name_fr']) : ($m['name_fr'] ?: $m['name']));
          $desc = $currentLang === 'ar' ? ($m['description_ar'] ?? '') : ($currentLang === 'en' ? ($m['description'] ?? '') : ($m['description_fr'] ?? ''));
          $cardColor = $m['color'] ?: '#1F6FEB';
        ?>
        <div class="ev-card" style="--card-accent: <?= e($cardColor) ?>;">
          <div class="ev-card-body">
            <div class="ev-card-top">
              <div class="ev-card-icon" style="background:<?= e($cardColor) ?>15;color:<?= e($cardColor) ?>;">
                <i class="fas <?= e($m['icon'] ?: 'fa-clipboard-check') ?>"></i>
              </div>
              <div class="ev-card-title-row">
                <h6><?= e($name) ?></h6>
                <?php if ($desc): ?>
                  <div class="ev-card-desc"><?= e(truncate($desc, 100)) ?></div>
                <?php endif; ?>
                <div style="margin-top:0.4rem;">
                  <?php if ($m['is_active']): ?>
                    <span class="ev-status-badge ev-status-active"><i class="fas fa-circle" style="font-size:0.4rem;"></i>
                      <?= $currentLang === 'en' ? 'Active' : ($currentLang === 'ar' ? 'نشط' : 'Actif') ?>
                    </span>
                  <?php else: ?>
                    <span class="ev-status-badge ev-status-inactive"><i class="fas fa-circle" style="font-size:0.4rem;"></i>
                      <?= $currentLang === 'en' ? 'Inactive' : ($currentLang === 'ar' ? 'غير نشط' : 'Inactif') ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="ev-toggle-wrap">
                <label class="ev-toggle">
                  <input type="checkbox" class="toggle-model" data-id="<?= $m['id'] ?>" <?= $m['is_active'] ? 'checked' : '' ?>>
                  <span class="slider"></span>
                </label>
              </div>
            </div>

            <div class="ev-card-stats">
              <div class="ev-card-stat">
                <div class="num"><?= $m['domains_count'] ?></div>
                <div class="lbl"><?= $currentLang === 'en' ? 'Domains' : ($currentLang === 'ar' ? 'المجالات' : 'Domaines') ?></div>
              </div>
              <div class="ev-card-stat">
                <div class="num"><?= $m['questions_count'] ?></div>
                <div class="lbl"><?= $currentLang === 'en' ? 'Questions' : ($currentLang === 'ar' ? 'الأسئلة' : 'Questions') ?></div>
              </div>
            </div>
          </div>

          <div class="ev-card-actions">
            <a href="/admin/evaluation-models/edit/<?= $m['id'] ?>" class="ev-btn ev-btn-outline ev-btn-sm" style="flex:1;">
              <i class="fas fa-sliders-h"></i>
              <?= $currentLang === 'en' ? 'Configure' : ($currentLang === 'ar' ? 'تكوين' : 'Configurer') ?>
            </a>
            <a href="/admin/evaluation-models/delete/<?= $m['id'] ?>" class="ev-btn ev-btn-danger ev-btn-sm" data-confirm="<?= $currentLang === 'en' ? 'Delete this evaluation model?' : ($currentLang === 'ar' ? 'حذف هذا النموذج؟' : 'Supprimer ce modèle d\'évaluation ?') ?>">
              <i class="fas fa-trash-alt"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ev-empty">
        <div class="ev-empty-icon"><i class="fas fa-layer-group"></i></div>
        <h5>
          <?= $currentLang === 'en' ? 'No Evaluation Models' : ($currentLang === 'ar' ? 'لا توجد نماذج تقييم' : 'Aucun modèle d\'évaluation') ?>
        </h5>
        <p>
          <?= $currentLang === 'en' ? 'Create your first evaluation model to structure your questionnaires.' : ($currentLang === 'ar' ? 'أنشئ أول نموذج تقييم لتنظيم استبياناتك.' : 'Créez votre premier modèle d\'évaluation pour structurer vos questionnaires.') ?>
        </p>
        <a href="/admin/evaluation-models/create" class="ev-btn ev-btn-primary">
          <i class="fas fa-plus"></i>
          <?= $currentLang === 'en' ? 'Create a Model' : ($currentLang === 'ar' ? 'إنشاء نموذج' : 'Créer un modèle') ?>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Info tip -->
  <div class="ev-tip">
    <i class="fas fa-lightbulb"></i>
    <small>
      <strong>
        <?= $currentLang === 'en' ? 'Tip:' : ($currentLang === 'ar' ? 'نصيحة:' : 'Conseil :') ?>
      </strong>
      <?= $currentLang === 'en'
        ? 'Create different evaluation models to cover multiple aspects (Quality, Lean, Industry 4.0, Safety). Each model can have its own domains and questions.'
        : ($currentLang === 'ar'
          ? 'أنشئ نماذج تقييم مختلفة لتغطية جوانب متعددة (الجودة، Lean، الصناعة 4.0، السلامة). كل نموذج يمكن أن يكون له مجالاته وأسئلته الخاصة.'
          : 'Créez différents modèles d\'évaluation pour couvrir plusieurs aspects (Qualité, Lean, Industrie 4.0, Sécurité). Chaque modèle peut avoir ses propres domaines et questions.') ?>
    </small>
  </div>
</div>

<?php
$extraScripts = <<<SCRIPTS
<script>
$(document).on('change', '.toggle-model', function() {
    $.post('/admin/evaluation-models/toggle/' + $(this).data('id'), {});
});
</script>
SCRIPTS;
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
