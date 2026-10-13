<?php
$title = __('profile.title');
$activeSection = 'profile';
$pageTitle = __('profile.nav_label') ?: 'Mon profil';
$pageSubtitle = __('profile.subtitle') ?: 'Gérez vos informations personnelles et votre sécurité';
ob_start();
$langCode = $_SESSION['lang'] ?? 'fr';
?>
<style>
/* ====== Profile page styles ====== */
.ud-profile-header {
  background: linear-gradient(135deg, rgba(31,111,235,0.08) 0%, rgba(255,253,248,0.94) 45%, #fffdf8 100%);
  backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  padding: 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.ud-profile-header::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 60%);
  border-radius: 50%;
  animation: udGlowPulse 6s ease-in-out infinite;
}
@keyframes udGlowPulse {
  0%,100% { opacity: 0.4; transform: scale(0.95); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
.ud-profile-avatar-lg {
  width: 80px; height: 80px;
  border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem; font-weight: 900; color: #fff;
  flex-shrink: 0;
  box-shadow: 0 8px 24px var(--vx-primary-glow);
  position: relative; z-index: 1;
}
.ud-profile-header-info { position: relative; z-index: 1; }
.ud-profile-header-info h2 { font-size: 1.35rem; font-weight: 800; color: var(--vx-text-primary); margin-bottom: 0.25rem; }
.ud-profile-header-info p { font-size: 0.8rem; color: var(--vx-text-muted); margin: 0; }
.ud-profile-role-badge {
  display: inline-flex; align-items: center; gap: 0.3rem;
  font-size: 0.65rem; font-weight: 700;
  padding: 0.2rem 0.6rem; border-radius: 0.375rem;
  background: var(--vx-primary-light); color: var(--vx-primary);
  margin-top: 0.5rem;
}

.ud-profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
@media (max-width: 768px) { .ud-profile-grid { grid-template-columns: 1fr; } }

.ud-profile-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
}
.ud-profile-card h3 {
  font-size: 0.8125rem; font-weight: 700; color: var(--vx-text-primary);
  margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;
  padding-bottom: 0.875rem; border-bottom: 1px solid var(--vx-divider);
}
.ud-profile-card h3 i { color: var(--vx-primary); }

.ud-form-group { margin-bottom: 1.25rem; }
.ud-form-group:last-child { margin-bottom: 0; }
.ud-form-label {
  display: block; font-size: 0.75rem; font-weight: 600;
  color: var(--vx-text-secondary); margin-bottom: 0.4rem;
}
.ud-form-input {
  width: 100%; padding: 0.625rem 0.875rem;
  border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md);
  font-family: var(--ud-font); font-size: 0.8125rem; font-weight: 500;
  color: var(--vx-text-primary); background: #fff;
  transition: all var(--vx-transition);
}
.ud-form-input:focus {
  outline: none; border-color: var(--vx-primary);
  box-shadow: 0 0 0 3px var(--vx-primary-light);
}
.ud-form-input:disabled { background: var(--vx-body-bg); color: var(--vx-text-muted); }
.ud-form-hint { font-size: 0.65rem; color: var(--vx-text-muted); margin-top: 0.3rem; }

.ud-info-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.625rem 0; border-bottom: 1px solid var(--vx-divider);
}
.ud-info-row:last-child { border-bottom: none; }
.ud-info-label { font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 500; }
.ud-info-value { font-size: 0.8125rem; color: var(--vx-text-primary); font-weight: 600; }

.ud-login-history-item {
  display: flex; align-items: center; gap: 0.75rem;
  padding: 0.625rem 0; border-bottom: 1px solid var(--vx-divider);
}
.ud-login-history-item:last-child { border-bottom: none; }
.ud-login-status-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0; }
.ud-login-status-success { background: var(--vx-success); }
.ud-login-status-failed { background: var(--vx-danger); }
.ud-login-history-info { flex: 1; }
.ud-login-history-ip { font-size: 0.75rem; color: var(--vx-text-primary); font-weight: 500; }
.ud-login-history-date { font-size: 0.65rem; color: var(--vx-text-muted); }

