<?php
/** @var array $config */
$mode  = $config['mode']  ?? 'manual';
$value = $config['value'] ?? '';
$size  = $config['size']  ?? 120;
$label = $config['label'] ?? '';
?>
<label class="form-label small fw-bold">Mode du QR code</label>
<select class="form-select form-select-sm mb-2 rs-prop" data-prop="mode">
    <option value="manual" <?= $mode === 'manual' ? 'selected' : '' ?>>Manuel (URL ou texte libre)</option>
    <option value="verify" <?= $mode === 'verify' ? 'selected' : '' ?>>Vérification de certificat (lien auto)</option>
</select>

<?php if ($mode === 'verify'): ?>
<div class="alert alert-info py-1 px-2 mb-2 small">
    <i class="bi bi-info-circle"></i> Le QR pointera automatiquement vers la page publique de vérification
    <code>/c/{token}</code> propre à chaque certificat généré. Aucune valeur manuelle requise.
</div>
<?php else: ?>
<label class="form-label small fw-bold">Donnée encodée (URL ou texte)</label>
<input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="value"
       value="<?= e($value) ?>" placeholder="https://aqmi.example">
<?php endif; ?>

<label class="form-label small fw-bold">Taille (px)</label>
<input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="size"
       min="60" max="400" value="<?= (int) $size ?>">

<label class="form-label small fw-bold">Libellé sous le QR</label>
<input type="text" class="form-control form-control-sm rs-prop" data-prop="label" value="<?= e($label) ?>">
