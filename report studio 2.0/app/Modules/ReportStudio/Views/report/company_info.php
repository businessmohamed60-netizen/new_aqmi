<?php
/** Company Information. @var array $config @var string $title */
$fields   = $config['fields'] ?? [];
$showLogo = $config['show_logo'] ?? true;
?>
<div class="rs-block-company-info py-2">
    <?php if ($title): ?><h5 class="rs-block-title"><?= e($title) ?></h5><?php endif; ?>
    <div class="d-flex gap-3 align-items-start">
        <?php if ($showLogo): ?>
            <div class="rs-ci-logo flex-shrink-0">
                <i class="bi bi-building fs-1 text-muted"></i>
            </div>
        <?php endif; ?>
        <dl class="row mb-0">
            <?php foreach ($fields as $f): ?>
                <dt class="col-sm-4 text-muted small"><?= e($f['label'] ?? '') ?></dt>
                <dd class="col-sm-8"><?= e($f['value'] ?? $f['key'] ?? '') ?></dd>
            <?php endforeach; ?>
            <?php if (empty($fields)): ?><dd class="col-12 text-muted">Aucune information</dd><?php endif; ?>
        </dl>
    </div>
</div>
