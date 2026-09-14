<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>" dir="<?= ($_SESSION['lang'] ?? 'fr') === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($title ?? 'NOVAQYS - Quality Management Suite Industrielle') ?></title>
    <meta name="description" content="NOVAQYS - Écosystème complet de développement des fabricants de pièces de rechange automobiles. Évaluation, formation, digitalisation et mise en relation industrielle.">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/novaqys.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/mobile.css') ?>">
    <?= $extraStyles ?? '' ?>
</head>
<body>
    <?php
    $currentLang = $_SESSION['lang'] ?? 'fr';
    $langLabels = ['fr' => 'Français', 'en' => 'English', 'ar' => 'العربية', 'es' => 'Español'];
    $langFlags = ['fr' => 'FR', 'en' => 'EN', 'ar' => 'AR', 'es' => 'ES'];
    ?>
    <?php if (!isset($hideLangSwitcher)): ?>
    <div style="position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;gap:0.25rem;background:rgba(255,253,248,0.9);backdrop-filter:blur(10px);border:1px solid var(--nova-border,#e7e1d7);border-radius:0.5rem;padding:0.25rem;">
      <i class="fas fa-globe" style="font-size:0.75rem;color:var(--nova-text-muted,#7d8794);align-self:center;margin:0 0.25rem;"></i>
      <?php foreach (['fr','en','ar','es'] as $l): ?>
        <a href="/lang/<?= $l ?>" style="font-size:0.7rem;font-weight:<?= $currentLang===$l?'700':'500'?>;padding:0.2rem 0.5rem;border-radius:0.3rem;text-decoration:none;color:<?= $currentLang===$l?'#fff':'var(--nova-text-primary,#17212b)'?>;background:<?= $currentLang===$l?'var(--nova-blue,#1F6FEB)':'transparent'?>;"><?= strtoupper($l) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?= $content ?? '' ?>

    <?php if (!empty($showFooter)): ?>
    <footer class="aqmi-auth-footer-layout">
      <div class="aqmi-auth-footer-inner">
        <div class="aqmi-auth-footer-copy">&copy; <?= date('Y') ?> NOVAQYS. Tous droits réservés.</div>
        <div class="aqmi-auth-footer-links">
          <a href="/">Accueil</a>
          <a href="/login">Connexion</a>
          <a href="/forgot">Mot de passe</a>
          <a href="/cgu">CGU</a>
          <a href="/privacy">Protection des données</a>
        </div>
      </div>
    </footer>
    <style>
      .aqmi-auth-footer-layout {
        background: var(--nova-bg-secondary);
        border-top: 1px solid var(--nova-border);
        padding: 2rem 0;
        position: relative;
        z-index: 1;
      }
      .aqmi-auth-footer-inner {
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
      }
      .aqmi-auth-footer-copy {
        font-size: 0.75rem;
        color: var(--nova-text-tertiary);
      }
      .aqmi-auth-footer-links {
        display: flex;
        gap: 1.5rem;
      }
      .aqmi-auth-footer-links a {
        font-size: 0.75rem;
        color: var(--nova-text-tertiary);
        text-decoration: none;
        transition: color var(--transition-fast, 0.2s);
      }
      .aqmi-auth-footer-links a:hover {
        color: var(--nova-blue);
      }
      @media (max-width: 640px) {
        .aqmi-auth-footer-inner { flex-direction: column; text-align: center; }
      }
    </style>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/mobile.js') ?>"></script>
    <script src="<?= asset('js/novaqys.js') ?>"></script>
    <?= $extraScripts ?? '' ?>
</body>
</html>