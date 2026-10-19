<?php
namespace App\Models;

use App\Helpers\Database;

class Domain
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM domains WHERE id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM domains ORDER BY sort_order");
    }

    public static function allActive(): array
    {
        return Database::fetchAll("SELECT * FROM domains WHERE is_active = 1 ORDER BY sort_order");
    }

    public static function allWithInactive(): array
    {
        return Database::fetchAll("SELECT * FROM domains ORDER BY sort_order");
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO domains (name, name_fr, name_ar, description, description_fr, description_ar, icon, weight, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['name'], $data['name_fr'] ?? '', $data['name_ar'] ?? '', $data['description'] ?? '', $data['description_fr'] ?? '', $data['description_ar'] ?? '', $data['icon'] ?? 'fa-folder', $data['weight'] ?? 1, $data['sort_order'] ?? 0, $data['is_active'] ?? 1]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['name', 'name_fr', 'name_ar', 'description', 'description_fr', 'description_ar', 'icon', 'weight', 'sort_order', 'is_active'] as $key) {
            if (isset($data[$key])) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE domains SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM domains WHERE id = ?", [$id]);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM domains")['count'];
    }
}