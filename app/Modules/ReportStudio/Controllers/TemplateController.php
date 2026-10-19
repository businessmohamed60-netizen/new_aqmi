<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Helpers\Database;
use App\Modules\ReportStudio\Services\BuilderService;
use App\Modules\ReportStudio\Services\TemplateService;

/**
 * CRUD for report templates (metadata only — block layout is in BuilderController).
 * Receives forms directly from Views via standard POST submissions.
 */
class TemplateController
{
    public function index(): void
    {
        $templates = [];
        if (Database::isConnected()) {
            try {
                $service = new TemplateService();
                $templates = array_map(fn($t) => $t->toArray(), $service->listTemplates());
            } catch (\Throwable $e) {
                error_log('ReportStudio templates list error: ' . $e->getMessage());
            }
        }
        view('reportstudio/templates/index', [
            'templates' => $templates,
        ]);
    }

    public function show(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        try {
            $service = new TemplateService();
            $template = $service->getTemplate($id);
        } catch (\Throwable $e) {
            error_log('ReportStudio template show error: ' . $e->getMessage());
            $template = null;
        }
        if (!$template) {
            abort(404);
        }

        $builderService = new BuilderService();
        $data = $builderService->loadForBuilder($id);

        view('reportstudio/templates/detail', [
            'template' => $data,
        ]);
    }

    public function create(): void
    {
        view('reportstudio/templates/form', [
            'template' => null,
        ]);
    }

    public function store(): void
    {
        $service = new TemplateService();
        $id = $service->createTemplate($_POST);
        redirect(route('reportstudio.builder.edit', ['id' => $id]));
    }

    public function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        try {
            $service = new TemplateService();
            $template = $service->getTemplate($id);
        } catch (\Throwable $e) {
            error_log('ReportStudio template edit error: ' . $e->getMessage());
            $template = null;
        }
        if (!$template) {
            abort(404);
        }

        view('reportstudio/templates/form', [
            'template' => $template->toArray(),
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $service = new TemplateService();
        $service->updateTemplate($id, $_POST);
        redirect(route('reportstudio.templates.index'));
    }

    public function destroy(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $service = new TemplateService();
        $service->deleteTemplate($id);
        redirect(route('reportstudio.templates.index'));
    }
}
