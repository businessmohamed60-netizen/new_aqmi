<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

/**
 * Registry mapping each built-in block_key to its metadata (category,
 * label, icon). Keeps keys + categories in one place so controllers,
 * the builder JS, and the PDF engine stay in sync.
 */
final class BlockRegistry
{
    /**
     * Block key => [category, label, icon].
     */
    private const BLOCKS = [
        'global_score'    => ['metrics',   'Global Score',         'bi-speedometer'],
        'radar_chart'     => ['charts',    'Radar Chart',          'bi-graph-up'],
        'bar_chart'       => ['charts',    'Bar Chart',            'bi-bar-chart'],
        'line_chart'      => ['charts',    'Line Chart',           'bi-graph-up-arrow'],
        'donut_chart'     => ['charts',    'Donut Chart',          'bi-pie-chart'],
        'area_chart'      => ['charts',    'Area Chart',           'bi-graph-up-arrow'],
        'gauge'           => ['metrics',   'Gauge',                'bi-dial'],
        'recommendations' => ['content',   'Recommendations',      'bi-list-check'],
        'company_info'    => ['content',   'Company Information',  'bi-building'],
        'aqmi_logo'       => ['branding',  'AQMI Logo',            'bi-award'],
        'company_logo'    => ['branding',  'Company Logo',         'bi-image'],
        'qr_code'         => ['utility',   'QR Code',              'bi-qr-code'],
        'official_stamp'  => ['branding',  'Official Stamp',       'bi-patch-check-fill'],
        'signature'       => ['utility',   'Signature',            'bi-pen'],
        'header'          => ['structure', 'Header',               'bi-text-left'],
        'footer'          => ['structure', 'Footer',               'bi-text-right'],
        'rich_text'       => ['content',   'Rich Text',            'bi-fonts'],
        'image'           => ['media',     'Image',                'bi-card-image'],
        'background'      => ['media',     'Background',           'bi-image-alt'],
        'cover_page'      => ['structure', 'Cover Page',           'bi-bookmark-star'],
        'kpi_card'        => ['metrics',   'KPI Card',             'bi-calendar2-check'],
        'domain_scores'   => ['metrics',   'Domain Scores Table',  'bi-table'],
        'page_break'      => ['structure', 'Page Break',           'bi-file-earmark-break'],
    ];

    public static function all(): array
    {
        return self::BLOCKS;
    }

    public static function keys(): array
    {
        return array_keys(self::BLOCKS);
    }

    public static function has(string $blockKey): bool
    {
        return isset(self::BLOCKS[$blockKey]);
    }

    public static function meta(string $blockKey): ?array
    {
        return self::BLOCKS[$blockKey] ?? null;
    }

    /**
     * Grouped by category for the block library sidebar.
     */
    public static function grouped(): array
    {
        $grouped = [];
        foreach (self::BLOCKS as $key => $meta) {
            [$category, $label, $icon] = $meta;
            $grouped[$category][] = [
                'block_key' => $key,
                'label'     => $label,
                'icon'      => $icon,
            ];
        }
        return $grouped;
    }

}
