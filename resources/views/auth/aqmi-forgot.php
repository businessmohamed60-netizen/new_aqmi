<?php
$title = 'Mot de passe oublié - NOVAQYS';
$showFooter = true;
ob_start();
?>
<div class="aqmi-auth-wrap">
  <div class="aqmi-orb aqmi-orb-1"></div>
  <div class="aqmi-orb aqmi-orb-2"></div>
  <div class="aqmi-auth-card aqmi-fade-in">
    <div class="aqmi-auth-header">
      <div class="aqmi-logo">N</div>
      <h2 class="aqmi-auth-title">Mot de passe oublié</h2>
      <p class="aqmi-auth-sub">Entrez votre email pour recevoir un lien de réinitialisation</p>
    </div>

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

    <form method="POST" action="/forgot" class="aqmi-form">
      <?= csrf_field() ?>
      <div class="aqmi-field">
        <label class="aqmi-label">Adresse email</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-envelope aqmi-input-icon"></i>
          <input type="email" name="email" class="aqmi-input" placeholder="vous@exemple.com" required autofocus>
        </div>
      </div>
      <button type="submit" class="aqmi-btn aqmi-btn-primary aqmi-btn-full">
        <span>Envoyer le lien</span>
        <i class="fas fa-paper-plane"></i>
      </button>
    </form>

    <div class="aqmi-auth-footer">
      <a href="/login" class="aqmi-link">
        <i class="fas fa-arrow-left"></i> Retour à la connexion
      </a>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>