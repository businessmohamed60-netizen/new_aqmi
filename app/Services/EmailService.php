<?php
namespace App\Services;

use App\Helpers\Database;
use App\Helpers\Mailer;

/**
 * EmailService — Notifications email
 * Utilise Mailer qui fonctionne avec ou sans PHPMailer
 */
class EmailService
{
    private array $config;
    public function __construct() { $this->config = require BASE_PATH . '/app/Config/mail.php'; }

    public function sendReportToLead(string $email, string $firstname, int $assessmentId, string $reportFile): bool
    {
        $subject = 'Votre Rapport AQMI - Évaluation de Maturité';
        $body = "<html><body style='font-family:Arial,sans-serif;'><h2 style='color:#1a56db;'>Rapport AQMI</h2>
        <p>Bonjour {$firstname},</p><p>Veuillez trouver ci-joint votre rapport d'évaluation AQMI.</p>
        <br><p>Cordialement,<br><strong>L'équipe AQMI</strong></p></body></html>";

        if ($reportFile && file_exists(BASE_PATH . '/storage/reports/' . $reportFile)) {
            $body .= "<p><em>Note: Le rapport PDF est disponible dans votre espace AQMI.</em></p>";
        }

        return Mailer::send($email, $subject, $body);
    }

    public function sendAdminNotification(string $leadName, string $company): bool
    {
        $adminEmail = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = 'admin_email'");
        $to = $adminEmail['setting_value'] ?? $this->config['from_address'];
        $subject = 'Nouveau Lead AQMI';
        $body = "<html><body><h2>Nouveau Lead</h2><p><strong>Nom:</strong> {$leadName}</p><p><strong>Entreprise:</strong> {$company}</p></body></html>";
        return Mailer::send($to, $subject, $body);
    }

    public function sendAdminReportRequest(string $leadName, string $company, int $assessmentId, string $reportUrl): bool
    {
        $adminEmail = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = 'admin_email'");
        $to = $adminEmail['setting_value'] ?? $this->config['from_address'];
        $subject = 'Demande de validation de rapport - ' . $company;
        $body = "<html><body style='font-family:Arial,sans-serif;'>
            <h2 style='color:#1a56db;'>Demande de validation de rapport</h2>
            <p><strong>Entreprise:</strong> {$company}</p>
            <p><strong>Contact:</strong> {$leadName}</p>
            <p><strong>Assessment #:</strong> {$assessmentId}</p>
            <p>Un utilisateur a terminé son évaluation et demande la validation de son rapport.</p>
            <p><a href='{$reportUrl}' style='display:inline-block;padding:10px 20px;background:#1a56db;color:#fff;text-decoration:none;border-radius:6px;'>Valider le rapport</a></p>
            <br><p>Cordialement,<br><strong>Système AQMI</strong></p>
        </body></html>";
        return Mailer::send($to, $subject, $body);
    }
}
