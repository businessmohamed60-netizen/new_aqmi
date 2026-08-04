<?php
namespace App\Models;

use App\Helpers\Database;

class Answer
{
    public static function findByAssessment(int $assessmentId): array
    {
        return Database::fetchAll(
            "SELECT aa.*, q.domain_id, q.title, q.title_fr, q.weight as question_weight, d.name as domain_name, d.name_fr as domain_name_fr
             FROM assessment_answers aa
             JOIN questions q ON aa.question_id = q.id
             JOIN domains d ON q.domain_id = d.id
             WHERE aa.assessment_id = ?
             ORDER BY d.sort_order, q.sort_order",
            [$assessmentId]
        );
    }

    public static function findByAssessmentAndQuestion(int $assessmentId, int $questionId): ?array
    {
        return Database::fetch("SELECT * FROM assessment_answers WHERE assessment_id = ? AND question_id = ?", [$assessmentId, $questionId]);
    }

    public static function save(int $assessmentId, int $questionId, int $score): int
    {
        $existing = self::findByAssessmentAndQuestion($assessmentId, $questionId);
        if ($existing) {
            Database::execute("UPDATE assessment_answers SET score = ? WHERE id = ?", [$score, $existing['id']]);
            return $existing['id'];
        }
        return Database::insert(
            "INSERT INTO assessment_answers (assessment_id, question_id, score) VALUES (?, ?, ?)",
            [$assessmentId, $questionId, $score]
        );
    }

    public static function getScoresByDomain(int $assessmentId): array
    {
        return Database::fetchAll(
            "SELECT d.id as domain_id, d.name as domain_name, d.name_fr as domain_name_fr, d.icon, d.weight as domain_weight,
                    AVG(aa.score) as avg_score, COUNT(aa.id) as question_count,
                    SUM(aa.score * q.weight) / SUM(q.weight) as weighted_score
             FROM assessment_answers aa
             JOIN questions q ON aa.question_id = q.id
             JOIN domains d ON q.domain_id = d.id
             WHERE aa.assessment_id = ?
             GROUP BY d.id, d.name, d.name_fr, d.icon, d.weight
             ORDER BY d.sort_order",
            [$assessmentId]
        );
    }

    public static function countByAssessment(int $assessmentId): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM assessment_answers WHERE assessment_id = ?", [$assessmentId])['count'];
    }
}