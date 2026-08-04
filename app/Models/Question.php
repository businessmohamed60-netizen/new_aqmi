<?php
namespace App\Models;

use App\Helpers\Database;

class Question
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT q.*, d.name as domain_name, d.name_fr as domain_name_fr, d.icon as domain_icon FROM questions q JOIN domains d ON q.domain_id = d.id WHERE q.id = ?", [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT q.*, d.name as domain_name, d.name_fr as domain_name_fr, d.icon as domain_icon, d.sort_order as domain_sort FROM questions q JOIN domains d ON q.domain_id = d.id WHERE q.is_active = 1 AND d.is_active = 1 ORDER BY d.sort_order, q.sort_order");
    }

    public static function getByDomain(int $domainId): array
    {
        return Database::fetchAll("SELECT * FROM questions WHERE domain_id = ? AND is_active = 1 ORDER BY sort_order", [$domainId]);
    }

    public static function getActiveByDomain(int $domainId): array
    {
        return Database::fetchAll("SELECT * FROM questions WHERE domain_id = ? AND is_active = 1 ORDER BY sort_order", [$domainId]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO questions (domain_id, model_id, question_type, title, title_fr, title_ar, options, is_required, help_text, help_text_fr, help_text_ar, description, description_fr, description_ar, weight, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['domain_id'],
                $data['model_id'] ?? null,
                $data['question_type'] ?? 'rating_scale',
                $data['title'] ?? '', $data['title_fr'] ?? '', $data['title_ar'] ?? '',
                $data['options'] ?? null,
                $data['is_required'] ?? 1,
                $data['help_text'] ?? '', $data['help_text_fr'] ?? '', $data['help_text_ar'] ?? '',
                $data['description'] ?? '', $data['description_fr'] ?? '', $data['description_ar'] ?? '',
                $data['weight'] ?? 1, $data['sort_order'] ?? 0, $data['is_active'] ?? 1
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        $allowed = ['domain_id', 'model_id', 'question_type', 'title', 'title_fr', 'title_ar', 'options', 'is_required', 'help_text', 'help_text_fr', 'help_text_ar', 'description', 'description_fr', 'description_ar', 'weight', 'sort_order', 'is_active'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE questions SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM questions WHERE id = ?", [$id]);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM questions")['count'];
    }

    public static function getActiveCount(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM questions WHERE is_active = 1")['count'];
    }

    public static function updateSortOrder(int $id, int $order): int
    {
        return Database::execute("UPDATE questions SET sort_order = ? WHERE id = ?", [$order, $id]);
    }

    public static function getAllWithDomain(): array
    {
        return Database::fetchAll("SELECT q.*, d.name as domain_name, d.name_fr as domain_name_fr, d.icon as domain_icon FROM questions q JOIN domains d ON q.domain_id = d.id ORDER BY d.sort_order, q.sort_order");
    }
}