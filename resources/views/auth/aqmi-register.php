<?php
$title = 'Créer un compte - NOVAQYS';
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
      <h2 class="aqmi-auth-title">Créer un compte</h2>
      <p class="aqmi-auth-sub">Rejoignez la plateforme NOVAQYS</p>
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

    <!-- Register Form -->
    <form method="POST" action="/aqmi/register" class="aqmi-form" autocomplete="off" id="registerForm">
      <?= csrf_field() ?>

      <div class="aqmi-register-name">
        <div class="aqmi-field">
          <label class="aqmi-label">Prénom</label>
          <div class="aqmi-input-wrap">
            <i class="fas fa-user aqmi-input-icon"></i>
            <input type="text" name="firstname" class="aqmi-input" placeholder="Jean" required>
          </div>
        </div>
        <div class="aqmi-field">
          <label class="aqmi-label">Nom</label>
          <div class="aqmi-input-wrap">
            <i class="fas fa-user aqmi-input-icon"></i>
            <input type="text" name="lastname" class="aqmi-input" placeholder="Dupont" required>
          </div>
        </div>
      </div>

      <div class="aqmi-field">
        <label class="aqmi-label">Adresse email</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-envelope aqmi-input-icon"></i>
          <input type="email" name="email" class="aqmi-input" placeholder="vous@exemple.com" required>
        </div>
      </div>

      <div class="aqmi-field">
        <label class="aqmi-label">Téléphone</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-phone aqmi-input-icon"></i>
          <input type="tel" name="phone" class="aqmi-input" placeholder="+33 6 12 34 56 78">
        </div>
      </div>

      <div class="aqmi-field">
        <label class="aqmi-label">Entreprise</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-building aqmi-input-icon"></i>
          <input type="text" name="company" class="aqmi-input" placeholder="Votre entreprise" required>
        </div>
      </div>

      <div class="aqmi-field">
        <label class="aqmi-label">Mot de passe</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-lock aqmi-input-icon"></i>
          <input type="password" name="password" class="aqmi-input" placeholder="Minimum 8 caractères" required minlength="8" id="regPassword">
          <button type="button" class="aqmi-pwd-toggle" onclick="togglePwd(this)" aria-label="Afficher">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="aqmi-pwd-strength" id="pwdStrength">
          <div class="aqmi-pwd-strength-bar" data-index="0"></div>
          <div class="aqmi-pwd-strength-bar" data-index="1"></div>
          <div class="aqmi-pwd-strength-bar" data-index="2"></div>
          <div class="aqmi-pwd-strength-bar" data-index="3"></div>
        </div>
        <div class="aqmi-pwd-strength-label" id="pwdStrengthLabel"></div>
      </div>

      <div class="aqmi-field">
        <label class="aqmi-label">Confirmer le mot de passe</label>
        <div class="aqmi-input-wrap">
          <i class="fas fa-check-circle aqmi-input-icon"></i>
          <input type="password" name="password_confirm" class="aqmi-input" placeholder="Confirmez votre mot de passe" required minlength="8">
        </div>
      </div>

      <div class="aqmi-checkbox-wrap">
        <input type="checkbox" name="terms" class="aqmi-checkbox" id="termsCheck" required>
        <label class="aqmi-checkbox-label" for="termsCheck">
          J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> et la
          <a href="#" target="_blank">politique de confidentialité</a>
        </label>
      </div>

      <button type="submit" class="aqmi-btn aqmi-btn-primary aqmi-btn-full" id="registerBtn">
        <span>Créer mon compte</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="aqmi-auth-divider">
      <span>Déjà inscrit ?</span>
    </div>

    <div class="aqmi-auth-footer">
      <a href="/aqmi/login" class="aqmi-link" style="color:#3b82f6;">Se connecter</a>
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

// Password strength
const pwdInput = document.getElementById('regPassword');
const strengthBars = document.querySelectorAll('.aqmi-pwd-strength-bar');
const strengthLabel = document.getElementById('pwdStrengthLabel');

if (pwdInput) {
  pwdInput.addEventListener('input', function() {
    const val = this.value;
    let score = 0;

    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
    const classes = ['', 'weak', 'medium', 'strong', 'very-strong'];
    const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];

    strengthBars.forEach((bar, i) => {
      bar.className = 'aqmi-pwd-strength-bar';
      if (i < score) {
        bar.classList.add(classes[Math.min(score, 4)] || 'weak');
      }
    });

    strengthLabel.textContent = score > 0 ? labels[Math.min(score, 4)] : '';
  });
}
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>