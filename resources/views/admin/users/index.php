<?php
$title = 'Gestion des Utilisateurs';
ob_start();
?>
<style>
.auto-users-header { margin-bottom: 1.5rem; }
.auto-users-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-table .user-name { color: var(--auto-text-primary); font-weight: 600; font-size: 0.8rem; }
.auto-table .user-email { color: var(--auto-text-muted); font-size: 0.7rem; }
.auto-table .actions-cell { display: flex; justify-content: center; gap: 0.35rem; }
.auto-table .btn-action {
  width: 30px; height: 30px;
  border-radius: var(--auto-radius-sm);
  border: 1px solid var(--auto-border);
  background: transparent;
  color: var(--auto-text-secondary);
  cursor: pointer;
  transition: var(--auto-transition);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 0.75rem;
}
.auto-table .btn-action:hover { border-color: var(--auto-cyan-glow); color: var(--auto-cyan); background: var(--auto-cyan-dim); }
.auto-table .btn-action-danger:hover { border-color: var(--auto-red-glow); color: var(--auto-red); background: rgba(255,51,102,0.08); }
@media (max-width: 768px) { .auto-users-wrap { overflow-x: auto; } }
</style>

<div class="auto-users-wrap auto-fade-in">
  <div class="auto-users-header d-flex justify-content-between align-items-center">
    <h5><i class="fas fa-user-shield me-2" style="color:var(--auto-cyan);"></i><?= count($users) ?> utilisateur(s)</h5>
    <a href="/admin/users/create" class="auto-btn auto-btn-primary auto-btn-sm"><i class="fas fa-plus me-1"></i>Nouvel utilisateur</a>
  </div>

  <div class="auto-glass-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="auto-table">
        <thead>
          <tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th class="text-center">Actif</th><th>Dernière connexion</th><th class="text-center">Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td style="color:var(--auto-text-muted);font-size:0.7rem;"><?= $u['id'] ?></td>
              <td><span class="user-name"><?= e($u['firstname'] . ' ' . $u['lastname']) ?></span></td>
              <td><span class="user-email"><?= e($u['email']) ?></span></td>
              <td><span class="auto-badge auto-badge-cyan"><?= e($u['role_name']) ?></span></td>
              <td class="text-center">
                <span class="auto-badge <?= $u['is_active'] ? 'auto-badge-green' : 'auto-badge-red' ?>">
                  <?= $u['is_active'] ? 'Oui' : 'Non' ?>
                </span>
              </td>
              <td style="font-size:0.7rem;color:var(--auto-text-muted);"><?= $u['last_login_at'] ? formatDate($u['last_login_at'], 'd/m/Y H:i') : 'Jamais' ?></td>
              <td class="text-center">
                <div class="actions-cell">
                  <a href="/admin/users/edit/<?= $u['id'] ?>" class="btn-action" title="Modifier"><i class="fas fa-edit"></i></a>
                  <?php if ($u['id'] !== 1): ?>
                    <a href="/admin/users/delete/<?= $u['id'] ?>" class="btn-action btn-action-danger" data-confirm="Supprimer cet utilisateur ?" title="Supprimer"><i class="fas fa-trash"></i></a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>