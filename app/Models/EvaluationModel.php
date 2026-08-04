<?php
namespace App\Models;

use App\Helpers\Database;

class EvaluationModel
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM evaluation_models WHERE id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM evaluation_models ORDER BY sort_order");
    }

    public static function allActive(): array
    {
        return Database::fetchAll("SELECT * FROM evaluation_models WHERE is_active = 1 ORDER BY sort_order");
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO evaluation_models (name, name_fr, name_ar, description, description_fr, description_ar, icon, color, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['name'], $data['name_fr'] ?? '', $data['name_ar'] ?? '', $data['description'] ?? '', $data['description_fr'] ?? '', $data['description_ar'] ?? '', $data['icon'] ?? 'fa-clipboard-check', $data['color'] ?? '#1a56db', $data['sort_order'] ?? 0, $data['is_active'] ?? 1]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['name', 'name_fr', 'name_ar', 'description', 'description_fr', 'description_ar', 'icon', 'color', 'sort_order', 'is_active'] as $key) {
            if (isset($data[$key])) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE evaluation_models SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM evaluation_models WHERE id = ?", [$id]);
    }

    public static function getDomains(int $modelId): array
    {
        return Database::fetchAll(
            "SELECT d.*, md.sort_order as pivot_order FROM model_domains md JOIN domains d ON md.domain_id = d.id WHERE md.model_id = ? ORDER BY md.sort_order",
            [$modelId]
        );
    }

    public static function setDomains(int $modelId, array $domainIds): void
    {
        Database::execute("DELETE FROM model_domains WHERE model_id = ?", [$modelId]);
        foreach ($domainIds as $i => $did) {
            Database::insert("INSERT INTO model_domains (model_id, domain_id, sort_order) VALUES (?, ?, ?)", [$modelId, (int)$did, $i]);
        }
    }

    public static function getQuestionsCount(int $modelId): int
    {
        return (int)Database::fetch(
            "SELECT COUNT(*) as c FROM questions q JOIN model_domains md ON q.domain_id = md.domain_id WHERE md.model_id = ? AND q.is_active = 1",
            [$modelId]
        )['c'];
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM evaluation_models")['count'];
    }
}