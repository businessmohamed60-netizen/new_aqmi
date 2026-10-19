<?php
namespace App\Services;

use App\Helpers\Database;

class ExportService
{
    public function exportQuestionsToCsv(): string
    {
        $data = Database::fetchAll(
            "SELECT d.name_fr as Domaine, q.title_fr as Question, q.description_fr as Description,
                    q.question_type as Type, q.weight as Poids, q.sort_order as Ordre,
                    q.is_required as Requis, q.help_text_fr as Aide,
                    em.name as Modèle, q.options as Options, q.is_active as Actif
             FROM questions q
             JOIN domains d ON q.domain_id = d.id
             LEFT JOIN evaluation_models em ON q.model_id = em.id
             ORDER BY d.sort_order, q.sort_order"
        );
        return $this->toCsv($data);
    }

    public function exportAssessmentsToCsv(): string
    {
        $data = Database::fetchAll(
            "SELECT a.id, a.total_score, a.maturity_level, a.completed_at,
                    l.company, l.firstname, l.lastname, l.email, l.phone, l.sector, l.country
             FROM assessments a LEFT JOIN leads l ON a.id = l.assessment_id
             WHERE a.status = 'completed' ORDER BY a.completed_at DESC"
        );
        return $this->toCsv($data);
    }

    public function exportLeadsToCsv(): string
    {
        $data = Database::fetchAll(
            "SELECT l.*, a.total_score, a.maturity_level FROM leads l LEFT JOIN assessments a ON l.assessment_id = a.id ORDER BY l.created_at DESC"
        );
        return $this->toCsv($data);
    }

    private function toCsv(array $data): string
    {
        if (empty($data)) return '';
        $output = fopen('php://temp', 'r+');
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) fputcsv($output, $row);
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }

    public function downloadCsv(string $content, string $filename): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "\xEF\xBB\xBF";
        echo $content;
        exit;
    }
}