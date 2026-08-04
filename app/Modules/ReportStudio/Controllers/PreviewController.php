<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\PreviewService;
use App\Modules\ReportStudio\Services\TemplateRenderer;

/**
 * Live preview of an assembled report template.
 * Renders the full report using the theme + ordered blocks.
 */
class PreviewController
{
    public function show(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $previewService = new PreviewService();
        $data = $previewService->loadForPreview($id);
        if (!$data) {
            abort(404);
        }

        view('reportstudio/preview/show', $data);
    }
}
