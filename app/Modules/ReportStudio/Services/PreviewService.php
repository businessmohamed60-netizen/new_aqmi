<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Modules\ReportStudio\Models\ReportTemplate;
use App\Modules\ReportStudio\Models\ReportTemplateBlock;
use App\Modules\ReportStudio\Models\ReportTheme;

/**
 * Assembles a template with its blocks and theme for live preview
 * and PDF handoff. Filters blocks by visibility context.
 */
class PreviewService
{
    /**
     * Load everything needed to render a full report preview.
     */
    public function loadForPreview(int $templateId): ?array
    {
        $template = ReportTemplate::find($templateId);
        if (!$template) {
            return null;
        }

        $blocks = ReportTemplateBlock::enabledForTemplate($templateId);
        $tplArray = $template->toArray();

        $theme = null;
        if (!empty($tplArray['theme_id'])) {
            $theme = ReportTheme::find((int) $tplArray['theme_id']);
        }
        if (!$theme) {
            $theme = ReportTheme::findDefault();
        }

        $compiler = new ThemeCompiler();

        return [
            'template'     => $tplArray,
            'blocks'       => array_map(fn($b) => $b->toArray(), $blocks),
            'theme'        => $theme?->toArray() ?? [],
            'themeCss'     => $theme ? $compiler->toCss($theme) : '',
            'themeStyle'   => $theme ? $compiler->toInlineStyle($theme) : '',
            'pageSettings' => [
                'orientation'         => $tplArray['orientation'] ?? 'portrait',
                'watermark_text'      => $tplArray['watermark_text'] ?? '',
                'watermark_opacity'   => (float) ($tplArray['watermark_opacity'] ?? 0.08),
                'report_number_prefix'=> $tplArray['report_number_prefix'] ?? 'AQMI-RPT-',
                'certification_date'  => $tplArray['certification_date'] ?? null,
                'expiration_date'     => $tplArray['expiration_date'] ?? null,
            ],
            'reportNumber' => ($tplArray['report_number_prefix'] ?? 'AQMI-RPT-') . str_pad((string) ($tplArray['id'] ?? '0'), 3, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * Filter blocks by visibility for a given context ('web' or 'pdf').
     */
    public function filterByVisibility(array $blocks, string $context): array
    {
        return array_values(array_filter(
            $blocks,
            static fn(array $b): bool => match ($b['visibility'] ?? 'web_pdf') {
                'web_only' => $context === 'web',
                'pdf_only' => $context === 'pdf',
                default    => true,
            }
        ));
    }

    /**
     * Resolve dynamic variables in header/footer text.
     */
    public function resolveDynamicVars(string $text, array $template, int $page = 1, int $totalPages = 1): string
    {
        $vars = [
            '{report_number}'      => ($template['report_number_prefix'] ?? 'AQMI-RPT-') . str_pad((string) ($template['id'] ?? '0'), 3, '0', STR_PAD_LEFT),
            '{certification_date}' => $this->formatDate($template['certification_date'] ?? null),
            '{expiration_date}'    => $this->formatDate($template['expiration_date'] ?? null),
            '{current_date}'       => date('d/m/Y'),
            '{template_name}'      => $template['name'] ?? '',
            '{page}'               => (string) $page,
            '{total_pages}'        => (string) $totalPages,
        ];

        return strtr($text, $vars);
    }

    private function formatDate(?string $date): string
    {
        if (!$date) return '—';
        $ts = strtotime($date);
        return $ts ? date('d/m/Y', $ts) : '—';
    }
}
