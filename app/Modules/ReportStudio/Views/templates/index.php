<?php
declare(strict_types=1);
/** @var array $templates */
$title = 'Modèles de rapport';
ob_start();
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-file-earmark-text"></i> Modèles de rapport</h2>
        <a href="<?= route('reportstudio.templates.create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th><th>Catégorie</th><th>Statut</th><th>Modifié</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($templates)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Aucun modèle</td></tr>
                <?php else: foreach ($templates as $t): ?>
                    <tr>
                        <td>
                            <strong><?= e($t['name'] ?? '') ?></strong>
                            <?php if (!empty($t['is_system'])): ?><span class="badge bg-info ms-1">Système</span><?php endif; ?>
                        </td>
                        <td><?= e($t['category'] ?? '—') ?></td>
                        <td><span class="badge bg-<?= ($t['status'] ?? '') === 'published' ? 'success' : (($t['status'] ?? '') === 'archived' ? 'danger' : 'secondary') ?>"><?= e($t['status'] ?? 'draft') ?></span></td>
                        <td class="small text-muted"><?= e($t['updated_at'] ?? '') ?></td>
                        <td class="text-end">
                            <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id'] ?? 0]) ?>" class="btn btn-sm btn-outline-primary" title="Builder"><i class="bi bi-gear"></i></a>
                            <a href="<?= route('reportstudio.templates.edit', ['id' => $t['id'] ?? 0]) ?>" class="btn btn-sm btn-outline-secondary" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <a href="<?= route('reportstudio.preview.show', ['id' => $t['id'] ?? 0]) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Aperçu"><i class="bi bi-eye"></i></a>
                            <form action="<?= route('reportstudio.templates.destroy', ['id' => $t['id'] ?? 0]) ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce modèle ?')">
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
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
