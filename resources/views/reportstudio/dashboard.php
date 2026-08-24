<?php
declare(strict_types=1);
/**
 * Report Studio dashboard — Elementor-style.
 * @var array $recent
 * @var int   $template_count
 * @var int   $published
 * @var int   $block_count
 * @var int   $theme_count
 */
$blockGroups = \App\Modules\ReportStudio\Services\BlockRegistry::grouped();
$totalBlocks = \App\Modules\ReportStudio\Services\BlockRegistry::all();
$draftCount = ($template_count ?? 0) - ($published ?? 0);
?>
<?php ob_start(); ?>

<style>
/* ============================================================
   Report Studio Dashboard — Elementor-inspired UI
   ============================================================ */
.rs-dash {
    --rsd-primary: #2563eb;
    --rsd-primary-light: #60a5fa;
    --rsd-primary-dim: rgba(37,99,235,0.08);
    --rsd-accent: #0d9488;
    --rsd-success: #059669;
    --rsd-warning: #d97706;
    --rsd-danger: #dc2626;
    --rsd-bg: #f1f5f9;
    --rsd-surface: #ffffff;
    --rsd-surface-2: #f8fafc;
    --rsd-border: #e2e8f0;
    --rsd-text: #1e293b;
    --rsd-text-muted: #64748b;
    --rsd-text-dim: #94a3b8;
    --rsd-radius: 14px;
    --rsd-radius-sm: 10px;
    --rsd-shadow: 0 1px 3px rgba(0,0,0,0.08);
    --rsd-shadow-lg: 0 8px 24px rgba(0,0,0,0.10);
    --rsd-transition: 200ms cubic-bezier(0.4,0,0.2,1);
}

.rs-dash {
    font-family: 'Inter', sans-serif;
    color: var(--rsd-text);
    padding: 0;
}

/* Hero Banner */
.rs-dash-hero {
    background: linear-gradient(135deg, #1F6FEB 0%, #2EC4B6 50%, #06B6D4 100%);
    border-radius: var(--rsd-radius);
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 32px rgba(37,99,235,0.20);
}
.rs-dash-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.rs-dash-hero::after {
    content: '';
    position: absolute;
    bottom: -30%; left: 20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(13,148,136,0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.rs-dash-hero-content { position: relative; z-index: 1; }
.rs-dash-hero h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 0.35rem;
    letter-spacing: -0.5px;
}
.rs-dash-hero p {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.85);
    margin: 0 0 1.5rem;
    max-width: 500px;
}
.rs-dash-hero-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.rs-dash-btn {
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
.rs-dash-btn-light {
    background: #fff;
    color: var(--rsd-primary);
}
.rs-dash-btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255,255,255,0.4);
}
.rs-dash-btn-ghost {
    background: rgba(255,255,255,0.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
}
.rs-dash-btn-ghost:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

/* KPI Cards */
.rs-dash-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.rs-dash-kpi {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all var(--rsd-transition);
}
.rs-dash-kpi:hover {
    transform: translateY(-3px);
    box-shadow: var(--rsd-shadow-lg);
    border-color: rgba(37,99,235,0.2);
}
.rs-dash-kpi-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
}
.rs-dash-kpi-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--rsd-text);
    line-height: 1;
    margin-bottom: 0.25rem;
}
.rs-dash-kpi-label {
    font-size: 0.72rem;
    color: var(--rsd-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.rs-dash-kpi-trend {
    position: absolute;
    top: 1.25rem; right: 1.5rem;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
}

/* Two-column layout */
.rs-dash-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 992px) {
    .rs-dash-grid { grid-template-columns: 1fr; }
}

/* Section Card */
.rs-dash-card {
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius);
    overflow: hidden;
}
.rs-dash-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--rsd-border);
}
.rs-dash-card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--rsd-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}
.rs-dash-card-title i { color: var(--rsd-primary); }
.rs-dash-card-body { padding: 1.25rem 1.5rem; }

