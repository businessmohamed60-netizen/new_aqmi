<?php
/** @var array $config */
$html = $config['html'] ?? '';
?>
<label class="form-label small fw-bold">Contenu</label>
<small class="d-block text-muted mb-1">Utilisez la barre d'outils ci-dessus pour mettre en forme : police, taille, gras, italique, couleurs, alignement, listes.</small>
<textarea class="form-control form-control-sm rs-prop" data-prop="html" rows="8" id="rs-richtext"><?= e($html) ?></textarea>
