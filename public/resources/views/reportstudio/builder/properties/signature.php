<?php
/** Signature properties — includes stamp option. @var array $config */
$label    = $config['label'] ?? '';
$role     = $config['role']  ?? '';
$date     = $config['show_date']  ?? true;
$showStamp = $config['show_stamp'] ?? false;
?>
<label class="form-label small fw-bold">Nom du signataire</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="<?= e($label) ?>">

<label class="form-label small fw-bold">Fonction</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="role" value="<?= e($role) ?>">

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="sig-date" <?= $date ? 'checked' : '' ?>>
    <label class="form-check-label small" for="sig-date">Afficher la date</label>
</div>
<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_stamp" id="sig-stamp" <?= $showStamp ? 'checked' : '' ?>>
    <label class="form-check-label small" for="sig-stamp">Afficher le tampon officiel AQMI</label>
</div>
