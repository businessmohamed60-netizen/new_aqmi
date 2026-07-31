<?php
declare(strict_types=1);
/** @var array $themes */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-palette"></i> Thèmes</h2>
        <a href="<?= route('reportstudio.themes.create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau thème</a>
    </div>
    <div class="row g-3">
        <?php foreach ($themes as $th): ?>
            <div class="col-md-4">
                <div class="card rs-theme-card h-100">
                    <div class="rs-theme-swatch" style="background: linear-gradient(135deg, <?= e($th['primary_color'] ?? '#0d47a1') ?>, <?= e($th['secondary_color'] ?? '#546e7a') ?>);">
                        <span class="rs-theme-accent" style="background: <?= e($th['accent_color'] ?? '#00897b') ?>"></span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title"><?= e($th['name'] ?? '') ?>
                            <?php if (!empty($th['is_default'])): ?><span class="badge bg-info">Défaut</span><?php endif; ?>
                        </h6>
                        <p class="text-muted small mb-2"><?= e($th['description'] ?? '') ?></p>
                        <div class="d-flex gap-2 small text-muted mb-2">
                            <span style="color:<?= e($th['primary_color'] ?? '') ?>">● Primaire</span>
                            <span style="color:<?= e($th['secondary_color'] ?? '') ?>">● Secondaire</span>
                            <span style="color:<?= e($th['accent_color'] ?? '') ?>">● Accent</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex gap-2">
                        <a href="<?= route('reportstudio.themes.edit', ['id' => $th['id'] ?? 0]) ?>" class="btn btn-sm btn-outline-secondary flex-fill"><i class="bi bi-pencil"></i> Modifier</a>
                        <?php if (empty($th['is_default'])): ?>
                            <form action="<?= route('reportstudio.themes.destroy', ['id' => $th['id'] ?? 0]) ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce thème ?')">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
