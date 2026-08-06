<?php
/** Footer — dynamic text with page numbering and report number.
 * @var array $config
 * @var string $title
 * @var array  $template
 * @var string $reportNumber
 */
$text       = $config['text']  ?? '';
$align      = $config['align'] ?? 'center';
$showPage   = $config['show_page_number']   ?? true;
$showReport = $config['show_report_number'] ?? false;
$showDate   = $config['show_date']          ?? false;

$tplData   = $template ?? [];
$rptNumber = $reportNumber ?? ($tplData['report_number_prefix'] ?? 'AQMI-RPT-') . ($tplData['id'] ?? '000');
$resolved  = strtr($text, [
    '{report_number}'      => $rptNumber,
    '{certification_date}' => !empty($tplData['certification_date']) ? date('d/m/Y', strtotime($tplData['certification_date'])) : '—',
    '{expiration_date}'    => !empty($tplData['expiration_date']) ? date('d/m/Y', strtotime($tplData['expiration_date'])) : '—',
    '{current_date}'       => date('d/m/Y'),
    '{template_name}'      => $tplData['name'] ?? '',
]);
$parts = [];
if ($resolved) $parts[] = e($resolved);
if ($showReport) $parts[] = 'N° ' . e($rptNumber);
if ($showDate) $parts[] = date('d/m/Y');
if ($showPage) $parts[] = '<span class="rs-dynamic-page"></span>';
?>
<div class="rs-block-footer py-2 text-<?= e($align) ?> border-top">
    <small class="text-muted"><?= implode(' · ', array_filter($parts, fn($p) => $p !== '')) ?: 'Pied de page' ?></small>
</div>