.ud-alert { padding: 0.75rem 1rem; border-radius: var(--vx-radius-md); font-size: 0.75rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.ud-alert-success { background: var(--vx-success-light); color: var(--vx-success); border: 1px solid rgba(16,185,129,0.20); }
.ud-alert-error { background: var(--vx-danger-light); color: var(--vx-danger); border: 1px solid rgba(239,68,68,0.20); }

@media (max-width: 768px) {
  .ud-profile-header { flex-direction: column; text-align: center; padding: 1.5rem; }
  .ud-profile-avatar-lg { width: 64px; height: 64px; font-size: 1.5rem; }
}
</style>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="ud-alert ud-alert-success">
    <i class="fas fa-check-circle"></i>
    <?= e($_SESSION['success']); unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="ud-alert ud-alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= e($_SESSION['error']); unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

<!-- Profile Header -->
<div class="ud-profile-header">
  <div class="ud-profile-avatar-lg"><?= e(strtoupper(mb_substr($user['firstname'] ?? 'U', 0, 1))) ?></div>
  <div class="ud-profile-header-info">
    <h2><?= e(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?></h2>
    <p><?= e($user['email'] ?? '') ?></p>
    <span class="ud-profile-role-badge">
      <i class="fas fa-user-tag"></i>
      <?= e(__('dashboard.nav.client') ?: 'Client') ?>
    </span>
  </div>
</div>

<div class="ud-profile-grid">
  <!-- Personal Information Form -->
  <div class="ud-profile-card">
    <h3><i class="fas fa-id-card"></i> <?= __('profile.section.personal') ?></h3>
    <form method="POST" action="/user/profile">
      <?= csrf_field() ?>
      <div class="ud-form-group">
        <label class="ud-form-label" for="firstname"><?= __('profile.label.firstname') ?></label>
        <input type="text" id="firstname" name="firstname" class="ud-form-input" value="<?= e($user['firstname'] ?? '') ?>" required>
      </div>
      <div class="ud-form-group">
        <label class="ud-form-label" for="lastname"><?= __('profile.label.lastname') ?></label>
        <input type="text" id="lastname" name="lastname" class="ud-form-input" value="<?= e($user['lastname'] ?? '') ?>" required>
      </div>
      <div class="ud-form-group">
        <label class="ud-form-label" for="email"><?= __('profile.label.email') ?></label>
        <input type="email" id="email" name="email" class="ud-form-input" value="<?= e($user['email'] ?? '') ?>" required>
      </div>
      <div class="ud-form-group">
        <label class="ud-form-label" for="phone"><?= __('profile.label.phone') ?></label>
        <input type="text" id="phone" name="phone" class="ud-form-input" value="<?= e($user['phone'] ?? '') ?>">
      </div>
      <button type="submit" class="nova-btn nova-btn-primary" style="width:100%;justify-content:center;">
        <i class="fas fa-save"></i> <?= __('action.save') ?>
      </button>
    </form>
  </div>

  <!-- Password Change + Account Info -->
  <div style="display:flex;flex-direction:column;gap:1.5rem;">
    <!-- Password Change -->
    <div class="ud-profile-card">
      <h3><i class="fas fa-shield-halved"></i> <?= __('profile.section.security') ?></h3>
      <form method="POST" action="/user/profile/password">
        <?= csrf_field() ?>
        <div class="ud-form-group">
          <label class="ud-form-label" for="current_password"><?= __('profile.label.current_password') ?></label>
          <input type="password" id="current_password" name="current_password" class="ud-form-input" required>
        </div>
        <div class="ud-form-group">
          <label class="ud-form-label" for="new_password"><?= __('profile.label.new_password') ?></label>
          <input type="password" id="new_password" name="new_password" class="ud-form-input" required>
          <div class="ud-form-hint"><?= __('profile.hint.password_length') ?></div>
        </div>
        <div class="ud-form-group">
          <label class="ud-form-label" for="confirm_password"><?= __('profile.label.confirm_password') ?></label>
          <input type="password" id="confirm_password" name="confirm_password" class="ud-form-input" required>
        </div>
        <button type="submit" class="nova-btn nova-btn-primary" style="width:100%;justify-content:center;">
          <i class="fas fa-key"></i> <?= __('profile.action.change_password') ?>
        </button>
      </form>
    </div>

    <!-- Account Info -->
    <div class="ud-profile-card">
      <h3><i class="fas fa-circle-info"></i> <?= __('profile.section.account') ?></h3>
      <div class="ud-info-row">
        <span class="ud-info-label"><?= __('profile.label.member_since') ?></span>
        <span class="ud-info-value"><?= $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : '—' ?></span>
      </div>
      <div class="ud-info-row">
        <span class="ud-info-label"><?= __('profile.label.last_login') ?></span>
        <span class="ud-info-value"><?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '—' ?></span>
      </div>
      <div class="ud-info-row">
        <span class="ud-info-label"><?= __('profile.label.status') ?></span>
        <span class="ud-info-value" style="color:var(--vx-success);">
          <i class="fas fa-circle-check" style="font-size:0.7rem;"></i> <?= __('profile.label.active') ?>
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Login History -->
<?php if (!empty($loginHistory)): ?>
<div class="ud-profile-card" style="margin-top:1.5rem;">
  <h3><i class="fas fa-clock-rotate-left"></i> <?= __('profile.section.login_history') ?></h3>
  <?php foreach ($loginHistory as $log): ?>
    <div class="ud-login-history-item">
      <div class="ud-login-status-dot <?= $log['status'] === 'success' ? 'ud-login-status-success' : 'ud-login-status-failed' ?>"></div>
      <div class="ud-login-history-info">
        <div class="ud-login-history-ip">
          <?= e($log['ip_address'] ?? '—') ?>
          <?php if ($log['status'] === 'failed'): ?>
            <span style="color:var(--vx-danger);font-size:0.65rem;margin-left:0.4rem;"><?= __('profile.label.failed') ?></span>
          <?php endif; ?>
        </div>
        <div class="ud-login-history-date"><?= e($log['created_at'] ? date('d/m/Y H:i', strtotime($log['created_at'])) : '') ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
