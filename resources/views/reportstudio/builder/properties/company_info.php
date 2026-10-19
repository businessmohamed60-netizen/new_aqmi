<?php
/** @var array $config */
$fields = $config['fields'] ?? [
    ['key' => 'name',    'label' => 'Nom de l\'entreprise'],
    ['key' => 'address', 'label' => 'Adresse'],
    ['key' => 'siret',   'label' => 'SIRET'],
    ['key' => 'contact', 'label' => 'Contact'],
];
$showLogo = $config['show_logo'] ?? true;
?>
<label class="form-label small fw-bold">Champs affichés</label>
<div id="rs-cinfo-fields">
    <?php foreach ($fields as $i => $field): ?>
        <div class="input-group input-group-sm mb-1 rs-cinfo-row">
            <input type="text" class="form-control rs-prop rs-cinfo-key" data-prop="field_key" placeholder="Clé" value="<?= e($field['key'] ?? '') ?>">
            <input type="text" class="form-control rs-prop rs-cinfo-label" data-prop="field_label" placeholder="Libellé" value="<?= e($field['label'] ?? '') ?>">
            <button type="button" class="btn btn-outline-danger rs-cinfo-remove"><i class="bi bi-x"></i></button>
        </div>
    <?php endforeach; ?>
</div>
<button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" id="rs-cinfo-add">
    <i class="bi bi-plus"></i> Ajouter un champ
</button>

<div class="form-check form-switch">
    <input class="form-check-input rs-prop" type="checkbox" data-prop="show_logo" id="ci-logo" <?= $showLogo ? 'checked' : '' ?>>
    <label class="form-check-label small" for="ci-logo">Inclure le logo entreprise</label>
</div>
