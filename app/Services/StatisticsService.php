<?php
namespace App\Services;

use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\Lead;
use App\Models\EvaluationModel;

class StatisticsService
{
    public function getOverview(): array
    {
        return [
            'total_assessments' => Assessment::count(),
            'completed_assessments' => Assessment::countCompleted(),
            'total_leads' => Lead::count(),
            'average_score' => Assessment::getAverageScore(),
            'completion_rate' => Assessment::getCompletionRate(),
            'total_models' => EvaluationModel::count(),
            'total_questions' => \App\Models\Question::getActiveCount(),
            'recent_assessments' => Assessment::getRecent(5),
            'recent_leads' => Lead::getRecent(5),
            'models_stats' => $this->getModelsStats(),
        ];
    }

    public function getModelsStats(): array
    {
        $models = EvaluationModel::allActive();
        $stats = [];
        foreach ($models as $m) {
            $stats[] = [
                'id' => $m['id'],
                'name' => $m['name_fr'] ?: $m['name'],
                'icon' => $m['icon'],
                'color' => $m['color'],
                'domains_count' => count(EvaluationModel::getDomains($m['id'])),
                'questions_count' => EvaluationModel::getQuestionsCount($m['id']),
            ];
        }
        return $stats;
    }

    public function getChartData(): array
    {
        return [
            'monthly_assessments' => Assessment::getMonthlyStats(12),
            'monthly_leads' => Lead::getMonthlyStats(12),
            'sector_distribution' => Lead::getSectorDistribution(),
            'country_distribution' => Lead::getCountryDistribution(),
            'score_distribution' => $this->getScoreDistribution(),
        ];
    }

    public function getScoreDistribution(): array
    {
        $levels = Database::fetchAll("SELECT * FROM score_levels WHERE is_active = 1 ORDER BY sort_order");
        $distribution = [];
        foreach ($levels as $level) {
            $count = (int)Database::fetch(
                "SELECT COUNT(*) as count FROM assessments WHERE status = 'completed' AND total_score >= ? AND (total_score <= ? OR ? = 100)",
                [$level['min_percent'], $level['max_percent'], $level['max_percent']]
            )['count'];
            $distribution[] = [
                'level' => $level['name_fr'] ?: $level['name'],
                'min' => $level['min_percent'], 'max' => $level['max_percent'],
                'color' => $level['color'], 'count' => $count,
            ];
        }
        return $distribution;
    }

    public function getDomainAverages(): array
    {
        return Database::fetchAll(
            "SELECT d.name, d.name_fr, d.icon, d.sort_order,
                    COALESCE(AVG(ds.avg_percent), 0) as avg_percent
             FROM domains d
             LEFT JOIN (SELECT q.domain_id, AVG(aa.score) / 5 * 100 as avg_percent
                        FROM assessment_answers aa JOIN questions q ON aa.question_id = q.id
                        JOIN assessments a ON aa.assessment_id = a.id
                        WHERE a.status = 'completed' GROUP BY q.domain_id, aa.assessment_id) ds ON d.id = ds.domain_id
             WHERE d.is_active = 1 GROUP BY d.id ORDER BY d.sort_order"
        );
    }

    public function getExportData(): array
    {
        return Database::fetchAll(
            "SELECT a.id, a.total_score, a.maturity_level, a.completed_at,
                    l.firstname, l.lastname, l.company, l.sector, l.email, l.country
             FROM assessments a LEFT JOIN leads l ON a.id = l.assessment_id
             WHERE a.status = 'completed' ORDER BY a.completed_at DESC"
        );
    }
}