<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Controllers;

use App\Modules\ReportStudio\Services\ThemeService;

/**
 * CRUD for report themes (colors, fonts, CSS variables).
 * Receives forms directly from Views via standard POST submissions.
 */
class ThemeController
{
    public function index(): void
    {
        $service = new ThemeService();
        view('reportstudio/themes/index', [
            'themes' => $service->listThemes(),
        ]);
    }

    public function create(): void
    {
        view('reportstudio/themes/form', ['theme' => null]);
    }

    public function store(): void
    {
        $service = new ThemeService();
        $service->createTheme($_POST);
        redirect(route('reportstudio.themes.index'));
    }

    public function edit(int $id): void
    {
        $service = new ThemeService();
        $theme = $service->getTheme($id);
        if (!$theme) {
            abort(404);
        }
        view('reportstudio/themes/form', ['theme' => $theme->toArray()]);
    }

    public function update(int $id): void
    {
        $service = new ThemeService();
        $service->updateTheme($id, $_POST);
        redirect(route('reportstudio.themes.index'));
    }

    public function destroy(int $id): void
    {
        $service = new ThemeService();
        $service->deleteTheme($id);
        redirect(route('reportstudio.themes.index'));
    }
}
