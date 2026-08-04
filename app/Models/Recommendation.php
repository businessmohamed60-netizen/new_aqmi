<?php
namespace App\Models;

use App\Helpers\Database;

class Recommendation
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM recommendations WHERE id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT r.*, d.name as domain_name, d.name_fr as domain_name_fr FROM recommendations r LEFT JOIN domains d ON r.domain_id = d.id WHERE r.is_active = 1 ORDER BY FIELD(r.priority, 'critical', 'high', 'medium', 'low'), r.id");
    }

    public static function allWithInactive(): array
    {
        return Database::fetchAll("SELECT r.*, d.name as domain_name FROM recommendations r LEFT JOIN domains d ON r.domain_id = d.id ORDER BY FIELD(r.priority, 'critical', 'high', 'medium', 'low'), r.id");
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO recommendations (domain_id, condition_field, condition_operator, condition_value, recommendation_text, recommendation_text_fr, recommendation_text_ar, priority, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['domain_id'] ?? null, $data['condition_field'], $data['condition_operator'] ?? '<', $data['condition_value'], $data['recommendation_text'], $data['recommendation_text_fr'] ?? '', $data['recommendation_text_ar'] ?? '', $data['priority'] ?? 'medium', $data['is_active'] ?? 1]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['domain_id', 'condition_field', 'condition_operator', 'condition_value', 'recommendation_text', 'recommendation_text_fr', 'recommendation_text_ar', 'priority', 'is_active'] as $key) {
            if (isset($data[$key])) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE recommendations SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM recommendations WHERE id = ?", [$id]);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM recommendations")['count'];
    }

    public static function getActiveRules(): array
    {
        return Database::fetchAll("SELECT * FROM recommendations WHERE is_active = 1 ORDER BY FIELD(priority, 'critical', 'high', 'medium', 'low')");
    }
}