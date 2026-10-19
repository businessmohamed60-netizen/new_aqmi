<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Domain;
use App\Models\ScoreLevel;
use App\Models\Recommendation;
use App\Models\Lead;
use App\Models\EvaluationModel;
use App\Helpers\Database;

/**
 * Aggregates all real assessment data into a single structured payload
 * for the comprehensive AQMI Final Report.
 *
 * Reuses ScoringService + RecommendationService — does not invent data.
 */
class AqmiReportService
{
    private ScoringService $scoringService;
    private RecommendationService $recommendationService;

    public function __construct()
    {
        $this->scoringService = new ScoringService();
        $this->recommendationService = new RecommendationService();
    }

    /**
     * Build the complete report data payload for a given assessment.
     * Returns null if the assessment doesn't exist or isn't completed.
     */
    public function build(int $assessmentId): ?array
    {
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') {
            return null;
        }

        $analysis     = $this->scoringService->analyzeAssessment($assessmentId);
        $recommendations = $this->recommendationService->generate($assessmentId);
        $lead         = Lead::findByAssessment($assessmentId);
        $user         = $assessment['user_id'] ? \App\Models\User::find($assessment['user_id']) : null;
        $scoreLevels  = ScoreLevel::all();
        $answers      = Answer::findByAssessment($assessmentId);

        $globalScore  = $analysis['global_score'];
        $maturity     = $analysis['maturity_level'];
        $domainScores = $analysis['domain_scores'];
        $strengths    = $analysis['strengths'];
        $weaknesses   = $analysis['weaknesses'];
        $priorities   = $analysis['priorities'];
        $benchmark    = $analysis['benchmark'];

        $gaps           = $this->calculateGaps($domainScores);
        $actionPlan     = $this->buildActionPlan($priorities, $recommendations);
        $execSummary    = $this->buildExecutiveSummary($globalScore, $maturity, $domainScores, $strengths, $weaknesses, $lead);
        $domainDetails  = $this->enrichDomainScores($domainScores, $answers);
        $scoreDistribution = $this->calculateScoreDistribution($answers);

        $evaluationModel = null;
        if (!empty($assessment['model_id'])) {
            $evaluationModel = EvaluationModel::find((int)$assessment['model_id']);
        }

