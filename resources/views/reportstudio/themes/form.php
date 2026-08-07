<?php
declare(strict_types=1);
/** @var array|null $theme */
$th = $theme ?? [];
?>
<?php ob_start(); ?>
<style>
.rs-form-page {
    --rsd-primary: #1F6FEB;
    --rsd-primary-light: #5B9DFF;
    --rsd-primary-dim: rgba(31,111,235,0.08);
    --rsd-surface: #1E293B;
    --rsd-surface-2: #334155;
    --rsd-border: rgba(148,163,184,0.12);
    --rsd-text: #F1F5F9;
    --rsd-text-muted: #94A3B8;
    --rsd-text-dim: #64748B;
    --rsd-radius: 14px;
    --rsd-radius-sm: 10px;
    --rsd-transition: 200ms cubic-bezier(0.4,0,0.2,1);
    font-family: 'Inter', sans-serif;
    color: var(--rsd-text);
}
.rs-form-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 1.5rem;
}
.rs-form-header h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--rsd-text);
    margin: 0;
}
.rs-form-header i { color: var(--rsd-primary-light); }
.rs-form-card {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}
.rs-form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--rsd-text-muted);
    margin-bottom: 0.35rem;
    display: block;
}
.rs-form-input, .rs-form-select {
    width: 100%;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    color: var(--rsd-text);
    padding: 0.6rem 0.85rem;
    font-size: 0.82rem;
    font-family: 'Inter', sans-serif;
    transition: all var(--rsd-transition);
}
.rs-form-input:focus, .rs-form-select:focus {
    outline: none;
    border-color: var(--rsd-primary-light);
    box-shadow: 0 0 0 3px var(--rsd-primary-dim);
}
.rs-form-input::placeholder { color: var(--rsd-text-dim); }
.rs-color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.rs-color-field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.rs-color-swatch {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    padding: 0.5rem 0.75rem;
}
.rs-color-swatch input[type=color] {
    width: 36px; height: 36px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: transparent;
    padding: 0;
}
.rs-color-swatch input[type=color]::-webkit-color-swatch {
    border-radius: 6px;
    border: 1px solid var(--rsd-border);
}
.rs-color-hex {
    font-size: 0.72rem;
    color: var(--rsd-text-dim);
    font-family: monospace;
}
.rs-form-switch {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.5rem 0;
}
.rs-form-switch input {
    width: 18px; height: 18px;
    accent-color: var(--rsd-primary);
    cursor: pointer;
}
.rs-form-switch label {
    font-size: 0.8rem;
    color: var(--rsd-text);
    cursor: pointer;
    margin: 0;
}
.rs-form-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.rs-form-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.6rem 1.4rem;
    border-radius: var(--rsd-radius-sm);
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all var(--rsd-transition);
}
.rs-form-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
}
.rs-form-btn-primary:hover {
    background: #1858C4;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(31,111,235,0.3);
}
.rs-form-btn-ghost {
    background: transparent;
    color: var(--rsd-text-muted);
    border: 1px solid var(--rsd-border);
}
.rs-form-btn-ghost:hover {
    color: var(--rsd-text);
    border-color: var(--rsd-text-dim);
}
</style>

<div class="rs-form-page container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="rs-form-header">
                <i class="fas fa-<?= !empty($th) ? 'pen' : 'plus' ?>"></i>
                <h2><?= !empty($th) ? 'Modifier le thème' : 'Nouveau thème' ?></h2>
            </div>

            <form action="<?= !empty($th) ? route('reportstudio.themes.update', ['id' => $th['id'] ?? 0]) : route('reportstudio.themes.store') ?>" method="POST">
                <div class="rs-form-card">
                    <div class="mb-3">
                        <label class="rs-form-label">Nom <span style="color:var(--rsd-primary-light);">*</span></label>
                        <input type="text" name="name" class="rs-form-input" required value="<?= e($th['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="rs-form-label">Description</label>
                        <input type="text" name="description" class="rs-form-input" value="<?= e($th['description'] ?? '') ?>">
                    </div>

                    <label class="rs-form-label" style="margin-bottom:0.75rem;">Palette de couleurs</label>
                    <div class="rs-color-grid">
                        <div class="rs-color-field">
                            <label class="rs-form-label">Primaire</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="primary_color" value="<?= e($th['primary_color'] ?? '#102A43') ?>">
                                <span class="rs-color-hex"><?= e($th['primary_color'] ?? '#102A43') ?></span>
                            </div>
                        </div>
                        <div class="rs-color-field">
                            <label class="rs-form-label">Secondaire</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="secondary_color" value="<?= e($th['secondary_color'] ?? '#486581') ?>">
                                <span class="rs-color-hex"><?= e($th['secondary_color'] ?? '#486581') ?></span>
                            </div>
                        </div>
                        <div class="rs-color-field">
                            <label class="rs-form-label">Accent</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="accent_color" value="<?= e($th['accent_color'] ?? '#2EC4B6') ?>">
                                <span class="rs-color-hex"><?= e($th['accent_color'] ?? '#2EC4B6') ?></span>
                            </div>
                        </div>
                        <div class="rs-color-field">
                            <label class="rs-form-label">Titres</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="heading_color" value="<?= e($th['heading_color'] ?? '#1a237e') ?>">
                                <span class="rs-color-hex"><?= e($th['heading_color'] ?? '#1a237e') ?></span>
                            </div>
                        </div>
                        <div class="rs-color-field">
                            <label class="rs-form-label">Texte</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="body_color" value="<?= e($th['body_color'] ?? '#37474f') ?>">
                                <span class="rs-color-hex"><?= e($th['body_color'] ?? '#37474f') ?></span>
                            </div>
                        </div>
                        <div class="rs-color-field">
                            <label class="rs-form-label">Fond</label>
                            <div class="rs-color-swatch">
                                <input type="color" name="background_color" value="<?= e($th['background_color'] ?? '#ffffff') ?>">
                                <span class="rs-color-hex"><?= e($th['background_color'] ?? '#ffffff') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="rs-form-label">Police</label>
                        <input type="text" name="font_family" class="rs-form-input" value="<?= e($th['font_family'] ?? 'Inter, Arial, sans-serif') ?>">
                    </div>

                    <div class="rs-form-switch">
                        <input class="form-check-input" type="checkbox" name="is_default" id="th-default" <?= !empty($th['is_default']) ? 'checked' : '' ?>>
                        <label for="th-default">Thème par défaut</label>
                    </div>
                    <div class="rs-form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="th-active" <?= (!isset($th['is_active']) || !empty($th['is_active'])) ? 'checked' : '' ?>>
                        <label for="th-active">Actif</label>
                    </div>
                </div>

                <div class="rs-form-actions">
                    <button type="submit" class="rs-form-btn rs-form-btn-primary">
                        <i class="fas fa-check"></i> Enregistrer
                    </button>
                    <a href="<?= route('reportstudio.themes.index') ?>" class="rs-form-btn rs-form-btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => !empty($th) ? 'Modifier le thème' : 'Nouveau thème',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
