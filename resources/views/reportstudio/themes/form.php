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
.rs-layout-editor-wrap {
    margin-bottom: 1rem;
}
.rs-layout-canvas {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    padding: 1rem;
    background: var(--rsd-surface-2);
    border: 1px solid var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    min-height: 60px;
    margin-bottom: 1rem;
}
.rs-layout-block {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.9rem;
    background: var(--rsd-surface);
    border: 1px solid var(--rsd-border);
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--rsd-text);
    cursor: grab;
    user-select: none;
    transition: all var(--rsd-transition);
}
.rs-layout-block:hover {
    border-color: var(--rsd-primary-light);
    background: var(--rsd-primary-dim);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31,111,235,0.2);
}
.rs-layout-block:active,
.rs-layout-block.dragging {
    cursor: grabbing;
    opacity: 0.5;
    transform: scale(0.95);
}
.rs-layout-block i {
    color: var(--rsd-primary-light);
    font-size: 0.85rem;
}
.rs-layout-block .rs-align-tag {
    font-size: 0.58rem;
    padding: 0.1rem 0.4rem;
    border-radius: 6px;
    background: rgba(148,163,184,0.15);
    color: var(--rsd-text-muted);
    margin-left: 0.3rem;
}
.rs-layout-zones {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0.75rem;
}
.rs-layout-zone {
    min-height: 120px;
    border: 2px dashed var(--rsd-border);
    border-radius: var(--rsd-radius-sm);
    padding: 0.75rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-content: flex-start;
    transition: all var(--rsd-transition);
}
.rs-layout-zone.drag-over {
    border-color: var(--rsd-primary-light);
    background: var(--rsd-primary-dim);
    transform: scale(1.02);
}
.rs-layout-zone-label {
    width: 100%;
    font-size: 0.68rem;
    font-weight: 700;
    color: var(--rsd-text-dim);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: center;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--rsd-border);
    margin-bottom: 0.5rem;
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

                    <hr style="border-color:var(--rsd-border);margin:1.5rem 0;">

                    <div class="rs-layout-editor-wrap">
                        <label class="rs-form-label" style="margin-bottom:0.5rem;">
                            <i class="fas fa-arrows-left-right me-1" style="color:var(--rsd-primary-light);"></i>
                            Disposition des blocs
                        </label>
                        <p style="font-size:0.72rem;color:var(--rsd-text-dim);margin:0 0 1rem;">
                            Glissez chaque bloc vers la gauche, le centre ou la droite avec la souris.
                        </p>

                        <div class="rs-layout-canvas" id="rsLayoutCanvas">
                            <div class="rs-layout-block" draggable="true" data-block="header" data-align="<?= e($th['css_variables']['block_align']['header'] ?? 'center') ?>">
                                <i class="fas fa-heading"></i>
                                <span>En-tête</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="logo" data-align="<?= e($th['css_variables']['block_align']['logo'] ?? 'left') ?>">
                                <i class="fas fa-image"></i>
                                <span>Logo</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="company_info" data-align="<?= e($th['css_variables']['block_align']['company_info'] ?? 'left') ?>">
                                <i class="fas fa-building"></i>
                                <span>Infos entreprise</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="global_score" data-align="<?= e($th['css_variables']['block_align']['global_score'] ?? 'center') ?>">
                                <i class="fas fa-chart-pie"></i>
                                <span>Score global</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="domain_scores" data-align="<?= e($th['css_variables']['block_align']['domain_scores'] ?? 'center') ?>">
                                <i class="fas fa-list-ol"></i>
                                <span>Scores par domaine</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="recommendations" data-align="<?= e($th['css_variables']['block_align']['recommendations'] ?? 'left') ?>">
                                <i class="fas fa-lightbulb"></i>
                                <span>Recommandations</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="signature" data-align="<?= e($th['css_variables']['block_align']['signature'] ?? 'right') ?>">
                                <i class="fas fa-signature"></i>
                                <span>Signature</span>
                            </div>
                            <div class="rs-layout-block" draggable="true" data-block="footer" data-align="<?= e($th['css_variables']['block_align']['footer'] ?? 'center') ?>">
                                <i class="fas fa-shoe-prints"></i>
                                <span>Pied de page</span>
                            </div>
                        </div>

                        <div class="rs-layout-zones">
                            <div class="rs-layout-zone" data-zone="left">
                                <span class="rs-layout-zone-label"><i class="fas fa-align-left"></i> Gauche</span>
                            </div>
                            <div class="rs-layout-zone" data-zone="center">
                                <span class="rs-layout-zone-label"><i class="fas fa-align-center"></i> Centré</span>
                            </div>
                            <div class="rs-layout-zone" data-zone="right">
                                <span class="rs-layout-zone-label"><i class="fas fa-align-right"></i> Droite</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="block_align_json" id="blockAlignJson" value="<?= e(json_encode($th['css_variables']['block_align'] ?? [])) ?>">

                    <hr style="border-color:var(--rsd-border);margin:1.5rem 0;">

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
    'extraScripts' => '
