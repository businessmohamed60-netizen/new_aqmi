<?php
/** Cover Page properties. @var array $config */
$companyName = $config['company_name']  ?? '';
$reportTitle = $config['report_title']  ?? '';
$subtitle    = $config['subtitle']     ?? '';
$showLogo    = $config['show_logo']     ?? true;
$showStamp   = $config['show_stamp']    ?? true;
$showDate    = $config['show_date']     ?? true;
$showNumber  = $config['show_number']  ?? true;
$accentColor = $config['accent_color'] ?? '#102A43';
?>
<label class="form-label small fw-bold">Titre du rapport</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="report_title" value="<?= e($reportTitle) ?>">

<label class="form-label small fw-bold">Sous-titre</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="subtitle" value="<?= e($subtitle) ?>">

<label class="form-label small fw-bold">Nom de l'entreprise</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="company_name" value="<?= e($companyName) ?>">

<label class="form-label small fw-bold">Couleur d'accent</label>
<input type="color" class="form-control form-control-color mb-2 rs-prop" data-prop="accent_color" value="<?= e($accentColor) ?>">

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_logo" id="cp-logo" <?= $showLogo ? 'checked' : '' ?>>
    <label class="form-check-label small" for="cp-logo">Afficher le logo AQMI</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_stamp" id="cp-stamp" <?= $showStamp ? 'checked' : '' ?>>
    <label class="form-check-label small" for="cp-stamp">Afficher le sceau</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="cp-date" <?= $showDate ? 'checked' : '' ?>>
    <label class="form-check-label small" for="cp-date">Afficher la date</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_number" id="cp-num" <?= $showNumber ? 'checked' : '' ?>>
    <label class="form-check-label small" for="cp-num">Afficher le n° de rapport</label>
</div>
