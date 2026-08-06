<?php
$title = 'Vérification de sécurité - NOVAQYS';
$showFooter = true;
$email = $email ?? '';
$device = $device ?? '';
$expire = $expire ?? '';
ob_start();
?>
<div class="aqmi-auth-wrap">
  <div class="aqmi-orb aqmi-orb-1"></div>
  <div class="aqmi-orb aqmi-orb-2"></div>
  <div class="aqmi-auth-card aqmi-fade-in">
    <!-- Header -->
    <div class="aqmi-auth-header">
      <div class="aqmi-logo">N</div>
      <h2 class="aqmi-auth-title">Vérification de sécurité</h2>
      <p class="aqmi-auth-sub">Un code à 6 chiffres vous a été envoyé</p>
    </div>

    <!-- Email info -->
    <div class="aqmi-otp-email">
      <i class="fas fa-envelope"></i>
      <span><?= e($email) ?></span>
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

    <!-- OTP Form -->
    <form method="POST" action="/otp" class="aqmi-form" autocomplete="off" id="otpForm">
      <?= csrf_field() ?>
      <div class="aqmi-field">
        <label class="aqmi-label">Code de vérification</label>
        <div class="aqmi-otp-inputs" id="otpInputs">
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autofocus required>
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" class="aqmi-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
        </div>
        <input type="hidden" name="otp_code" id="otp_code">
      </div>

      <button type="submit" class="aqmi-btn aqmi-btn-primary aqmi-btn-full" id="otpSubmitBtn">
        <span>Vérifier</span>
        <i class="fas fa-shield-halved"></i>
      </button>
    </form>

    <!-- Timer -->
    <div class="aqmi-otp-timer" id="otpTimer">
      <i class="fas fa-clock"></i>
      <span id="timerDisplay">05:00</span>
    </div>

    <!-- Device info -->
    <?php if ($device): ?>
    <div class="aqmi-otp-device">
      <i class="fas fa-laptop"></i>
      <span><?= e($device) ?></span>
    </div>
    <?php endif; ?>

    <!-- Resend -->
    <div class="aqmi-otp-resend">
      <span>Vous n'avez pas reçu le code ?</span>
      <a href="/otp/resend" class="aqmi-link" id="resendLink">Renvoyer un nouveau code</a>
    </div>

    <!-- Back to login -->
    <div class="aqmi-auth-footer">
      <a href="/login" class="aqmi-link">
        <i class="fas fa-arrow-left"></i> Retour à la connexion
      </a>
    </div>
  </div>
</div>

<script>
(function() {
  // OTP input auto-advance
  const inputs = document.querySelectorAll('.aqmi-otp-digit');
  const hiddenInput = document.getElementById('otp_code');

  inputs.forEach((input, index) => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
      if (this.value && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
      updateCode();
    });

    input.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && index > 0) {
        inputs[index - 1].focus();
      }
      updateCode();
    });

    input.addEventListener('paste', function(e) {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
      [...paste].forEach((char, i) => {
        if (inputs[index + i]) {
          inputs[index + i].value = char;
          if (index + i < inputs.length - 1) {
            inputs[index + i + 1].focus();
          }
        }
      });
      updateCode();
    });
  });

  function updateCode() {
    let code = '';
    inputs.forEach(inp => code += inp.value);
    hiddenInput.value = code;
  }

  // Timer countdown
  const expireStr = '<?= $expire ?>';
  if (expireStr) {
    const expireTime = new Date(expireStr.replace(' ', 'T')).getTime();
    const timerDisplay = document.getElementById('timerDisplay');
    let timerActive = true;

    function updateTimer() {
      if (!timerActive) return;
      const now = new Date().getTime();
      const diff = expireTime - now;

      if (diff <= 0) {
        timerDisplay.textContent = '00:00';
        timerDisplay.style.color = '#f43f5e';
        timerActive = false;
        document.querySelector('.aqmi-otp-timer i').style.color = '#f43f5e';
        return;
      }

      const minutes = Math.floor(diff / 60000);
      const seconds = Math.floor((diff % 60000) / 1000);
      timerDisplay.textContent =
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0');

      if (diff < 60000) {
        timerDisplay.style.color = '#f59e0b';
      }

      setTimeout(updateTimer, 1000);
    }

    updateTimer();
  }
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>