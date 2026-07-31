<?php
declare(strict_types=1);

namespace App\Modules\ReportStudio\Models;

use App\Modules\ReportStudio\Traits\HasJsonFields;
use Database;

/**
 * A reusable report theme (colors, fonts, CSS variables).
 *
 * Uses the AQMI Database (PDO) helper directly — no ORM.
 */
class ReportTheme
{
    use HasJsonFields;

    protected string $table = 'report_themes';
    protected array $jsonFields = ['css_variables'];

    public static function all(): array
    {
        $rows = Database::fetchAll("SELECT * FROM report_themes ORDER BY is_default DESC, name ASC");
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
    }

    public static function find(int $id): ?self
    {
        $row = Database::fetchOne("SELECT * FROM report_themes WHERE id = ?", [$id]);
        return $row ? (new self())->hydrate($row) : null;
    }

    public static function create(array $data): int
    {
        $instance = new self();
        $data = $instance->packJson($data);
        return Database::insert('report_themes', $data);
    }

    public static function update(int $id, array $data): bool
    {
        $instance = new self();
        $data = $instance->packJson($data);
        return Database::update('report_themes', $data, ['id' => $id]);
    }

    public static function delete(int $id): bool
    {
        return Database::delete('report_themes', ['id' => $id]);
    }

    public static function findDefault(): ?self
    {
        $row = Database::fetchOne("SELECT * FROM report_themes WHERE is_default = 1 LIMIT 1");
        return $row ? (new self())->hydrate($row) : null;
    }

    public static function activeList(): array
    {
        $rows = Database::fetchAll("SELECT * FROM report_themes WHERE is_active = 1 ORDER BY name ASC");
        return array_map(fn($r) => (new self())->hydrate($r), $rows);
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

    /**
     * Return CSS custom properties derived from this theme.
     */
    public function toCssVars(): array
    {
        $vars = [
            '--rs-primary'    => $this->primary_color ?? '#0d47a1',
            '--rs-secondary'  => $this->secondary_color ?? '#546e7a',
            '--rs-accent'     => $this->accent_color ?? '#00897b',
            '--rs-background' => $this->background_color ?? '#ffffff',
            '--rs-font'       => $this->font_family ?? 'Inter, Arial, sans-serif',
        ];

        if (!empty($this->heading_color)) {
            $vars['--rs-heading'] = $this->heading_color;
        }
        if (!empty($this->body_color)) {
            $vars['--rs-body'] = $this->body_color;
        }

        foreach ($this->css_variables ?? [] as $key => $value) {
            $vars['--rs-' . $key] = $value;
        }

        return $vars;
    }
}
