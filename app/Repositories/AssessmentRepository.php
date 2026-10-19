<?php
namespace App\Repositories;

use App\Helpers\Database;

class AssessmentRepository
{
    public function getCompletedWithDetails(int $page = 1, int $perPage = 20): array
    {
        $total = (int)Database::fetch("SELECT COUNT(*) as total FROM assessments WHERE status = 'completed'")['total'];
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT a.*, l.company, l.firstname, l.lastname, l.sector, l.country FROM assessments a LEFT JOIN leads l ON a.id = l.assessment_id WHERE a.status = 'completed' ORDER BY a.completed_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        return ['items' => $items, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => max(1, ceil($total / $perPage))];
    }

    public function getDomainScores(int $assessmentId): array
    {
        return Database::fetchAll(
            "SELECT d.id, d.name, d.name_fr, d.icon, d.weight, AVG(aa.score) as avg_score, COUNT(aa.id) as answered_count, COUNT(q.id) as total_questions
             FROM domains d LEFT JOIN questions q ON d.id = q.domain_id AND q.is_active = 1 LEFT JOIN assessment_answers aa ON q.id = aa.question_id AND aa.assessment_id = ?
             WHERE d.is_active = 1 GROUP BY d.id ORDER BY d.sort_order",
            [$assessmentId]
        );
    }
}