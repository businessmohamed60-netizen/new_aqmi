<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

/**
 * Registry mapping each built-in block_key to its metadata and the
 * name of the partial that renders it. Keeps keys + categories in one
 * place so controllers, the builder JS, and the PDF engine stay in sync.
 */
final class BlockRegistry
{
    /**
     * Block key => [category, label, icon, renderer partial].
     */
    private const BLOCKS = [
        'global_score'    => ['metrics',   'Global Score',         'bi-speedometer',  'blocks/global_score'],
        'radar_chart'     => ['charts',    'Radar Chart',          'bi-graph-up',     'blocks/radar_chart'],
        'gauge'           => ['metrics',   'Gauge',                'bi-dial',         'blocks/gauge'],
        'recommendations' => ['content',   'Recommendations',      'bi-list-check',   'blocks/recommendations'],
        'company_info'    => ['content',   'Company Information',  'bi-building',     'blocks/company_info'],
        'aqmi_logo'       => ['branding',  'AQMI Logo',            'bi-award',        'blocks/aqmi_logo'],
        'company_logo'    => ['branding',  'Company Logo',         'bi-image',        'blocks/company_logo'],
        'qr_code'         => ['utility',   'QR Code',              'bi-qr-code',      'blocks/qr_code'],
        'official_stamp'  => ['branding',  'Official Stamp',       'bi-patch-check-fill', 'blocks/official_stamp'],
        'signature'       => ['utility',   'Signature',            'bi-pen',          'blocks/signature'],
        'header'          => ['structure', 'Header',               'bi-text-left',    'blocks/header'],
        'footer'          => ['structure', 'Footer',               'bi-text-right',   'blocks/footer'],
        'rich_text'       => ['content',   'Rich Text',            'bi-fonts',        'blocks/rich_text'],
        'image'           => ['media',     'Image',                'bi-card-image',   'blocks/image'],
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
            [$category, $label, $icon, $partial] = $meta;
            $grouped[$category][] = [
                'block_key' => $key,
                'label'     => $label,
                'icon'      => $icon,
                'partial'   => $partial,
            ];
        }
        return $grouped;
    }

    public static function partial(string $blockKey): ?string
    {
        return self::BLOCKS[$blockKey][3] ?? null;
    }
}
