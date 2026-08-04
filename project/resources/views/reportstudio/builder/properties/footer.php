<?php
/** Footer properties — supports dynamic variables and page numbering. @var array $config */
$text       = $config['text']  ?? '';
$align      = $config['align'] ?? 'center';
$showPage   = $config['show_page_number']   ?? true;
$showReport = $config['show_report_number'] ?? false;
$showDate   = $config['show_date']          ?? false;
?>
<label class="form-label small fw-bold">Texte du pied de page</label>
<input type="text" class="form-control form-control-sm mb-1 rs-prop" data-prop="text" value="<?= e($text) ?>">
<small class="d-block text-muted mb-2">Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}</small>

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_report_number" id="ft-rn" <?= $showReport ? 'checked' : '' ?>>
    <label class="form-check-label small" for="ft-rn">Afficher N° rapport</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="ft-d" <?= $showDate ? 'checked' : '' ?>>
    <label class="form-check-label small" for="ft-d">Afficher la date</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_page_number" id="ft-pn" <?= $showPage ? 'checked' : '' ?>>
    <label class="form-check-label small" for="ft-pn">Afficher la pagination</label>
</div>
