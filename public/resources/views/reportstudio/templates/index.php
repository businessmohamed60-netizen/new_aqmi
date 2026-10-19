<?php
declare(strict_types=1);
/** @var array $templates */
?>
<?php ob_start(); ?>
<style>
.rs-tpl-page {
    --rsd-primary: #2563eb;
    --rsd-primary-light: #60a5fa;
    --rsd-primary-dim: rgba(37,99,235,0.08);
    --rsd-accent: #0d9488;
    --rsd-success: #059669;
    --rsd-warning: #d97706;
    --rsd-danger: #dc2626;
    --rsd-surface: #ffffff;
    --rsd-surface-2: #f8fafc;
    --rsd-border: #e2e8f0;
    --rsd-text: #1e293b;
    --rsd-text-muted: #64748b;
    --rsd-text-dim: #94a3b8;
    --rsd-radius: 14px;
    --rsd-radius-sm: 10px;
    --rsd-shadow-lg: 0 8px 24px rgba(0,0,0,0.10);
    --rsd-transition: 200ms cubic-bezier(0.4,0,0.2,1);
    font-family: 'Inter', sans-serif;
    color: var(--rsd-text);
}
.rs-tpl-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.rs-tpl-header h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--rsd-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.rs-tpl-header h2 i { color: var(--rsd-primary-light); }
.rs-tpl-btn {
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
.rs-tpl-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
}
.rs-tpl-btn-primary:hover {
    background: #1858C4;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(31,111,235,0.3);
}
.rs-tpl-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}
.rs-tpl-card {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    overflow: hidden;
    transition: all var(--rsd-transition);
    position: relative;
}
.rs-tpl-card:hover {
    transform: translateY(-4px);
    border-color: var(--rsd-primary-light);
    box-shadow: var(--rsd-shadow-lg);
}
.rs-tpl-card-thumb {
    height: 130px;
    background: linear-gradient(135deg, var(--rsd-surface-2), var(--rsd-surface));
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.rs-tpl-card-thumb::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 40%, rgba(31,111,235,0.06) 100%);
}
.rs-tpl-card-thumb i {
    font-size: 2.2rem;
    color: var(--rsd-text-dim);
    z-index: 1;
}
.rs-tpl-card-status {
    position: absolute;
    top: 0.6rem; right: 0.6rem;
    font-size: 0.62rem;
    font-weight: 700;
    padding: 0.2rem 0.7rem;
    border-radius: 20px;
    z-index: 2;
}
.rs-tpl-card-status-published { background: rgba(5,150,105,0.10); color: var(--rsd-success); }
.rs-tpl-card-status-draft { background: rgba(100,116,139,0.10); color: var(--rsd-text-muted); }
.rs-tpl-card-status-archived { background: rgba(217,119,6,0.10); color: var(--rsd-warning); }
.rs-tpl-card-body { padding: 1rem 1.25rem; }
.rs-tpl-card-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--rsd-text);
    margin: 0 0 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.rs-tpl-card-cat {
    font-size: 0.7rem;
    color: var(--rsd-text-muted);
    margin: 0 0 0.75rem;
}
.rs-tpl-card-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.65rem;
    color: var(--rsd-text-dim);
    margin-bottom: 0.85rem;
}
.rs-tpl-card-actions {
    display: flex;
    gap: 0.4rem;
    padding: 0 1.25rem 1.25rem;
}
.rs-tpl-card-btn {
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
.rs-tpl-card-btn:hover {
    background: var(--rsd-primary-dim);
    color: var(--rsd-primary);
    border-color: var(--rsd-primary);
}
.rs-tpl-card-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
    border-color: var(--rsd-primary);
}
.rs-tpl-card-btn-primary:hover {
    background: #1858C4;
    color: #fff;
}
.rs-tpl-card-btn-danger:hover {
    background: rgba(220,38,38,0.08);
    color: var(--rsd-danger);
    border-color: var(--rsd-danger);
}
.rs-tpl-empty {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--rsd-text-muted);
}
.rs-tpl-empty i {
    font-size: 3rem;
    color: var(--rsd-text-dim);
    margin-bottom: 1rem;
    display: block;
}
.rs-tpl-empty p { font-size: 0.9rem; margin: 0 0 1.5rem; }
</style>

<div class="rs-tpl-page container-fluid py-4">
    <div class="rs-tpl-header">
        <h2><i class="fas fa-file-lines"></i> Modèles de rapport</h2>
        <a href="<?= route('reportstudio.templates.create') ?>" class="rs-tpl-btn rs-tpl-btn-primary">
            <i class="fas fa-plus"></i> Nouveau rapport
        </a>
    </div>

    <?php if (empty($templates)): ?>
        <div class="rs-tpl-empty">
            <i class="fas fa-inbox"></i>
            <p>Aucun modèle pour le moment. Créez votre premier rapport.</p>
            <a href="<?= route('reportstudio.templates.create') ?>" class="rs-tpl-btn rs-tpl-btn-primary">
                <i class="fas fa-plus"></i> Commencer
            </a>
        </div>
    <?php else: ?>
        <div class="rs-tpl-grid">
            <?php foreach ($templates as $t):
                $status = $t['status'] ?? 'draft';
                $statusClass = $status === 'published' ? 'rs-tpl-card-status-published'
                    : ($status === 'archived' ? 'rs-tpl-card-status-archived' : 'rs-tpl-card-status-draft');
                $statusLabel = $status === 'published' ? 'Publié'
                    : ($status === 'archived' ? 'Archivé' : 'Brouillon');
            ?>
                <div class="rs-tpl-card">
                    <div class="rs-tpl-card-thumb">
                        <i class="fas fa-file-lines"></i>
                        <span class="rs-tpl-card-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                        <?php if (!empty($t['is_system'])): ?>
                            <span style="position:absolute;top:0.6rem;left:0.6rem;font-size:0.6rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:20px;background:rgba(13,148,136,0.10);color:var(--rsd-accent);z-index:2;">
                                <i class="fas fa-lock me-1"></i>Système
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="rs-tpl-card-body">
                        <div class="rs-tpl-card-name"><?= e($t['name'] ?? 'Sans titre') ?></div>
                        <div class="rs-tpl-card-cat"><?= e($t['category'] ?? '—') ?></div>
                        <div class="rs-tpl-card-meta">
                            <i class="fas fa-clock"></i>
                            <span><?= e($t['updated_at'] ?? '—') ?></span>
                        </div>
                    </div>
                    <div class="rs-tpl-card-actions">
                        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id'] ?? 0]) ?>" class="rs-tpl-card-btn rs-tpl-card-btn-primary" title="Éditeur visuel">
                            <i class="fas fa-pen-to-square"></i> Éditer
                        </a>
                        <a href="<?= route('reportstudio.templates.edit', ['id' => $t['id'] ?? 0]) ?>" class="rs-tpl-card-btn" title="Paramètres">
                            <i class="fas fa-gear"></i>
                        </a>
                        <a href="<?= route('reportstudio.preview.show', ['id' => $t['id'] ?? 0]) ?>" target="_blank" class="rs-tpl-card-btn" title="Aperçu">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if (empty($t['is_system'])): ?>
                            <form action="<?= route('reportstudio.templates.destroy', ['id' => $t['id'] ?? 0]) ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce modèle de rapport ? Cette action est irréversible.')">
                                <button class="rs-tpl-card-btn rs-tpl-card-btn-danger" title="Supprimer">
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
    'title'       => 'Modèles de rapport',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
