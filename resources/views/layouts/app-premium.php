<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($title ?? 'AQMI - Automotive Quality Maturity Index') ?></title>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/aqmi-premium.css">
    <?= $extraStyles ?? '' ?>
    <style>
      body {
        margin: 0;
        padding: 0;
        background: #08080e;
      }
    </style>
</head>
<body>
    <?= $content ?? '' ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?= $extraScripts ?? '' ?>
</body>
</html>