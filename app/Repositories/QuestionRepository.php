<?php
namespace App\Repositories;

use App\Helpers\Database;
use App\Models\Question;

class QuestionRepository
{
    public function getAllPaginated(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $where = ['1=1']; $params = [];
        if (!empty($filters['domain_id'])) { $where[] = "q.domain_id = ?"; $params[] = $filters['domain_id']; }
        if (isset($filters['is_active'])) { $where[] = "q.is_active = ?"; $params[] = $filters['is_active']; }
        if (!empty($filters['search'])) { $where[] = "(q.title LIKE ? OR q.title_fr LIKE ?)"; $s = "%{$filters['search']}%"; $params[] = $s; $params[] = $s; }
        $whereClause = implode(' AND ', $where);

        $total = (int)Database::fetch("SELECT COUNT(*) as total FROM questions q WHERE {$whereClause}", $params)['total'];
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT q.*, d.name as domain_name, d.name_fr as domain_name_fr, d.icon as domain_icon FROM questions q JOIN domains d ON q.domain_id = d.id WHERE {$whereClause} ORDER BY d.sort_order, q.sort_order LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return ['items' => $items, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => max(1, ceil($total / $perPage))];
    }

    public function duplicate(int $id): ?int
    {
        $question = Database::fetch("SELECT * FROM questions WHERE id = ?", [$id]);
        if (!$question) return null;
        return Database::insert(
            "INSERT INTO questions (domain_id, title, title_fr, title_ar, description, description_fr, description_ar, weight, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$question['domain_id'], $question['title'] . ' (copie)', $question['title_fr'] . ' (copie)', $question['title_ar'], $question['description'], $question['description_fr'], $question['description_ar'], $question['weight'], ($question['sort_order'] ?? 0) + 1, 0]
        );
    }

    public function importFromExcel(array $rows): array
    {
        $imported = 0; $errors = [];
        foreach ($rows as $index => $row) {
            $domain = Database::fetch("SELECT id FROM domains WHERE name_fr = ? OR name = ?", [$row['domaine'] ?? '', $row['domain'] ?? '']);
            if (!$domain) { $errors[] = "Ligne " . ($index + 2) . ": Domaine non trouvé"; continue; }
            Question::create([
                'domain_id' => $domain['id'], 'title' => $row['title_en'] ?? $row['title'] ?? '', 'title_fr' => $row['title_fr'] ?? $row['title'] ?? '',
                'weight' => $row['weight'] ?? 1, 'sort_order' => 0, 'is_active' => 1
            ]);
            $imported++;
        }
        return ['imported' => $imported, 'errors' => $errors];
    }

    public function exportAll(): array
    {
        return Database::fetchAll("SELECT d.name_fr as domaine, q.* FROM questions q JOIN domains d ON q.domain_id = d.id ORDER BY d.sort_order, q.sort_order");
    }
}