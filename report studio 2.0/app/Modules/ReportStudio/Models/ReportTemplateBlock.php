<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Models;

use App\Modules\ReportStudio\Traits\HasJsonFields;
use Database;

/**
 * A concrete block instance placed on a template by the builder.
 *
 * Uses the AQMI Database (PDO) helper directly — no ORM.
 */
class ReportTemplateBlock
{
    use HasJsonFields;

    protected string $table = 'report_template_blocks';
    protected array $jsonFields = ['block_config'];

    public static function forTemplate(int $templateId): array
    {
        $rows = Database::fetchAll(
            "SELECT * FROM report_template_blocks WHERE template_id = ? ORDER BY sort_order",
            [$templateId]
        );
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function enabledForTemplate(int $templateId): array
    {
        $rows = Database::fetchAll(
            "SELECT * FROM report_template_blocks WHERE template_id = ? AND is_enabled = 1 ORDER BY sort_order",
            [$templateId]
        );
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function replaceForTemplate(int $templateId, array $rows): void
    {
        Database::delete('report_template_blocks', ['template_id' => $templateId]);

        foreach ($rows as $row) {
            $instance = new self();
            $row['block_config'] = $row['block_config'] ?? [];
            $row = $instance->packJson($row);
            Database::insert('report_template_blocks', $row);
        }
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
