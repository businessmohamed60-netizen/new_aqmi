<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Models\Domain;
use App\Models\EvaluationModel;
use App\Models\LeadCustomField;
use App\Models\Question;
use App\Models\Assessment;
use App\Models\Lead;
use App\Models\User;
use App\Models\ScoreLevel;
use App\Models\Recommendation;
use App\Models\Report;
use App\Services\StatisticsService;
use App\Services\ExportService;
use App\Helpers\Auth;

class AdminController
{
    private StatisticsService $statsService;
    public function __construct() { $this->statsService = new StatisticsService(); }

    public function login(): void
    {
        if (Auth::check()) { redirect('/admin'); return; }

        // Handle GET-based login (proxy workaround: Cloudflare blocks POST)
        $email = $_GET['email'] ?? '';
        $password = $_GET['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            if (Auth::login($email, $password)) {
                redirect('/admin');
            } else {
                $_SESSION['error'] = 'Email ou mot de passe incorrect.';
                redirect('/admin/login');
            }
            return;
        }

        view('auth.login');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    // === DASHBOARD ===
    public function dashboard(): void
    {
        Auth::requireAuth();
        $stats = $this->statsService->getOverview();
        $chartData = $this->statsService->getChartData();
        $domainAverages = $this->statsService->getDomainAverages();
        view('admin.dashboard.index', compact('stats', 'chartData', 'domainAverages'));
    }

    // === QUESTIONS ===
    public function questions(): void
    {
        Auth::requireAuth();
        $questions = Database::fetchAll(
            "SELECT q.*, d.name_fr as domain_name_fr, d.name as domain_name, em.name_fr as model_name_fr, em.name as model_name FROM questions q JOIN domains d ON q.domain_id = d.id LEFT JOIN evaluation_models em ON q.model_id = em.id ORDER BY d.sort_order, q.sort_order"
        );
        $domains = Domain::all();
        view('admin.questions.index', compact('questions', 'domains'));
    }

