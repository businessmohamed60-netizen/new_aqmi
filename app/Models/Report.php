<?php
namespace App\Models;

use App\Helpers\Database;

class Report
{
    /** Statuts valides du workflow de certification */
    public const STATUSES = [
        'certification_requested',
        'under_review',
        'approved',
        'rejected',
        'certified',
    ];

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE id = ?", [$id]);
    }

    public static function findByAssessment(int $assessmentId): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE assessment_id = ? ORDER BY id DESC LIMIT 1", [$assessmentId]);
    }

    /**
     * Utilisé par la page publique /verify/{report_number}.
     */
    public static function findByNumber(string $reportNumber): ?array
    {
        return Database::fetch(
            "SELECT r.*, l.company, l.country FROM reports r LEFT JOIN leads l ON r.lead_id = l.id WHERE r.report_number = ? LIMIT 1",
            [$reportNumber]
        );
    }

    /**
     * Crée une demande de certification.
     * N'est appelée QUE lorsque l'utilisateur clique sur
     * "Demander un Rapport AQMI Certifié" — jamais automatiquement
     * à la fin du questionnaire.
     */
    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO reports (assessment_id, lead_id, file_path, status, certification_requested_at) VALUES (?, ?, ?, ?, NOW())",
            [
                $data['assessment_id'],
                $data['lead_id'] ?? null,
                $data['file_path'] ?? null,
                $data['status'] ?? 'certification_requested'
            ]
        );
    }

    public static function all(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname, l.email,
                    a.total_score, a.maturity_level, a.user_id,
                    u.firstname as user_firstname, u.lastname as user_lastname
             FROM reports r
             LEFT JOIN leads l ON r.lead_id = l.id
             LEFT JOIN assessments a ON r.assessment_id = a.id
             LEFT JOIN users u ON a.user_id = u.id
             ORDER BY r.generated_at DESC"
        );
    }

    /** Demandes en attente de traitement admin (nouvelle + en cours d'examen) */
    public static function pendingCertifications(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname, l.email, l.phone,
                    a.total_score, a.maturity_level
             FROM reports r
             LEFT JOIN leads l ON r.lead_id = l.id
             LEFT JOIN assessments a ON r.assessment_id = a.id
             WHERE r.status IN ('certification_requested', 'under_review')
             ORDER BY r.certification_requested_at ASC"
        );
    }

    public static function certified(): array
    {
        return Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname FROM reports r LEFT JOIN leads l ON r.lead_id = l.id WHERE r.status = 'certified' ORDER BY r.certified_at DESC"
        );
    }

    /**
     * Transition générique de statut.
     */
    public static function updateStatus(int $id, string $status, ?string $validatedBy = null, ?string $filePath = null): int
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException("Statut de rapport invalide: {$status}");
        }

        $sets = ["status = ?"];
        $params = [$status];

        if ($status === 'certified') {
            $sets[] = "certified_at = NOW()";
            $sets[] = "validated_at = NOW()";
            if ($validatedBy !== null) { $sets[] = "validated_by = ?"; $params[] = $validatedBy; }
        }

        // Renvoi d'une demande après rejet (ou remise en file d'attente) :
        // on réinitialise la date de demande pour qu'elle réapparaisse
        // correctement dans Report::pendingCertifications(), qui trie par
        // certification_requested_at ASC.
        if ($status === 'certification_requested') {
            $sets[] = "certification_requested_at = NOW()";
        }

        if ($filePath !== null) {
            $sets[] = "file_path = ?";
            $params[] = $filePath;
        }

        $params[] = $id;
        return Database::execute("UPDATE reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    /**
     * Enregistre le travail d'analyse de l'administrateur :
     * commentaires, observations, plan d'action, niveau AQMI attribué.
     */
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
        return Database::execute("UPDATE reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    /**
     * Marque la demande comme "en cours d'examen" dès qu'un admin l'ouvre,
     * uniquement si elle est encore au statut initial.
     */
    public static function markUnderReviewIfNeeded(int $id): void
    {
        $report = self::find($id);
        if ($report && $report['status'] === 'certification_requested') {
            Database::execute("UPDATE reports SET status = 'under_review' WHERE id = ?", [$id]);
        }
    }

    public static function setQrCodePath(int $id, string $path): int
    {
        return Database::execute("UPDATE reports SET qr_code_path = ? WHERE id = ?", [$path, $id]);
    }

    public static function setSignature(int $id, string $signature): int
    {
        return Database::execute("UPDATE reports SET admin_signature = ? WHERE id = ?", [$signature, $id]);
    }

    public static function setThemeId(int $id, ?int $themeId): int
    {
        return Database::execute("UPDATE reports SET theme_id = ? WHERE id = ?", [$themeId, $id]);
    }

    /**
     * Génère un numéro de rapport unique du type AQMI-2026-000123
     * et le persiste sur la ligne.
     */
    public static function assignReportNumber(int $id): string
    {
        $existing = self::find($id);
        if ($existing && !empty($existing['report_number'])) {
            return $existing['report_number'];
        }
        $number = 'AQMI-' . date('Y') . '-' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
        Database::execute("UPDATE reports SET report_number = ? WHERE id = ?", [$number, $id]);
        return $number;
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports")['count'];
    }

    public static function countByStatus(string $status): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports WHERE status = ?", [$status])['count'];
    }
}