        return [
            'assessment'         => $assessment,
            'evaluation_model'   => $evaluationModel,
            'lead'               => $lead,
            'user'               => $user,
            'analysis'           => $analysis,
            'global_score'       => $globalScore,
            'maturity_level'     => $maturity,
            'score_levels'       => $scoreLevels,
            'domain_scores'      => $domainScores,
            'domain_details'     => $domainDetails,
            'strengths'          => $strengths,
            'weaknesses'         => $weaknesses,
            'gaps'               => $gaps,
            'priorities'         => $priorities,
            'recommendations'    => $recommendations,
            'action_plan'        => $actionPlan,
            'executive_summary'  => $execSummary,
            'benchmark'          => $benchmark,
            'score_distribution' => $scoreDistribution,
            'answers'            => $answers,
            'report_date'        => date('d/m/Y'),
            'report_ref'         => 'AQMI-' . $assessment['id'] . '-' . date('Ymd'),
        ];
    }

    /**
     * Calculate gaps (distance from 100%) for each domain.
     */
    private function calculateGaps(array $domainScores): array
    {
        $gaps = [];
        foreach ($domainScores as $ds) {
            $gap = round(100 - $ds['percent_score'], 1);
            $gaps[] = [
                'domain_id'     => $ds['domain_id'],
                'domain_name'   => $ds['domain_name'],
                'domain_name_fr'=> $ds['domain_name_fr'] ?? $ds['domain_name'],
                'icon'          => $ds['icon'] ?? '',
                'score'         => $ds['percent_score'],
                'gap'           => $gap,
                'severity'      => $gap > 60 ? 'critical' : ($gap > 40 ? 'high' : ($gap > 20 ? 'medium' : 'low')),
            ];
        }
        usort($gaps, fn($a, $b) => $b['gap'] <=> $a['gap']);
        return $gaps;
    }

    /**
     * Build a structured action plan organized by time horizon.
     * Uses real priorities and recommendations — no invented data.
     */
    private function buildActionPlan(array $priorities, array $recommendations): array
    {
        $short = [];
        $medium = [];
        $long = [];

        // Sort recommendations into time horizons by priority
        foreach ($recommendations as $rec) {
            $p = $rec['priority'] ?? 'medium';
            $entry = [
                'text'     => $rec['text'] ?? '',
                'priority' => $p,
                'domain_id'=> $rec['domain_id'] ?? null,
            ];
            if ($p === 'critical') {
                $short[] = $entry;
            } elseif ($p === 'high') {
                $medium[] = $entry;
            } else {
                $long[] = $entry;
            }
        }

        // Add priority axes to short-term if they're critical/high
        foreach ($priorities as $pri) {
            if ($pri['priority'] === 'critical' || $pri['priority'] === 'high') {
                $short[] = [
                    'text'     => sprintf(
                        'Addresser l\'écart sur %s (score actuel: %s%%, écart: %s%%)',
                        $pri['domain_name_fr'] ?? $pri['domain_name'],
                        $pri['score'],
                        $pri['gap']
                    ),
                    'priority' => $pri['priority'],
                    'domain_id'=> $pri['domain_id'],
                ];
            }
        }

        // De-duplicate by text
        $short = $this->dedupe($short);
        $medium = $this->dedupe($medium);
        $long = $this->dedupe($long);

        return [
            'short_term'  => ['label' => 'Court Terme (0-3 mois)', 'items' => array_slice($short, 0, 5)],
            'medium_term' => ['label' => 'Moyen Terme (3-9 mois)', 'items' => array_slice($medium, 0, 5)],
            'long_term'   => ['label' => 'Long Terme (9-24 mois)', 'items' => array_slice($long, 0, 5)],
            'counts'      => [
                'critical' => count(array_filter($recommendations, fn($r) => ($r['priority'] ?? '') === 'critical')),
                'high'     => count(array_filter($recommendations, fn($r) => ($r['priority'] ?? '') === 'high')),
                'medium'   => count(array_filter($recommendations, fn($r) => ($r['priority'] ?? '') === 'medium')),
                'low'      => count(array_filter($recommendations, fn($r) => ($r['priority'] ?? '') === 'low')),
                'total'    => count($recommendations),
            ],
        ];
    }

    private function dedupe(array $items): array
    {
        $seen = [];
        $result = [];
        foreach ($items as $item) {
            $key = md5($item['text']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Build an executive summary paragraph based on real scores.
     */
    private function buildExecutiveSummary(
        float $globalScore,
        ?array $maturity,
        array $domainScores,
        array $strengths,
        array $weaknesses,
        ?array $lead
    ): array
    {
        $levelName = $maturity['name_fr'] ?? $maturity['name'] ?? 'Non défini';
        $levelColor = $maturity['color'] ?? '#6c757d';
        $companyName = $lead['company'] ?? 'L\'entreprise';

        $topStrength = !empty($strengths) ? ($strengths[0]['domain_name_fr'] ?? $strengths[0]['domain_name']) : '—';
        $topWeakness = !empty($weaknesses) ? ($weaknesses[0]['domain_name_fr'] ?? $weaknesses[0]['domain_name']) : '—';

        $domainCount = count($domainScores);
        $strongCount = count(array_filter($domainScores, fn($d) => $d['percent_score'] >= 70));
        $weakCount   = count(array_filter($domainScores, fn($d) => $d['percent_score'] < 50));

        $summary = sprintf(
            "%s obtient un score global AQMI de %s%%, correspondant au niveau de maturité \"%s\". " .
            "Sur %s domaines évalués, %s présentent un niveau satisfaisant (>= 70%%) tandis que %s nécessitent une attention particulière (< 50%%). " .
            "Le principal point fort identified se situe dans le domaine \"%s\", tandis que l'axe d'amélioration prioritaire concerne \"%s\". " .
            "Ce rapport présente l'analyse détaillée des résultats, les recommandations stratégiques et le plan d'action recommandé pour progresser vers le niveau de maturité supérieur.",
            htmlspecialchars($companyName),
            $globalScore,
            $levelName,
            $domainCount,
            $strongCount,
            $weakCount,
            $topStrength,
            $topWeakness
        );

        return [
            'text'         => $summary,
            'level_name'   => $levelName,
            'level_color'  => $levelColor,
            'global_score' => $globalScore,
            'domain_count' => $domainCount,
            'strong_count' => $strongCount,
            'weak_count'   => $weakCount,
            'top_strength' => $topStrength,
            'top_weakness' => $topWeakness,
        ];
    }

    /**
     * Enrich domain scores with per-domain question/answer details.
     */
    private function enrichDomainScores(array $domainScores, array $answers): array
    {
        $enriched = [];
        foreach ($domainScores as $ds) {
            $domainAnswers = array_filter($answers, fn($a) => $a['domain_id'] == $ds['domain_id']);
            $questions = [];
            foreach ($domainAnswers as $a) {
                $questions[] = [
                    'title'      => $a['title_fr'] ?? $a['title'] ?? '',
                    'score'      => (int)$a['score'],
                    'max'        => 5,
                    'type'       => $a['question_type'] ?? 'rating_scale',
                    'weight'     => (float)($a['question_weight'] ?? 1),
                ];
            }
            $enriched[] = array_merge($ds, [
                'questions'   => $questions,
                'answer_count'=> count($questions),
            ]);
        }
        return $enriched;
    }

    /**
     * Distribution of answer scores (0-5) for a histogram view.
     */
    private function calculateScoreDistribution(array $answers): array
    {
        $dist = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($answers as $a) {
            $score = (int)($a['score'] ?? -1);
            if ($score >= 0 && $score <= 5) {
                $dist[$score]++;
            }
        }
        return $dist;
    }
}
