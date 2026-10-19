<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Models;

use App\Helpers\Database;
use App\Modules\ReportStudio\Traits\HasJsonFields;

#[\AllowDynamicProperties]
class ReportTemplate
{
    use HasJsonFields;

    protected string $table = 'report_templates';
    protected array $jsonFields = ['settings'];

    public static function all(): array
    {
        $rows = Database::fetchAll("SELECT * FROM report_templates ORDER BY updated_at DESC");
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function recent(int $limit = 6): array
    {
        $rows = Database::fetchAll(
            "SELECT * FROM report_templates ORDER BY updated_at DESC LIMIT ?",
            [$limit]
        );
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function find(int $id): ?self
    {
        $row = Database::fetchOne("SELECT * FROM report_templates WHERE id = ?", [$id]);
        return $row ? (new self())->hydrate($row) : null;
    }

    public static function create(array $data): int
    {
        $instance = new self();
        $data = $instance->packJson($data);
        return Database::insertRow('report_templates', $data);
    }

    public static function update(int $id, array $data): bool
    {
        $instance = new self();
        $data = $instance->packJson($data);
        return Database::updateRow('report_templates', $data, ['id' => $id]);
    }

    public static function delete(int $id): bool
    {
        return Database::deleteRow('report_templates', ['id' => $id]);
    }

    public static function count(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) as cnt FROM report_templates");
        return (int) ($row['cnt'] ?? 0);
    }

    public static function publishedCount(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) as cnt FROM report_templates WHERE status = 'published'");
        return (int) ($row['cnt'] ?? 0);
    }

    public function hydrate(array $row): self
    {
        $row = $this->hydrateJson($row);
        foreach ($row as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    public function toArray(): array
    {
        $vars = get_object_vars($this);
        unset($vars['table'], $vars['jsonFields']);
        return $vars;
    }
}
