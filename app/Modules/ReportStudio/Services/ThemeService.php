<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Modules\ReportStudio\Models\ReportTheme;

/**
 * Handles theme CRUD and compilation.
 */
class ThemeService
{
    public function listThemes(): array
    {
        return ReportTheme::all();
    }

    public function getTheme(int $id): ?ReportTheme
    {
        return ReportTheme::find($id);
    }

    public function activeThemes(): array
    {
        return ReportTheme::activeList();
    }

    public function defaultTheme(): ?ReportTheme
    {
        return ReportTheme::findDefault();
    }

    public function createTheme(array $post): int
    {
        return ReportTheme::create($this->validate($post));
    }

    public function updateTheme(int $id, array $post): bool
    {
        return ReportTheme::update($id, $this->validate($post));
    }

    public function deleteTheme(int $id): bool
    {
        $theme = ReportTheme::find($id);
        if (!$theme || $theme->is_default) {
            return false;
        }
        return ReportTheme::delete($id);
    }

    private function validate(array $post): array
    {
        return [
            'name'             => trim((string) ($post['name'] ?? '')),
            'description'      => trim((string) ($post['description'] ?? '')),
            'primary_color'    => trim((string) ($post['primary_color'] ?? '#0d47a1')),
            'secondary_color'  => trim((string) ($post['secondary_color'] ?? '#546e7a')),
            'accent_color'     => trim((string) ($post['accent_color'] ?? '#00897b')),
            'heading_color'    => !empty($post['heading_color']) ? trim($post['heading_color']) : null,
            'body_color'       => !empty($post['body_color']) ? trim($post['body_color']) : null,
            'background_color' => trim((string) ($post['background_color'] ?? '#ffffff')),
            'font_family'      => trim((string) ($post['font_family'] ?? 'Inter, Arial, sans-serif')),
            'css_variables'    => $post['css_variables'] ?? [],
            'is_default'       => (bool) ($post['is_default'] ?? false),
            'is_active'        => (bool) ($post['is_active'] ?? true),
        ];
    }
}
