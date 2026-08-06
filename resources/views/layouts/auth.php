<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($title ?? 'NOVAQYS - Administration') ?></title>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/novaqys.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
    <style>
        .no-input-icon .aqmi-input { padding-left: 0.875rem; }
    </style>
</head>
<body>
    <div class="aqmi-auth-wrap">
        <div class="aqmi-orb aqmi-orb-1"></div>
        <div class="aqmi-orb aqmi-orb-2"></div>
        <div class="aqmi-auth-card aqmi-fade-in">
            <div class="text-center mb-4">
                <div class="aqmi-logo">N</div>
                <h2 class="aqmi-auth-title">NOVAQYS</h2>
                <p class="aqmi-auth-sub">Administration</p>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="aqmi-alert aqmi-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= e($_SESSION['error']) ?><?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="aqmi-alert aqmi-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= e($_SESSION['success']) ?><?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>