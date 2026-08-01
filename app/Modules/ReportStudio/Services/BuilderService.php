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
                'block_config' => (array) ($item['block_config'] ?? []),
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
}
