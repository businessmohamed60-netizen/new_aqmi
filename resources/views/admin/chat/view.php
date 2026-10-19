<?php
$content = null;
ob_start();
?>
<style>
.nova-chat-wrap {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: calc(100vh - 200px);
  min-height: 400px;
}
.nova-chat-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--vx-divider);
  display: flex; align-items: center; gap: 0.75rem;
  background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(255,253,248,0.9));
}
.nova-chat-header-info h4 { font-size: 0.9rem; font-weight: 800; margin: 0; color: var(--vx-text-primary); }
.nova-chat-header-info p { font-size: 0.72rem; color: var(--vx-text-muted); margin: 0; }
.nova-chat-messages {
  flex: 1; overflow-y: auto; padding: 1.25rem 1.5rem;
  display: flex; flex-direction: column; gap: 0.75rem;
}
.nova-chat-msg {
  max-width: 75%; padding: 0.75rem 1rem;
  border-radius: 0.875rem; font-size: 0.8125rem; line-height: 1.5;
  word-wrap: break-word; animation: novaMsgIn 0.3s ease;
}
@keyframes novaMsgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.nova-chat-msg-admin {
  align-self: flex-end;
  background: var(--vx-primary-gradient);
  color: #fff;
  border-bottom-right-radius: 0.25rem;
}
.nova-chat-msg-user {
  align-self: flex-start;
  background: var(--vx-body-bg);
  border: 1px solid var(--vx-card-border);
  color: var(--vx-text-primary);
  border-bottom-left-radius: 0.25rem;
}
.nova-chat-msg-meta { font-size: 0.6rem; margin-top: 0.4rem; opacity: 0.6; }
.nova-chat-input-area {
  border-top: 1px solid var(--vx-divider);
  padding: 0.875rem 1.25rem;
  display: flex; gap: 0.5rem; align-items: flex-end;
}
.nova-chat-input {
  flex: 1; resize: none; border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md); padding: 0.625rem 0.875rem;
  font-size: 0.8125rem; max-height: 120px; min-height: 42px;
}
.nova-chat-input:focus { outline: none; border-color: var(--vx-primary); box-shadow: 0 0 0 3px var(--vx-primary-light); }
.nova-chat-send {
  flex-shrink: 0; padding: 0.625rem 1.25rem; border: none; border-radius: var(--vx-radius-md);
  background: var(--vx-primary-gradient); color: #fff; font-size: 0.8125rem; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; gap: 0.4rem;
}
.nova-chat-send:hover { filter: brightness(1.06); }
</style>

<div style="margin-bottom:1rem;">
  <a href="/admin/chat" class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-arrow-left"></i> Retour  la liste
  </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="nova-alert nova-alert-success"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="nova-alert nova-alert-error"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="nova-chat-wrap" id="chatWrap">
  <div class="nova-chat-header">
    <div style="width:2.5rem;height:2.5rem;border-radius:50%;background:var(--vx-primary-gradient);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.9rem;font-weight:800;flex-shrink:0;">
      <?= e(strtoupper(mb_substr(($conversation['firstname'] ?? 'U'), 0, 1))) ?>
    </div>
    <div class="nova-chat-header-info">
      <h4><?= e(($conversation['firstname'] ?? '') . ' ' . ($conversation['lastname'] ?? '')) ?></h4>
      <p><?= e($conversation['email'] ?? '') ?>  <?= e($conversation['company'] ?? '') ?></p>
    </div>
  </div>

  <div class="nova-chat-messages" id="chatMessages">
    <?php if (empty($messages)): ?>
      <div style="text-align:center;padding:3rem 1rem;color:var(--vx-text-muted);">
        <i class="fas fa-inbox" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;"></i>
        <p>Aucun message. Dmarrez la conversation.</p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php $isAdmin = $msg['sender_type'] === 'admin'; ?>
        <div class="nova-chat-msg <?= $isAdmin ? 'nova-chat-msg-admin' : 'nova-chat-msg-user' ?>" id="msg-<?= (int)$msg['id'] ?>">
          <?= nl2br(e($msg['body'])) ?>
          <div class="nova-chat-msg-meta"><?= e(formatDate($msg['created_at'], 'd/m/Y H:i')) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="nova-chat-input-area">
    <form method="POST" action="/admin/chat/<?= (int)$conversation['id'] ?>/send" id="chatForm" style="flex:1;display:flex;gap:0.5rem;align-items:flex-end;">
      <?= csrf_field() ?>
      <textarea name="body" id="chatInput" class="nova-chat-input" placeholder="Rpondre  l'utilisateur..." rows="1" required></textarea>
      <button type="submit" class="nova-chat-send"><i class="fas fa-paper-plane"></i> Envoyer</button>
    </form>
  </div>
</div>

<script>
(function() {
  var msgContainer = document.getElementById('chatMessages');
  var lastId = 0;
  <?php if (!empty($messages)): ?>
    lastId = <?= (int)end($messages)['id'] ?>;
    msgContainer.scrollTop = msgContainer.scrollHeight;
  <?php endif; ?>

  var convId = <?= (int)$conversation['id'] ?>;
  function poll() {
    fetch('/api/chat/poll?conversation_id=' + convId + '&last_id=' + lastId, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.messages && data.messages.length > 0) {
        data.messages.forEach(function(msg) {
          if (document.getElementById('msg-' + msg.id)) return;
          var div = document.createElement('div');
          var isAdmin = msg.sender_type === 'admin';
          div.className = isAdmin ? 'nova-chat-msg nova-chat-msg-admin' : 'nova-chat-msg nova-chat-msg-user';
          div.id = 'msg-' + msg.id;
          var body = document.createElement('div');
          body.innerHTML = msg.body.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
          div.appendChild(body);
          var meta = document.createElement('div');
          meta.className = 'nova-chat-msg-meta';
          meta.textContent = msg.created_at;
          div.appendChild(meta);
          msgContainer.appendChild(div);
          lastId = parseInt(msg.id);
        });
        msgContainer.scrollTop = msgContainer.scrollHeight;
      }
    }).catch(function() {});
  }
  setInterval(poll, 5000);

  var input = document.getElementById('chatInput');
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); document.getElementById('chatForm').submit(); }
  });
  input.addEventListener('input', function() { this.style.height = 'auto'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'; });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
