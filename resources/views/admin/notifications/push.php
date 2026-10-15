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
      Les utilisateurs recevront une notification in-app (cloche  ). Une copie email sera envoye  depuis <strong>noreply@novaqys.com</strong> vers l'email du profil utilisateur, si l'option "Recevoir les notifications par email" est active dans leur profil.
    </div>
  </div>

  <form method="POST" action="/admin/notifications/send">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.8rem;">Destinataire</label>
      <select name="target" id="pushTarget" class="form-select" style="font-size:0.85rem;">
        <option value="all">Tous les utilisateurs actifs</option>
        <option value="single">Un utilisateur spcifique</option>
      </select>
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
      <input type="text" name="title" class="form-control" style="font-size:0.85rem;" placeholder="Ex: Nouvelle fonctionnalit disponible" required maxlength="255">
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
document.getElementById('pushTarget').addEventListener('change', function() {
  document.getElementById('userSelectWrap').style.display = this.value === 'single' ? 'block' : 'none';
});
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
