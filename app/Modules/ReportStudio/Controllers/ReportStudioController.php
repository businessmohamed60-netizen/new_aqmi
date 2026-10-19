<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Helpers\Database;
use App\Modules\ReportStudio\Services\TemplateService;

/**
 * Landing dashboard for the Report Studio module.
 * Shows recent templates and quick entry points into the builder.
 * Gracefully degrades when the database is unavailable.
 */
class ReportStudioController
{
    public function index(): void
    {
        if (Database::isConnected()) {
            try {
                $service = new TemplateService();
                $stats = $service->dashboardStats();
            } catch (\Throwable $e) {
                error_log('ReportStudio dashboard error: ' . $e->getMessage());
                $stats = $this->fallbackStats();
            }
        } else {
            $stats = $this->fallbackStats();
        }

        view('reportstudio/dashboard', $stats);
    }

    private function fallbackStats(): array
    {
        return [
            'recent'         => [],
            'template_count' => 0,
            'published'      => 0,
            'block_count'    => 14,
        ];
    }
}