<script>
(function() {
    var canvas = document.getElementById("rsLayoutCanvas");
    if (!canvas) return;
    var zones = document.querySelectorAll(".rs-layout-zone");
    var hiddenInput = document.getElementById("blockAlignJson");
    var alignMap = {};

    function getAlignMap() {
        try { return JSON.parse(hiddenInput.value || "{}"); } catch(e) { return {}; }
    }
    function saveAlignMap() {
        hiddenInput.value = JSON.stringify(alignMap);
    }
    function updateTags() {
        document.querySelectorAll(".rs-layout-block").forEach(function(b) {
            var a = b.getAttribute("data-align") || "left";
            var tag = b.querySelector(".rs-align-tag");
            if (!tag) {
                tag = document.createElement("span");
                tag.className = "rs-align-tag";
                b.appendChild(tag);
            }
            var labels = {left:"Gauche", center:"Centre", right:"Droite"};
            tag.textContent = labels[a] || a;
        });
    }
    function placeBlockInZone(block, zone) {
        var zoneName = zone.getAttribute("data-zone");
        var blockName = block.getAttribute("data-block");
        block.setAttribute("data-align", zoneName);
        alignMap[blockName] = zoneName;
        zone.appendChild(block);
        saveAlignMap();
        updateTags();
    }

    alignMap = getAlignMap();

    // Initialize: move blocks to their saved zones
    document.querySelectorAll(".rs-layout-block").forEach(function(block) {
        var align = block.getAttribute("data-align") || "left";
        var zone = document.querySelector(".rs-layout-zone[data-zone=\"" + align + "\"]");
        if (zone) {
            zone.appendChild(block);
        }
    });
    updateTags();

    // Drag events on blocks
    document.querySelectorAll(".rs-layout-block").forEach(function(block) {
        block.addEventListener("dragstart", function(e) {
            e.dataTransfer.setData("text/plain", block.getAttribute("data-block"));
            e.dataTransfer.effectAllowed = "move";
            block.classList.add("dragging");
        });
        block.addEventListener("dragend", function() {
            block.classList.remove("dragging");
            zones.forEach(function(z) { z.classList.remove("drag-over"); });
        });
    });

    // Drop zone events
    zones.forEach(function(zone) {
        zone.addEventListener("dragover", function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = "move";
            zone.classList.add("drag-over");
        });
        zone.addEventListener("dragleave", function() {
            zone.classList.remove("drag-over");
        });
        zone.addEventListener("drop", function(e) {
            e.preventDefault();
            zone.classList.remove("drag-over");
            var blockName = e.dataTransfer.getData("text/plain");
            var block = document.querySelector(".rs-layout-block[data-block=\"" + blockName + "\"]");
            if (block) placeBlockInZone(block, zone);
        });
    });

    // Also allow dragging back to canvas (unassigned / free area)
    canvas.addEventListener("dragover", function(e) { e.preventDefault(); });
    canvas.addEventListener("drop", function(e) {
        e.preventDefault();
        var blockName = e.dataTransfer.getData("text/plain");
        var block = document.querySelector(".rs-layout-block[data-block=\"" + blockName + "\"]");
        if (block) {
            canvas.appendChild(block);
            // Reset to center when moved back to canvas
            block.setAttribute("data-align", "center");
            alignMap[block.getAttribute("data-block")] = "center";
            saveAlignMap();
            updateTags();
        }
    });
})();
</script>
',
]);
