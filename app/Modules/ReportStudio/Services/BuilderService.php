<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Modules\ReportStudio\Models\ReportBlock;
use App\Modules\ReportStudio\Models\ReportTemplate;
use App\Modules\ReportStudio\Models\ReportTemplateBlock;

/**
 * Handles the drag & drop builder: loading a template's block layout,
 * normalizing the AJAX save payload, and persisting it.
 */
class BuilderService
{
    /**
     * Load a template with its block instances for the builder canvas.
     */
    public function loadForBuilder(int $templateId): ?array
    {
        $template = ReportTemplate::find($templateId);
        if (!$template) {
            return null;
        }

        $blocks = ReportTemplateBlock::forTemplate($templateId);

        return [
            'template' => $template->toArray(),
            'blocks'   => array_map(fn($b) => $b->toArray(), $blocks),
        ];
    }

    /**
     * Normalize and persist the block layout from the builder AJAX save.
     * Returns the count of saved blocks.
     */
    public function saveLayout(int $templateId, array $blocks, array $settings = []): int
    {
        $rows = $this->normalizeBlocks($blocks, $templateId);
        ReportTemplateBlock::replaceForTemplate($templateId, $rows);

        if (!empty($settings)) {
            ReportTemplate::update($templateId, $this->validateSettings($settings));
        }

        return count($rows);
    }

    /**
     * Convert raw builder payload into DB-ready rows.
     */
    public function normalizeBlocks(array $payload, int $templateId): array
    {
        $validVisibility = ['web_pdf', 'web_only', 'pdf_only'];
        $rows = [];
        $order = 0;

        foreach ($payload as $item) {
            $blockKey = (string) ($item['block_key'] ?? '');
            if ($blockKey === '' || !BlockRegistry::has($blockKey)) {
                continue;
            }

            $block = ReportBlock::findByKey($blockKey);
            $vis = (string) ($item['visibility'] ?? 'web_pdf');

            $rows[] = [
                'template_id'  => $templateId,
                'block_key'    => $blockKey,
                'block_id'     => $block?->id,
                'title'        => $item['title'] ?? null,
                'block_config' => $this->normalizeBlockConfig($blockKey, (array) ($item['block_config'] ?? [])),
                'sort_order'   => $order++,
                'is_enabled'   => isset($item['is_enabled']) ? (bool) $item['is_enabled'] : true,
                'visibility'   => in_array($vis, $validVisibility, true) ? $vis : 'web_pdf',
                'column_span'  => $this->validateColumnSpan($item['column_span'] ?? 12),
                'row_id'       => (int) ($item['row_id'] ?? 0),
            ];
        }
        return $rows;
    }

    private function validateSettings(array $settings): array
    {
        $orientation = in_array($settings['orientation'] ?? '', ['portrait', 'landscape'], true)
            ? $settings['orientation'] : 'portrait';

        return [
            'orientation'          => $orientation,
            'watermark_text'       => trim((string) ($settings['watermark_text'] ?? '')) ?: null,
            'watermark_opacity'    => (float) ($settings['watermark_opacity'] ?? 0.08),
            'report_number_prefix' => trim((string) ($settings['report_number_prefix'] ?? 'AQMI-RPT-')),
            'certification_date'   => !empty($settings['certification_date']) ? $settings['certification_date'] : null,
            'expiration_date'      => !empty($settings['expiration_date']) ? $settings['expiration_date'] : null,
        ];
    }

    private function validateColumnSpan($span): int
    {
        $span = (int) $span;
        return max(1, min(12, $span === 0 ? 12 : $span));
    }

    /**
     * Normalize block config: convert _raw textareas to structured arrays,
     * cast booleans, and strip invalid keys.
     */
    private function normalizeBlockConfig(string $blockKey, array $config): array
    {
        // Convert axes_raw → axes for radar_chart
        if ($blockKey === 'radar_chart' && isset($config['axes_raw'])) {
            $config['axes'] = $this->parseCsvLines($config['axes_raw'], 2, static fn($p) => [
                'label' => $p[0] ?? '', 'value' => (int) ($p[1] ?? 0),
            ]);
            unset($config['axes_raw']);
        }

        // Convert items_raw → items for recommendations
        if ($blockKey === 'recommendations' && isset($config['items_raw'])) {
            $config['items'] = array_values(array_filter(array_map(
                static fn($line) => ['text' => trim($line)],
                explode("\n", (string) $config['items_raw'])
            )));
            unset($config['items_raw']);
        }

        // Convert domains_raw → domains for domain_scores
        if ($blockKey === 'domain_scores' && isset($config['domains_raw'])) {
            $config['domains'] = $this->parseCsvLines($config['domains_raw'], 3, static fn($p) => [
                'label' => $p[0] ?? '', 'score' => (int) ($p[1] ?? 0), 'max' => (int) ($p[2] ?? 100),
            ]);
            unset($config['domains_raw']);
        }

        // Convert company_info field rows → fields array
        if ($blockKey === 'company_info' && isset($config['field_key'])) {
            $keys = $config['field_key'];
            $labels = $config['field_label'] ?? [];
            $fields = [];
            if (is_array($keys)) {
                foreach ($keys as $i => $k) {
                    $fields[] = ['key' => $k, 'label' => $labels[$i] ?? ''];
                }
            }
            $config['fields'] = $fields;
            unset($config['field_key'], $config['field_label']);
        }

        // Cast booleans for known checkbox props
        foreach (['horizontal', 'legend', 'smooth', 'show_rating', 'show_logo', 'show_stamp',
                   'show_date', 'show_number', 'show_page_number', 'show_report_number',
                   'data_source_enabled', 'trend_direction',
                   'show_progress', 'show_markers', 'fill_area', 'stacked', 'show_percent',
                   'show_label', 'show_value', 'numbered', 'uppercase', 'border_bottom',
                   'border_top', 'alternating_rows', 'show_progress_bar', 'show_label_page_break',
                   'icon_bg', 'show_trend'] as $boolKey) {
            if (isset($config[$boolKey])) {
                $config[$boolKey] = filter_var($config[$boolKey], FILTER_VALIDATE_BOOLEAN);
            }
        }

        // Preserve _style_* common style overrides as-is (strings)
        foreach ($config as $k => $v) {
            if (str_starts_with($k, '_style_') && is_string($v)) {
                $config[$k] = trim($v);
                if ($config[$k] === '') {
                    unset($config[$k]);
                }
            }
        }

        return $config;
    }

    /**
     * Parse a textarea of comma-separated lines into structured arrays.
     */
    private function parseCsvLines(string $raw, int $parts, callable $mapper): array
    {
        $result = [];
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $p = str_getcsv($line);
            $result[] = $mapper($p);
        }
        return $result;
    }
}
