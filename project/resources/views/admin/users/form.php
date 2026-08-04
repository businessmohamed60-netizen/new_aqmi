<?php
$title = $user ? "Modifier l'utilisateur" : 'Nouvel utilisateur';
ob_start();
?>
<style>
.auto-form-wrap { max-width: 800px; }
.auto-form-header { margin-bottom: 1.5rem; }
.auto-form-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }
</style>

<div class="auto-form-wrap auto-fade-in">
  <div class="auto-form-header">
    <h5><i class="fas fa-user-shield me-2" style="color:var(--auto-cyan);"></i><?= $user ? "Modifier l'utilisateur" : 'Nouvel utilisateur' ?></h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/users/save">
      <?= csrf_field() ?>
      <?php if ($user): ?><input type="hidden" name="id" value="<?= $user['id'] ?>"><?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Prénom *</label>
          <input type="text" name="firstname" class="auto-input" value="<?= e($user['firstname'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Nom *</label>
          <input type="text" name="lastname" class="auto-input" value="<?= e($user['lastname'] ?? '') ?>" required>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Email *</label>
          <input type="email" name="email" class="auto-input" value="<?= e($user['email'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Téléphone</label>
          <input type="text" name="phone" class="auto-input" value="<?= e($user['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="auto-label">Rôle</label>
          <select name="role_id" class="auto-select">
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>" <?= ($user['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="auto-label">Mot de passe <?= $user ? '(laisser vide pour conserver)' : '' ?></label>
          <input type="password" name="password" class="auto-input" <?= $user ? '' : 'required' ?>>
        </div>
      </div>

      <div class="mb-4">
        <div class="form-check form-switch">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !isset($user['is_active']) || $user['is_active'] ? 'checked' : '' ?>>
          <label class="form-check-label">Actif</label>
        </div>
      </div>

      <div class="d-flex gap-2" style="border-top:1px solid var(--auto-border);padding-top:1.25rem;">
        <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        <a href="/admin/users" class="auto-btn auto-btn-secondary"><i class="fas fa-times me-1"></i>Annuler</a>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>