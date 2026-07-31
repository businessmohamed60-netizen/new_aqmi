<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\TemplateService;

/**
 * Landing dashboard for the Report Studio module.
 * Shows recent templates and quick entry points into the builder.
 */
class ReportStudioController
{
    public function index(): void
    {
        $service = new TemplateService();
        $stats = $service->dashboardStats();

        view('reportstudio/dashboard', $stats);
    }
}
