<?php
namespace App\Models;

use App\Helpers\Database;

class Lead
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id WHERE l.id = ?", [$id]);
    }

    public static function findByAssessment(int $assessmentId): ?array
    {
        return Database::fetch("SELECT * FROM leads WHERE assessment_id = ?", [$assessmentId]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO leads (assessment_id, firstname, lastname, company, sector, job_title, phone, email, country, company_size, website, certifications, founded_year, production_type, notes, consent_contact, consent_share_industry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['assessment_id'], $data['firstname'], $data['lastname'], $data['company'],
                $data['sector'] ?? '', $data['job_title'] ?? '', $data['phone'] ?? '', $data['email'],
                $data['country'] ?? '', $data['company_size'] ?? '', $data['website'] ?? '',
                $data['certifications'] ?? '', $data['founded_year'] ?? '', $data['production_type'] ?? '',
                $data['notes'] ?? '',
                !empty($data['consent_contact']) ? 1 : 0,
                !empty($data['consent_share_industry']) ? 1 : 0,
            ]
        );
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id ORDER BY l.created_at DESC");
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM leads")['count'];
    }

    public static function getSectorDistribution(): array
    {
        return Database::fetchAll("SELECT sector, COUNT(*) as total FROM leads WHERE sector IS NOT NULL AND sector != '' GROUP BY sector ORDER BY total DESC");
    }

    public static function getCountryDistribution(): array
    {
        return Database::fetchAll("SELECT country, COUNT(*) as total FROM leads WHERE country IS NOT NULL AND country != '' GROUP BY country ORDER BY total DESC");
    }

    public static function getMonthlyStats(int $months = 6): array
    {
        return Database::fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month",
            [$months]
        );
    }

    public static function getRecent(int $limit = 10): array
    {
        return Database::fetchAll("SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id ORDER BY l.created_at DESC LIMIT ?", [$limit]);
    }
}