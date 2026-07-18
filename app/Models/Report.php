<?php
namespace App\Models;

use App\Helpers\Database;

class Report
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE id = ?", [$id]);
    }

    public static function findByAssessment(int $assessmentId): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE assessment_id = ? ORDER BY id DESC LIMIT 1", [$assessmentId]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO reports (assessment_id, lead_id, file_path, status) VALUES (?, ?, ?, ?)",
            [
                $data['assessment_id'],
                $data['lead_id'] ?? null,
                $data['file_path'] ?? null,
                $data['status'] ?? 'pending'
            ]
        );
    }

    public static function all(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname FROM reports r LEFT JOIN leads l ON r.lead_id = l.id ORDER BY r.generated_at DESC"
        );
    }

    public static function pending(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname, l.email, l.phone FROM reports r LEFT JOIN leads l ON r.lead_id = l.id WHERE r.status = 'pending' ORDER BY r.generated_at DESC"
        );
    }

    public static function validated(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname FROM reports r LEFT JOIN leads l ON r.lead_id = l.id WHERE r.status = 'validated' ORDER BY r.generated_at DESC"
        );
    }

    public static function updateStatus(int $id, string $status, ?string $validatedBy = null, ?string $filePath = null): int
    {
        $sets = ["status = ?"];
        $params = [$status];

        if ($status === 'validated') {
            $sets[] = "validated_at = NOW()";
            $sets[] = "validated_by = ?";
            $params[] = $validatedBy;
        }

        if ($filePath !== null) {
            $sets[] = "file_path = ?";
            $params[] = $filePath;
        }

        $params[] = $id;
        return Database::execute("UPDATE reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports")['count'];
    }

    public static function countPending(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'")['count'];
    }

    public static function countValidated(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports WHERE status = 'validated'")['count'];
    }
}