<?php
namespace App\Controllers;

use App\Models\Assessment;
use App\Models\Lead;
use App\Models\Report;
use App\Services\PdfService;

class ReportController
{
    public function download(array $params): void
    {
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        // Check report validation status
        $report = Report::findByAssessment($assessmentId);
        if (!$report || $report['status'] !== 'validated') {
            $_SESSION['error'] = 'Le rapport doit être validé par un administrateur avant téléchargement.';
            back();
            return;
        }

        $pdfService = new PdfService();
        try {
            $filename = $pdfService->generate($assessmentId);
            $filePath = BASE_PATH . '/storage/reports/' . $filename;

            if (file_exists($filePath)) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if ($ext === 'pdf') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                } else {
                    header('Content-Type: text/html; charset=UTF-8');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                }
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }
            $_SESSION['error'] = 'Erreur lors de la génération du rapport.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
        }
        back();
    }
}