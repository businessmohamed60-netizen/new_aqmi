<?php
/** Header — premium dynamic text with variable substitution.
 * @var array $config
 * @var string $title
 * @var array  $template
 * @var string $reportNumber
 */
$text       = $config['text']  ?? '';
$level      = max(1, min(3, (int) ($config['level'] ?? 1)));
$align      = $config['align'] ?? 'left';
$showPage   = $config['show_page_number']   ?? false;
$showReport = $config['show_report_number'] ?? false;
$showDate   = $config['show_date']          ?? false;
$color      = $config['color'] ?? '#0f2845';
$fontSize   = $config['font_size'] ?? '';
$uppercase  = $config['uppercase'] ?? false;
$borderBottom = $config['border_bottom'] ?? false;
$tag = 'h' . $level;

$tplData   = $template ?? [];
$rptNumber = $reportNumber ?? ($tplData['report_number_prefix'] ?? 'AQMI-RPT-') . ($tplData['id'] ?? '000');
$resolved  = strtr($text, [
    '{report_number}'      => $rptNumber,
    '{certification_date}' => !empty($tplData['certification_date']) ? date('d/m/Y', strtotime($tplData['certification_date'])) : '—',
    '{expiration_date}'    => !empty($tplData['expiration_date']) ? date('d/m/Y', strtotime($tplData['expiration_date'])) : '—',
    '{current_date}'       => date('d/m/Y'),
    '{template_name}'      => $tplData['name'] ?? '',
]);

$hStyle = 'color: ' . e($color) . ';';
if ($fontSize) $hStyle .= ' font-size: ' . e($fontSize) . ';';
if ($uppercase) $hStyle .= ' text-transform: uppercase; letter-spacing: 0.04em;';
?>
<div class="rs-block-header py-2 text-<?= e($align) ?>" style="<?= $borderBottom ? 'border-bottom: 2px solid ' . e($color) . '20; padding-bottom: 10px;' : '' ?>">
    <<?= $tag ?> class="rs-header-title mb-0" style="<?= $hStyle ?>"><?= e($text ? $resolved : ($title ?: '')) ?></<?= $tag ?>>
    <?php if ($showReport || $showDate || $showPage): ?>
    <div class="rs-header-meta small d-flex gap-3 mt-1 justify-content-<?= e($align === 'center' ? 'center' : ($align === 'right' ? 'end' : 'start')) ?>" style="color: #8298b5;">
        <?php if ($showReport): ?><span style="font-size: 0.72rem; letter-spacing: 0.03em;"><i class="bi bi-hash" style="font-size: 0.68rem;"></i> <?= e($rptNumber) ?></span><?php endif; ?>
        <?php if ($showDate): ?><span style="font-size: 0.72rem;"><i class="bi bi-calendar3" style="font-size: 0.68rem;"></i> <?= date('d/m/Y') ?></span><?php endif; ?>
        <?php if ($showPage): ?><span class="rs-dynamic-page" style="font-size: 0.72rem;"></span><?php endif; ?>
    </div>
    <?php endif; ?>
</div>
