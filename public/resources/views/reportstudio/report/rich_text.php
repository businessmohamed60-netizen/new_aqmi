<?php
/** Rich Text. @var array $config @var string $title */
$html = $config['html'] ?? '';
?>
<div class="rs-block-rich-text py-2">
    <?php if ($title): ?><h5 class="rs-block-title"><?= e($title) ?></h5><?php endif; ?>
    <div class="rs-richtext-content"><?= $html ?></div>
</div>
