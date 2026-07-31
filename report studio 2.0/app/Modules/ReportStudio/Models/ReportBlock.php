<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Models;

use App\Modules\ReportStudio\Traits\HasJsonFields;
use Database;

/**
 * Catalog entry describing one block type the builder can drop.
 *
 * Uses the AQMI Database (PDO) helper directly — no ORM.
 */
class ReportBlock
{
    use HasJsonFields;

    protected string $table = 'report_blocks';
    protected array $jsonFields = ['default_config'];

    public static function all(): array
    {
        $rows = Database::fetchAll("SELECT * FROM {$this->table()} WHERE is_active = 1 ORDER BY sort_order");
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function grouped(): array
    {
        $rows = Database::fetchAll("SELECT * FROM {$this->table()} WHERE is_active = 1 ORDER BY sort_order");
        $grouped = [];
        foreach ($rows as $row) {
            $instance = (new self())->hydrate($row);
            $grouped[$instance->category][] = $instance;
        }
        return $grouped;
    }

    public static function findByKey(string $key): ?self
    {
        $row = Database::fetchOne("SELECT * FROM {$this->table()} WHERE block_key = ?", [$key]);
        return $row ? (new self())->hydrate($row) : null;
    }

    public static function builtInKeys(): array
    {
        return [
            'global_score', 'radar_chart', 'gauge', 'recommendations',
            'company_info', 'aqmi_logo', 'company_logo', 'official_stamp',
            'qr_code', 'signature', 'header', 'footer', 'rich_text', 'image',
        ];
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
        return get_object_vars($this);
    }

    private static function table(): string
    {
        return (new self())->table;
    }
}
