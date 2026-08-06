<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\BuilderService;
use App\Modules\ReportStudio\Services\DataSourceService;
use App\Modules\ReportStudio\Services\PreviewService;
use App\Modules\ReportStudio\Services\TemplateService;
use App\Modules\ReportStudio\Services\ThemeService;
use App\Modules\ReportStudio\Services\BlockRegistry;

/**
 * Drag & drop report builder.
 *
 * GET  /builder/{id}/edit   renders the 3-pane builder interface
 * PUT  /builder/{id}        AJAX-only: persists block layout + settings
 */
class BuilderController
{
    public function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $builderService = new BuilderService();
        $data = $builderService->loadForBuilder($id);
        if (!$data) {
            abort(404);
        }

        $themeService = new ThemeService();
        $theme = null;
        if (!empty($data['template']['theme_id'])) {
            $theme = $themeService->getTheme((int) $data['template']['theme_id']);
        }
        if (!$theme) {
            $theme = $themeService->defaultTheme();
        }

        $compiler = new \App\Modules\ReportStudio\Services\ThemeCompiler();

        view('reportstudio/builder/canvas', [
            'template'  => $data,
            'palette'   => BlockRegistry::grouped(),
            'themeCss'  => $theme ? $compiler->toCss($theme) : '',
        ]);
    }

    /**
     * AJAX endpoint: persists the block layout + template settings.
     * Expects JSON body: { blocks: [...], settings: {...} }
     */
    public function update(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
            return;
        }

        $blocks = $input['blocks'] ?? [];
        $settings = $input['settings'] ?? [];

        $builderService = new BuilderService();
        $count = $builderService->saveLayout($id, $blocks, $settings);

        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'count' => $count]);
    }

    /**
     * AJAX: list all tables available for data-source binding.
     */
    public function dataSources(): void
    {
        $svc = new DataSourceService();
        jsonResponse(['ok' => true, 'tables' => $svc->listTables()]);
    }

    /**
     * AJAX: get columns + preview rows for a specific table.
     */
    public function tableInfo(array $params): void
    {
        $table = $_GET['table'] ?? ($params['table'] ?? '');
        $svc = new DataSourceService();

        if (!$svc->isAllowedTable((string) $table)) {
            jsonResponse(['ok' => false, 'error' => 'Table not allowed'], 400);
        }

        $columns = $svc->getColumns((string) $table);
        $rows    = $svc->previewRows((string) $table);

        jsonResponse(['ok' => true, 'columns' => $columns, 'rows' => $rows]);
    }

    /**
     * AJAX: preview chart data for a given data-source config.
     */
    public function dataPreview(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            jsonResponse(['ok' => false, 'error' => 'Invalid JSON'], 400);
        }

        $svc = new DataSourceService();
        $data = $svc->fetchChartData($input);

        jsonResponse(['ok' => true, 'data' => $data]);
    }
}
