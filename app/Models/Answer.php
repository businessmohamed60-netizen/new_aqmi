<?php
namespace App\Models;

use App\Helpers\Database;

class Answer
{
    public static function findByAssessment(int $assessmentId): array
    {
        return Database::fetchAll(
            "SELECT aa.*, q.domain_id, q.title, q.title_fr, q.question_type, q.options, q.weight as question_weight, d.name as domain_name, d.name_fr as domain_name_fr
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

    public static function save(int $assessmentId, int $questionId, ?int $score, string $answerText = '', string $answerValue = ''): int
    {
        $existing = self::findByAssessmentAndQuestion($assessmentId, $questionId);
        if ($existing) {
            Database::execute(
                "UPDATE assessment_answers SET score = ?, answer_text = ?, answer_value = ? WHERE id = ?",
                [$score, $answerText !== '' ? $answerText : null, $answerValue !== '' ? $answerValue : null, $existing['id']]
            );
            return $existing['id'];
        }
        return Database::insert(
            "INSERT INTO assessment_answers (assessment_id, question_id, score, answer_text, answer_value) VALUES (?, ?, ?, ?, ?)",
            [$assessmentId, $questionId, $score, $answerText !== '' ? $answerText : null, $answerValue !== '' ? $answerValue : null]
        );
    }

    public static function getScoresByDomain(int $assessmentId): array
    {
        return Database::fetchAll(
            "SELECT d.id as domain_id, d.name as domain_name, d.name_fr as domain_name_fr, d.icon, d.weight as domain_weight, d.sort_order,
                    AVG(aa.score) as avg_score, COUNT(aa.id) as question_count,
                    SUM(aa.score * q.weight) / SUM(q.weight) as weighted_score
             FROM assessment_answers aa
             JOIN questions q ON aa.question_id = q.id
             JOIN domains d ON q.domain_id = d.id
             WHERE aa.assessment_id = ? AND aa.score IS NOT NULL AND q.question_type IN ('rating_scale', 'yes_no')
             GROUP BY d.id, d.name, d.name_fr, d.icon, d.weight, d.sort_order
             ORDER BY d.sort_order",
            [$assessmentId]
        );
    }

    public static function countByAssessment(int $assessmentId): int
    {
        return (int)Database::fetch("SELECT COUNT(*) as count FROM assessment_answers WHERE assessment_id = ?", [$assessmentId])['count'];
    }
}