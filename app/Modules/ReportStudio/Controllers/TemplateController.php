<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

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
        $service = new TemplateService();
        view('reportstudio/templates/index', [
            'templates' => array_map(fn($t) => $t->toArray(), $service->listTemplates()),
        ]);
    }

    public function show(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $service = new TemplateService();
        $template = $service->getTemplate($id);
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
        $service = new TemplateService();
        $template = $service->getTemplate($id);
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