    public function questionForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $question = $id ? Question::find($id) : null;
        $domains = Domain::all();
        $evaluationModels = EvaluationModel::allActive();
        $modelDomains = [];
        foreach ($evaluationModels as $em) {
            $modelDomains[$em['id']] = array_column(EvaluationModel::getDomains($em['id']), 'id');
        }
        view('admin.questions.form', compact('question', 'domains', 'evaluationModels', 'modelDomains'));
    }

    public function questionSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);

        // Encode options for multiple_choice type
        $options = $_POST['options'] ?? [];
        $optionsJson = null;
        if (!empty($options) && is_array($options)) {
            $parsed = [];
            foreach ($options as $opt) {
                if (!empty($opt['value']) && !empty($opt['label'])) {
                    $parsed[] = ['value' => $opt['value'], 'label' => $opt['label']];
                }
            }
            if (!empty($parsed)) $optionsJson = json_encode($parsed);
        }

        $data = [
            'domain_id' => (int)($_POST['domain_id'] ?? 0),
            'model_id' => !empty($_POST['model_id']) ? (int)$_POST['model_id'] : null,
            'question_type' => $_POST['question_type'] ?? 'rating_scale',
            'title' => $_POST['title'] ?? '',
            'title_fr' => $_POST['title_fr'] ?? '',
            'title_ar' => $_POST['title_ar'] ?? '',
            'options' => $optionsJson,
            'is_required' => isset($_POST['is_required']) ? 1 : 0,
            'help_text' => $_POST['help_text'] ?? '',
            'help_text_fr' => $_POST['help_text_fr'] ?? '',
            'help_text_ar' => $_POST['help_text_ar'] ?? '',
            'description' => $_POST['description'] ?? '',
            'description_fr' => $_POST['description_fr'] ?? '',
            'description_ar' => $_POST['description_ar'] ?? '',
            'weight' => (float)($_POST['weight'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { Question::update($id, $data); } else { Question::create($data); }
        $_SESSION['success'] = 'Question enregistrée.';
        redirect('/admin/questions');
    }

    public function questionDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) Question::delete($id);
        $_SESSION['success'] = 'Question supprimée.';
        redirect('/admin/questions');
    }

    public function questionDuplicate(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $original = Question::find($id);
        if ($original) {
            unset($original['id'], $original['created_at'], $original['updated_at']);
            $original['title'] = ($original['title'] ?? '') . ' (copie)';
            $original['title_fr'] = ($original['title_fr'] ?? '') . ' (copie)';
            Question::create($original);
            $_SESSION['success'] = 'Question dupliquée.';
        }
        redirect('/admin/questions');
    }

    public function questionToggle(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $q = Question::find($id);
        if ($q) {
            Question::update($id, ['is_active' => $q['is_active'] ? 0 : 1]);
        }
        jsonResponse(['success' => true]);
    }

    public function questionReorder(): void
    {
        Auth::requireAuth();
        $order = $_POST['order'] ?? [];
        foreach ($order as $index => $id) {
            Database::execute("UPDATE questions SET sort_order = ? WHERE id = ?", [(int)$index, (int)$id]);
        }
        jsonResponse(['success' => true]);
    }

    public function questionImport(): void
    {
        Auth::requireAuth();
        // CSV Import - basic implementation
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erreur lors de l\'import du fichier.';
            redirect('/admin/questions');
        }

        $file = fopen($_FILES['import_file']['tmp_name'], 'r');
        $headers = fgetcsv($file);
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);
            $domain = Database::fetch("SELECT id FROM domains WHERE name_fr = ? OR name = ? LIMIT 1", [$data['Domaine'], $data['Domaine']]);
            if (!$domain) continue;

            Question::create([
                'domain_id' => $domain['id'],
                'title' => $data['Question'] ?? '',
                'title_fr' => $data['Question'] ?? '',
                'description' => $data['Description'] ?? '',
                'description_fr' => $data['Description'] ?? '',
                'weight' => (float)($data['Poids'] ?? 1),
                'sort_order' => (int)($data['Ordre'] ?? 0),
                'is_active' => 1,
            ]);
            $count++;
        }
        fclose($file);
        $_SESSION['success'] = "$count questions importées.";
        redirect('/admin/questions');
    }

    public function questionExport(): void
    {
        Auth::requireAuth();
        $export = new ExportService();
        $csv = $export->exportQuestionsToCsv();
        $export->downloadCsv($csv, 'questions_aqmi_' . date('Y-m-d') . '.csv');
    }

    // === DOMAINS ===
    public function domains(): void
    {
        Auth::requireAuth();
        $domains = Domain::all();
        view('admin.domains.index', compact('domains'));
    }

    public function domainForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $domain = $id ? Domain::find($id) : null;
        view('admin.domains.form', compact('domain'));
    }

    public function domainSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'name_fr' => $_POST['name_fr'] ?? '',
            'name_ar' => $_POST['name_ar'] ?? '',
            'description' => $_POST['description'] ?? '',
            'description_fr' => $_POST['description_fr'] ?? '',
            'description_ar' => $_POST['description_ar'] ?? '',
            'icon' => $_POST['icon'] ?? 'fa-folder',
            'weight' => (float)($_POST['weight'] ?? 1),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { Domain::update($id, $data); } else { Domain::create($data); }
        $_SESSION['success'] = 'Domaine enregistré.';
        redirect('/admin/domains');
    }

    public function domainDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) Domain::delete($id);
        $_SESSION['success'] = 'Domaine supprimé.';
        redirect('/admin/domains');
    }

    // === SCORE LEVELS ===
    public function scoreLevels(): void
    {
        Auth::requireAuth();
        $levels = Database::fetchAll("SELECT * FROM score_levels ORDER BY sort_order");
        view('admin.score-levels.index', compact('levels'));
    }

    public function scoreLevelForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $level = $id ? ScoreLevel::find($id) : null;
        view('admin.score-levels.form', compact('level'));
    }

    public function scoreLevelSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'name_fr' => $_POST['name_fr'] ?? '',
            'name_ar' => $_POST['name_ar'] ?? '',
            'min_percent' => (float)($_POST['min_percent'] ?? 0),
            'max_percent' => (float)($_POST['max_percent'] ?? 100),
            'color' => $_POST['color'] ?? '#6c757d',
            'icon' => $_POST['icon'] ?? 'fa-chart-bar',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { ScoreLevel::update($id, $data); } else { ScoreLevel::create($data); }
        $_SESSION['success'] = 'Niveau de score enregistré.';
        redirect('/admin/score-levels');
    }

    public function scoreLevelDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) ScoreLevel::delete($id);
        $_SESSION['success'] = 'Niveau supprimé.';
        redirect('/admin/score-levels');
    }

    // === RECOMMENDATIONS ===
    public function recommendations(): void
    {
        Auth::requireAuth();
        $recommendations = Database::fetchAll(
            "SELECT r.*, d.name_fr as domain_name_fr, d.name as domain_name
             FROM recommendations r LEFT JOIN domains d ON r.domain_id = d.id ORDER BY FIELD(r.priority,'critical','high','medium','low'), r.id"
        );
        $domains = Domain::all();
        view('admin.recommendations.index', compact('recommendations', 'domains'));
    }

    public function recommendationForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $recommendation = $id ? Recommendation::find($id) : null;
        $domains = Domain::all();
        view('admin.recommendations.form', compact('recommendation', 'domains'));
    }

    public function recommendationSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'domain_id' => !empty($_POST['domain_id']) ? (int)$_POST['domain_id'] : null,
            'condition_field' => $_POST['condition_field'] ?? 'global_score',
            'condition_operator' => $_POST['condition_operator'] ?? '<',
            'condition_value' => !empty($_POST['condition_value']) ? (float)$_POST['condition_value'] : null,
            'recommendation_text' => $_POST['recommendation_text'] ?? '',
            'recommendation_text_fr' => $_POST['recommendation_text_fr'] ?? '',
            'recommendation_text_ar' => $_POST['recommendation_text_ar'] ?? '',
            'priority' => $_POST['priority'] ?? 'medium',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { Recommendation::update($id, $data); } else { Recommendation::create($data); }
        $_SESSION['success'] = 'Recommandation enregistrée.';
        redirect('/admin/recommendations');
    }

    public function recommendationDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) Recommendation::delete($id);
        $_SESSION['success'] = 'Recommandation supprimée.';
        redirect('/admin/recommendations');
    }

    // === LEADS ===
    public function leads(): void
    {
        Auth::requireAuth();
        $sector = $_GET['sector'] ?? '';
        $country = $_GET['country'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];
        if ($sector) { $where[] = "l.sector = ?"; $params[] = $sector; }
        if ($country) { $where[] = "l.country = ?"; $params[] = $country; }
        if ($search) { $where[] = "(l.firstname LIKE ? OR l.lastname LIKE ? OR l.company LIKE ? OR l.email LIKE ?)"; $s = "%$search%"; $params = array_merge($params, [$s, $s, $s, $s]); }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int)Database::fetch("SELECT COUNT(*) as total FROM leads l $whereClause", $params)['total'];
        $leads = Database::fetchAll(
            "SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id $whereClause ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );
        $sectors = Database::fetchAll("SELECT DISTINCT sector FROM leads WHERE sector IS NOT NULL AND sector != '' ORDER BY sector");
        $countries = Database::fetchAll("SELECT DISTINCT country FROM leads WHERE country IS NOT NULL AND country != '' ORDER BY country");

        view('admin.leads.index', compact('leads', 'sectors', 'countries', 'sector', 'country', 'search', 'page', 'total', 'perPage'));
    }

    public function leadExport(): void
    {
        Auth::requireAuth();
        $export = new ExportService();
        $csv = $export->exportLeadsToCsv();
        $export->downloadCsv($csv, 'leads_aqmi_' . date('Y-m-d') . '.csv');
    }

    public function leadDetail(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $lead = Lead::find($id);
        if (!$lead) { http_response_code(404); echo 'Lead not found'; return; }
        $assessment = $lead['assessment_id'] ? Assessment::find($lead['assessment_id']) : null;
        $customFields = \App\Models\LeadCustomField::getValues($id);
        $documents = Database::fetchAll("SELECT * FROM lead_documents WHERE lead_id = ?", [$id]);
        view('admin.leads.detail', compact('lead', 'assessment', 'customFields', 'documents'));
    }

    public function leadSaveFields(array $params): void
    {
        Auth::requireAuth();
        $leadId = (int)($params['id'] ?? 0);
        $lead = Lead::find($leadId);
        if (!$lead) { $_SESSION['error'] = 'Lead introuvable.'; redirect('/admin/leads'); return; }

        // Update lead main fields
        $leadData = [];
        foreach (['company_size', 'website', 'certifications', 'founded_year', 'production_type', 'notes'] as $f) {
            if (isset($_POST[$f])) $leadData[$f] = $_POST[$f];
        }
        if (!empty($leadData)) Lead::update($leadId, $leadData);

        // Save custom field values
        if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
            \App\Models\LeadCustomField::saveValues($leadId, $_POST['custom_fields']);
        }

        // Handle file uploads
        if (!empty($_FILES['documents'])) {
            $uploadDir = BASE_PATH . '/storage/leads/' . $leadId;
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            foreach ($_FILES['documents']['tmp_name'] as $i => $tmp) {
                if ($tmp && $_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['documents']['name'][$i], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    move_uploaded_file($tmp, "$uploadDir/$filename");
                    Database::insert(
                        "INSERT INTO lead_documents (lead_id, field_id, filename, original_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?)",
                        [$leadId, $_POST['doc_field_id'] ?? null, $filename, $_FILES['documents']['name'][$i], $_FILES['documents']['size'][$i], $_FILES['documents']['type'][$i]]
                    );
                }
            }
        }

        $_SESSION['success'] = 'Informations du lead mises à jour.';
        redirect('/admin/leads/detail/' . $leadId);
    }

    public function leadDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) Lead::delete($id);
        $_SESSION['success'] = 'Lead supprimé.';
        redirect('/admin/leads');
    }

    // === EVALUATION MODELS ===
    public function evaluationModels(): void
    {
        Auth::requireAuth();
        $models = EvaluationModel::all();
        foreach ($models as &$m) {
            $m['domains_count'] = count(EvaluationModel::getDomains($m['id']));
            $m['questions_count'] = EvaluationModel::getQuestionsCount($m['id']);
        }
        view('admin.evaluation-models.index', compact('models'));
    }

    public function evaluationModelForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $model = $id ? EvaluationModel::find($id) : null;
        $allDomains = Domain::all();
        $selectedDomains = $model ? EvaluationModel::getDomains($model['id']) : [];
        $selectedDomainIds = array_map(fn($d) => $d['id'], $selectedDomains);
        view('admin.evaluation-models.form', compact('model', 'allDomains', 'selectedDomainIds'));
    }

    public function evaluationModelSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'name_fr' => $_POST['name_fr'] ?? '',
            'name_ar' => $_POST['name_ar'] ?? '',
            'description' => $_POST['description'] ?? '',
            'description_fr' => $_POST['description_fr'] ?? '',
            'description_ar' => $_POST['description_ar'] ?? '',
            'icon' => $_POST['icon'] ?? 'fa-clipboard-check',
            'color' => $_POST['color'] ?? '#1F6FEB',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { EvaluationModel::update($id, $data); } else { $id = EvaluationModel::create($data); }
        // Save domain associations
        $domainIds = $_POST['domain_ids'] ?? [];
        EvaluationModel::setDomains($id, $domainIds);
        $_SESSION['success'] = 'Modèle d\'évaluation enregistré.';
        redirect('/admin/evaluation-models');
    }

    public function evaluationModelDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) EvaluationModel::delete($id);
        $_SESSION['success'] = 'Modèle supprimé.';
        redirect('/admin/evaluation-models');
    }

    public function evaluationModelDomainsSave(): void
    {
        Auth::requireAuth();
        $modelId = (int)($_POST['model_id'] ?? 0);
        $domainIds = $_POST['domain_ids'] ?? [];
        EvaluationModel::setDomains($modelId, $domainIds);
        jsonResponse(['success' => true]);
    }

    // === LEAD CUSTOM FIELDS ===
    public function leadFields(): void
    {
        Auth::requireAuth();
        $fields = LeadCustomField::all();
        view('admin.lead-fields.index', compact('fields'));
    }

    public function leadFieldForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $field = $id ? LeadCustomField::find($id) : null;
        view('admin.lead-fields.form', compact('field'));
    }

    public function leadFieldSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $options = $_POST['options'] ?? [];
        // Encode options array to JSON for select/multiselect types
        $optionsJson = null;
        if (!empty($options) && is_array($options)) {
            $parsed = [];
            foreach ($options as $opt) {
                if (!empty($opt['value']) && !empty($opt['label'])) {
                    $parsed[] = ['value' => $opt['value'], 'label' => $opt['label']];
                }
            }
            if (!empty($parsed)) $optionsJson = json_encode($parsed);
        }
        $data = [
            'label' => $_POST['label'] ?? '',
            'label_fr' => $_POST['label_fr'] ?? '',
            'label_ar' => $_POST['label_ar'] ?? '',
            'field_type' => $_POST['field_type'] ?? 'text',
            'options' => $optionsJson,
            'placeholder' => $_POST['placeholder'] ?? '',
            'placeholder_fr' => $_POST['placeholder_fr'] ?? '',
            'is_required' => isset($_POST['is_required']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'section' => $_POST['section'] ?? 'general',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) { LeadCustomField::update($id, $data); } else { LeadCustomField::create($data); }
        $_SESSION['success'] = 'Champ personnalisé enregistré.';
        redirect('/admin/lead-fields');
    }

    public function leadFieldDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) LeadCustomField::delete($id);
        $_SESSION['success'] = 'Champ supprimé.';
        redirect('/admin/lead-fields');
    }

    // === REPORTS ===
    public function reports(): void
    {
        Auth::requireAuth();
        $reports = Report::all();
        view('admin.reports.index', compact('reports'));
    }

    public function reportValidate(array $params): void
    {
        // NOTE: méthode conservée pour compatibilité de route (ne jamais
        // supprimer une route existante), mais le workflow de validation
        // passe maintenant par reportApprove() + reportCertify() depuis la
        // page de dossier (/admin/reports/{id}). 'validated' n'existe plus
        // dans les statuts valides ; on route donc vers 'certified' pour
        // éviter une exception si cette route legacy est encore appelée.
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = Report::find($id);
        if (!$report) { $_SESSION['error'] = 'Rapport introuvable.'; redirect('/admin/reports'); return; }

        $assessmentId = $report['assessment_id'];
        $pdfService = new \App\Services\PdfService();
        try {
            $adminName = trim((Auth::user()['firstname'] ?? '') . ' ' . (Auth::user()['lastname'] ?? 'Admin'));
            Report::assignReportNumber($id);
            $filename = $pdfService->generateCertificate($id);
            Report::updateStatus($id, 'certified', $adminName, $filename);
            $_SESSION['success'] = 'Rapport certifié avec succès.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur de génération PDF: ' . $e->getMessage();
        }
        redirect('/admin/reports');
    }

    public function reportReject(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = Report::find($id);
        if (!$report) { $_SESSION['error'] = 'Rapport introuvable.'; redirect('/admin/reports'); return; }

        Report::saveAdminReview($id, ['admin_comment' => $_POST['admin_comment'] ?? $report['admin_comment']]);
        Report::updateStatus($id, 'rejected');
        $_SESSION['success'] = 'Demande rejetée.';
        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/reports');
    }

    /**
     * Dossier complet d'une demande de certification :
     * rapport, évaluation, lead, réponses détaillées, scores,
     * recommandations, champs personnalisés.
     */
    public function reportDetail(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = Report::find($id);
        if (!$report) { $_SESSION['error'] = 'Dossier introuvable.'; redirect('/admin/reports'); return; }

        // Ouvrir le dossier d'une demande fraîche la fait passer "en cours d'examen"
        Report::markUnderReviewIfNeeded($id);
        $report = Report::find($id);

        $assessment = Assessment::find($report['assessment_id']);
        $lead = $assessment ? Lead::findByAssessment($assessment['id']) : null;
        $customFields = $lead ? LeadCustomField::getValues($lead['id']) : [];

        $scoringService = new \App\Services\ScoringService();
        $analysis = $assessment ? $scoringService->analyzeAssessment($assessment['id']) : null;
        $recommendations = $assessment ? (new \App\Services\RecommendationService())->generate($assessment['id']) : [];

        $answers = $assessment ? Database::fetchAll(
            "SELECT aa.score, q.title, q.title_fr, d.name as domain_name, d.name_fr as domain_name_fr
             FROM assessment_answers aa
             JOIN questions q ON aa.question_id = q.id
             JOIN domains d ON q.domain_id = d.id
             WHERE aa.assessment_id = ?
             ORDER BY d.sort_order, q.sort_order",
            [$assessment['id']]
        ) : [];

        view('admin.reports.detail', compact('report', 'assessment', 'lead', 'customFields', 'analysis', 'recommendations', 'answers'));
    }

    public function reportStartReview(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        Report::markUnderReviewIfNeeded($id);
        $_SESSION['success'] = 'Dossier passé en cours d\'examen.';
        redirect('/admin/reports/' . $id);
    }

    public function reportApprove(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = Report::find($id);
        if (!$report) { $_SESSION['error'] = 'Dossier introuvable.'; redirect('/admin/reports'); return; }

        Report::saveAdminReview($id, [
            'admin_comment' => $_POST['admin_comment'] ?? null,
            'observations' => $_POST['observations'] ?? null,
            'action_plan' => $_POST['action_plan'] ?? null,
            'aqmi_level_assigned' => $_POST['aqmi_level_assigned'] ?? null,
        ]);
        Report::updateStatus($id, 'approved');
        $_SESSION['success'] = 'Dossier approuvé. Vous pouvez maintenant le certifier.';
        redirect('/admin/reports/' . $id);
    }

    public function reportCertify(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = Report::find($id);
        if (!$report) { $_SESSION['error'] = 'Dossier introuvable.'; redirect('/admin/reports'); return; }
        if ($report['status'] !== 'approved') {
            $_SESSION['error'] = 'Le dossier doit être approuvé avant certification.';
            redirect('/admin/reports/' . $id);
            return;
        }

        $templateId = !empty($_POST['template_id']) ? (int)$_POST['template_id'] : null;
        Report::saveAdminReview($id, [
            'admin_comment' => $_POST['admin_comment'] ?? $report['admin_comment'],
            'observations' => $_POST['observations'] ?? $report['observations'],
            'action_plan' => $_POST['action_plan'] ?? $report['action_plan'],
            'aqmi_level_assigned' => $_POST['aqmi_level_assigned'] ?? $report['aqmi_level_assigned'],
        ]);
        Report::setTemplateId($id, $templateId);

        $reportNumber = Report::assignReportNumber($id);
        $adminName = trim((Auth::user()['firstname'] ?? '') . ' ' . (Auth::user()['lastname'] ?? 'Admin'));
        Report::setSignature($id, $adminName);

        try {
            $pdfService = new \App\Services\PdfService();
            $filename = $pdfService->generateCertificate($id, $templateId);
            Report::updateStatus($id, 'certified', $adminName, $filename);
            $_SESSION['success'] = "Rapport certifié sous le numéro {$reportNumber}.";
        } catch (\Exception $e) {
            Report::updateStatus($id, 'certified', $adminName);
            $_SESSION['error'] = "Certifié (n° {$reportNumber}) mais la génération du PDF a échoué : " . $e->getMessage();
        }

        redirect('/admin/reports/' . $id);
    }

    // === USERS ===
    public function users(): void
    {
        Auth::requireAuth();
        $users = Database::fetchAll(
            "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC"
        );
        view('admin.users.index', compact('users'));
    }

    public function userForm(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $user = $id ? User::find($id) : null;
        $roles = Database::fetchAll("SELECT * FROM roles ORDER BY id");
        view('admin.users.form', compact('user', 'roles'));
    }

    public function userSave(): void
    {
        Auth::requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'role_id' => (int)($_POST['role_id'] ?? 0),
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        if ($id > 0) {
            User::update($id, $data);
        } else {
            $data['password'] = password_hash($_POST['password'] ?? 'password', PASSWORD_BCRYPT);
            User::create($data);
        }
        $_SESSION['success'] = 'Utilisateur enregistré.';
        redirect('/admin/users');
    }

    public function userDelete(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        if ($id > 0) User::delete($id);
        $_SESSION['success'] = 'Utilisateur supprimé.';
        redirect('/admin/users');
    }

    // === SETTINGS ===
    public function settings(): void
    {
        Auth::requireAuth();
        $settings = Database::fetchAll("SELECT * FROM settings ORDER BY setting_key");
        view('admin.settings.index', compact('settings'));
    }

    public function settingsSave(): void
    {
        Auth::requireAuth();
        foreach ($_POST['settings'] ?? [] as $key => $value) {
            $existing = Database::fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            if ($existing) {
                Database::execute("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
            } else {
                Database::insert("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            }
        }
        $_SESSION['success'] = 'Paramètres enregistrés.';
        redirect('/admin/settings');
    }

    // === CONSOLIDATED REPORTS ===
    public function consolidatedReports(): void
    {
        Auth::requireAuth();
        $reports = \App\Models\ConsolidatedReport::pendingCertifications();
        $certified = Database::fetchAll(
            "SELECT cr.*, u.firstname, u.lastname, u.email
             FROM consolidated_reports cr
             JOIN users u ON cr.user_id = u.id
             WHERE cr.status = 'certified'
             ORDER BY cr.certified_at DESC"
        );
        view('admin.consolidated.index', compact('reports', 'certified'));
    }

    public function consolidatedDetail(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report) { $_SESSION['error'] = 'Rapport introuvable.'; redirect('/admin/consolidated'); return; }

        \App\Models\ConsolidatedReport::markUnderReviewIfNeeded($id);
        $report = \App\Models\ConsolidatedReport::find($id);
        $items = \App\Models\ConsolidatedReport::getItems($id);
        $user = Database::fetch("SELECT * FROM users WHERE id = ?", [$report['user_id']]);

        view('admin.consolidated.detail', compact('report', 'items', 'user'));
    }

    public function consolidatedReview(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report) { redirect('/admin/consolidated'); return; }

        \App\Models\ConsolidatedReport::saveAdminReview($id, [
            'admin_comment' => $_POST['admin_comment'] ?? null,
            'observations' => $_POST['observations'] ?? null,
            'action_plan' => $_POST['action_plan'] ?? null,
            'aqmi_level_assigned' => $_POST['aqmi_level_assigned'] ?? null,
        ]);
        $_SESSION['success'] = 'Analyse enregistrée.';
        redirect('/admin/consolidated/' . $id);
    }

    public function consolidatedApprove(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report) { redirect('/admin/consolidated'); return; }

        \App\Models\ConsolidatedReport::updateStatus($id, 'approved');
        $_SESSION['success'] = 'Rapport consolidé approuvé.';
        redirect('/admin/consolidated/' . $id);
    }

    public function consolidatedCertify(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report) { redirect('/admin/consolidated'); return; }

        $admin = Auth::user();
        \App\Models\ConsolidatedReport::updateStatus($id, 'certified', $admin['firstname'] . ' ' . $admin['lastname']);
        \App\Models\ConsolidatedReport::assignReportNumber($id);

        $_SESSION['success'] = 'Rapport consolidé certifié. Numéro: ' . \App\Models\ConsolidatedReport::assignReportNumber($id);
        redirect('/admin/consolidated/' . $id);
    }

    public function consolidatedReject(array $params): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $report = \App\Models\ConsolidatedReport::find($id);
        if (!$report) { redirect('/admin/consolidated'); return; }

        \App\Models\ConsolidatedReport::saveAdminReview($id, [
            'admin_comment' => $_POST['admin_comment'] ?? 'Demande rejetée.',
        ]);
        \App\Models\ConsolidatedReport::updateStatus($id, 'rejected');
        $_SESSION['success'] = 'Demande rejetée.';
        redirect('/admin/consolidated/' . $id);
    }
}