<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Models;

use App\Helpers\Database;
use App\Modules\ReportStudio\Traits\HasJsonFields;

#[\AllowDynamicProperties]
class ReportBlock
{
    use HasJsonFields;

    protected string $table = 'report_blocks';
    protected array $jsonFields = ['default_config'];

    public static function all(): array
    {
        $rows = Database::fetchAll("SELECT * FROM report_blocks WHERE is_active = 1 ORDER BY sort_order");
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function grouped(): array
    {
        $rows = Database::fetchAll("SELECT * FROM report_blocks WHERE is_active = 1 ORDER BY sort_order");
        $grouped = [];
        foreach ($rows as $row) {
            $instance = (new self())->hydrate($row);
            $grouped[$instance->category][] = $instance;
        }
        return $grouped;
    }

    public static function findByKey(string $key): ?self
    {
        $row = Database::fetchOne("SELECT * FROM report_blocks WHERE block_key = ?", [$key]);
        return $row ? (new self())->hydrate($row) : null;
    }

    public static function builtInKeys(): array
    {
        return [
            'global_score', 'radar_chart', 'bar_chart', 'line_chart',
            'donut_chart', 'area_chart', 'gauge', 'recommendations',
            'company_info', 'aqmi_logo', 'company_logo', 'official_stamp',
            'qr_code', 'signature', 'header', 'footer', 'rich_text', 'image',
            'cover_page', 'kpi_card', 'domain_scores', 'page_break',
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
        $vars = get_object_vars($this);
        unset($vars['table'], $vars['jsonFields']);
        return $vars;
    }
}
