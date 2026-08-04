<?php
declare(strict_types=1);
/** @var array|null $theme */
$th = $theme ?? [];
?>
<?php ob_start(); ?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-3"><i class="bi bi-<?= !empty($th) ? 'pencil' : 'plus-lg' ?>"></i> <?= !empty($th) ? 'Modifier le thème' : 'Nouveau thème' ?></h2>

            <form action="<?= !empty($th) ? route('reportstudio.themes.update', ['id' => $th['id'] ?? 0]) : route('reportstudio.themes.store') ?>" method="POST">
                <div class="card mb-3"><div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= e($th['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="<?= e($th['description'] ?? '') ?>">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Couleur primaire</label>
                            <input type="color" name="primary_color" class="form-control form-control-color" value="<?= e($th['primary_color'] ?? '#102A43') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Couleur secondaire</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color" value="<?= e($th['secondary_color'] ?? '#486581') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Couleur accent</label>
                            <input type="color" name="accent_color" class="form-control form-control-color" value="<?= e($th['accent_color'] ?? '#2EC4B6') ?>">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Couleur des titres</label>
                            <input type="color" name="heading_color" class="form-control form-control-color" value="<?= e($th['heading_color'] ?? '#1a237e') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Couleur du texte</label>
                            <input type="color" name="body_color" class="form-control form-control-color" value="<?= e($th['body_color'] ?? '#37474f') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Couleur de fond</label>
                            <input type="color" name="background_color" class="form-control form-control-color" value="<?= e($th['background_color'] ?? '#ffffff') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Police</label>
                        <input type="text" name="font_family" class="form-control" value="<?= e($th['font_family'] ?? 'Inter, Arial, sans-serif') ?>">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_default" id="th-default" <?= !empty($th['is_default']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="th-default">Thème par défaut</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="th-active" <?= (!isset($th['is_active']) || !empty($th['is_active'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="th-active">Actif</label>
                    </div>
                </div></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
                    <a href="<?= route('reportstudio.themes.index') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => 'Nouveau thème',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
