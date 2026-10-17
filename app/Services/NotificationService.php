<?php
namespace App\Services;

use App\Helpers\Database;
use App\Helpers\Mailer;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function sendToUser(int $userId, string $title, string $body, string $type = 'general', string $link = null): int
    {
        $notifId = Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'link'    => $link,
        ]);

        $this->sendEmailCopy($userId, $title, $body);

        return $notifId;
    }

    public function sendToAllUsers(string $title, string $body, string $type = 'general', string $link = null): int
    {
        $count = Notification::createForAllUsers([
            'type'  => $type,
            'title' => $title,
            'body'  => $body,
            'link'  => $link,
        ]);

        $users = Database::fetchAll(
            "SELECT id FROM users WHERE is_active = 1 AND (notify_push = 1 OR notify_push IS NULL) AND role_id NOT IN (SELECT id FROM roles WHERE slug IN ('super_admin','admin'))"
        );
        foreach ($users as $user) {
            $this->sendEmailCopy((int)$user['id'], $title, $body);
        }

        return $count;
    }

    public function sendChatNotification(int $recipientId, string $senderName, string $messagePreview, int $conversationId): void
    {
        $title = 'Nouveau message de ' . $senderName;
        $body  = 'Vous avez reçu un nouveau message : "' . mb_substr($messagePreview, 0, 100) . '"';
        $link  = '/user/messages';

        Notification::create([
            'user_id' => $recipientId,
            'type'    => 'chat',
            'title'   => $title,
            'body'    => $body,
            'link'    => $link,
        ]);

        $this->sendEmailCopy($recipientId, $title, $body);
    }

    /**
     * Notifie le propriétaire d'un rapport d'un changement de statut
     * (validation, certification, rejet, révocation). Respecte les
     * préférences notify_push et notify_email de l'utilisateur.
     */
    public function notifyReportStatusChange(int $reportId, string $event, array $context = []): void
    {
        $userId = \App\Models\Report::getOwnerUserId($reportId);
        if ($userId === null) return;

        $user = User::find($userId);
        if (!$user) return;

        $pushEnabled = isset($user['notify_push']) ? (int)$user['notify_push'] : 1;
        if ($pushEnabled !== 1) return;

        $reportNumber = $context['report_number'] ?? ('#' . $reportId);
        $company = $context['company'] ?? '';

        switch ($event) {
            case 'approved':
                $title = 'Rapport approuvé';
                $body  = "Votre demande de certification {$reportNumber}" . ($company ? " ({$company})" : '') . " a été approuvée par l'administrateur. Elle est maintenant éligible à la certification.";
                $link  = '/admin/reports/' . $reportId;
                $type  = 'report_approved';
                break;
            case 'certified':
                $title = 'Certificat AQMI délivré';
                $body  = "Félicitations ! Votre rapport {$reportNumber}" . ($company ? " ({$company})" : '') . " a été certifié. Le certificat est disponible.";
                $link  = '/admin/reports/' . $reportId;
                $type  = 'report_certified';
                break;
            case 'rejected':
                $reason = $context['reason'] ?? '';
                $title = 'Demande de certification rejetée';
                $body  = "Votre demande {$reportNumber}" . ($company ? " ({$company})" : '') . " a été rejetée." . ($reason ? " Motif : {$reason}" : '');
                $link  = '/admin/reports/' . $reportId;
                $type  = 'report_rejected';
                break;
            case 'revoked':
                $reason = $context['reason'] ?? '';
                $title = 'Certificat révoqué';
                $body  = "Le certificat {$reportNumber}" . ($company ? " ({$company})" : '') . " a été révoqué par l'administrateur." . ($reason ? " Motif : {$reason}" : '');
                $link  = '/admin/reports/' . $reportId;
                $type  = 'report_revoked';
                break;
            case 'reactivated':
                $title = 'Certificat réactivé';
                $body  = "Le certificat {$reportNumber}" . ($company ? " ({$company})" : '') . " a été réactivé et est de nouveau valide.";
                $link  = '/admin/reports/' . $reportId;
                $type  = 'report_reactivated';
                break;
            default:
                return;
        }

        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'link'    => $link,
        ]);

        $this->sendEmailCopy($userId, $title, $body);
    }

    /**
     * Notifie le propriétaire d'un rapport consolidé d'un changement de statut.
     */
    public function notifyConsolidatedStatusChange(int $reportId, string $event, array $context = []): void
    {
        $report = \App\Models\ConsolidatedReport::find($reportId);
        if (!$report) return;
        $userId = (int)($report['user_id'] ?? 0);
        if ($userId <= 0) return;

        $user = User::find($userId);
        if (!$user) return;

        $pushEnabled = isset($user['notify_push']) ? (int)$user['notify_push'] : 1;
        if ($pushEnabled !== 1) return;

        $reportNumber = $context['report_number'] ?? ('#' . $reportId);
        $title = $context['title'] ?? $report['title'] ?? 'Rapport consolidé';

        switch ($event) {
            case 'approved':
                $notifTitle = 'Rapport consolidé approuvé';
                $body  = "Votre rapport consolidé \"{$title}\" ({$reportNumber}) a été approuvé par l'administrateur.";
                $link  = '/admin/consolidated/' . $reportId;
                $type  = 'consolidated_approved';
                break;
            case 'certified':
                $notifTitle = 'Rapport consolidé certifié';
                $body  = "Félicitations ! Votre rapport consolidé \"{$title}\" ({$reportNumber}) a été certifié.";
                $link  = '/admin/consolidated/' . $reportId;
                $type  = 'consolidated_certified';
                break;
            case 'rejected':
                $reason = $context['reason'] ?? '';
                $notifTitle = 'Rapport consolidé rejeté';
                $body  = "Votre rapport consolidé \"{$title}\" ({$reportNumber}) a été rejeté." . ($reason ? " Motif : {$reason}" : '');
                $link  = '/admin/consolidated/' . $reportId;
                $type  = 'consolidated_rejected';
                break;
            default:
                return;
        }

        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $notifTitle,
            'body'    => $body,
            'link'    => $link,
        ]);

        $this->sendEmailCopy($userId, $notifTitle, $body);
    }

    private function sendEmailCopy(int $userId, string $title, string $body): void
    {
        $user = User::find($userId);
        if (!$user) return;

        if (!($user['notify_email'] ?? 1)) return;

        $userName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $html = Mailer::notificationTemplate($title, $body, $userName);
        Mailer::send($user['email'], $title, $html);
    }
}