/* Template Cards */
.rs-tpl-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
}
.rs-tpl-item {
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    overflow: hidden;
    transition: all var(--rsd-transition);
    position: relative;
}
.rs-tpl-item:hover {
    transform: translateY(-3px);
    border-color: var(--rsd-primary);
    box-shadow: 0 8px 24px rgba(37,99,235,0.12);
}
.rs-tpl-thumb {
    height: 120px;
    background: linear-gradient(135deg, var(--rsd-surface-2), #eef2f7);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.rs-tpl-thumb::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 40%, rgba(37,99,235,0.05) 100%);
}
.rs-tpl-thumb i {
    font-size: 2rem;
    color: #94a3b8;
    z-index: 1;
}
.rs-tpl-status {
    position: absolute;
    top: 0.5rem; right: 0.5rem;
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.6rem;
    border-radius: 20px;
    z-index: 2;
}
.rs-tpl-status-published { background: rgba(5,150,105,0.10); color: var(--rsd-success); }
.rs-tpl-status-draft { background: rgba(100,116,139,0.10); color: var(--rsd-text-muted); }
.rs-tpl-status-archived { background: rgba(217,119,6,0.10); color: var(--rsd-warning); }
.rs-tpl-info { padding: 0.85rem 1rem; }
.rs-tpl-name {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--rsd-text);
    margin: 0 0 0.2rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.rs-tpl-desc {
    font-size: 0.68rem;
    color: var(--rsd-text-muted);
    margin: 0 0 0.6rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.rs-tpl-actions { display: flex; gap: 0.4rem; }
.rs-tpl-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.4rem 0.6rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all var(--rsd-transition);
    border: 1px solid var(--rsd-border);
    background: transparent;
    color: var(--rsd-text-muted);
}
.rs-tpl-btn:hover {
    background: var(--rsd-primary-dim);
    color: var(--rsd-primary);
    border-color: var(--rsd-primary);
}
.rs-tpl-btn-primary {
    background: var(--rsd-primary);
    color: #fff;
    border-color: var(--rsd-primary);
}
.rs-tpl-btn-primary:hover {
    background: #1858C4;
    color: #fff;
    transform: translateY(-1px);
}
.rs-tpl-btn-danger {
    color: var(--rsd-danger);
    border-color: var(--rsd-border);
}
.rs-tpl-btn-danger:hover {
    background: rgba(220,38,38,0.08);
    color: var(--rsd-danger);
    border-color: var(--rsd-danger);
}
.rs-tpl-delete-form {
    flex: 0 0 auto;
    display: inline-flex;
}
.rs-tpl-delete-form .rs-tpl-btn {
    flex: none;
    padding: 0.4rem 0.55rem;
}

/* Empty state */
.rs-dash-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--rsd-text-muted);
}
.rs-dash-empty i {
    font-size: 2.5rem;
    color: var(--rsd-text-dim);
    margin-bottom: 0.75rem;
    display: block;
}
.rs-dash-empty p { font-size: 0.85rem; margin: 0 0 1rem; }

/* Block Library Preview */
.rs-block-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 420px;
    overflow-y: auto;
}
.rs-block-cat {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--rsd-text-dim);
    padding: 0.5rem 0 0.25rem;
}
.rs-block-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: 8px;
    font-size: 0.75rem;
    color: var(--rsd-text);
    transition: all var(--rsd-transition);
}
.rs-block-chip:hover {
    border-color: var(--rsd-primary);
    background: var(--rsd-primary-dim);
}
.rs-block-chip i {
    width: 28px; height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(37,99,235,0.08);
    color: var(--rsd-primary);
    font-size: 0.8rem;
}

/* Quick Actions */
.rs-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.75rem;
}
.rs-qa-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.25rem 0.75rem;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    text-decoration: none;
    color: var(--rsd-text);
    transition: all var(--rsd-transition);
}
.rs-qa-item:hover {
    transform: translateY(-3px);
    border-color: var(--rsd-primary);
    box-shadow: 0 8px 24px rgba(37,99,235,0.10);
    color: var(--rsd-text);
}
.rs-qa-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 0.6rem;
}
.rs-qa-label {
    font-size: 0.75rem;
    font-weight: 600;
}
.rs-qa-sub {
    font-size: 0.62rem;
    color: var(--rsd-text-dim);
    margin-top: 0.15rem;
}

/* DB warning */
.rs-dash-alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: rgba(217,119,6,0.06);
    border: 1px solid rgba(217,119,6,0.25);
    border-radius: var(--rsd-radius);
    margin-bottom: 1.5rem;
    color: var(--rsd-warning);
    font-size: 0.8rem;
}
.rs-dash-alert i { font-size: 1.2rem; }
</style>

