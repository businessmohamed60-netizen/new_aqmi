<?php
$title = 'Réinitialisation du mot de passe - NOVAQYS';
$showFooter = true;
$token = $token ?? '';
ob_start();
?>
<div class="aqmi-auth-wrap">
  <div class="aqmi-orb aqmi-orb-1"></div>
  <div class="aqmi-orb aqmi-orb-2"></div>
  <div class="aqmi-auth-card aqmi-fade-in">
    <div class="aqmi-auth-header">
      <div class="aqmi-logo">N</div>
      <h2 class="aqmi-auth-title">Nouveau mot de passe</h2>
      <p class="aqmi-auth-sub">Choisissez un mot de passe sécurisé</p>
    </div>

    <?php if (\App\Helpers\Session::hasFlash('error')): ?>
      <div class="aqmi-alert aqmi-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= e(\App\Helpers\Session::getFlash('error')) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/reset" class="aqmi-form">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="aqmi-field">
        <label class="aqmi-label">Nouveau mot de passe</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-lock aqmi-input-icon"></i>
          <input type="password" name="password" class="aqmi-input" placeholder="Minimum 8 caractères" required minlength="8">
          <button type="button" class="aqmi-pwd-toggle" onclick="togglePwd(this)" aria-label="Afficher">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <div class="aqmi-field">
        <label class="aqmi-label">Confirmer le mot de passe</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-check-circle aqmi-input-icon"></i>
          <input type="password" name="password_confirm" class="aqmi-input" placeholder="Confirmez votre mot de passe" required minlength="8">
        </div>
      </div>
      <button type="submit" class="aqmi-btn aqmi-btn-primary aqmi-btn-full">
        <span>Réinitialiser</span>
        <i class="fas fa-check"></i>
      </button>
    </form>

    <div class="aqmi-auth-footer">
      <a href="/login" class="aqmi-link">
        <i class="fas fa-arrow-left"></i> Retour à la connexion
      </a>
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