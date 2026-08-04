<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

/**
 * Renders a report block partial, passing config + template context.
 * Used by the preview view to iterate blocks and include their partials.
 */
class TemplateRenderer
{
    /**
     * Render a single block partial by its block_key.
     * If the block has a data_source binding, live data is fetched
     * from the database and merged into the config before rendering.
     *
     * @param string $blockKey   e.g. 'global_score'
     * @param array  $config     block config array
     * @param string $title      optional block title
     * @param array  $template   full template row (for dynamic vars in header/footer)
     * @param string $reportNumber  resolved report number string
     */
    public function renderBlock(string $blockKey, array $config, string $title = '', array $template = [], string $reportNumber = ''): string
    {
        $partialPath = BASE_PATH . '/resources/views/reportstudio/report/' . $blockKey . '.php';

        if (!file_exists($partialPath)) {
            return '';
        }

        // Resolve data-source binding: fetch live data from DB if configured
        if (!empty($config['data_source']['table'])) {
            $dsService = new DataSourceService();
            $config = $dsService->resolveBlockConfig($config);
        }

        ob_start();
        require $partialPath;
        return ob_get_clean();
    }

    /**
     * Render all blocks for a given context (web or pdf).
     */
    public function renderAll(array $blocks, array $template, string $reportNumber, string $context = 'web'): string
    {
        $previewService = new PreviewService();
        $filtered = $previewService->filterByVisibility($blocks, $context);

        $html = '';
        foreach ($filtered as $block) {
            $html .= $this->renderBlock(
                $block['block_key'] ?? '',
                $block['block_config'] ?? [],
                $block['title'] ?? '',
                $template,
                $reportNumber
            );
        }
        return $html;
    }
}
