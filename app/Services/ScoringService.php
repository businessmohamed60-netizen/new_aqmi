<?php
namespace App\Services;

use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Domain;
use App\Models\ScoreLevel;

class ScoringService
{
    public function calculateDomainScores(int $assessmentId): array
    {
        $scores = Answer::getScoresByDomain($assessmentId);
        $domains = Domain::allActive();
        $result = [];

        foreach ($domains as $domain) {
            $domainScore = null;
            foreach ($scores as $score) {
                if ($score['domain_id'] == $domain['id']) { $domainScore = $score; break; }
            }

            if ($domainScore) {
                $avgScore = (float)$domainScore['weighted_score'];
                $maxScore = 5;
                $percentScore = ($maxScore > 0) ? ($avgScore / $maxScore) * 100 : 0;
                $level = ScoreLevel::findByScore($percentScore);
                $result[] = [
                    'domain_id' => $domain['id'],
                    'domain_name' => $domain['name'],
                    'domain_name_fr' => $domain['name_fr'],
                    'icon' => $domain['icon'],
                    'weight' => (float)$domain['weight'],
                    'avg_score' => round($avgScore, 2),
                    'max_score' => $maxScore,
                    'percent_score' => round($percentScore, 1),
                    'level' => $level,
                    'question_count' => (int)$domainScore['question_count'],
                ];
            } else {
                $result[] = [
                    'domain_id' => $domain['id'],
                    'domain_name' => $domain['name'],
                    'domain_name_fr' => $domain['name_fr'],
                    'icon' => $domain['icon'],
                    'weight' => (float)$domain['weight'],
                    'avg_score' => 0,
                    'max_score' => 5,
                    'percent_score' => 0,
                    'level' => null,
                    'question_count' => 0,
                ];
            }
        }
        return $result;
    }

    public function calculateGlobalScore(array $domainScores): float
    {
        $totalWeight = 0; $weightedSum = 0;
        foreach ($domainScores as $domain) {
            $totalWeight += $domain['weight'];
            $weightedSum += $domain['percent_score'] * $domain['weight'];
        }
        return $totalWeight > 0 ? round($weightedSum / $totalWeight, 1) : 0;
    }

    public function getMaturityLevel(float $globalScore): ?array
    {
        return ScoreLevel::findByScore($globalScore);
    }

    public function analyzeAssessment(int $assessmentId): array
    {
        $domainScores = $this->calculateDomainScores($assessmentId);
        $globalScore = $this->calculateGlobalScore($domainScores);
        return [
            'domain_scores' => $domainScores,
            'global_score' => $globalScore,
            'maturity_level' => $this->getMaturityLevel($globalScore),
            'benchmark' => $this->getBenchmark($domainScores),
            'strengths' => $this->getTopStrengths($domainScores, 3),
            'weaknesses' => $this->getTopWeaknesses($domainScores, 3),
            'priorities' => $this->getPriorityAxes($domainScores),
        ];
    }

    public function getBenchmark(array $domainScores): array
    {
        $globalAvg = Assessment::getAverageScore();
        $domainAverages = Database::fetchAll(
            "SELECT d.id, AVG(ds.avg_percent) as avg FROM (
                SELECT q.domain_id, AVG(aa.score) / 5 * 100 as avg_percent
                FROM assessment_answers aa JOIN questions q ON aa.question_id = q.id
                JOIN assessments a ON aa.assessment_id = a.id
                WHERE a.status = 'completed' AND aa.score IS NOT NULL AND q.question_type IN ('rating_scale', 'yes_no')
                GROUP BY q.domain_id, aa.assessment_id
            ) ds JOIN domains d ON ds.domain_id = d.id GROUP BY d.id"
        );
        $domainBenchmark = [];
        foreach ($domainAverages as $avg) { $domainBenchmark[$avg['id']] = round((float)$avg['avg'], 1); }
        return ['global_avg' => round($globalAvg, 1), 'domain_avgs' => $domainBenchmark];
    }

    public function getTopStrengths(array $domainScores, int $count = 3): array
    {
        $sorted = $domainScores;
        usort($sorted, fn($a, $b) => $b['percent_score'] <=> $a['percent_score']);
        return array_slice($sorted, 0, $count);
    }

    public function getTopWeaknesses(array $domainScores, int $count = 3): array
    {
        $sorted = $domainScores;
        usort($sorted, fn($a, $b) => $a['percent_score'] <=> $b['percent_score']);
        $sorted = array_filter($sorted, fn($d) => $d['percent_score'] > 0);
        return array_slice(array_values($sorted), 0, $count);
    }

    public function getPriorityAxes(array $domainScores): array
    {
        $weaknesses = $this->getTopWeaknesses($domainScores, 5);
        $priorities = [];
        foreach ($weaknesses as $w) {
            $priorities[] = [
                'domain_id' => $w['domain_id'],
                'domain_name' => $w['domain_name'],
                'domain_name_fr' => $w['domain_name_fr'],
                'score' => $w['percent_score'],
                'gap' => round(100 - $w['percent_score'], 1),
                'priority' => $w['percent_score'] < 30 ? 'critical' : ($w['percent_score'] < 50 ? 'high' : 'medium'),
            ];
        }
        return $priorities;
    }
}