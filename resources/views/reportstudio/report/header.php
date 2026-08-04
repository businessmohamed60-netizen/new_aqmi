<?php
/** Header — dynamic text with variable substitution and optional metadata line.
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
?>
<div class="rs-block-header py-2 text-<?= e($align) ?>">
    <<?= $tag ?> class="rs-header-title mb-0"><?= e($text ? $resolved : ($title ?: '')) ?></<?= $tag ?>>
    <?php if ($showReport || $showDate || $showPage): ?>
    <div class="rs-header-meta small text-muted d-flex gap-3 mt-1 justify-content-<?= e($align === 'center' ? 'center' : ($align === 'right' ? 'end' : 'start')) ?>">
        <?php if ($showReport): ?><span><i class="bi bi-hash"></i> <?= e($rptNumber) ?></span><?php endif; ?>
        <?php if ($showDate): ?><span><i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?></span><?php endif; ?>
        <?php if ($showPage): ?><span class="rs-dynamic-page"></span><?php endif; ?>
    </div>
    <?php endif; ?>
</div>
