<?php
declare(strict_types=1);
/**
 * Template create/edit form (metadata only — block layout is in the builder).
 * @var array|null $template
 * @var array      $themes
 */
$t = $template ?? [];
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-3"><i class="bi bi-<?= !empty($t) ? 'pencil' : 'plus-lg' ?>"></i> <?= !empty($t) ? 'Modifier le modèle' : 'Nouveau modèle' ?></h2>

            <form action="<?= !empty($t) ? route('reportstudio.templates.update', ['id' => $t['id'] ?? 0]) : route('reportstudio.templates.store') ?>"
                  method="POST">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du modèle <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="<?= e($t['name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"><?= e($t['description'] ?? '') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Catégorie</label>
                                <input type="text" name="category" class="form-control" value="<?= e($t['category'] ?? '') ?>" placeholder="Audit, Diagnostic...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Thème</label>
                                <select name="theme_id" class="form-select">
                                    <option value="">— Par défaut —</option>
                                    <?php foreach ($themes as $th): ?>
                                        <option value="<?= e($th['id'] ?? '') ?>" <?= ((int)($t['theme_id'] ?? 0) === (int)($th['id'] ?? 0)) ? 'selected' : '' ?>>
                                            <?= e($th['name'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut</label>
                                <select name="status" class="form-select">
                                    <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($t['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
                    <a href="<?= route('reportstudio.templates.index') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <?php if (!empty($t)): ?>
                        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id']]) ?>" class="btn btn-success ms-auto">
                            <i class="bi bi-gear"></i> Ouvrir le builder
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
