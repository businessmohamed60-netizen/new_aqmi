<?php
namespace App\Repositories;

use App\Helpers\Database;

class LeadRepository
{
    public function getAllPaginated(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $where = ['1=1']; $params = [];
        if (!empty($filters['sector'])) { $where[] = "l.sector = ?"; $params[] = $filters['sector']; }
        if (!empty($filters['country'])) { $where[] = "l.country = ?"; $params[] = $filters['country']; }
        if (!empty($filters['search'])) { $s = "%{$filters['search']}%"; $where[] = "(l.firstname LIKE ? OR l.lastname LIKE ? OR l.company LIKE ? OR l.email LIKE ?)"; $params = array_merge($params, [$s, $s, $s, $s]); }
        $whereClause = implode(' AND ', $where);

        $total = (int)Database::fetch("SELECT COUNT(*) as total FROM leads l WHERE {$whereClause}", $params)['total'];
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id WHERE {$whereClause} ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );
        return ['items' => $items, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => max(1, ceil($total / $perPage))];
    }

    public function exportCsv(): string
    {
        $leads = Database::fetchAll("SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id ORDER BY l.created_at DESC");
        $csv = "Nom,Prenom,Entreprise,Secteur,Fonction,Telephone,Email,Pays,Score,Niveau,Date\n";
        foreach ($leads as $l) {
            $csv .= '"' . ($l['lastname'] ?? '') . '","' . ($l['firstname'] ?? '') . '","' . ($l['company'] ?? '') . '","' . ($l['sector'] ?? '') . '","' . ($l['job_title'] ?? '') . '","' . ($l['phone'] ?? '') . '","' . ($l['email'] ?? '') . '","' . ($l['country'] ?? '') . '",' . ($l['total_score'] ?? '') . ',"' . ($l['maturity_level'] ?? '') . '","' . ($l['created_at'] ?? '') . "\"\n";
        }
        return $csv;
    }
}