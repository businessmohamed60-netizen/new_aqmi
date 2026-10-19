<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Security;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\NotificationService;

class ChatController
{
    private NotificationService $notifService;

    public function __construct()
    {
        $this->notifService = new NotificationService();
    }

    public function userMessages(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Accès non autorisé.';
            redirect('/');
            return;
        }

        $userId = Auth::id();
        $conversation = ChatConversation::findByUserId($userId);
        $messages = [];
        if ($conversation) {
            $messages = ChatMessage::findByConversation((int)$conversation['id']);
            ChatMessage::markConversationReadByUser((int)$conversation['id']);
        }

        $title = 'Messagerie';
        $activeSection = 'messages';
        $pageTitle = 'Messagerie';
        $pageSubtitle = 'Communiquez avec l\'quipe NOVAQYS';
        ob_start();
        require BASE_PATH . '/resources/views/user/messages.php';
        $content = ob_get_clean();
        require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
    }

    public function userSend(): void
    {
        if (!Auth::check()) { redirect('/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/user/messages'); return; }

        if (!Security::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            redirect('/user/messages');
            return;
        }

        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            redirect('/');
            return;
        }

        $userId = Auth::id();
        $body = trim($_POST['body'] ?? '');
        if ($body === '') {
            $_SESSION['error'] = 'Le message ne peut pas être vide.';
            redirect('/user/messages');
            return;
        }
        if (mb_strlen($body) > 5000) {
            $_SESSION['error'] = 'Le message est trop long (max 5000 caractères).';
            redirect('/user/messages');
            return;
        }

        $conversation = ChatConversation::findByUserId($userId);
        if (!$conversation) {
            $convId = ChatConversation::create([
                'user_id' => $userId,
                'admin_id' => null,
                'subject' => 'Conversation avec ' . trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')),
            ]);
            $conversation = ChatConversation::find($convId);
        }

        ChatMessage::create([
            'conversation_id' => (int)$conversation['id'],
            'sender_id' => $userId,
            'sender_type' => 'user',
            'body' => $body,
        ]);
        ChatConversation::touch((int)$conversation['id']);

        $admins = Database::fetchAll(
            "SELECT u.id, u.firstname, u.lastname FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug IN ('super_admin','admin') AND u.is_active = 1"
        );
        $senderName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        foreach ($admins as $admin) {
            $this->notifService->sendChatNotification(
                (int)$admin['id'],
                $senderName,
                $body,
                (int)$conversation['id']
            );
        }

        redirect('/user/messages');
    }

    public function adminChatList(): void
    {
        Auth::requireAdmin();
        $conversations = ChatConversation::all();
        $unreadCount = ChatConversation::unreadCountForAdmin();

        $title = 'Messagerie';
        view('admin.chat.index', compact('conversations', 'unreadCount', 'title'));
    }

    public function adminChatView(array $params = []): void
    {
        Auth::requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $conversation = ChatConversation::find($id);
        if (!$conversation) {
            $_SESSION['error'] = 'Conversation introuvable.';
            redirect('/admin/chat');
            return;
        }

        $messages = ChatMessage::findByConversation($id);
        ChatMessage::markConversationReadByAdmin($id);

        if (!$conversation['admin_id']) {
            Database::execute("UPDATE chat_conversations SET admin_id = ? WHERE id = ?", [Auth::id(), $id]);
            $conversation['admin_id'] = Auth::id();
        }

        $title = 'Messagerie - ' . trim(($conversation['firstname'] ?? '') . ' ' . ($conversation['lastname'] ?? ''));
        view('admin.chat.view', compact('conversation', 'messages', 'title'));
    }

    public function adminSend(array $params = []): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/chat'); return; }

        if (!Security::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            redirect('/admin/chat');
            return;
        }

        $id = (int)($params['id'] ?? 0);
        $conversation = ChatConversation::find($id);
        if (!$conversation) {
            $_SESSION['error'] = 'Conversation introuvable.';
            redirect('/admin/chat');
            return;
        }

        $body = trim($_POST['body'] ?? '');
        if ($body === '') {
            $_SESSION['error'] = 'Le message ne peut pas être vide.';
            redirect('/admin/chat/' . $id);
            return;
        }
        if (mb_strlen($body) > 5000) {
            $_SESSION['error'] = 'Le message est trop long (max 5000 caractères).';
            redirect('/admin/chat/' . $id);
            return;
        }

        $admin = Auth::user();
        ChatMessage::create([
            'conversation_id' => $id,
            'sender_id' => Auth::id(),
            'sender_type' => 'admin',
            'body' => $body,
        ]);
        ChatConversation::touch($id);

        $adminName = trim(($admin['firstname'] ?? 'Admin') . ' ' . ($admin['lastname'] ?? ''));
        $this->notifService->sendChatNotification(
            (int)$conversation['user_id'],
            $adminName,
            $body,
            $id
        );

        redirect('/admin/chat/' . $id);
    }

    public function pollNewMessages(): void
    {
        if (!Auth::check()) { jsonResponse(['messages' => []]); return; }
        $user = Auth::user();
        $conversationId = (int)($_GET['conversation_id'] ?? 0);
        $lastId = (int)($_GET['last_id'] ?? 0);

        if ($user['role_slug'] === 'client') {
            $conv = ChatConversation::findByUserId(Auth::id());
            if (!$conv || (int)$conv['id'] !== $conversationId) {
                jsonResponse(['messages' => []]);
                return;
            }
            $messages = Database::fetchAll(
                "SELECT m.*, u.firstname, u.lastname FROM chat_messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? AND m.id > ? ORDER BY m.created_at ASC",
                [$conversationId, $lastId]
            );
            ChatMessage::markConversationReadByUser($conversationId);
        } else {
            $messages = Database::fetchAll(
                "SELECT m.*, u.firstname, u.lastname FROM chat_messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? AND m.id > ? ORDER BY m.created_at ASC",
                [$conversationId, $lastId]
            );
            ChatMessage::markConversationReadByAdmin($conversationId);
        }

        jsonResponse(['messages' => $messages]);
    }
}
