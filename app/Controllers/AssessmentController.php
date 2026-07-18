<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Domain;
use App\Services\ScoringService;
use App\Services\EmailService;
use App\Models\Lead;

class AssessmentController
{
    private ScoringService $scoringService;
    public function __construct() { $this->scoringService = new ScoringService(); }

    public function start(): void
    {
        $sessionId = session_id();

        // Check if there's an existing in-progress assessment
        $existing = Database::fetch(
            "SELECT id FROM assessments WHERE session_id = ? AND status = 'in_progress' ORDER BY id DESC LIMIT 1",
            [$sessionId]
        );

        if ($existing) {
            redirect('/assessment/' . $existing['id']);
            return;
        }

        $assessmentId = Assessment::create([
            'session_id' => $sessionId,
            'user_id' => Auth::check() ? Auth::id() : null,
        ]);
        redirect('/assessment/' . $assessmentId);
    }

    public function show(array $params): void
    {
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        $domains = Domain::allActive();
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
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        // Calculate final scores
        $analysis = $this->scoringService->analyzeAssessment($assessmentId);
        Assessment::updateScore($assessmentId, $analysis['global_score'], $analysis['maturity_level']['name'] ?? 'N/A');

        // Show lead form
        view('assessment.lead', compact('assessment', 'analysis'));
    }

    public function saveLead(): void
    {
        $assessmentId = (int)($_GET['assessment_id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) { redirect('/'); return; }

        $data = [
            'assessment_id' => $assessmentId,
            'firstname' => $_GET['firstname'] ?? '',
            'lastname' => $_GET['lastname'] ?? '',
            'company' => $_GET['company'] ?? '',
            'sector' => $_GET['sector'] ?? '',
            'job_title' => $_GET['job_title'] ?? '',
            'phone' => $_GET['phone'] ?? '',
            'email' => $_GET['email'] ?? '',
            'country' => $_GET['country'] ?? '',
            'company_size' => $_GET['company_size'] ?? '',
            'website' => $_GET['website'] ?? '',
            'founded_year' => $_GET['founded_year'] ?? '',
        ];

        $leadId = \App\Models\Lead::create($data);
        Assessment::complete($assessmentId);

        // Save custom field values
        if (isset($_GET['custom_fields']) && is_array($_GET['custom_fields'])) {
            \App\Models\LeadCustomField::saveValues($leadId, $_GET['custom_fields']);
        }

        // Create a pending report record for admin validation
        \App\Models\Report::create([
            'assessment_id' => $assessmentId,
            'lead_id' => $leadId,
            'status' => 'pending',
        ]);

        $_SESSION['assessment_completed_' . $assessmentId] = true;
        $_SESSION['lead_id'] = $leadId;

        redirect('/assessment/' . $assessmentId . '/results');
    }

    public function results(array $params): void
    {
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
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        $lead = Lead::findByAssessment($assessmentId);
        if (!$lead) { redirect('/assessment/' . $assessmentId . '/results'); return; }

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