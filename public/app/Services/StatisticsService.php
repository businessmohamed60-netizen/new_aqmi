<?php
namespace App\Services;

use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\Lead;
use App\Models\EvaluationModel;
use App\Models\Report;

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
            'pending_certifications' => Report::pendingCertifications(),
            'pending_certifications_count' => count(Report::pendingCertifications()),
        ];
    }

    /**
     * Données orientées administration : utilisateurs, utilisateurs en ligne,
     * modèles d'évaluation, domaines, questions.
     */
    public function getAdminOverview(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'online_users' => $this->getOnlineUsers(),
            'online_users_count' => count($this->getOnlineUsers()),
            'recent_users' => \App\Models\User::getRecent(8),
            'evaluation_models' => $this->getModelsStats(),
            'domains' => $this->getDomainsList(),
            'questions' => $this->getQuestionsList(),
            'total_domains' => \App\Models\Domain::count(),
            'total_questions' => \App\Models\Question::getActiveCount(),
            'total_models' => EvaluationModel::count(),
            'total_assessments' => Assessment::count(),
            'total_leads' => Lead::count(),
            'pending_certifications' => Report::pendingCertifications(),
            'pending_certifications_count' => count(Report::pendingCertifications()),
        ];
    }

    /**
     * Utilisateurs "en ligne" : sessions actives dans les 15 dernières minutes.
     */
    public function getOnlineUsers(): array
    {
        return Database::fetchAll(
            "SELECT u.id, u.firstname, u.lastname, u.email, r.name as role_name,
                    MAX(lh.login_date) as last_login
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN login_history lh ON lh.user_id = u.id AND lh.result = 'success'
             WHERE u.is_active = 1
               AND (lh.login_date IS NOT NULL AND lh.login_date > DATE_SUB(NOW(), INTERVAL 15 MINUTE))
             GROUP BY u.id
             ORDER BY lh.login_date DESC"
        );
    }

    /**
     * Liste des domaines avec le nombre de questions par domaine.
     */
    public function getDomainsList(): array
    {
        return Database::fetchAll(
            "SELECT d.id, d.name, d.name_fr, d.icon, d.is_active, d.sort_order,
                    (SELECT COUNT(*) FROM questions q WHERE q.domain_id = d.id) as questions_count
             FROM domains d
             ORDER BY d.sort_order"
        );
    }

    /**
     * Liste des questions avec domaine et modèle associés.
     */
    public function getQuestionsList(): array
    {
        return Database::fetchAll(
            "SELECT q.id, q.title, q.title_fr, q.question_type, q.is_active, q.sort_order,
                    d.name_fr as domain_name_fr, d.name as domain_name,
                    em.name_fr as model_name_fr, em.name as model_name
             FROM questions q
             JOIN domains d ON q.domain_id = d.id
             LEFT JOIN evaluation_models em ON q.model_id = em.id
             ORDER BY d.sort_order, q.sort_order
             LIMIT 50"
        );
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