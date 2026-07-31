<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\BuilderService;
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
    public function edit(int $id): void
    {
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
    public function update(int $id): void
    {
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
}
