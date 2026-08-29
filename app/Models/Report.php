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

    /** Statuts du certificat pour la vérification publique */
    public const CERT_STATUS_ACTIVE  = 'active';
    public const CERT_STATUS_REVOKED = 'revoked';
    public const CERT_STATUS_EXPIRED = 'expired';

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE id = ?", [$id]);
    }

    public static function findByAssessment(int $assessmentId): ?array
    {
        return Database::fetch("SELECT * FROM reports WHERE assessment_id = ? ORDER BY id DESC LIMIT 1", [$assessmentId]);
    }

    /**
     * Utilisé par la page publique /verify/{report_number} (legacy).
     */
    public static function findByNumber(string $reportNumber): ?array
    {
        return Database::fetch(
            "SELECT r.*, l.company, l.country, l.sector FROM reports r LEFT JOIN leads l ON r.lead_id = l.id WHERE r.report_number = ? LIMIT 1",
            [$reportNumber]
        );
    }

    /**
     * Recherche un certificat par son jeton aléatoire (page publique /c/{token}).
     * Ne joint que les colonnes non sensibles nécessaires à la vérification.
     */
    public static function findByVerifyToken(string $token): ?array
    {
        if (strlen($token) < 16) return null;
        return Database::fetch(
            "SELECT r.id, r.report_number, r.status, r.certificate_status,
                    r.issued_at, r.expires_at, r.revoked_at, r.revoked_reason,
                    r.certified_at, r.aqmi_level_assigned,
                    l.company, l.country, l.sector
             FROM reports r
             LEFT JOIN leads l ON r.lead_id = l.id
             WHERE r.verify_token = ? LIMIT 1",
            [$token]
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

    /**
     * Attribue un jeton de vérification aléatoire (64 hex chars) au rapport.
     * Retourne le jeton généré. Si un jeton existe déjà, le retourne tel quel.
     */
    public static function assignVerifyToken(int $id): string
    {
        $existing = self::find($id);
        if ($existing && !empty($existing['verify_token'])) {
            return $existing['verify_token'];
        }
        $token = bin2hex(random_bytes(32)); // 64 chars, non devinable
        Database::execute("UPDATE reports SET verify_token = ? WHERE id = ?", [$token, $id]);
        return $token;
    }

    /**
     * Récupère le jeton de vérification d'un rapport (ou l'attribue si absent).
     */
    public static function getOrAssignVerifyToken(int $id): string
    {
        $report = self::find($id);
        if ($report && !empty($report['verify_token'])) {
            return $report['verify_token'];
        }
        return self::assignVerifyToken($id);
    }

    /**
     * Active le certificat : statut active, date d'émission, date d'expiration.
     * Appelée au moment de la certification par l'administrateur.
     */
    public static function activateCertificate(int $id, ?string $expiresAt = null): void
    {
        $report = self::find($id);
        $token = ($report && !empty($report['verify_token']))
            ? $report['verify_token']
            : self::assignVerifyToken($id);

        $sets = [
            'certificate_status = ?',
            'issued_at = ?',
            'expires_at = ?',
            'revoked_at = NULL',
            'revoked_reason = NULL',
        ];
        $params = [
            self::CERT_STATUS_ACTIVE,
            date('Y-m-d'),
            $expiresAt ?? date('Y-m-d', strtotime('+1 year')),
        ];
        $params[] = $id;
        Database::execute("UPDATE reports SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    /**
     * Révoque le certificat : statut revoked + motif + horodatage.
     */
    public static function revokeCertificate(int $id, ?string $reason = null): void
    {
        Database::execute(
            "UPDATE reports SET certificate_status = ?, revoked_at = NOW(), revoked_reason = ? WHERE id = ?",
            [self::CERT_STATUS_REVOKED, $reason, $id]
        );
    }

    /**
     * Réactive un certificat révoqué (statut active, purge des champs révocation).
     * Recalcule l'expiration si elle est passée.
     */
    public static function reactivateCertificate(int $id, ?string $expiresAt = null): void
    {
        $report = self::find($id);
        $newExpiry = $expiresAt;
        if ($newExpiry === null) {
            $currentExpiry = $report['expires_at'] ?? null;
            if ($currentExpiry && strtotime($currentExpiry) > time()) {
                $newExpiry = $currentExpiry;
            } else {
                $newExpiry = date('Y-m-d', strtotime('+1 year'));
            }
        }
        Database::execute(
            "UPDATE reports SET certificate_status = ?, revoked_at = NULL, revoked_reason = NULL, expires_at = ? WHERE id = ?",
            [self::CERT_STATUS_ACTIVE, $newExpiry, $id]
        );
    }

    /**
     * Détermine le statut effectif du certificat en tenant compte de l'expiration
     * automatique (un certificat actif dont la date d'expiration est dépassée
     * est considéré comme expiré).
     */
    public static function effectiveStatus(?array $report): string
    {
        if (!$report) return 'not_found';
        $status = $report['certificate_status'] ?? null;
        if ($status === self::CERT_STATUS_REVOKED) return self::CERT_STATUS_REVOKED;
        if ($status === self::CERT_STATUS_EXPIRED) return self::CERT_STATUS_EXPIRED;
        // active or null — check expiry
        $expiresAt = $report['expires_at'] ?? null;
        if ($expiresAt && strtotime($expiresAt) < strtotime(date('Y-m-d'))) {
            return self::CERT_STATUS_EXPIRED;
        }
        return self::CERT_STATUS_ACTIVE;
    }

    public static function setSignature(int $id, string $signature): int
    {
        return Database::execute("UPDATE reports SET admin_signature = ? WHERE id = ?", [$signature, $id]);
    }

    /**
     * Enregistre le modèle Report Studio choisi par l'administrateur
     * au moment de la certification.
     */
    public static function setTemplateId(int $id, ?int $templateId): int
    {
        return Database::execute("UPDATE reports SET template_id = ? WHERE id = ?", [$templateId, $id]);
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

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM reports WHERE id = ?", [$id]);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports")['count'];
    }

    public static function countByStatus(string $status): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM reports WHERE status = ?", [$status])['count'];
    }

    /**
     * Vérifie périodiquement les certificats actifs expirés et met à jour
     * leur statut en base. Retourne le nombre de certificats marqués expirés.
     */
    public static function expireOverdueCertificates(): int
    {
        return Database::execute(
            "UPDATE reports SET certificate_status = ? WHERE certificate_status = ? AND expires_at IS NOT NULL AND expires_at < CURDATE()",
            [self::CERT_STATUS_EXPIRED, self::CERT_STATUS_ACTIVE]
        );
    }
}