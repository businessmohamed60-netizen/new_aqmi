<?php
$title = 'Historique des connexions - AQMI';
$showFooter = true;
$user = $user ?? [];
$history = $history ?? [];
ob_start();
?>
<div class="aqmi-dash-wrap">
  <header class="aqmi-dash-header">
    <div class="aqmi-dash-header-inner">
      <div class="aqmi-dash-brand">
        <img src="<?= asset('img/logo-novaqys-aqmi.png') ?>" alt="NOVAQYS AQMI" width="42" height="28" style="height:28px;width:auto">
        <span>AQMI</span>
      </div>
      <div class="aqmi-dash-user">
        <span class="aqmi-dash-name"><?= e($user['firstname'] ?? '') ?> <?= e($user['lastname'] ?? '') ?></span>
        <div class="aqmi-dash-avatar"><?= strtoupper(substr($user['firstname'] ?? 'U', 0, 1)) ?></div>
      </div>
    </div>
  </header>

  <main class="aqmi-dash-main">
    <div class="aqmi-dash-welcome" style="margin-bottom:0">
      <div>
        <h1>Historique des connexions</h1>
        <p>Consultez l'historique de vos accès à la plateforme</p>
      </div>
      <div class="aqmi-dash-actions">
        <a href="/user/dashboard" class="aqmi-btn aqmi-btn-outline">
          <i class="fas fa-arrow-left"></i>
          <span>Retour</span>
        </a>
      </div>
    </div>

    <?php if (empty($history)): ?>
      <div class="aqmi-dash-empty" style="margin-top:2rem">
        <div class="aqmi-dash-empty-icon">
          <i class="fas fa-clock-rotate"></i>
        </div>
        <h3>Aucun historique</h3>
        <p>Les connexions apparaîtront ici</p>
      </div>
    <?php else: ?>
      <div class="aqmi-dash-table-wrap" style="margin-top:2rem">
        <table class="aqmi-dash-table">
          <thead>
            <tr>
              <th>Date & Heure</th>
              <th>Adresse IP</th>
              <th>Navigateur</th>
              <th>Système</th>
              <th>Résultat</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $h): ?>
              <tr>
                <td data-label="Date & Heure"><?= date('d/m/Y H:i', strtotime($h['login_date'])) ?></td>
                <td data-label="IP"><code><?= e($h['ip_address'] ?? '—') ?></code></td>
                <td data-label="Navigateur"><?= e($h['browser'] ?? '—') ?></td>
                <td data-label="Système"><?= e($h['operating_system'] ?? '—') ?></td>
                <td data-label="Résultat">
                  <span class="aqmi-badge <?= $h['result'] === 'success' ? 'aqmi-badge-success' : 'aqmi-badge-error' ?>">
                    <?= $h['result'] === 'success' ? 'Succès' : 'Échec' ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>