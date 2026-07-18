<?php $title = 'Connexion Administration - NOVAQYS'; ob_start(); ?>
<form method="GET" action="/login" class="aqmi-form">
    <?= csrf_field() ?>
    <div class="aqmi-field">
        <label class="aqmi-label">Email</label>
        <div class="aqmi-input-wrap">
            <i class="fas fa-envelope aqmi-input-icon"></i>
            <input type="email" name="email" class="aqmi-input" placeholder="admin@novaqys.com" required autofocus>
        </div>
    </div>
    <div class="aqmi-field">
        <label class="aqmi-label">Mot de passe</label>
        <div class="aqmi-input-wrap">
            <i class="fas fa-lock aqmi-input-icon"></i>
            <input type="password" name="password" class="aqmi-input" placeholder="••••••••" required>
            <button type="button" class="aqmi-pwd-toggle" onclick="togglePwd(this)" aria-label="Afficher le mot de passe">
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
    <a href="/" class="aqmi-link"><i class="fas fa-arrow-left"></i> Retour au site</a>
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
require BASE_PATH . '/resources/views/layouts/auth.php';
?>