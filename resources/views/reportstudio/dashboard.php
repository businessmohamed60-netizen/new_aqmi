<?php
declare(strict_types=1);
/**
 * Report Studio dashboard.
 * @var array $recent         recent ReportTemplate rows
 * @var int   $template_count
 * @var int   $published
 * @var int   $block_count
 * @var int   $theme_count
 */
?>
<?php ob_start(); ?>

<div class="container-fluid py-4">
    <?php if (!\App\Helpers\Database::isConnected()): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong>Base de données non connectée.</strong>
            Vérifiez les identifiants MySQL dans le fichier <code>.env</code> et que les tables Report Studio ont été importées (phpMyAdmin &gt; <code>report_studio_all_in_one.sql</code>).
        </div>
    </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-speedometer2"></i> AQMI Report Studio</h2>
            <p class="text-muted mb-0">Créez et publiez des rapports structurés</p>
        </div>
        <a href="<?= route('reportstudio.templates.create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau rapport
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card rs-stat-card"><div class="card-body">
                <i class="bi bi-file-earmark-text fs-2 text-primary"></i>
                <h3 class="mt-2 mb-0"><?= $template_count ?? 0 ?></h3><small class="text-muted">Modèles</small>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card rs-stat-card"><div class="card-body">
                <i class="bi bi-blocks fs-2 text-success"></i>
                <h3 class="mt-2 mb-0"><?= $block_count ?? 14 ?></h3><small class="text-muted">Blocs disponibles</small>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card rs-stat-card"><div class="card-body">
                <i class="bi bi-palette fs-2 text-info"></i>
                <h3 class="mt-2 mb-0"><?= $theme_count ?? 3 ?></h3><small class="text-muted">Thèmes</small>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card rs-stat-card"><div class="card-body">
                <i class="bi bi-check-circle fs-2 text-warning"></i>
                <h3 class="mt-2 mb-0"><?= $published ?? 0 ?></h3><small class="text-muted">Publiés</small>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history"></i> Modèles récents</span>
            <a href="<?= route('reportstudio.templates.index') ?>" class="btn btn-sm btn-link">Tout voir →</a>
        </div>
        <div class="card-body">
            <?php if (empty($recent)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p>Aucun modèle pour le moment</p>
                    <a href="<?= route('reportstudio.templates.create') ?>" class="btn btn-primary">Créer le premier</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($recent as $tpl): ?>
                        <div class="col-md-4">
                            <div class="card rs-tpl-card h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?= e($tpl['name'] ?? 'Sans titre') ?></h6>
                                    <p class="text-muted small mb-2"><?= e($tpl['description'] ?? '') ?></p>
                                    <span class="badge bg-<?= ($tpl['status'] ?? '') === 'published' ? 'success' : 'secondary' ?>">
                                        <?= e($tpl['status'] ?? 'draft') ?>
                                    </span>
                                </div>
                                <div class="card-footer bg-transparent d-flex gap-2">
                                    <a href="<?= route('reportstudio.builder.edit', ['id' => $tpl['id'] ?? 0]) ?>"
                                       class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-gear"></i> Modifier</a>
                                    <a href="<?= route('reportstudio.preview.show', ['id' => $tpl['id'] ?? 0]) ?>"
                                       target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => 'AQMI Report Studio',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
