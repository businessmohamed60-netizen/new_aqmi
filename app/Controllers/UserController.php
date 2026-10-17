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
        $scoringService = new \App\Services\ScoringService();

        foreach ($assessments as $idx => $a) {
            if ($a['status'] === 'completed') {
                $completedCount++;

                // Recalculate the real score from the answers, so the dashboard
                // never shows stale or broken values. Persist the corrected
                // score so other pages (results, reports, consolidated) are
                // also fixed.
                $realScore = null;
                $realLevel = null;
                try {
                    $analysis = $scoringService->analyzeAssessment((int)$a['id']);
                    $realScore = round($analysis['global_score'], 1);
                    $realLevel = $analysis['maturity_level']['name'] ?? null;
                } catch (\Throwable $e) {
                    $realScore = $a['total_score'] !== null ? round((float)$a['total_score'], 1) : null;
                    $realLevel = $a['maturity_level'] ?? null;
                }

                if ($realScore !== null) {
                    // Update stored value if it drifted
                    if ($a['total_score'] === null || abs((float)$a['total_score'] - $realScore) >= 0.05) {
                        Assessment::updateScore((int)$a['id'], $realScore, $realLevel ?? 'N/A');
                    }

                    $assessments[$idx]['total_score'] = $realScore;
                    $assessments[$idx]['maturity_level'] = $realLevel;

                    $completedScores[] = $realScore;
                    $scoreHistory[] = [
                        'date' => date('Y-m-d', strtotime($a['completed_at'] ?? $a['created_at'])),
                        'score' => $realScore,
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

        $assessmentLimit = \App\Models\User::getAssessmentLimit($userId);
        $assessmentsRemaining = null;
        $assessmentsUsed = $totalAssessments;
        if ($assessmentLimit !== null) {
            $assessmentsRemaining = max(0, $assessmentLimit - $assessmentsUsed);
        }

        $modelStats = $this->buildModelStats($userId, $assessments, $scoringService);

        view('user.dashboard', compact(
            'assessments', 'totalAssessments', 'completedCount', 'user', 'consolidatedReports',
            'bestScore', 'avgScore', 'latestScore', 'progressDelta', 'maturityLevel',
            'completionRate', 'scoreHistory', 'scoreLevels',
            'assessmentLimit', 'assessmentsRemaining', 'assessmentsUsed',
            'modelStats'
        ));
    }

    public function models(): void
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
        $modelStats = $this->buildModelStats($userId, $assessments);

        view('user.models', compact('user', 'modelStats', 'totalAssessments'));
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

        $consolidationService = new \App\Services\ConsolidationService();
        $methods = $consolidationService->getAvailableMethods();

        view('user.consolidated', compact('completedAssessments', 'consolidatedReports', 'user', 'methods'));
    }

    public function consolidatedCreate(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/consolidated'); return; }

        $userId = Auth::id();
        $title = trim($_POST['title'] ?? '');
        $assessmentIds = $_POST['assessment_ids'] ?? [];
        $method = trim($_POST['calculation_method'] ?? 'weighted_average');
        $weightInput = $_POST['weights'] ?? [];

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

        // Construire le tableau des poids
        $weights = [];
        foreach ($validAssessments as $a) {
            $aid = (int)$a['id'];
            $w = (float)($weightInput[$aid] ?? 1.0);
            if ($w < 0) $w = 0;
            if ($w > 10) $w = 10;
            $weights[$aid] = $w;
        }

        // Calculer avec le moteur de consolidation
        $consolidationService = new \App\Services\ConsolidationService();
        $assessmentIdList = array_map(fn($a) => (int)$a['id'], $validAssessments);
        $result = $consolidationService->calculate($assessmentIdList, $method, $weights);

        // Créer le rapport consolidé
        $consolidatedId = \App\Models\ConsolidatedReport::create($userId, $title);

        // Ajouter les items avec leurs poids
        foreach ($validAssessments as $a) {
            $aid = (int)$a['id'];
            $model = Database::fetch("SELECT name, name_fr FROM evaluation_models WHERE id = ?", [$a['model_id'] ?? 0]);
            $modelName = $model ? ($model['name_fr'] ?: $model['name']) : 'Modèle';

            // Trouver le détail du score dans les résultats
            $itemDetail = null;
            foreach ($result['items_detail'] as $detail) {
                if ((int)$detail['assessment_id'] === $aid) { $itemDetail = $detail; break; }
            }

            $score = $itemDetail ? $itemDetail['score'] : (float)($a['total_score'] ?? 0);
            $level = $itemDetail['maturity_level'] ?? ($a['maturity_level'] ?? null);

            \App\Models\ConsolidatedReport::addItem(
                $consolidatedId, $aid, $a['model_id'] ?? null, $modelName, $score, $level, $weights[$aid]
            );
        }

        // Persister les méta-données de calcul
        $consolidationService->persist($consolidatedId, $result, $weights);

        $_SESSION['success'] = 'Rapport consolidé créé avec la méthode "' . ucfirst(str_replace('_', ' ', $method)) . '".';
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
        $domainBreakdown = \App\Models\ConsolidatedReport::getDomainBreakdown($id);
        $user = Auth::user();

        view('user.consolidated_detail', compact('report', 'items', 'domainBreakdown', 'user'));
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
        $user = Auth::user();
        $body = "<h2>Nouvelle demande de rapport consolidé certifié</h2>"
              . "<p><strong>Utilisateur:</strong> " . e($user['firstname'] . ' ' . $user['lastname']) . "</p>"
              . "<p><strong>Titre:</strong> " . e($report['title']) . "</p>"
              . "<p><strong>Score consolidé:</strong> " . round((float)$report['consolidated_score']) . "/100</p>"
              . "<p><a href=\"" . ($_ENV['APP_URL'] ?? '') . "/admin/consolidated/" . $id . "\"> Examiner la demande</a></p>";
        Mailer::notifyAdmin('Nouvelle demande de rapport consolidé - NOVAQYS', $body);

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

    private function buildModelStats(int $userId, array $assessments, ?\App\Services\ScoringService $scoringService = null): array
    {
        $scoringService = $scoringService ?? new \App\Services\ScoringService();
        $langCode = $_SESSION['lang'] ?? 'fr';
        $modelStats = [];

        $userModels = Database::fetchAll(
            "SELECT DISTINCT em.id, em.name, em.name_fr, em.name_ar, em.icon, em.color,
                    em.description, em.description_fr, em.description_ar
             FROM evaluation_models em
             INNER JOIN assessments a ON a.model_id = em.id
             WHERE a.user_id = ?
             ORDER BY em.sort_order",
            [$userId]
        );

        foreach ($userModels as $em) {
            $modelId = (int)$em['id'];
            $modelAssessments = array_filter($assessments, fn($a) => (int)($a['model_id'] ?? 0) === $modelId);
            $modelTotal = count($modelAssessments);
            $modelCompleted = 0;
            $modelScores = [];
            $modelLatestScore = null;
            $modelLatestAssessmentId = null;
            $modelLatestDate = null;

            foreach ($modelAssessments as $a) {
                if ($a['status'] === 'completed') {
                    $modelCompleted++;
                    if ($a['total_score'] !== null) {
                        $modelScores[] = (float)$a['total_score'];
                        $aDate = $a['completed_at'] ?? $a['created_at'];
                        if ($modelLatestDate === null || strtotime($aDate) > strtotime($modelLatestDate)) {
                            $modelLatestDate = $aDate;
                            $modelLatestScore = round((float)$a['total_score'], 1);
                            $modelLatestAssessmentId = (int)$a['id'];
                        }
                    }
                }
            }

            $modelBestScore = !empty($modelScores) ? round(max($modelScores), 1) : null;
            $modelAvgScore = !empty($modelScores) ? round(array_sum($modelScores) / count($modelScores), 1) : null;
            $modelMaturityLevel = null;
            $modelDomainScores = [];

            if ($modelLatestAssessmentId !== null) {
                $modelMaturityLevel = \App\Models\ScoreLevel::findByScore($modelLatestScore);
                $modelDomainScores = $scoringService->calculateDomainScores($modelLatestAssessmentId);
            }

            $modelDomains = \App\Models\EvaluationModel::getDomains($modelId);
            $domainLabels = [];
            foreach ($modelDomains as $d) {
                $labelName = $langCode === 'ar' ? ($d['name_ar'] ?? '') : ($d['name_fr'] ?? $d['name']);
                $domainLabels[] = $labelName ?: $d['name'];
            }

            $chartLabels = [];
            $chartValues = [];
            foreach ($modelDomainScores as $ds) {
                $chartLabels[] = $ds['domain_label'];
                $chartValues[] = round($ds['percent_score'], 1);
            }
            if (empty($chartLabels)) {
                $chartLabels = $domainLabels;
                $chartValues = array_fill(0, count($domainLabels), 0);
            }

            $modelStats[] = [
                'id' => $modelId,
                'name' => $em['name'],
                'name_fr' => $em['name_fr'] ?? '',
                'name_ar' => $em['name_ar'] ?? '',
                'display_name' => $langCode === 'ar' ? ($em['name_ar'] ?? '') : ($em['name_fr'] ?: $em['name']),
                'icon' => $em['icon'] ?? 'fa-clipboard-check',
                'color' => $em['color'] ?? '#1a56db',
                'description' => $em['description'] ?? '',
                'description_fr' => $em['description_fr'] ?? '',
                'description_ar' => $em['description_ar'] ?? '',
                'total' => $modelTotal,
                'completed' => $modelCompleted,
                'best_score' => $modelBestScore,
                'avg_score' => $modelAvgScore,
                'latest_score' => $modelLatestScore,
                'latest_assessment_id' => $modelLatestAssessmentId,
                'maturity_level' => $modelMaturityLevel,
                'domain_scores' => $modelDomainScores,
                'chart_labels' => $chartLabels,
                'chart_values' => $chartValues,
            ];
        }

        return $modelStats;
    }

    public function profile(): void
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

        $loginHistory = Database::fetchAll(
            "SELECT * FROM login_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10",
            [Auth::id()]
        );

        view('user.profile', compact('user', 'loginHistory'));
    }

    public function updateProfile(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/profile'); return; }

        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Accès non autorisé.';
            redirect('/');
            return;
        }

        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $requiredFields = [
            'company', 'job_title', 'sector', 'country', 'company_size',
            'website', 'founded_year', 'production_type', 'certifications',
        ];
        $missing = [];
        foreach ($requiredFields as $rf) {
            if (empty(trim($_POST[$rf] ?? ''))) {
                $missing[] = $rf;
            }
        }
        if (empty($firstname) || empty($lastname) || empty($email)) {
            $missing[] = 'firstname';
            $missing[] = 'lastname';
            $missing[] = 'email';
        }
        if (!empty($missing)) {
            $_SESSION['error'] = __('profile.error.required') ?: 'Veuillez remplir tous les champs obligatoires.';
            redirect('/user/profile');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = __('profile.error.invalid_email');
            redirect('/user/profile');
            return;
        }

        $existing = \App\Models\User::findByEmail($email);
        if ($existing && $existing['id'] !== Auth::id()) {
            $_SESSION['error'] = __('profile.error.email_taken');
            redirect('/user/profile');
            return;
        }

        $numFields = ['export_percentage', 'production_sites', 'workforce_production',
            'workforce_engineering', 'founded_year', 'machine_count', 'ppm_target',
            'otd_rate', 'fta_rate', 'scrap_rate', 'rd_budget_percent'];
        $textFields = ['company', 'job_title', 'sector', 'activity_category', 'oem_tier',
            'product_category', 'main_clients', 'annual_revenue', 'country', 'company_size',
            'website', 'certifications', 'production_type', 'production_capacity',
            'machine_types', 'main_materials', 'process_technologies',
            'traceability_system', 'logistics_system', 'current_erp'];

        $updateData = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone ?: null,
        ];

        foreach ($textFields as $f) {
            $updateData[$f] = trim($_POST[$f] ?? '') !== '' ? trim($_POST[$f]) : null;
        }
        foreach ($numFields as $f) {
            $val = trim($_POST[$f] ?? '');
            $updateData[$f] = $val !== '' ? (strpos($f, 'rate') !== false || strpos($f, 'percent') !== false ? (float)$val : (int)$val) : null;
        }

        \App\Models\User::update(Auth::id(), $updateData);

        $_SESSION['success'] = __('profile.success.updated');
        redirect('/user/profile');
    }

    public function updatePassword(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/profile'); return; }

        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Accès non autorisé.';
            redirect('/');
            return;
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $_SESSION['error'] = __('profile.error.password_required');
            redirect('/user/profile');
            return;
        }

        if (!\App\Helpers\Security::verifyPassword($current, $user['password'])) {
            $_SESSION['error'] = __('profile.error.wrong_password');
            redirect('/user/profile');
            return;
        }

        if (strlen($new) < 8) {
            $_SESSION['error'] = __('profile.error.password_short');
            redirect('/user/profile');
            return;
        }

        if ($new !== $confirm) {
            $_SESSION['error'] = __('profile.error.password_mismatch');
            redirect('/user/profile');
            return;
        }

        \App\Models\User::update(Auth::id(), [
            'password' => \App\Helpers\Security::hashPassword($new),
        ]);

        $_SESSION['success'] = __('profile.success.password_changed');
        redirect('/user/profile');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
