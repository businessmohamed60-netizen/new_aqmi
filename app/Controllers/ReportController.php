<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Assessment;
use App\Models\Lead;
use App\Models\Report;
use App\Services\PdfService;

class ReportController
{
    /**
     * Rapport officiel certifié — nécessite que l'admin ait certifié la demande.
     * Interdit à l'utilisateur de générer lui-même le PDF officiel tant que ce
     * n'est pas fait (statut `certified`).
     */
    public function download(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        // Check report validation status
        $report = Report::findByAssessment($assessmentId);
        if (!$report || $report['status'] !== 'certified') {
            $_SESSION['error'] = 'Le rapport doit être certifié par un administrateur avant téléchargement.';
            back();
            return;
        }

        $pdfService = new PdfService();
        try {
            $filename = $pdfService->generateCertificate($report['id'], (int)($report['theme_id'] ?? 0));
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
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
        }
        back();
    }

    /**
     * Page publique de vérification d'un certificat AQMI, accessible via
     * le QR code imprimé sur le PDF officiel. Ne révèle que des
     * informations non sensibles (pas les réponses, pas les coordonnées).
     */
    public function verify(array $params): void
    {
        $reportNumber = trim($params['report_number'] ?? '');
        $report = $reportNumber !== '' ? Report::findByNumber($reportNumber) : null;
        $isValid = $report !== null && $report['status'] === 'certified';

        view('public/verify', compact('report', 'reportNumber', 'isValid'));
    }

    /**
     * Résumé GRATUIT, non certifié. Toujours disponible dès que le
     * questionnaire est terminé — aucune validation admin requise.
     * Ne crée et ne touche à aucune ligne dans `reports`.
     *
     * NOTE: réutilise pour l'instant le même gabarit que le rapport officiel
     * (PdfService::generate). L'étape suivante (PDF certifié) ajoutera un
     * paramètre pour distinguer visuellement "résumé gratuit" (sans QR code,
     * signature, cachet, numéro de certification) du rapport officiel.
     */
    public function downloadSummary(array $params): void
    {
        Auth::requireAuth();
        $assessmentId = (int)($params['id'] ?? 0);
        $assessment = Assessment::find($assessmentId);
        if (!$assessment || $assessment['status'] !== 'completed') { redirect('/'); return; }

        $pdfService = new PdfService();
        try {
            $filename = $pdfService->generate($assessmentId);
            $filePath = BASE_PATH . '/storage/reports/' . $filename;

            if (file_exists($filePath)) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $downloadName = 'resume-gratuit-aqmi-' . $assessmentId . '.' . $ext;
                header('Content-Type: ' . ($ext === 'pdf' ? 'application/pdf' : 'text/html; charset=UTF-8'));
                header('Content-Disposition: attachment; filename="' . $downloadName . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }
            $_SESSION['error'] = 'Erreur lors de la génération du résumé.';
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
        }
        back();
    }
}