<?php
namespace App\Services;

use App\Models\Recommendation;

class RecommendationService
{
    private ScoringService $scoringService;
    public function __construct() { $this->scoringService = new ScoringService(); }

    public function generate(int $assessmentId): array
    {
        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        $rules = Recommendation::getActiveRules();
        $recommendations = [];
        $lang = $_SESSION['lang'] ?? 'fr';

        foreach ($rules as $rule) {
            $field = $rule['condition_field'];
            if ($field === null || $field === '') continue;
            $score = $this->getFieldValue($field, $analysis);
            if ($score !== null && $this->compare($score, $rule['condition_operator'], (float)$rule['condition_value'])) {
                $textKey = "recommendation_text_" . ($lang === 'fr' ? 'fr' : ($lang === 'ar' ? 'ar' : ''));
                $text = $rule[$textKey] ?: $rule['recommendation_text'];
                $recommendations[] = [
                    'id' => $rule['id'],
                    'text' => $text,
                    'priority' => $rule['priority'],
                    'domain_id' => $rule['domain_id'],
                    'score' => $score,
                    'threshold' => (float)$rule['condition_value'],
                ];
            }
        }

        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($recommendations, fn($a, $b) => ($priorityOrder[$a['priority']] ?? 99) <=> ($priorityOrder[$b['priority']] ?? 99));

        foreach ($analysis['priorities'] as $p) {
            $text = $lang === 'fr'
                ? "Action prioritaire requise pour {$p['domain_name_fr']}. Score: {$p['score']}%. Plan d'action recommandé."
                : "Priority action required for {$p['domain_name']}. Score: {$p['score']}%. Action plan recommended.";
            $recommendations[] = [
                'id' => 0, 'text' => $text, 'priority' => $p['priority'],
                'domain_id' => $p['domain_id'], 'is_auto' => true,
            ];
        }

        return $recommendations;
    }

    private function getFieldValue(string $field, array $analysis): ?float
    {
        if ($field === 'global_score') return $analysis['global_score'];
        if (str_starts_with($field, 'domain_')) {
            $domainId = (int)str_replace('domain_', '', $field);
            foreach ($analysis['domain_scores'] as $ds) {
                if ($ds['domain_id'] === $domainId) return $ds['percent_score'];
            }
        }
        foreach ($analysis['domain_scores'] as $ds) {
            if (generateSlug($ds['domain_name']) === generateSlug(str_replace('score_', '', $field))) return $ds['percent_score'];
        }
        return null;
    }

    private function compare(float $value, string $operator, float $threshold): bool
    {
        return match ($operator) {
            '<' => $value < $threshold,
            '>' => $value > $threshold,
            '<=' => $value <= $threshold,
            '>=' => $value >= $threshold,
            '==' => abs($value - $threshold) < 0.01,
            default => false,
        };
    }
}