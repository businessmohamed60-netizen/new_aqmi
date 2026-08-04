<?php
/** Header properties — supports dynamic variables and page numbering. @var array $config */
$text       = $config['text']  ?? '';
$level      = $config['level'] ?? 1;
$align      = $config['align'] ?? 'left';
$showPage   = $config['show_page_number']   ?? false;
$showReport = $config['show_report_number'] ?? false;
$showDate   = $config['show_date']          ?? false;
?>
<label class="form-label small fw-bold">Texte</label>
<input type="text" class="form-control form-control-sm mb-1 rs-prop" data-prop="text" value="<?= e($text) ?>">
<small class="d-block text-muted mb-2">Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}</small>

<label class="form-label small fw-bold">Niveau de titre</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="level">
    <option value="1" <?= (int) $level === 1 ? 'selected' : '' ?>>H1</option>
    <option value="2" <?= (int) $level === 2 ? 'selected' : '' ?>>H2</option>
    <option value="3" <?= (int) $level === 3 ? 'selected' : '' ?>>H3</option>
</select>

<label class="form-label small fw-bold">Alignement</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="align">
    <option value="left"   <?= $align === 'left'   ? 'selected' : '' ?>>Gauche</option>
    <option value="center" <?= $align === 'center' ? 'selected' : '' ?>>Centre</option>
    <option value="right"  <?= $align === 'right'  ? 'selected' : '' ?>>Droite</option>
</select>

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_report_number" id="hd-rn" <?= $showReport ? 'checked' : '' ?>>
    <label class="form-check-label small" for="hd-rn">Afficher N° rapport</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="hd-d" <?= $showDate ? 'checked' : '' ?>>
    <label class="form-check-label small" for="hd-d">Afficher la date</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_page_number" id="hd-pn" <?= $showPage ? 'checked' : '' ?>>
    <label class="form-check-label small" for="hd-pn">Afficher la pagination</label>
</div>
