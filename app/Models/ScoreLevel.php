<?php
namespace App\Models;

use App\Helpers\Database;

class ScoreLevel
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM score_levels WHERE id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM score_levels WHERE is_active = 1 ORDER BY sort_order");
    }

    public static function allWithInactive(): array
    {
        return Database::fetchAll("SELECT * FROM score_levels ORDER BY sort_order");
    }

    public static function findByScore(float $percent): ?array
    {
        return Database::fetch(
            "SELECT * FROM score_levels WHERE ? >= min_percent AND ? <= max_percent AND is_active = 1 ORDER BY sort_order ASC LIMIT 1",
            [$percent, $percent]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO score_levels (name, name_fr, name_ar, min_percent, max_percent, color, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['name'], $data['name_fr'] ?? '', $data['name_ar'] ?? '', $data['min_percent'], $data['max_percent'], $data['color'] ?? '#6c757d', $data['icon'] ?? 'fa-chart-bar', $data['sort_order'] ?? 0, $data['is_active'] ?? 1]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['name', 'name_fr', 'name_ar', 'min_percent', 'max_percent', 'color', 'icon', 'sort_order', 'is_active'] as $key) {
            if (isset($data[$key])) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE score_levels SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM score_levels WHERE id = ?", [$id]);
    }
}