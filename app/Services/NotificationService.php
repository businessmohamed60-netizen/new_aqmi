<?php
namespace App\Services;

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
            "SELECT id FROM users WHERE is_active = 1 AND role_id NOT IN (SELECT id FROM roles WHERE slug IN ('super_admin','admin'))"
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
