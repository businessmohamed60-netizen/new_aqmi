<?php $title = 'Connexion - Espace Client NOVAQYS'; ob_start(); ?>
<div class="aqmi-auth-wrap">
  <div class="aqmi-orb aqmi-orb-1"></div>
  <div class="aqmi-orb aqmi-orb-2"></div>
  <div class="aqmi-auth-card aqmi-fade-in">
    <!-- Header -->
    <div class="aqmi-auth-header">
      <div class="aqmi-logo">N</div>
      <h2 class="aqmi-auth-title">Espace Client</h2>
      <p class="aqmi-auth-sub">Connectez-vous pour accéder à vos évaluations</p>
    </div>

    <!-- Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="aqmi-alert aqmi-alert-success">
        <i class="fas fa-check-circle"></i>
        <?= e($_SESSION['success']) ?><?php unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="aqmi-alert aqmi-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= e($_SESSION['error']) ?><?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="GET" action="/user/login" class="aqmi-form">
      <?= csrf_field() ?>
      <div class="aqmi-field">
        <label class="aqmi-label">Email</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-envelope aqmi-input-icon"></i>
          <input type="email" name="email" class="aqmi-input" placeholder="votre@email.com" required autofocus>
        </div>
      </div>
      <div class="aqmi-field">
        <label class="aqmi-label">Mot de passe</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-lock aqmi-input-icon"></i>
          <input type="password" name="password" class="aqmi-input" placeholder="••••••••" required>
          <button type="button" class="aqmi-pwd-toggle" onclick="togglePwd(this)" aria-label="Afficher">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="aqmi-btn aqmi-btn-primary aqmi-btn-full">
        <span>Se connecter</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="aqmi-auth-footer">
      <a href="/" class="aqmi-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
      <span class="aqmi-sep">·</span>
      <a href="/login" class="aqmi-link">Administration</a>
    </div>
  </div>
</div>

<script>
function togglePwd(btn) {
  const input = btn.closest('.aqmi-input-wrap').querySelector('input');
  if (input.type === 'password') {
    input.type = 'text';
    btn.querySelector('i').className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    btn.querySelector('i').className = 'fas fa-eye';
  }
}
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>