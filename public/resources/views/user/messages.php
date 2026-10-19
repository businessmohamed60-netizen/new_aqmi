<?php
/* User chat view — rendered inside user_dashboard layout */
?>
<style>
.ud-chat-wrap {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  box-shadow: var(--vx-shadow-md);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: calc(100vh - 220px);
  min-height: 400px;
}
.ud-chat-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--vx-divider);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(255,253,248,0.9));
}
.ud-chat-header-icon {
  width: 2.5rem; height: 2.5rem; border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.1rem; flex-shrink: 0;
  box-shadow: 0 4px 12px var(--vx-primary-glow);
}
.ud-chat-header h3 { font-size: 0.95rem; font-weight: 800; color: var(--vx-text-primary); margin: 0; }
.ud-chat-header p { font-size: 0.7rem; color: var(--vx-text-muted); margin: 0; }

.ud-chat-messages {
  flex: 1; overflow-y: auto; padding: 1.25rem 1.5rem;
  display: flex; flex-direction: column; gap: 0.75rem;
}
.ud-chat-msg {
  max-width: 75%; padding: 0.75rem 1rem;
  border-radius: 0.875rem; font-size: 0.8125rem; line-height: 1.5;
  word-wrap: break-word; position: relative;
  animation: udMsgIn 0.3s ease;
}
@keyframes udMsgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.ud-chat-msg-user {
  align-self: flex-end;
  background: var(--vx-primary-gradient);
  color: #fff;
  border-bottom-right-radius: 0.25rem;
  box-shadow: 0 2px 8px var(--vx-primary-glow);
}
.ud-chat-msg-admin {
  align-self: flex-start;
  background: var(--vx-body-bg);
  border: 1px solid var(--vx-card-border);
  color: var(--vx-text-primary);
  border-bottom-left-radius: 0.25rem;
}
.ud-chat-msg-meta {
  font-size: 0.6rem; margin-top: 0.4rem; opacity: 0.6;
}
.ud-chat-empty {
  text-align: center; padding: 3rem 1rem; color: var(--vx-text-muted);
}
.ud-chat-empty i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3; }
.ud-chat-empty p { font-size: 0.8rem; margin: 0; }

.ud-chat-input-area {
  border-top: 1px solid var(--vx-divider);
  padding: 0.875rem 1.25rem;
  display: flex; align-items: flex-end; gap: 0.5rem;
  background: var(--vx-card-bg);
}
.ud-chat-input {
  flex: 1; resize: none; border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md); padding: 0.625rem 0.875rem;
  font-family: var(--ud-font); font-size: 0.8125rem; color: var(--vx-text-primary);
  background: #fff; transition: all var(--vx-transition); max-height: 120px; min-height: 42px;
}
.ud-chat-input:focus { outline: none; border-color: var(--vx-primary); box-shadow: 0 0 0 3px var(--vx-primary-light); }
.ud-chat-send {
  flex-shrink: 0; padding: 0.625rem 1.25rem; border: none; border-radius: var(--vx-radius-md);
  background: var(--vx-primary-gradient); color: #fff; font-size: 0.8125rem; font-weight: 700;
  cursor: pointer; transition: all var(--vx-transition);
  box-shadow: 0 4px 12px var(--vx-primary-glow);
  display: flex; align-items: center; gap: 0.4rem;
}
.ud-chat-send:hover { transform: translateY(-1px); box-shadow: 0 6px 18px var(--vx-primary-glow); filter: brightness(1.06); }
.ud-chat-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
</style>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="ud-alert ud-alert-success"><i class="fas fa-check-circle"></i> <?= e($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="ud-alert ud-alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="ud-chat-wrap" id="chatWrap">
  <div class="ud-chat-header">
    <div class="ud-chat-header-icon"><i class="fas fa-comments"></i></div>
    <div>
      <h3>Messagerie NOVAQYS</h3>
      <p> discutez avec l'quipe d'administration</p>
    </div>
  </div>

  <div class="ud-chat-messages" id="chatMessages">
    <?php if (empty($messages)): ?>
      <div class="ud-chat-empty">
        <i class="fas fa-paper-plane"></i>
        <p>Aucun message pour le moment.<br>Envoyez votre premier message  l'quipe NOVAQYS.</p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php $isUser = $msg['sender_type'] === 'user'; ?>
        <div class="ud-chat-msg <?= $isUser ? 'ud-chat-msg-user' : 'ud-chat-msg-admin' ?>" id="msg-<?= (int)$msg['id'] ?>">
          <?= nl2br(e($msg['body'])) ?>
          <div class="ud-chat-msg-meta"><?= e(formatDate($msg['created_at'], 'd/m/Y H:i')) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="ud-chat-input-area">
    <form method="POST" action="/user/messages/send" id="chatForm" style="flex:1;display:flex;gap:0.5rem;align-items:flex-end;">
      <?= csrf_field() ?>
      <textarea name="body" id="chatInput" class="ud-chat-input" placeholder="Votre message..." rows="1" required></textarea>
      <button type="submit" class="ud-chat-send" id="chatSendBtn">
        <i class="fas fa-paper-plane"></i> Envoyer
      </button>
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

  var convId = <?= isset($conversation) ? (int)$conversation['id'] : 0 ?>;
  var pollTimer;

  function poll() {
    if (!convId) return;
    fetch('/api/chat/poll?conversation_id=' + convId + '&last_id=' + lastId, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.messages && data.messages.length > 0) {
        data.messages.forEach(function(msg) {
          if (document.getElementById('msg-' + msg.id)) return;
          var div = document.createElement('div');
          var isUser = msg.sender_type === 'user';
          div.className = isUser ? 'ud-chat-msg ud-chat-msg-user' : 'ud-chat-msg ud-chat-msg-admin';
          div.id = 'msg-' + msg.id;
          var body = document.createElement('div');
          body.innerHTML = msg.body.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
          div.appendChild(body);
          var meta = document.createElement('div');
          meta.className = 'ud-chat-msg-meta';
          meta.textContent = msg.created_at;
          div.appendChild(meta);
          msgContainer.appendChild(div);
          lastId = parseInt(msg.id);
        });
        msgContainer.scrollTop = msgContainer.scrollHeight;
      }
    })
    .catch(function() {});
  }

  pollTimer = setInterval(poll, 5000);

  var input = document.getElementById('chatInput');
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      document.getElementById('chatForm').submit();
    }
  });
  input.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  });
})();
</script>
