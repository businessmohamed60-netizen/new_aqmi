<?php
namespace App\Repositories;

use App\Helpers\Database;

class ReportRepository
{
    public function getAllPaginated(int $page = 1, int $perPage = 20): array
    {
        $total = (int)Database::fetch("SELECT COUNT(*) as total FROM reports")['total'];
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT r.*, l.company, l.firstname, l.lastname, a.total_score, a.maturity_level FROM reports r LEFT JOIN leads l ON r.lead_id = l.id LEFT JOIN assessments a ON r.assessment_id = a.id ORDER BY r.generated_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        return ['items' => $items, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => max(1, ceil($total / $perPage))];
    }
}