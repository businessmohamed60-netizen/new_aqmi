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
                $text = $this->pickRecommendationText($rule, $lang);
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
            $domainLabel = $p['domain_label'] ?? $p['domain_name_fr'] ?? $p['domain_name'];
            $text = $this->autoPriorityText($domainLabel, $p['score'], $lang);
            $recommendations[] = [
                'id' => 0, 'text' => $text, 'priority' => $p['priority'],
                'domain_id' => $p['domain_id'], 'is_auto' => true,
            ];
        }

        return $recommendations;
    }

    private function pickRecommendationText(array $rule, string $lang): string
    {
        if ($lang === 'ar' && !empty($rule['recommendation_text_ar'])) {
            return $rule['recommendation_text_ar'];
        }
        if (!empty($rule['recommendation_text_fr'])) {
            return $rule['recommendation_text_fr'];
        }
        return $rule['recommendation_text'];
    }

    private function autoPriorityText(string $domainLabel, float $score, string $lang): string
    {
        $templates = [
            'fr' => "Action prioritaire requise pour {$domainLabel}. Score: {$score}%. Plan d'action recommandé.",
            'en' => "Priority action required for {$domainLabel}. Score: {$score}%. Action plan recommended.",
            'ar' => "إجراء ذو أولوية مطلوب لـ {$domainLabel}. النتيجة: {$score}%. يُوصى بوضع خطة عمل.",
            'es' => "Acción prioritaria requerida para {$domainLabel}. Puntuación: {$score}%. Se recomienda un plan de acción.",
        ];
        return $templates[$lang] ?? $templates['fr'];
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