<?php
declare(strict_types=1);
/**
 * Template create/edit form (metadata only — block layout is in the builder).
 * @var array|null $template
 * @var array      $themes
 */
$t = $template ?? [];
?>
<?php ob_start(); ?>
<style>
.rs-form-page {
    --rsd-primary: #4F46E5;
    --rsd-primary-light: #818CF8;
    --rsd-primary-dim: rgba(79,70,229,0.08);
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
.rs-form-input, .rs-form-select, .rs-form-textarea {
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
.rs-form-input:focus, .rs-form-select:focus, .rs-form-textarea:focus {
    outline: none;
    border-color: var(--rsd-primary-light);
    box-shadow: 0 0 0 3px var(--rsd-primary-dim);
}
.rs-form-input::placeholder { color: var(--rsd-text-dim); }
.rs-form-textarea { resize: vertical; min-height: 70px; }
.rs-form-select option { background: var(--rsd-surface); }
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
    background: #4338CA;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(79,70,229,0.3);
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
.rs-form-btn-success {
    background: #10B981;
    color: #fff;
}
.rs-form-btn-success:hover {
    background: #059669;
    color: #fff;
    transform: translateY(-2px);
}
</style>

<div class="rs-form-page container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="rs-form-header">
                <i class="fas fa-<?= !empty($t) ? 'pen' : 'plus' ?>"></i>
                <h2><?= !empty($t) ? 'Modifier le modèle' : 'Nouveau modèle' ?></h2>
            </div>

            <form action="<?= !empty($t) ? route('reportstudio.templates.update', ['id' => $t['id'] ?? 0]) : route('reportstudio.templates.store') ?>" method="POST">
                <div class="rs-form-card">
                    <div class="mb-3">
                        <label class="rs-form-label">Nom du modèle <span style="color:var(--rsd-primary-light);">*</span></label>
                        <input type="text" name="name" class="rs-form-input" required value="<?= e($t['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="rs-form-label">Description</label>
                        <textarea name="description" class="rs-form-textarea" rows="2"><?= e($t['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="rs-form-label">Catégorie</label>
                            <input type="text" name="category" class="rs-form-input" value="<?= e($t['category'] ?? '') ?>" placeholder="Audit, Diagnostic...">
                        </div>
                        <div class="col-md-4">
                            <label class="rs-form-label">Thème</label>
                            <select name="theme_id" class="rs-form-select">
                                <option value="">— Par défaut —</option>
                                <?php foreach ($themes as $th): ?>
                                    <option value="<?= e($th['id'] ?? '') ?>" <?= ((int)($t['theme_id'] ?? 0) === (int)($th['id'] ?? 0)) ? 'selected' : '' ?>>
                                        <?= e($th['name'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="rs-form-label">Statut</label>
                            <select name="status" class="rs-form-select">
                                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($t['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rs-form-actions">
                    <button type="submit" class="rs-form-btn rs-form-btn-primary">
                        <i class="fas fa-check"></i> Enregistrer
                    </button>
                    <a href="<?= route('reportstudio.templates.index') ?>" class="rs-form-btn rs-form-btn-ghost">Annuler</a>
                    <?php if (!empty($t)): ?>
                        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id']]) ?>" class="rs-form-btn rs-form-btn-success ms-auto">
                            <i class="fas fa-tools"></i> Ouvrir le builder
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => !empty($t) ? 'Modifier le modèle' : 'Nouveau modèle',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
