<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>" dir="<?= ($_SESSION['lang'] ?? 'fr') === 'ar' ? 'rtl' : 'ltr' ?>" data-bs-theme="dark">
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
    <?php if (isset($_SESSION['success'])): ?>
      <div style="position:fixed;top:1.25rem;left:50%;transform:translateX(-50%);z-index:100000;background:rgba(46,196,182,0.15);border:1px solid rgba(46,196,182,0.4);color:#f0f0f4;padding:0.85rem 1.5rem;border-radius:12px;font-family:'Inter',sans-serif;font-size:0.85rem;backdrop-filter:blur(12px);">
        <i class="fas fa-check-circle" style="color:#2EC4B6;margin-right:0.5rem;"></i><?= e($_SESSION['success']) ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div style="position:fixed;top:1.25rem;left:50%;transform:translateX(-50%);z-index:100000;background:rgba(229,72,77,0.15);border:1px solid rgba(229,72,77,0.4);color:#f0f0f4;padding:0.85rem 1.5rem;border-radius:12px;font-family:'Inter',sans-serif;font-size:0.85rem;backdrop-filter:blur(12px);">
        <i class="fas fa-triangle-exclamation" style="color:#E5484D;margin-right:0.5rem;"></i><?= e($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?= $content ?? '' ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?= $extraScripts ?? '' ?>
</body>
</html>