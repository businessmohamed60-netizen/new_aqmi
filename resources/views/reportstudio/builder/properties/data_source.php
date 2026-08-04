<?php
/**
 * Data Source binding panel — shared partial included by every chart/metric block.
 * Lets the user bind a block to a database table instead of manual data entry.
 *
 * @var array $config  block config (contains optional 'data_source' key)
 */
$ds = $config['data_source'] ?? [];
$bound = !empty($ds['table']);
?>
<div class="rs-datasource-panel" data-datasource-panel>
    <hr class="rs-prop-sep">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-database-fill text-primary"></i>
        <span class="fw-bold small">Source de données</span>
        <div class="form-check form-switch ms-auto">
            <input class="form-check-input rs-prop rs-datasource-toggle" type="checkbox"
                   data-prop="data_source_enabled" id="ds-toggle-<?= uniqid() ?>"
                   <?= $bound ? 'checked' : '' ?>>
            <label class="form-check-label small" for="ds-toggle-<?= uniqid() ?>">Lier à une table</label>
        </div>
    </div>

    <div class="rs-datasource-fields" style="<?= $bound ? '' : 'display:none;' ?>">
        <input type="hidden" class="rs-prop" data-prop="data_source.table"
               value="<?= e($ds['table'] ?? '') ?>">

        <!-- Table selector -->
        <label class="form-label small fw-bold">Table</label>
        <select class="form-select form-select-sm mb-2 rs-datasource-table">
            <option value="">— Choisir une table —</option>
        </select>

        <!-- Column mappers -->
        <div class="row g-2 mb-2">
            <div class="col-6">
                <label class="form-label small">Colonne libellés</label>
                <select class="form-select form-select-sm rs-prop rs-ds-label-col" data-prop="data_source.label_column">
                    <option value="">—</option>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label small">Colonne valeurs</label>
                <select class="form-select form-select-sm rs-prop rs-ds-value-col" data-prop="data_source.value_column">
                    <option value="">—</option>
                </select>
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-6">
                <label class="form-label small">Colonne séries <small class="text-muted">(optionnel)</small></label>
                <select class="form-select form-select-sm rs-prop rs-ds-series-col" data-prop="data_source.series_column">
                    <option value="">—</option>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label small">Limite</label>
                <input type="number" class="form-control form-control-sm rs-prop" data-prop="data_source.limit"
                       min="1" max="500" value="<?= (int)($ds['limit'] ?? 50) ?>">
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-7">
                <label class="form-label small">Trier par</label>
                <select class="form-select form-select-sm rs-prop rs-ds-order-col" data-prop="data_source.order_by">
                    <option value="">— Par défaut —</option>
                </select>
            </div>
            <div class="col-5">
                <label class="form-label small">Direction</label>
                <select class="form-select form-select-sm rs-prop" data-prop="data_source.order_dir">
                    <option value="ASC" <?= ($ds['order_dir'] ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Ascendant</option>
                    <option value="DESC" <?= ($ds['order_dir'] ?? '') === 'DESC' ? 'selected' : '' ?>>Descendant</option>
                </select>
            </div>
        </div>

        <!-- Where clause -->
        <label class="form-label small">Filtre (WHERE) <small class="text-muted">(optionnel)</small></label>
        <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="data_source.where_clause"
               placeholder="ex: status='published'" value="<?= e($ds['where_clause'] ?? '') ?>">

        <!-- Preview button -->
        <button type="button" class="btn btn-sm btn-outline-primary w-100 rs-ds-preview-btn">
            <i class="bi bi-eye"></i> Prévisualiser les données
        </button>
        <div class="rs-ds-preview-result mt-2" style="display:none;">
            <small class="text-muted">Données chargées :</small>
            <div class="rs-ds-preview-data small table-responsive"></div>
        </div>
    </div>
</div>
