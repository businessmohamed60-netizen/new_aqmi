<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\TemplateService;
use App\Modules\ReportStudio\Services\ThemeService;

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
            'templates' => $service->listTemplates(),
        ]);
    }

    public function show(int $id): void
    {
        $service = new TemplateService();
        $template = $service->getTemplate($id);
        if (!$template) {
            abort(404);
        }

        view('reportstudio/templates/detail', [
            'template' => $template->toArray(),
        ]);
    }

    public function create(): void
    {
        $themeService = new ThemeService();
        view('reportstudio/templates/form', [
            'template' => null,
            'themes'   => $themeService->activeThemes(),
        ]);
    }

    public function store(): void
    {
        $service = new TemplateService();
        $id = $service->createTemplate($_POST);
        redirect(route('reportstudio.builder.edit', ['id' => $id]));
    }

    public function edit(int $id): void
    {
        $service = new TemplateService();
        $template = $service->getTemplate($id);
        if (!$template) {
            abort(404);
        }

        $themeService = new ThemeService();
        view('reportstudio/templates/form', [
            'template' => $template->toArray(),
            'themes'   => $themeService->activeThemes(),
        ]);
    }

    public function update(int $id): void
    {
        $service = new TemplateService();
        $service->updateTemplate($id, $_POST);
        redirect(route('reportstudio.templates.index'));
    }

    public function destroy(int $id): void
    {
        $service = new TemplateService();
        $service->deleteTemplate($id);
        redirect(route('reportstudio.templates.index'));
    }
}
