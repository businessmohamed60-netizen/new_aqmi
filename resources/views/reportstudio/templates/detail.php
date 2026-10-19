<?php
declare(strict_types=1);
/** @var array $template */
$t = $template['template'] ?? ($template ?? []);
$blocks = $template['blocks'] ?? [];
?>
<?php ob_start(); ?>
<style>
.rs-detail-page {
    --rsd-primary: #1F6FEB;
    --rsd-primary-light: #5B9DFF;
    --rsd-primary-dim: rgba(31,111,235,0.08);
    --rsd-accent: #06B6D4;
    --rsd-success: #10B981;
    --rsd-warning: #F59E0B;
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
.rs-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.rs-detail-header h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--rsd-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.rs-detail-header h2 i { color: var(--rsd-primary-light); }
.rs-detail-btn {
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
}
.rs-detail-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
}
.rs-detail-btn-primary:hover {
    background: #1858C4;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(31,111,235,0.3);
}
.rs-detail-card {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
}
.rs-detail-card-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--rsd-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.rs-detail-card-title i { color: var(--rsd-primary-light); }
.rs-detail-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.rs-detail-info-list li {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--rsd-border);
    font-size: 0.82rem;
}
.rs-detail-info-list li:last-child { border-bottom: none; }
.rs-detail-info-list .label { color: var(--rsd-text-muted); }
.rs-detail-info-list .value { color: var(--rsd-text); font-weight: 600; }
.rs-detail-block-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.rs-detail-block-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    transition: all var(--rsd-transition);
}
.rs-detail-block-item:hover {
    border-color: var(--rsd-primary-light);
}
.rs-detail-block-item i.grip {
    color: var(--rsd-text-dim);
    cursor: grab;
}
.rs-detail-block-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--rsd-text);
    flex: 1;
}
.rs-detail-block-key {
    font-size: 0.7rem;
    color: var(--rsd-text-dim);
    font-family: monospace;
}
.rs-detail-badge {
    font-size: 0.62rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
}
.rs-detail-badge-on { background: rgba(16,185,129,0.15); color: var(--rsd-success); }
.rs-detail-badge-off { background: rgba(148,163,184,0.12); color: var(--rsd-text-muted); }
</style>

<div class="rs-detail-page container-fluid py-4">
    <div class="rs-detail-header">
        <h2><i class="fas fa-file-lines"></i> <?= e($t['name'] ?? 'Modèle') ?></h2>
        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id'] ?? 0]) ?>" class="rs-detail-btn rs-detail-btn-primary">
            <i class="fas fa-tools"></i> Ouvrir le builder
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="rs-detail-card">
                <div class="rs-detail-card-title"><i class="fas fa-circle-info"></i> Informations</div>
                <p style="font-size:0.78rem;color:var(--rsd-text-muted);margin:0 0 1rem;"><?= e($t['description'] ?? 'Aucune description') ?></p>
                <ul class="rs-detail-info-list">
                    <li><span class="label">Statut</span><span class="value"><?= e($t['status'] ?? 'draft') ?></span></li>
                    <li><span class="label">Catégorie</span><span class="value"><?= e($t['category'] ?? '—') ?></span></li>
                    <li><span class="label">Blocs</span><span class="value"><?= count($blocks) ?></span></li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="rs-detail-card">
                <div class="rs-detail-card-title"><i class="fas fa-cubes"></i> Structure du rapport</div>
                <?php if (empty($blocks)): ?>
                    <div style="text-align:center;padding:2.5rem 1rem;color:var(--rsd-text-muted);">
                        <i class="fas fa-inbox" style="font-size:2rem;color:var(--rsd-text-dim);display:block;margin-bottom:0.75rem;"></i>
                        <p style="font-size:0.85rem;margin:0 0 1rem;">Aucun bloc — ouvrez le builder pour commencer.</p>
                        <a href="<?= route('reportstudio.builder.edit', ['id' => $t['id'] ?? 0]) ?>" class="rs-detail-btn rs-detail-btn-primary">
                            <i class="fas fa-plus"></i> Ajouter des blocs
                        </a>
                    </div>
                <?php else: ?>
                    <div class="rs-detail-block-list">
                        <?php foreach ($blocks as $b): ?>
                            <div class="rs-detail-block-item">
                                <i class="fas fa-grip-vertical grip"></i>
                                <span class="rs-detail-block-name"><?= e($b['title'] ?: $b['block_key']) ?></span>
                                <code class="rs-detail-block-key"><?= e($b['block_key']) ?></code>
                                <span class="rs-detail-badge <?= !empty($b['is_enabled']) ? 'rs-detail-badge-on' : 'rs-detail-badge-off' ?>">
                                    <?= !empty($b['is_enabled']) ? 'Actif' : 'Désactivé' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => $t['name'] ?? 'Détail du modèle',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
