<?php
namespace App\Models;

use App\Helpers\Database;

class User
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?", [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch("SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?", [$email]);
    }

    public static function all(): array
    {
        return Database::fetchAll("SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO users (role_id, firstname, lastname, email, password, phone, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$data['role_id'], $data['firstname'], $data['lastname'], $data['email'], $data['password'], $data['phone'] ?? null, $data['is_active'] ?? 1]
        );
    }

    public static function update(int $id, array $data): int
    {
        $sets = []; $params = [];
        $keys = ['role_id', 'firstname', 'lastname', 'email', 'password', 'phone', 'is_active', 'avatar', 'assessment_limit',
            'company', 'job_title', 'sector', 'activity_category', 'oem_tier', 'product_category',
            'main_clients', 'annual_revenue', 'export_percentage', 'production_sites',
            'workforce_production', 'workforce_engineering', 'country', 'company_size',
            'website', 'certifications', 'founded_year', 'production_type', 'production_capacity',
            'machine_count', 'machine_types', 'main_materials', 'process_technologies',
            'ppm_target', 'otd_rate', 'fta_rate', 'scrap_rate', 'traceability_system',
            'logistics_system', 'rd_budget_percent', 'current_erp',
        ];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) { $sets[] = "{$key} = ?"; $params[] = $data[$key]; }
        }
        if (empty($sets)) return 0;
        $params[] = $id;
        return Database::execute("UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?", $params);
    }

    public static function getAssessmentLimit(int $userId): ?int
    {
        $row = Database::fetch("SELECT assessment_limit FROM users WHERE id = ?", [$userId]);
        if (!$row) return null;
        return $row['assessment_limit'] !== null ? (int)$row['assessment_limit'] : null;
    }

    public static function delete(int $id): int
    {
        return Database::execute("DELETE FROM users WHERE id = ?", [$id]);
    }

    public static function count(): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM users")['count'];
    }

    public static function getRecent(int $limit = 5): array
    {
        return Database::fetchAll("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC LIMIT ?", [$limit]);
    }
}