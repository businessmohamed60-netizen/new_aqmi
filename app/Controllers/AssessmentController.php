<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Domain;
use App\Models\EvaluationModel;
use App\Services\ScoringService;
use App\Services\EmailService;
use App\Models\Lead;

class AssessmentController
{
    private ScoringService $scoringService;
    public function __construct() { $this->scoringService = new ScoringService(); }

    public function start(): void
    {
        Auth::requireAuth();
        $sessionId = session_id();

        // Check if there's an existing in-progress assessment
        $existing = Database::fetch(
            "SELECT id, model_id FROM assessments WHERE session_id = ? AND status = 'in_progress' ORDER BY id DESC LIMIT 1",
            [$sessionId]
        );

        if ($existing && $existing['model_id']) {
            redirect('/assessment/' . $existing['id']);
            return;
        }

        if ($existing && !$existing['model_id']) {
            redirect('/assessment/' . $existing['id']);
            return;
        }

        $assessmentId = Assessment::create([
            'session_id' => $sessionId,
            'user_id' => Auth::check() ? Auth::id() : null,
        ]);
        redirect('/assessment/' . $assessmentId);
    }

    public function selectModel(): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($_POST['assessment_id'] ?? 0);
        $modelId = (int)($_POST['model_id'] ?? 0);

        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'in_progress') {
            jsonResponse(['success' => false, 'error' => 'Invalid assessment'], 400);
            return;
        }

        $model = EvaluationModel::find($modelId);
        if (!$model || !$model['is_active']) {
            jsonResponse(['success' => false, 'error' => 'Invalid model'], 400);
            return;
        }

        Assessment::setModel($assessmentId, $modelId);
        jsonResponse(['success' => true, 'redirect' => '/assessment/' . $assessmentId]);
    }

    public function show(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        // If no model selected yet, show model selection screen
        if (!$assessment['model_id']) {
            $models = EvaluationModel::allActive();
            view('assessment.index', compact('assessment', 'models'));
            return;
        }

        $modelId = (int)$assessment['model_id'];
        $modelDomains = EvaluationModel::getDomains($modelId);
        $domainIds = array_column($modelDomains, 'id');

        if (empty($domainIds)) {
            $domains = Domain::allActive();
        } else {
            $placeholders = implode(',', array_fill(0, count($domainIds), '?'));
            $domains = Database::fetchAll(
                "SELECT * FROM domains WHERE id IN ($placeholders) AND is_active = 1 ORDER BY sort_order",
                $domainIds
            );
        }

        $answers = Answer::findByAssessment($assessmentId);
        $answeredQuestionIds = array_column($answers, 'question_id');

        // Build domain progress
        $domainQuestions = [];
        $totalQuestions = 0;
        $totalAnswered = count($answeredQuestionIds);
        $currentDomainIndex = 0;

        foreach ($domains as $i => $domain) {
            $questions = Database::fetchAll(
                "SELECT * FROM questions WHERE domain_id = ? AND is_active = 1 ORDER BY sort_order",
                [$domain['id']]
            );
            if (empty($questions)) continue;

            $domainTotal = count($questions);
            $domainAnswered = 0;
            foreach ($questions as $q) {
                if (in_array($q['id'], $answeredQuestionIds)) $domainAnswered++;
            }

            $unanswered = [];
            foreach ($questions as $q) {
                if (!in_array($q['id'], $answeredQuestionIds)) {
                    $unanswered[] = $q;
                }
            }

            // If current domain is complete, find next incomplete
            if ($domainAnswered === $domainTotal && $currentDomainIndex === $i && $i < count($domains) - 1) {
                $currentDomainIndex = $i + 1;
            }

            $domainQuestions[] = [
                'domain' => $domain,
                'questions' => $questions,
                'total' => $domainTotal,
                'answered' => $domainAnswered,
                'unanswered' => $unanswered,
                'is_complete' => $domainAnswered === $domainTotal,
            ];
            $totalQuestions += $domainTotal;
        }

        $completionPercent = $totalQuestions > 0 ? round(($totalAnswered / $totalQuestions) * 100) : 0;
        $currentDomain = $domains[$currentDomainIndex] ?? null;

        view('assessment.index', compact(
            'assessment', 'domains', 'domainQuestions', 'answers',
            'totalQuestions', 'totalAnswered', 'completionPercent',
            'currentDomainIndex', 'currentDomain'
        ));
    }

    public function saveAnswer(): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($_GET['assessment_id'] ?? 0);
        $questionId = (int)($_GET['question_id'] ?? 0);
        $score = (int)($_GET['score'] ?? -1);

        if ($score < 0 || $score > 5) {
            jsonResponse(['success' => false, 'error' => 'Invalid score'], 400);
            return;
        }

        Answer::save($assessmentId, $questionId, $score);
        jsonResponse(['success' => true]);
    }

    public function complete(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        // Calculate final scores
        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        Assessment::updateScore($assessmentId, $analysis['global_score'], $analysis['maturity_level']['name'] ?? 'N/A');
        Assessment::complete($assessmentId);

        // Le score est affiché immédiatement ; le formulaire lead n'est
        // proposé que si l'utilisateur demande un rapport certifié.
        redirect('/assessment/' . $assessmentId . '/results');
    }

    public function showLeadForm(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        view('assessment.lead', compact('assessment', 'analysis'));
    }

    public function saveLead(): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($_POST['assessment_id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        $consentContact = $_POST['consent_contact'] ?? '';
        $consentShare = $_POST['consent_share_industry'] ?? '';

        if ($consentContact !== 'yes' || $consentShare !== 'yes') {
            $_SESSION['error'] = 'Vous devez accepter les deux consentements pour transmettre votre demande.';
            redirect('/assessment/' . $assessmentId . '/lead');
            return;
        }

        $data = [
            'assessment_id' => $assessmentId,
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'company' => $_POST['company'] ?? '',
            'sector' => $_POST['sector'] ?? '',
            'job_title' => $_POST['job_title'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'country' => $_POST['country'] ?? '',
            'company_size' => $_POST['company_size'] ?? '',
            'website' => $_POST['website'] ?? '',
            'founded_year' => $_POST['founded_year'] ?? '',
            'consent_contact' => $consentContact === 'yes' ? 1 : 0,
            'consent_share_industry' => $consentShare === 'yes' ? 1 : 0,
        ];

        $leadId = \App\Models\Lead::create($data);

        // Save custom field values
        if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
            \App\Models\LeadCustomField::saveValues($leadId, $_POST['custom_fields']);
        }

        // Create a pending report record for admin validation.
        // Aucun PDF n'est généré ici : seul l'administrateur génère,
        // signe et publie le rapport certifié depuis son espace.
        \App\Models\Report::create([
            'assessment_id' => $assessmentId,
            'lead_id' => $leadId,
            'status' => 'certification_requested',
        ]);

        $_SESSION['lead_id'] = $leadId;
        $_SESSION['success'] = 'Votre demande a été transmise à l\'administrateur.';

        redirect('/assessment/' . $assessmentId . '/results');
    }

    public function results(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        $recommendations = (new \App\Services\RecommendationService())->generate($assessmentId);
        $report = \App\Models\Report::findByAssessment($assessmentId);

        view('assessment.results', compact('assessment', 'analysis', 'recommendations', 'report'));
    }

    public function requestReport(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        $lead = Lead::findByAssessment($assessmentId);
        if (!$lead) { redirect('/assessment/' . $assessmentId . '/results'); return; }

        // Ce bouton n'est affiché que pour RENVOYER une demande déjà
        // rejetée (voir results.php, cas $isRejected). Sans ceci, la
        // demande restait bloquée au statut 'rejected' en base et ne
        // réapparaissait donc jamais dans la liste des demandes en
        // attente de l'admin (Report::pendingCertifications() ne
        // remonte que 'certification_requested' / 'under_review') —
        // seul un email était envoyé, la base n'était jamais mise à jour.
        $report = \App\Models\Report::findByAssessment($assessmentId);
        if ($report) {
            \App\Models\Report::updateStatus($report['id'], 'certification_requested');
        }

        $leadName = ($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? '');
        $company = $lead['company'] ?? 'Entreprise';
        $adminUrl = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '/') . '/admin/reports';

        try {
            $emailService = new EmailService();
            $emailService->sendAdminReportRequest($leadName, $company, $assessmentId, $adminUrl);
            $_SESSION['success'] = 'Votre demande de rapport a été envoyée. Un administrateur validera votre rapport sous peu.';
        } catch (\Exception $e) {
            $_SESSION['success'] = 'Votre demande a été enregistrée. Un administrateur traitera votre rapport sous peu.';
        }

        redirect('/assessment/' . $assessmentId . '/results');
    }

    }