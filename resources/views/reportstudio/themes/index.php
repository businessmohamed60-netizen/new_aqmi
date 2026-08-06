<?php
declare(strict_types=1);
/** @var array $themes */
?>
<?php ob_start(); ?>
<style>
.rs-themes-page {
    --rsd-primary: #4F46E5;
    --rsd-primary-light: #818CF8;
    --rsd-primary-dim: rgba(79,70,229,0.08);
    --rsd-accent: #06B6D4;
    --rsd-success: #10B981;
    --rsd-warning: #F59E0B;
    --rsd-danger: #EF4444;
    --rsd-surface: #1E293B;
    --rsd-surface-2: #334155;
    --rsd-border: rgba(148,163,184,0.12);
    --rsd-text: #F1F5F9;
    --rsd-text-muted: #94A3B8;
    --rsd-text-dim: #64748B;
    --rsd-radius: 14px;
    --rsd-radius-sm: 10px;
    --rsd-shadow-lg: 0 10px 40px rgba(0,0,0,0.4);
    --rsd-transition: 200ms cubic-bezier(0.4,0,0.2,1);
    font-family: 'Inter', sans-serif;
    color: var(--rsd-text);
}
.rs-themes-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.rs-themes-header h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--rsd-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.rs-themes-header h2 i { color: var(--rsd-primary-light); }
.rs-themes-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.4rem;
    border-radius: var(--rsd-radius-sm);
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all var(--rsd-transition);
    white-space: nowrap;
}
.rs-themes-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
}
.rs-themes-btn-primary:hover {
    background: #4338CA;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(79,70,229,0.3);
}
.rs-themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}
.rs-theme-card {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    overflow: hidden;
    transition: all var(--rsd-transition);
}
.rs-theme-card:hover {
    transform: translateY(-4px);
    border-color: var(--rsd-primary-light);
    box-shadow: var(--rsd-shadow-lg);
}
.rs-theme-swatch {
    height: 100px;
    position: relative;
    overflow: hidden;
}
.rs-theme-swatch::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.15) 100%);
}
.rs-theme-accent {
    position: absolute;
    bottom: 0.75rem; right: 0.75rem;
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.3);
    z-index: 1;
}
.rs-theme-body { padding: 1.1rem 1.25rem; }
.rs-theme-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--rsd-text);
    margin: 0 0 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.rs-theme-desc {
    font-size: 0.72rem;
    color: var(--rsd-text-muted);
    margin: 0 0 0.85rem;
}
.rs-theme-colors {
    display: flex;
    gap: 0.6rem;
    margin-bottom: 0.85rem;
}
.rs-theme-color-chip {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.65rem;
    color: var(--rsd-text-dim);
}
.rs-theme-color-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.1);
}
.rs-theme-actions {
    display: flex;
    gap: 0.4rem;
    padding: 0 1.25rem 1.25rem;
}
.rs-theme-action-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.45rem 0.7rem;
    font-size: 0.72rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all var(--rsd-transition);
    border: 1px solid var(--rsd-border);
    background: transparent;
    color: var(--rsd-text-muted);
}
.rs-theme-action-btn:hover {
    background: var(--rsd-primary-dim);
    color: var(--rsd-primary-light);
    border-color: var(--rsd-primary-light);
}
.rs-theme-action-btn-danger:hover {
    background: rgba(239,68,68,0.1);
    color: var(--rsd-danger);
    border-color: var(--rsd-danger);
}
.rs-theme-badge-default {
    font-size: 0.58rem;
    font-weight: 700;
    padding: 0.15rem 0.55rem;
    border-radius: 20px;
    background: rgba(6,182,212,0.12);
    color: var(--rsd-accent);
}
.rs-themes-empty {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--rsd-text-muted);
}
.rs-themes-empty i {
    font-size: 3rem;
    color: var(--rsd-text-dim);
    margin-bottom: 1rem;
    display: block;
}
.rs-themes-empty p { font-size: 0.9rem; margin: 0 0 1.5rem; }
</style>

<div class="rs-themes-page container-fluid py-4">
    <div class="rs-themes-header">
        <h2><i class="fas fa-palette"></i> Thèmes de rapport</h2>
        <a href="<?= route('reportstudio.themes.create') ?>" class="rs-themes-btn rs-themes-btn-primary">
            <i class="fas fa-plus"></i> Nouveau thème
        </a>
    </div>

    <?php if (empty($themes)): ?>
        <div class="rs-themes-empty">
            <i class="fas fa-palette"></i>
            <p>Aucun thème pour le moment. Créez votre premier thème.</p>
            <a href="<?= route('reportstudio.themes.create') ?>" class="rs-themes-btn rs-themes-btn-primary">
                <i class="fas fa-plus"></i> Créer un thème
            </a>
        </div>
    <?php else: ?>
        <div class="rs-themes-grid">
            <?php foreach ($themes as $th): ?>
                <div class="rs-theme-card">
                    <div class="rs-theme-swatch" style="background: linear-gradient(135deg, <?= e($th['primary_color'] ?? '#102A43') ?>, <?= e($th['secondary_color'] ?? '#486581') ?>);">
                        <span class="rs-theme-accent" style="background: <?= e($th['accent_color'] ?? '#2EC4B6') ?>"></span>
                    </div>
                    <div class="rs-theme-body">
                        <div class="rs-theme-name">
                            <?= e($th['name'] ?? '') ?>
                            <?php if (!empty($th['is_default'])): ?>
                                <span class="rs-theme-badge-default">Défaut</span>
                            <?php endif; ?>
                        </div>
                        <p class="rs-theme-desc"><?= e($th['description'] ?? 'Aucune description') ?></p>
                        <div class="rs-theme-colors">
                            <div class="rs-theme-color-chip">
                                <span class="rs-theme-color-dot" style="background: <?= e($th['primary_color'] ?? '') ?>"></span>
                                Primaire
                            </div>
                            <div class="rs-theme-color-chip">
                                <span class="rs-theme-color-dot" style="background: <?= e($th['secondary_color'] ?? '') ?>"></span>
                                Secondaire
                            </div>
                            <div class="rs-theme-color-chip">
                                <span class="rs-theme-color-dot" style="background: <?= e($th['accent_color'] ?? '') ?>"></span>
                                Accent
                            </div>
                        </div>
                    </div>
                    <div class="rs-theme-actions">
                        <a href="<?= route('reportstudio.themes.edit', ['id' => $th['id'] ?? 0]) ?>" class="rs-theme-action-btn">
                            <i class="fas fa-pen"></i> Modifier
                        </a>
                        <?php if (empty($th['is_default'])): ?>
                            <form action="<?= route('reportstudio.themes.destroy', ['id' => $th['id'] ?? 0]) ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce thème ?')">
                                <button class="rs-theme-action-btn rs-theme-action-btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => 'Thèmes de rapport',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
