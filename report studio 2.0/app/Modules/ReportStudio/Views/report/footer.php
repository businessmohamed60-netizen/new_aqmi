<?php
/** Footer — dynamic text with page numbering and report number. @var array $config @var string $title @var array $template */
$text        = $config['text']  ?? '';
$align       = $config['align'] ?? 'center';
$showPage    = $config['show_page_number']   ?? true;
$showReport  = $config['show_report_number'] ?? false;
$showDate    = $config['show_date']          ?? false;

$tplData    = $template ?? [];
$rptNumber  = $reportNumber ?? ($tplData['report_number_prefix'] ?? 'AQMI-RPT-') . ($tplData['id'] ?? '000');
$resolved   = strtr($text, [
    '{report_number}'      => $rptNumber,
    '{certification_date}' => !empty($tplData['certification_date']) ? date('d/m/Y', strtotime($tplData['certification_date'])) : '—',
    '{expiration_date}'    => !empty($tplData['expiration_date']) ? date('d/m/Y', strtotime($tplData['expiration_date'])) : '—',
    '{current_date}'       => date('d/m/Y'),
    '{template_name}'      => $tplData['name'] ?? '',
]);
?>
<div class="rs-block-footer py-2 text-<?= e($align) ?> border-top">
    <small class="text-muted">
        <?php if ($resolved): ?><?= e($resolved) ?><?php endif; ?>
        <?php if ($showReport): ?><?= $resolved ? ' · ' : '' ?>N° <?= e($rptNumber) ?><?php endif; ?>
        <?php if ($showDate): ?><?= ($resolved || $showReport) ? ' · ' : '' ?><?= date('d/m/Y') ?><?php endif; ?>
        <?php if ($showPage): ?><?= ($resolved || $showReport || $showDate) ? ' · ' : '' ?><span class="rs-dynamic-page">Page {page}/{total_pages}</span><?php endif; ?>
    </small>
</div>
