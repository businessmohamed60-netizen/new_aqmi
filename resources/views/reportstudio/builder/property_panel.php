<?php
declare(strict_types=1);

/**
 * Property panel — loads the matching per-block form partial.
 * @var array|null $block  selected block or null
 */
?>
<?php if ($block && !empty($block['block_key'])): ?>
    <?php
        $key    = $block['block_key'];
        $config = $block['block_config'] ?? [];
        $title  = $block['title'] ?? '';
        $id     = $block['id'] ?? '';
    ?>
    <input type="hidden" id="rs-prop-block-id" value="<?= e($id) ?>">
    <input type="hidden" id="rs-prop-block-key" value="<?= e($key) ?>">

    <div class="rs-prop-title">
        <label class="form-label small fw-bold">Titre du bloc</label>
        <input type="text" class="form-control form-control-sm" id="rs-prop-title"
               value="<?= e($title) ?>" placeholder="Titre optionnel">
    </div>

    <hr class="rs-prop-sep">

    <div class="rs-prop-fields">
        <?php
        $partialMap = [
            'global_score'    => 'global_score',
            'radar_chart'     => 'radar_chart',
            'gauge'           => 'gauge',
            'recommendations' => 'recommendations',
            'company_info'    => 'company_info',
            'aqmi_logo'       => 'aqmi_logo',
            'company_logo'    => 'company_logo',
            'official_stamp'  => 'official_stamp',
            'qr_code'         => 'qr_code',
            'signature'       => 'signature',
            'header'          => 'header',
            'footer'          => 'footer',
            'rich_text'       => 'rich_text',
            'image'           => 'image',
            'cover_page'      => 'cover_page',
            'kpi_card'        => 'kpi_card',
            'domain_scores'   => 'domain_scores',
            'page_break'      => 'page_break',
        ];
        $partial = $partialMap[$key] ?? null;
        ?>
        <?php if ($partial): ?>
            <?= view_partial("reportstudio/builder/properties/{$partial}", ['config' => $config], true) ?>
        <?php else: ?>
            <div class="text-muted small">Aucune propriété pour ce bloc.</div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="rs-properties-empty">
        <i class="bi bi-hand-index"></i>
        <p>Sélectionnez un bloc pour éditer ses propriétés</p>
    </div>
<?php endif; ?>
