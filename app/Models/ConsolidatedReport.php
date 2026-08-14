<?php
namespace App\Models;

use App\Helpers\Database;

class ConsolidatedReport
{
    public const STATUSES = [
        'draft',
        'certification_requested',
        'under_review',
        'approved',
        'rejected',
        'certified',
    ];

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM consolidated_reports WHERE id = ?", [$id]);
    }

    public static function findByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT * FROM consolidated_reports WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public static function create(int $userId, string $title): int
    {
        return Database::insert(
            "INSERT INTO consolidated_reports (user_id, title, status) VALUES (?, ?, 'draft')",
            [$userId, $title]
        );
    }

    public static function addItem(int $consolidatedId, int $assessmentId, ?int $modelId, ?string $modelName, ?float $score, ?string $level): int
    {
        return Database::insert(
            "INSERT INTO consolidated_report_items (consolidated_report_id, assessment_id, model_id, model_name, score, maturity_level) VALUES (?, ?, ?, ?, ?, ?)",
            [$consolidatedId, $assessmentId, $modelId, $modelName, $score, $level]
        );
    }

    public static function getItems(int $consolidatedId): array
    {
        return Database::fetchAll(
            "SELECT cri.*, a.total_score, a.maturity_level as assessment_level,
                    l.company, l.firstname, l.lastname
             FROM consolidated_report_items cri
             JOIN assessments a ON cri.assessment_id = a.id
             LEFT JOIN leads l ON a.id = l.assessment_id
             WHERE cri.consolidated_report_id = ?
             ORDER BY cri.id",
            [$consolidatedId]
        );
    }

    public static function updateScore(int $id, float $score, string $level): int
    {
        return Database::execute(
            "UPDATE consolidated_reports SET consolidated_score = ?, maturity_level = ? WHERE id = ?",
            [$score, $level, $id]
        );
    }

    public static function updateStatus(int $id, string $status, ?string $validatedBy = null): int
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException("Statut invalide: {$status}");
        }

        $sets = ["status = ?"];
        $params = [$status];

        if ($status === 'certified') {
            $sets[] = "certified_at = NOW()";
            $sets[] = "validated_at = NOW()";
            if ($validatedBy !== null) { $sets[] = "validated_by = ?"; $params[] = $validatedBy; }
        }
        if ($status === 'certification_requested') {
            $sets[] = "certification_requested_at = NOW()";
        }

        $params[] = $id;
        return Database::execute("UPDATE consolidated_reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function saveAdminReview(int $id, array $data): int
    {
        $sets = [];
        $params = [];
        foreach (['admin_comment', 'observations', 'action_plan', 'aqmi_level_assigned'] as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE consolidated_reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function markUnderReviewIfNeeded(int $id): void
    {
        $report = self::find($id);
        if ($report && $report['status'] === 'certification_requested') {
            Database::execute("UPDATE consolidated_reports SET status = 'under_review' WHERE id = ?", [$id]);
        }
    }

    public static function assignReportNumber(int $id): string
    {
        $existing = self::find($id);
        if ($existing && !empty($existing['report_number'])) {
            return $existing['report_number'];
        }
        $number = 'AQMI-CONS-' . date('Y') . '-' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
        Database::execute("UPDATE consolidated_reports SET report_number = ? WHERE id = ?", [$number, $id]);
        return $number;
    }

    public static function pendingCertifications(): array
    {
        return Database::fetchAll(
            "SELECT cr.*, u.firstname, u.lastname, u.email
             FROM consolidated_reports cr
             JOIN users u ON cr.user_id = u.id
             WHERE cr.status IN ('certification_requested', 'under_review')
             ORDER BY cr.certification_requested_at ASC"
        );
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM consolidated_reports WHERE id = ?", [$id]);
    }
}