<div class="rs-dash container-fluid py-4">

    <?php if (!\App\Helpers\Database::isConnected()): ?>
    <div class="rs-dash-alert">
        <i class="fas fa-triangle-exclamation"></i>
        <div>
            <strong>Base de données non connectée.</strong>
            Vérifiez les identifiants MySQL dans le fichier <code>.env</code> et que les tables Report Studio ont été importées.
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Banner -->
    <div class="rs-dash-hero">
        <div class="rs-dash-hero-content">
            <h1><i class="fas fa-palette me-2"></i>AQMI Report Studio</h1>
            <p>Concevez et publiez des rapports de certification professionnels. Glissez-déposez des blocs, personnalisez les thèmes, et générez des PDF prêts à imprimer.</p>
            <div class="rs-dash-hero-actions">
                <a href="<?= route('reportstudio.templates.create') ?>" class="rs-dash-btn rs-dash-btn-light">
                    <i class="fas fa-plus"></i> Nouveau rapport
                </a>
                <a href="<?= route('reportstudio.themes.index') ?>" class="rs-dash-btn rs-dash-btn-ghost">
                    <i class="fas fa-palette"></i> Gérer les thèmes
                </a>
                <a href="<?= route('reportstudio.templates.index') ?>" class="rs-dash-btn rs-dash-btn-ghost">
                    <i class="fas fa-list"></i> Tous les modèles
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="rs-dash-kpis">
        <div class="rs-dash-kpi">
            <div class="rs-dash-kpi-icon" style="background:rgba(37,99,235,0.08);color:var(--rsd-primary);">
                <i class="fas fa-file-lines"></i>
            </div>
            <div class="rs-dash-kpi-value"><?= $template_count ?? 0 ?></div>
            <div class="rs-dash-kpi-label">Modèles</div>
            <div class="rs-dash-kpi-trend" style="background:rgba(37,99,235,0.08);color:var(--rsd-primary);">
                <?= $published ?? 0 ?> publiés
            </div>
        </div>
        <div class="rs-dash-kpi">
            <div class="rs-dash-kpi-icon" style="background:rgba(5,150,105,0.08);color:var(--rsd-success);">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="rs-dash-kpi-value"><?= $block_count ?? count($totalBlocks) ?></div>
            <div class="rs-dash-kpi-label">Blocs disponibles</div>
            <div class="rs-dash-kpi-trend" style="background:rgba(5,150,105,0.08);color:var(--rsd-success);">
                <?= count($blockGroups) ?> catégories
            </div>
        </div>
        <div class="rs-dash-kpi">
            <div class="rs-dash-kpi-icon" style="background:rgba(13,148,136,0.08);color:var(--rsd-accent);">
                <i class="fas fa-paintbrush"></i>
            </div>
            <div class="rs-dash-kpi-value"><?= $theme_count ?? 0 ?></div>
            <div class="rs-dash-kpi-label">Thèmes</div>
        </div>
        <div class="rs-dash-kpi">
            <div class="rs-dash-kpi-icon" style="background:rgba(217,119,6,0.08);color:var(--rsd-warning);">
                <i class="fas fa-file-circle-plus"></i>
            </div>
            <div class="rs-dash-kpi-value"><?= max(0, $draftCount) ?></div>
            <div class="rs-dash-kpi-label">Brouillons</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="rs-dash-card mb-4">
        <div class="rs-dash-card-header">
            <h5 class="rs-dash-card-title"><i class="fas fa-bolt"></i> Actions rapides</h5>
        </div>
        <div class="rs-dash-card-body">
            <div class="rs-quick-actions">
                <a href="<?= route('reportstudio.templates.create') ?>" class="rs-qa-item">
                    <div class="rs-qa-icon" style="background:rgba(37,99,235,0.08);color:var(--rsd-primary);">
                        <i class="fas fa-file-circle-plus"></i>
                    </div>
                    <div class="rs-qa-label">Nouveau rapport</div>
                    <div class="rs-qa-sub">Partir de zéro</div>
                </a>
                <a href="<?= route('reportstudio.templates.index') ?>" class="rs-qa-item">
                    <div class="rs-qa-icon" style="background:rgba(13,148,136,0.08);color:var(--rsd-accent);">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="rs-qa-label">Mes modèles</div>
                    <div class="rs-qa-sub"><?= $template_count ?? 0 ?> au total</div>
                </a>
                <a href="<?= route('reportstudio.themes.index') ?>" class="rs-qa-item">
                    <div class="rs-qa-icon" style="background:rgba(13,148,136,0.08);color:var(--rsd-accent);">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="rs-qa-label">Thèmes</div>
                    <div class="rs-qa-sub"><?= $theme_count ?? 0 ?> disponibles</div>
                </a>
                <a href="/admin/reports" class="rs-qa-item">
                    <div class="rs-qa-icon" style="background:rgba(5,150,105,0.08);color:var(--rsd-success);">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="rs-qa-label">Certifications</div>
                    <div class="rs-qa-sub">Demandes en attente</div>
                </a>
                <a href="/admin/dashboard" class="rs-qa-item">
                    <div class="rs-qa-icon" style="background:rgba(217,119,6,0.08);color:var(--rsd-warning);">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="rs-qa-label">Dashboard</div>
                    <div class="rs-qa-sub">Vue globale</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid: Templates + Block Library -->
    <div class="rs-dash-grid">
        <!-- Recent Templates -->
        <div class="rs-dash-card">
            <div class="rs-dash-card-header">
                <h5 class="rs-dash-card-title"><i class="fas fa-clock-rotate-left"></i> Modèles récents</h5>
                <a href="<?= route('reportstudio.templates.index') ?>" class="rs-dash-btn rs-dash-btn-ghost" style="font-size:0.72rem;padding:0.35rem 0.85rem;background:var(--rsd-surface-2);color:var(--rsd-text-muted);border:1px solid var(--rsd-border);">
                    Tout voir <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="rs-dash-card-body">
                <?php if (empty($recent)): ?>
                    <div class="rs-dash-empty">
                        <i class="fas fa-inbox"></i>
                        <p>Aucun modèle pour le moment.</p>
                        <a href="<?= route('reportstudio.templates.create') ?>" class="rs-dash-btn rs-dash-btn-light" style="background:var(--rsd-primary);color:#fff;">
                            <i class="fas fa-plus"></i> Créer le premier rapport
                        </a>
                    </div>
                <?php else: ?>
                    <div class="rs-tpl-grid">
                        <?php foreach ($recent as $tpl):
                            $status = $tpl['status'] ?? 'draft';
                            $statusClass = $status === 'published' ? 'rs-tpl-status-published'
                                : ($status === 'archived' ? 'rs-tpl-status-archived' : 'rs-tpl-status-draft');
                            $statusLabel = $status === 'published' ? 'Publié'
                                : ($status === 'archived' ? 'Archivé' : 'Brouillon');
                        ?>
                            <div class="rs-tpl-item">
                                <div class="rs-tpl-thumb">
                                    <i class="fas fa-file-lines"></i>
                                    <span class="rs-tpl-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </div>
                                <div class="rs-tpl-info">
                                    <div class="rs-tpl-name"><?= e($tpl['name'] ?? 'Sans titre') ?></div>
                                    <div class="rs-tpl-desc"><?= e($tpl['description'] ?? 'Aucune description') ?></div>
                                    <div class="rs-tpl-actions">
                                        <a href="<?= route('reportstudio.builder.edit', ['id' => $tpl['id'] ?? 0]) ?>"
                                           class="rs-tpl-btn rs-tpl-btn-primary">
                                            <i class="fas fa-pen-to-square"></i> Modifier
                                        </a>
                                        <a href="<?= route('reportstudio.preview.show', ['id' => $tpl['id'] ?? 0]) ?>"
                                           target="_blank" class="rs-tpl-btn" title="Aperçu">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (empty($tpl['is_system'])): ?>
                                        <form action="<?= route('reportstudio.templates.destroy', ['id' => $tpl['id'] ?? 0]) ?>" method="POST" class="rs-tpl-delete-form" onsubmit="return confirm('Supprimer ce modèle de rapport ? Cette action est irréversible.')">
                                            <button type="submit" class="rs-tpl-btn rs-tpl-btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Block Library Sidebar -->
        <div class="rs-dash-card">
            <div class="rs-dash-card-header">
                <h5 class="rs-dash-card-title"><i class="fas fa-cubes-stacked"></i> Bibliothèque de blocs</h5>
            </div>
            <div class="rs-dash-card-body">
                <div class="rs-block-list">
                    <?php foreach ($blockGroups as $category => $blocks): ?>
                        <div class="rs-block-cat"><?= ucfirst($category) ?></div>
                        <?php foreach ($blocks as $block): ?>
                            <div class="rs-block-chip">
                                <i class="fas fa-<?= match($block['block_key']) {
                                    'global_score' => 'gauge-high',
                                    'radar_chart' => 'chart-area',
                                    'bar_chart' => 'chart-column',
                                    'line_chart' => 'chart-line',
                                    'donut_chart' => 'chart-pie',
                                    'area_chart' => 'chart-area',
                                    'gauge' => 'gauge',
                                    'recommendations' => 'list-check',
                                    'company_info' => 'building',
                                    'aqmi_logo' => 'award',
                                    'company_logo' => 'image',
                                    'qr_code' => 'qrcode',
                                    'official_stamp' => 'stamp',
                                    'signature' => 'signature',
                                    'header' => 'align-left',
                                    'footer' => 'align-right',
                                    'rich_text' => 'align-justify',
                                    'image' => 'image',
                                    'cover_page' => 'bookmark',
                                    'kpi_card' => 'calendar-check',
                                    'domain_scores' => 'table-list',
                                    'page_break' => 'file-lines',
                                    default => 'cube',
                                } ?>"></i>
                                <span><?= e($block['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();

\App\Helpers\ViewHelper::renderLayout('admin', [
    'title'       => 'AQMI Report Studio',
    'content'     => $content,
    'extraStyles' => '<link rel="stylesheet" href="/assets/modules/reportstudio/css/report_studio.css">',
]);
