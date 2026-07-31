<?php
/** @var array $config */
$html = $config['html'] ?? '';
?>
<label class="form-label small fw-bold">Contenu</label>
<small class="d-block text-muted mb-1">HTML autorisé. Un éditeur riche est disponible sur le canvas.</small>
<textarea class="form-control form-control-sm rs-prop" data-prop="html" rows="8" id="rs-richtext"><?= e($html) ?></textarea>
