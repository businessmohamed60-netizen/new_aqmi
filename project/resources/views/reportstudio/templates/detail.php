<?php
declare(strict_types=1);
/** @var array $template */
$t = $template['template'] ?? ($template ?? []);
$blocks = $template['blocks'] ?? [];
$title = $t['name'] ?? 'Détail du modèle';
ob_start();
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-file-earmark-text"></i> <?= e($t['name'] ?? '') ?></h2>
        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id'] ?? 0]) ?>" class="btn btn-primary">
            <i class="bi bi-gear"></i> Ouvrir le builder
        </a>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <h6>Informations</h6>
                <p class="text-muted small mb-1"><?= e($t['description'] ?? '') ?></p>
                <ul class="list-unstyled small mb-0">
                    <li><strong>Statut:</strong> <?= e($t['status'] ?? 'draft') ?></li>
                    <li><strong>Catégorie:</strong> <?= e($t['category'] ?? '—') ?></li>
                    <li><strong>Blocs:</strong> <?= count($blocks) ?></li>
                </ul>
            </div></div>
        </div>
        <div class="col-md-8">
            <div class="card"><div class="card-header">Structure du rapport</div>
                <div class="list-group list-group-flush">
                    <?php if (empty($blocks)): ?>
                        <div class="list-group-item text-muted text-center py-3">Aucun bloc — ouvrez le builder</div>
                    <?php else: foreach ($blocks as $b): ?>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-grip-vertical text-muted me-2"></i>
                            <span><?= e($b['title'] ?: $b['block_key']) ?></span>
                            <code class="ms-2 small text-muted"><?= e($b['block_key']) ?></code>
                            <span class="ms-auto badge bg-<?= !empty($b['is_enabled']) ? 'success' : 'secondary' ?>">
                                <?= !empty($b['is_enabled']) ? 'actif' : 'désactivé' ?>
                            </span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<link href="/assets/modules/reportstudio/css/report_studio.css" rel="stylesheet">';
\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'        => $title,
    'content'      => $content,
    'extraStyles'  => $extraStyles,
]);
