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
            "INSERT INTO leads (
                assessment_id, firstname, lastname, company, sector, activity_category, oem_tier,
                product_category, main_clients, annual_revenue, export_percentage, production_sites,
                workforce_production, workforce_engineering, job_title, phone, email, country,
                company_size, website, certifications, founded_year, production_type, production_capacity,
                machine_count, machine_types, main_materials, process_technologies, ppm_target, otd_rate,
                fta_rate, scrap_rate, traceability_system, logistics_system, rd_budget_percent,
                current_erp, notes, consent_contact, consent_share_industry
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )",
            [
                $data['assessment_id'], $data['firstname'], $data['lastname'], $data['company'],
                $data['sector'] ?? '', $data['activity_category'] ?? '', $data['oem_tier'] ?? '',
                $data['product_category'] ?? '', $data['main_clients'] ?? '', $data['annual_revenue'] ?? '',
                isset($data['export_percentage']) && $data['export_percentage'] !== '' ? (int)$data['export_percentage'] : null,
                isset($data['production_sites']) && $data['production_sites'] !== '' ? (int)$data['production_sites'] : null,
                isset($data['workforce_production']) && $data['workforce_production'] !== '' ? (int)$data['workforce_production'] : null,
                isset($data['workforce_engineering']) && $data['workforce_engineering'] !== '' ? (int)$data['workforce_engineering'] : null,
                $data['job_title'] ?? '', $data['phone'] ?? '', $data['email'],
                $data['country'] ?? '', $data['company_size'] ?? '', $data['website'] ?? '',
                $data['certifications'] ?? '', $data['founded_year'] ?? '', $data['production_type'] ?? '',
                $data['production_capacity'] ?? '',
                isset($data['machine_count']) && $data['machine_count'] !== '' ? (int)$data['machine_count'] : null,
                $data['machine_types'] ?? '', $data['main_materials'] ?? '', $data['process_technologies'] ?? '',
                isset($data['ppm_target']) && $data['ppm_target'] !== '' ? (int)$data['ppm_target'] : null,
                isset($data['otd_rate']) && $data['otd_rate'] !== '' ? (float)$data['otd_rate'] : null,
                isset($data['fta_rate']) && $data['fta_rate'] !== '' ? (float)$data['fta_rate'] : null,
                isset($data['scrap_rate']) && $data['scrap_rate'] !== '' ? (float)$data['scrap_rate'] : null,
                $data['traceability_system'] ?? '', $data['logistics_system'] ?? '',
                isset($data['rd_budget_percent']) && $data['rd_budget_percent'] !== '' ? (float)$data['rd_budget_percent'] : null,
                $data['current_erp'] ?? '',
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