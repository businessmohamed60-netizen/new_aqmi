<?php
$title = 'Connexion NOVAQYS - Qualité Industrielle';
$showFooter = true;
ob_start();
?>
<div class="aqmi-auth-wrap">
  <div class="aqmi-orb aqmi-orb-1"></div>
  <div class="aqmi-orb aqmi-orb-2"></div>
  <div class="aqmi-auth-card aqmi-fade-in">
    <!-- Header -->
    <div class="aqmi-auth-header">
      <div class="aqmi-logo">N</div>
      <h2 class="aqmi-auth-title">Connexion NOVAQYS</h2>
      <p class="aqmi-auth-sub">Plateforme de maturité qualité industrielle</p>
    </div>

    <!-- Alerts -->
    <?php if (\App\Helpers\Session::hasFlash('success')): ?>
      <div class="aqmi-alert aqmi-alert-success">
        <i class="fas fa-check-circle"></i>
        <?= e(\App\Helpers\Session::getFlash('success')) ?>
      </div>
    <?php endif; ?>
    <?php if (\App\Helpers\Session::hasFlash('error')): ?>
      <div class="aqmi-alert aqmi-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= e(\App\Helpers\Session::getFlash('error')) ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="/aqmi/login" class="aqmi-form" autocomplete="off">
      <?= csrf_field() ?>
      <div class="aqmi-field">
        <label class="aqmi-label">Adresse email</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-envelope aqmi-input-icon"></i>
          <input type="email" name="email" class="aqmi-input" placeholder="vous@exemple.com" required autofocus>
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

    <!-- Footer links -->
    <div class="aqmi-auth-footer">
      <a href="/aqmi/forgot" class="aqmi-link">Mot de passe oublié ?</a>
      <span class="aqmi-sep">·</span>
      <a href="/" class="aqmi-link">Retour à l'accueil</a>
    </div>

    <div class="aqmi-auth-divider">
      <span>Plateforme sécurisée</span>
    </div>

    <div class="aqmi-auth-security">
      <i class="fas fa-shield-halved"></i>
      <span>Protégé par OTP · Session chiffrée</span>
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