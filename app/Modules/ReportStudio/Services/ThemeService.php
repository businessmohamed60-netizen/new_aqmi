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
        $data = $this->validate($post);
        $id = ReportTheme::create($data);
        if ($data['is_default']) {
            $this->unsetOtherDefaults($id);
        }
        return $id;
    }

    public function updateTheme(int $id, array $post): bool
    {
        $data = $this->validate($post);
        $ok = ReportTheme::update($id, $data);
        if ($ok && $data['is_default']) {
            $this->unsetOtherDefaults($id);
        }
        return $ok;
    }

    public function deleteTheme(int $id): bool
    {
        $theme = ReportTheme::find($id);
        if (!$theme || $theme->is_default) {
            return false;
        }
        return ReportTheme::delete($id);
    }

    private function unsetOtherDefaults(int $keepId): void
    {
        \App\Helpers\Database::query(
            'UPDATE report_themes SET is_default = 0 WHERE id != ?',
            [$keepId]
        );
    }

    private function validate(array $post): array
    {
        $cssVariables = $post['css_variables'] ?? [];
        if (!is_array($cssVariables)) $cssVariables = [];

        // Persist block alignment data from the drag-and-drop layout editor
        $blockAlign = [];
        if (!empty($post['block_align_json'])) {
            $decoded = json_decode($post['block_align_json'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $block => $align) {
                    if (in_array($align, ['left', 'center', 'right'], true)) {
                        $blockAlign[$block] = $align;
                    }
                }
            }
        }
        if (!empty($blockAlign)) {
            $cssVariables['block_align'] = $blockAlign;
        }

        return [
            'name'             => trim((string) ($post['name'] ?? '')),
            'description'      => trim((string) ($post['description'] ?? '')),
            'primary_color'    => trim((string) ($post['primary_color'] ?? '#102A43')),
            'secondary_color'  => trim((string) ($post['secondary_color'] ?? '#486581')),
            'accent_color'     => trim((string) ($post['accent_color'] ?? '#2EC4B6')),
            'heading_color'    => !empty($post['heading_color']) ? trim($post['heading_color']) : null,
            'body_color'       => !empty($post['body_color']) ? trim($post['body_color']) : null,
            'background_color' => trim((string) ($post['background_color'] ?? '#ffffff')),
            'font_family'      => trim((string) ($post['font_family'] ?? 'Inter, Arial, sans-serif')),
            'css_variables'    => $cssVariables,
            'is_default'       => (bool) ($post['is_default'] ?? false),
            'is_active'        => (bool) ($post['is_active'] ?? true),
        ];
    }
}
