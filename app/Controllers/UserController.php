<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Mailer;
use App\Models\Assessment;

class UserController
{
    public function login(): void
    {
        redirect('/login');
    }

    public function dashboard(): void
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à votre espace.';
            redirect('/login');
            return;
        }
        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Accès non autorisé.';
            redirect('/');
            return;
        }

        $userId = Auth::id();
        $assessments = Database::fetchAll(
            "SELECT a.*, em.name as model_name, em.name_fr as model_name_fr, em.color as model_color,
                    l.company, l.firstname as lead_firstname, l.lastname as lead_lastname,
                    r.status as report_status, r.id as report_id
             FROM assessments a
             LEFT JOIN leads l ON a.id = l.assessment_id
             LEFT JOIN reports r ON r.assessment_id = a.id
             LEFT JOIN evaluation_models em ON a.model_id = em.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC",
            [$userId]
        );

        $totalAssessments = count($assessments);
        $completedCount = 0;
        $completedScores = [];
        $scoreHistory = [];
        foreach ($assessments as $a) {
            if ($a['status'] === 'completed') {
                $completedCount++;
                if ($a['total_score'] !== null) {
                    $completedScores[] = (float)$a['total_score'];
                    $scoreHistory[] = [
                        'date' => date('Y-m-d', strtotime($a['completed_at'] ?? $a['created_at'])),
                        'score' => round((float)$a['total_score'], 1),
                        'company' => $a['company'] ?? ($a['lead_firstname'] ?? '') . ' ' . ($a['lead_lastname'] ?? ''),
                    ];
                }
            }
        }

        usort($scoreHistory, fn($x, $y) => $x['date'] <=> $y['date']);

        $bestScore = !empty($completedScores) ? round(max($completedScores), 1) : null;
        $avgScore = !empty($completedScores) ? round(array_sum($completedScores) / count($completedScores), 1) : null;
        $latestScore = !empty($scoreHistory) ? end($scoreHistory)['score'] : null;
        $firstScore = !empty($scoreHistory) ? reset($scoreHistory)['score'] : null;
        $progressDelta = ($bestScore !== null && $firstScore !== null) ? round($latestScore - $firstScore, 1) : 0;

        $maturityLevel = null;
        if ($latestScore !== null) {
            $maturityLevel = \App\Models\ScoreLevel::findByScore($latestScore);
        }

        $completionRate = $totalAssessments > 0 ? round(($completedCount / $totalAssessments) * 100) : 0;

        $scoreLevels = \App\Models\ScoreLevel::all();

        $consolidatedReports = \App\Models\ConsolidatedReport::findByUser($userId);

        view('user.dashboard', compact(
            'assessments', 'totalAssessments', 'completedCount', 'user', 'consolidatedReports',
            'bestScore', 'avgScore', 'latestScore', 'progressDelta', 'maturityLevel',
            'completionRate', 'scoreHistory', 'scoreLevels'
        ));
    }

    public function consolidatedView(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        $user = Auth::user();
        $userId = Auth::id();

        $completedAssessments = Database::fetchAll(
            "SELECT a.*, em.name as model_name, em.name_fr as model_name_fr, em.color as model_color,
                    l.company
             FROM assessments a
             LEFT JOIN evaluation_models em ON a.model_id = em.id
             LEFT JOIN leads l ON a.id = l.assessment_id
             WHERE a.user_id = ? AND a.status = 'completed'
             ORDER BY a.completed_at DESC",
            [$userId]
        );

        $consolidatedReports = \App\Models\ConsolidatedReport::findByUser($userId);

        view('user.consolidated', compact('completedAssessments', 'consolidatedReports', 'user'));
    }

    public function consolidatedCreate(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/consolidated'); return; }

        $userId = Auth::id();
        $title = trim($_POST['title'] ?? '');
        $assessmentIds = $_POST['assessment_ids'] ?? [];

        if (empty($title) || count($assessmentIds) < 2) {
            $_SESSION['error'] = 'Veuillez saisir un titre et sélectionner au moins 2 évaluations.';
            redirect('/user/consolidated');
            return;
        }

        $validAssessments = [];
        foreach ($assessmentIds as $aid) {
            $aid = (int)$aid;
            $a = Assessment::find($aid);
            if ($a && $a['user_id'] == $userId && $a['status'] === 'completed') {
                $validAssessments[] = $a;
            }
        }

        if (count($validAssessments) < 2) {
            $_SESSION['error'] = 'Sélection invalide.';
            redirect('/user/consolidated');
            return;
        }

        $consolidatedId = \App\Models\ConsolidatedReport::create($userId, $title);

        $totalScore = 0;
        $count = 0;
        foreach ($validAssessments as $a) {
            $model = Database::fetch("SELECT name, name_fr FROM evaluation_models WHERE id = ?", [$a['model_id'] ?? 0]);
            $modelName = $model ? ($model['name_fr'] ?: $model['name']) : 'Modèle';
            $score = (float)($a['total_score'] ?? 0);
            \App\Models\ConsolidatedReport::addItem(
                $consolidatedId, $a['id'], $a['model_id'] ?? null, $modelName, $score, $a['maturity_level'] ?? null
            );
            $totalScore += $score;
            $count++;
        }

        $avgScore = $count > 0 ? round($totalScore / $count, 2) : 0;
        $level = $avgScore >= 86 ? 'Excellence' : ($avgScore >= 71 ? 'Performant' : ($avgScore >= 51 ? 'Structuré' : ($avgScore >= 31 ? 'En Développement' : 'Débutant')));
        \App\Models\ConsolidatedReport::updateScore($consolidatedId, $avgScore, $level);

        $_SESSION['success'] = 'Rapport consolidé créé avec succès.';
        redirect('/user/consolidated/' . $consolidatedId);
    }

    public function consolidatedDetail(array $params): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report || $report['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Rapport introuvable.';
            redirect('/user/consolidated');
            return;
        }

        $items = \App\Models\ConsolidatedReport::getItems($id);
        $user = Auth::user();

        view('user.consolidated_detail', compact('report', 'items', 'user'));
    }

    public function consolidatedRequest(array $params): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/consolidated'); return; }

        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report || $report['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Rapport introuvable.';
            redirect('/user/consolidated');
            return;
        }

        if (!in_array($report['status'], ['draft', 'rejected'])) {
            $_SESSION['error'] = 'Cette demande ne peut pas être renvoyée.';
            redirect('/user/consolidated/' . $id);
            return;
        }

        \App\Models\ConsolidatedReport::updateStatus($id, 'certification_requested');

        $adminEmail = Database::fetch("SELECT email FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'admin' LIMIT 1");
        if ($adminEmail) {
            $user = Auth::user();
            $body = "<h2>Nouvelle demande de rapport consolidé certifié</h2>"
                  . "<p><strong>Utilisateur:</strong> " . e($user['firstname'] . ' ' . $user['lastname']) . "</p>"
                  . "<p><strong>Titre:</strong> " . e($report['title']) . "</p>"
                  . "<p><strong>Score consolidé:</strong> " . round((float)$report['consolidated_score']) . "/100</p>"
                  . "<p><a href=\"" . ($_ENV['APP_URL'] ?? '') . "/admin/consolidated/" . $id . "\"> Examiner la demande</a></p>";
            Mailer::send($adminEmail['email'], 'Nouvelle demande de rapport consolidé - NOVAQYS', $body);
        }

        $_SESSION['success'] = 'Demande de certification envoyée à l\'administration.';
        redirect('/user/consolidated/' . $id);
    }

    public function consolidatedDelete(array $params): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/consolidated'); return; }

        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report || $report['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Rapport introuvable.';
            redirect('/user/consolidated');
            return;
        }

        if (in_array($report['status'], ['certification_requested', 'under_review', 'approved', 'certified'])) {
            $_SESSION['error'] = 'Impossible de supprimer un rapport en cours de certification.';
            redirect('/user/consolidated/' . $id);
            return;
        }

        \App\Models\ConsolidatedReport::delete($id);
        $_SESSION['success'] = 'Rapport consolidé supprimé.';
        redirect('/user/consolidated');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
