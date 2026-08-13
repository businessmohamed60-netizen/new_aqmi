<?php
namespace App\Models;

use App\Helpers\Database;

class Assessment
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM assessments WHERE id = ?", [$id]);
    }

    public static function findBySession(string $sessionId): ?array
    {
        return Database::fetch("SELECT * FROM assessments WHERE session_id = ? ORDER BY id DESC LIMIT 1", [$sessionId]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO assessments (user_id, session_id, model_id, status, current_step) VALUES (?, ?, ?, ?, ?)",
            [$data['user_id'] ?? null, $data['session_id'], $data['model_id'] ?? null, $data['status'] ?? 'in_progress', $data['current_step'] ?? 0]
        );
    }

    public static function setModel(int $id, int $modelId): int
    {
        return Database::execute("UPDATE assessments SET model_id = ? WHERE id = ?", [$modelId, $id]);
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        foreach (['status', 'current_step', 'total_score', 'maturity_level', 'completed_at', 'model_id'] as $key) {
            if (array_key_exists($key, $data)) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE assessments SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM assessments")['count'];
    }

    public static function countCompleted(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM assessments WHERE status = 'completed'")['count'];
    }

    public static function getAverageScore(): float
    {
        $result = Database::fetch("SELECT AVG(total_score) as avg_score FROM assessments WHERE status = 'completed'");
        return (float)($result['avg_score'] ?? 0);
    }

    public static function getMonthlyStats(int $months = 6): array
    {
        return Database::fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed FROM assessments WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month",
            [$months]
        );
    }

    public static function getRecent(int $limit = 10): array
    {
        return Database::fetchAll("SELECT a.*, l.company, l.firstname, l.lastname FROM assessments a LEFT JOIN leads l ON a.id = l.assessment_id ORDER BY a.created_at DESC LIMIT ?", [$limit]);
    }

    public static function updateScore(int $id, float $totalScore, string $level): int
    {
        return Database::execute("UPDATE assessments SET total_score = ?, maturity_level = ? WHERE id = ?", [$totalScore, $level, $id]);
    }

    public static function complete(int $id): int
    {
        return Database::execute("UPDATE assessments SET status = 'completed', completed_at = NOW() WHERE id = ?", [$id]);
    }

    public static function getCompletionRate(): float
    {
        $total = self::count();
        if ($total === 0) return 0;
        $completed = self::countCompleted();
        return round(($completed / $total) * 100, 1);
    }
}