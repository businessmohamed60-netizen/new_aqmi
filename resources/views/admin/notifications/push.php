<?php
$users = \App\Helpers\Database::fetchAll(
  "SELECT u.id, u.firstname, u.lastname, u.email, u.company FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'client' AND u.is_active = 1 ORDER BY u.firstname ASC"
);
$content = null;
ob_start();
?>
<style>
.nova-push-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 2rem;
  max-width: 720px;
}
.nova-push-card h3 { font-size: 0.9rem; font-weight: 800; margin: 0 0 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
.nova-push-card h3 i { color: var(--vx-primary); }
.nova-push-info {
  background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15);
  border-radius: var(--vx-radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem;
  font-size: 0.8rem; color: var(--vx-text-secondary); display: flex; gap: 0.5rem; align-items: flex-start;
}
.nova-push-info i { color: var(--vx-primary); margin-top: 0.15rem; }
.user-pick-list {
  max-height: 240px; overflow-y: auto; border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md); padding: 0.5rem; background: #fff;
}
.user-pick {
  display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.5rem;
  border-radius: 0.375rem; cursor: pointer; transition: background 0.15s ease;
  font-size: 0.8rem;
}
.user-pick:hover { background: var(--vx-primary-light); }
.user-pick input { flex-shrink: 0; }
.user-pick-name { font-weight: 600; color: var(--vx-text-primary); }
.user-pick-email { font-size: 0.7rem; color: var(--vx-text-muted); }
.user-pick-search {
  width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-md); font-size: 0.8rem; margin-bottom: 0.5rem;
  font-family: inherit;
}
.user-pick-search:focus { outline: none; border-color: var(--vx-primary); box-shadow: 0 0 0 3px var(--vx-primary-light); }
.user-pick-actions { display: flex; gap: 0.5rem; margin-bottom: 0.5rem; }
.user-pick-actions button {
  font-size: 0.7rem; padding: 0.25rem 0.6rem; border: 1px solid var(--vx-card-border);
  border-radius: 0.3rem; background: var(--vx-card-bg); cursor: pointer; color: var(--vx-text-secondary);
  font-family: inherit;
}
.user-pick-actions button:hover { background: var(--vx-primary-light); color: var(--vx-primary); border-color: var(--vx-primary); }
</style>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="nova-alert nova-alert-success"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="nova-alert nova-alert-error"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="nova-push-card">
  <h3><i class="fas fa-bullhorn"></i> Envoyer une notification push</h3>

  <div class="nova-push-info">
    <i class="fas fa-info-circle"></i>
    <div>
      Les utilisateurs recevront une notification in-app (cloche). Une copie email sera envoyée depuis <strong>noreply@novaqys.com</strong> vers l'email du profil utilisateur, si l'option "Recevoir les notifications par email" est active dans leur profil.
    </div>
  </div>

  <form method="POST" action="/admin/notifications/send">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Destinataires</label>
      <select name="target" id="pushTarget" class="form-select" style="font-size:0.85rem;">
        <option value="all">Tous les utilisateurs actifs</option>
        <option value="multiple">Plusieurs utilisateurs spécifiques</option>
        <option value="single">Un utilisateur spécifique</option>
      </select>
    </div>

    <div class="mb-3" id="userMultiWrap" style="display:none;">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Sélectionner les utilisateurs</label>
      <input type="text" class="user-pick-search" id="userSearchInput" placeholder="Rechercher par nom ou email...">
      <div class="user-pick-actions">
        <button type="button" id="selectAllUsers">Tout sélectionner</button>
        <button type="button" id="deselectAllUsers">Tout désélectionner</button>
      </div>
      <div class="user-pick-list" id="userPickList">
        <?php foreach ($users as $u): ?>
        <label class="user-pick">
          <input type="checkbox" name="user_ids[]" value="<?= (int)$u['id'] ?>">
          <div>
            <div class="user-pick-name"><?= e(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? '')) ?></div>
            <div class="user-pick-email"><?= e($u['email'] ?? '') ?> · <?= e($u['company'] ?? 'N/A') ?></div>
          </div>
        </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="mb-3" id="userSelectWrap" style="display:none;">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Utilisateur</label>
      <select name="user_id" class="form-select" style="font-size:0.85rem;">
        <?php foreach ($users as $u): ?>
          <option value="<?= (int)$u['id'] ?>"><?= e(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? '') . ' - ' . ($u['email'] ?? '') . ' (' . ($u['company'] ?? 'N/A') . ')') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Titre <span style="color:#dc2626;">*</span></label>
      <input type="text" name="title" class="form-control" style="font-size:0.85rem;" placeholder="Ex: Nouvelle fonctionnalité disponible" required maxlength="255">
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Message <span style="color:#dc2626;">*</span></label>
      <textarea name="body" class="form-control" style="font-size:0.85rem;min-height:100px;" placeholder="Contenu de la notification..." required maxlength="2000"></textarea>
    </div>

    <button type="submit" class="btn btn-primary" style="background:var(--vx-primary-gradient);border:none;font-weight:700;">
      <i class="fas fa-paper-plane"></i> Envoyer la notification
    </button>
  </form>
</div>

<script>
(function() {
  var targetSelect = document.getElementById('pushTarget');
  var multiWrap = document.getElementById('userMultiWrap');
  var singleWrap = document.getElementById('userSelectWrap');

  function updateVisibility() {
    multiWrap.style.display = targetSelect.value === 'multiple' ? 'block' : 'none';
    singleWrap.style.display = targetSelect.value === 'single' ? 'block' : 'none';
  }
  targetSelect.addEventListener('change', updateVisibility);

  // Search filter
  var searchInput = document.getElementById('userSearchInput');
  var pickList = document.getElementById('userPickList');
  searchInput.addEventListener('input', function() {
    var q = this.value.toLowerCase();
    pickList.querySelectorAll('.user-pick').forEach(function(item) {
      var text = item.textContent.toLowerCase();
      item.style.display = text.indexOf(q) !== -1 ? '' : 'none';
    });
  });

  // Select all / deselect all
  document.getElementById('selectAllUsers').addEventListener('click', function() {
    pickList.querySelectorAll('.user-pick:visible input').forEach(function(cb) {
      if (cb.closest('.user-pick').style.display !== 'none') cb.checked = true;
    });
    pickList.querySelectorAll('.user-pick input').forEach(function(cb) {
      if (cb.closest('.user-pick').style.display !== 'none') cb.checked = true;
    });
  });
  document.getElementById('deselectAllUsers').addEventListener('click', function() {
    pickList.querySelectorAll('.user-pick input').forEach(function(cb) { cb.checked = false; });
  });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
