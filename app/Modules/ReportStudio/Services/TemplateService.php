<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Services;

use App\Modules\ReportStudio\Models\ReportBlock;
use App\Modules\ReportStudio\Models\ReportTemplate;

/**
 * Handles template metadata CRUD (not the block layout — that's BuilderService).
 */
class TemplateService
{
    public function listTemplates(): array
    {
        return ReportTemplate::all();
    }

    public function recentTemplates(int $limit = 6): array
    {
        return ReportTemplate::recent($limit);
    }

    public function getTemplate(int $id): ?ReportTemplate
    {
        return ReportTemplate::find($id);
    }

    public function createTemplate(array $post): int
    {
        return ReportTemplate::create($this->validate($post));
    }

    public function updateTemplate(int $id, array $post): bool
    {
        return ReportTemplate::update($id, $this->validate($post));
    }

    public function deleteTemplate(int $id): bool
    {
        $template = ReportTemplate::find($id);
        if (!$template || $template->is_system) {
            return false;
        }
        return ReportTemplate::delete($id);
    }

    public function dashboardStats(): array
    {
        return [
            'recent'         => array_map(fn($t) => $t->toArray(), ReportTemplate::recent(6)),
            'template_count' => ReportTemplate::count(),
            'published'      => ReportTemplate::publishedCount(),
            'block_count'    => count(ReportBlock::all()),
        ];
    }

    private function validate(array $post): array
    {
        return [
            'name'        => trim((string) ($post['name'] ?? '')),
            'description' => trim((string) ($post['description'] ?? '')),
            'category'    => trim((string) ($post['category'] ?? '')),
            'status'      => in_array($post['status'] ?? '', ['draft', 'published', 'archived'], true)
                                ? $post['status'] : 'draft',
        ];
    }
}
