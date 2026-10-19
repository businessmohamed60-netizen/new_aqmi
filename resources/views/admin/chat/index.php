<?php
$title = 'Messagerie';
$content = null;
ob_start();
?>
<div class="nova-card">
  <div class="nova-card-header">
    <h3><i class="fas fa-comments"></i> Conversations</h3>
    <?php if ($unreadCount > 0): ?>
      <span class="badge bg-danger"><?= $unreadCount ?> non lu(s)</span>
    <?php endif; ?>
  </div>
  <div class="nova-card-body">
    <?php if (empty($conversations)): ?>
      <div style="text-align:center;padding:3rem 1rem;color:var(--vx-text-muted);">
        <i class="fas fa-inbox" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;"></i>
        <p>Aucune conversation pour le moment.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Utilisateur</th>
              <th>Entreprise</th>
              <th>Sujet</th>
              <th>Dernier message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($conversations as $conv): ?>
              <?php
                $lastMsg = \App\Models\ChatMessage::getLastMessage((int)$conv['id']);
                $hasUnread = \App\Helpers\Database::fetch(
                  "SELECT COUNT(*) as c FROM chat_messages WHERE conversation_id = ? AND sender_type = 'user' AND is_read = 0",
                  [(int)$conv['id']]
                );
                $unread = $hasUnread ? (int)$hasUnread['c'] : 0;
              ?>
              <tr>
                <td>
                  <a href="/admin/chat/<?= (int)$conv['id'] ?>" style="text-decoration:none;color:inherit;font-weight:<?= $unread > 0 ? '700' : '400' ?>;">
                    <?= e(($conv['firstname'] ?? '') . ' ' . ($conv['lastname'] ?? '')) ?>
                    <?php if ($unread > 0): ?><span class="badge bg-primary ms-1"><?= $unread ?></span><?php endif; ?>
                  </a>
                </td>
                <td><?= e($conv['company'] ?? 'N/A') ?></td>
                <td><?= e($conv['subject'] ?? 'N/A') ?></td>
                <td style="font-size:0.8rem;color:var(--vx-text-muted);">
                  <?= $lastMsg ? e(formatDate($lastMsg['created_at'], 'd/m/Y H:i')) : e(formatDate($conv['created_at'], 'd/m/Y H:i')) ?>
                </td>
                <td>
                  <a href="/admin/chat/<?= (int)$conv['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-reply"></i> Ouvrir
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
