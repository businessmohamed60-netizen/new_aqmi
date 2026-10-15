<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Security;
use App\Models\Notification;
use App\Services\NotificationService;

class NotificationController
{
    private NotificationService $notifService;

    public function __construct()
    {
        $this->notifService = new NotificationService();
    }

    public function poll(): void
    {
        if (!Auth::check()) { jsonResponse(['notifications' => [], 'unread' => 0]); return; }

        $userId = Auth::id();
        $notifications = Notification::findByUserId($userId, 10);
        $unread = Notification::unreadCount($userId);

        $html = '';
        ob_start();
        foreach ($notifications as $notif): ?>
          <a href="<?= e($notif['link'] ?? '#') ?>" class="notif-dropdown-item <?= $notif['is_read'] ? '' : 'unread' ?>" data-id="<?= (int)$notif['id'] ?>">
            <div class="notif-dot <?= $notif['is_read'] ? '' : 'active' ?>"></div>
            <div class="notif-content">
              <div class="notif-title"><?= e($notif['title']) ?></div>
              <div class="notif-body"><?= e(truncate($notif['body'] ?? '', 80)) ?></div>
              <div class="notif-time"><?= e(formatDate($notif['created_at'], 'd/m/Y H:i')) ?></div>
            </div>
          </a>
        <?php endforeach;
        $html = ob_get_clean();

        jsonResponse([
            'notifications' => $notifications,
            'unread' => $unread,
            'html' => $html,
        ]);
    }

    public function markRead(): void
    {
        if (!Auth::check()) { jsonResponse(['success' => false]); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(['success' => false]); return; }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Notification::markRead($id);
        }
        jsonResponse(['success' => true]);
    }

    public function markAllRead(): void
    {
        if (!Auth::check()) { jsonResponse(['success' => false]); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(['success' => false]); return; }

        Notification::markAllRead(Auth::id());
        jsonResponse(['success' => true]);
    }

    public function adminPushForm(): void
    {
        Auth::requireAdmin();
        $title = 'Notifications Push';
        view('admin.notifications.push', compact('title'));
    }

    public function adminPushSend(): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/notifications'); return; }

        if (!Security::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            redirect('/admin/notifications');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $target = $_POST['target'] ?? 'all';
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($title === '' || $body === '') {
            $_SESSION['error'] = 'Le titre et le message sont obligatoires.';
            redirect('/admin/notifications');
            return;
        }

        if ($target === 'all') {
            $count = $this->notifService->sendToAllUsers($title, $body, 'admin_push');
            $_SESSION['success'] = "Notification envoye  {$count} utilisateur(s). Copie email envoye  ceux qui ont l'option active.";
        } else {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                $_SESSION['error'] = 'Utilisateur introuvable.';
                redirect('/admin/notifications');
                return;
            }
            $this->notifService->sendToUser($userId, $title, $body, 'admin_push');
            $_SESSION['success'] = 'Notification envoye  l\'utilisateur. Copie email envoye si l\'option est active.';
        }

        redirect('/admin/notifications');
    }
}
