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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/novaqys.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/mobile.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/automotive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/nova-assessment.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/effects.css') ?>">
    <?= $extraStyles ?? '' ?>
    <style>
      /* ── Dark Bootstrap overrides ── */
      body {
        background: var(--nova-bg);
        color: var(--nova-text-primary);
        font-family: var(--font-primary);
      }
      .navbar {
        background: rgba(10,10,15,0.85) !important;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-bottom: 1px solid var(--nova-border) !important;
        padding: 0.75rem 0;
      }
      .navbar-brand {
        font-family: var(--font-display);
        font-weight: 800;
        color: var(--nova-text-primary) !important;
        letter-spacing: -0.02em;
      }
      .navbar-brand span { color: var(--nova-blue) !important; }
      .navbar-brand small { color: var(--nova-text-tertiary) !important; }
      .nav-link {
        color: var(--nova-text-secondary) !important;
        font-weight: 500;
        font-size: 0.875rem;
        transition: color var(--transition-fast);
        padding: 0.5rem 1rem !important;
      }
      .nav-link:hover, .nav-link.active { color: var(--nova-text-primary) !important; }
      .navbar-toggler {
        border-color: var(--nova-border) !important;
        color: var(--nova-text-secondary) !important;
      }
      .navbar-toggler-icon {
        filter: invert(0.7);
      }
      .navbar .btn-primary {
        background: var(--nova-blue) !important;
        border: none !important;
        font-weight: 600;
        font-size: 0.8125rem;
        padding: 0.5rem 1.25rem;
        box-shadow: 0 4px 20px rgba(59,130,246,0.25);
      }
      .navbar .btn-primary:hover {
        background: #60a5fa !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 30px rgba(59,130,246,0.35);
      }
      .dropdown-menu {
        background: var(--nova-bg-card) !important;
        border: 1px solid var(--nova-border) !important;
        border-radius: var(--radius-md) !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4) !important;
      }
      .dropdown-item {
        color: var(--nova-text-secondary) !important;
        font-size: 0.8125rem;
        padding: 0.5rem 1rem;
        transition: all var(--transition-fast);
      }
      .dropdown-item:hover, .dropdown-item.active {
        background: var(--nova-bg-elevated) !important;
        color: var(--nova-text-primary) !important;
      }
      .dropdown-item.active { color: var(--nova-blue) !important; }
      .text-muted { color: var(--nova-text-tertiary) !important; }
      .text-light-emphasis { color: var(--nova-text-secondary) !important; }
      .border-secondary { border-color: var(--nova-border) !important; }
      hr { border-color: var(--nova-border) !important; opacity: 1; }
      main { min-height: 100vh; }

      /* ── Modern footer ── */
      .aqmi-footer {
        background: var(--nova-bg-secondary);
        border-top: 1px solid var(--nova-border);
        padding: 4rem 0 0;
        position: relative;
        overflow: hidden;
      }
      .aqmi-footer::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--nova-blue), var(--nova-purple), transparent);
      }
      .aqmi-footer-brand {
        font-family: var(--font-display);
        font-weight: 800;
        font-size: 1.25rem;
        color: var(--nova-text-primary);
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
      }
      .aqmi-footer-brand span { color: var(--nova-blue); }
      .aqmi-footer-desc {
        font-size: 0.8125rem;
        color: var(--nova-text-tertiary);
        line-height: 1.7;
        max-width: 320px;
      }
      .aqmi-footer-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--nova-text-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
      }
      .aqmi-footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .aqmi-footer-links li { margin-bottom: 0.5rem; }
      .aqmi-footer-links a {
        color: var(--nova-text-tertiary);
        font-size: 0.8125rem;
        text-decoration: none;
        transition: color var(--transition-fast);
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
      }
      .aqmi-footer-links a:hover { color: var(--nova-blue); }
      .aqmi-footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .aqmi-footer-contact li {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.8125rem;
        color: var(--nova-text-tertiary);
        margin-bottom: 0.625rem;
      }
      .aqmi-footer-contact li i {
        width: 2rem;
        height: 2rem;
        border-radius: var(--radius-sm);
        background: var(--nova-bg-elevated);
        border: 1px solid var(--nova-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: var(--nova-blue);
        flex-shrink: 0;
      }
      .aqmi-footer-bottom {
        margin-top: 3rem;
        padding: 1.5rem 0;
        border-top: 1px solid var(--nova-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
      }
      .aqmi-footer-copy {
        font-size: 0.75rem;
        color: var(--nova-text-tertiary);
      }
      .aqmi-footer-socials {
        display: flex;
        gap: 0.5rem;
      }
      .aqmi-footer-socials a {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        background: var(--nova-bg-elevated);
        border: 1px solid var(--nova-border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--nova-text-tertiary);
        font-size: 0.8125rem;
        transition: all var(--transition-fast);
        text-decoration: none;
      }
      .aqmi-footer-socials a:hover {
        background: var(--nova-blue);
        border-color: var(--nova-blue);
        color: #fff;
        transform: translateY(-2px);
      }
      @media (max-width: 768px) {
        .aqmi-footer { padding: 3rem 0 0; }
        .aqmi-footer-bottom { flex-direction: column; text-align: center; }
      }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <span>AQMI</span>
                <small class="d-none d-md-inline fw-normal" style="font-size:0.7rem;">Automotive Quality Maturity Index</small>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link <?= isActive('/') ?>" href="/"><?= __('nav.home') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="/#methodology">Méthodologie</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#contact">Contact</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary rounded-pill px-4" href="/assessment/start">
                            <i class="fas fa-clipboard-check me-2"></i><?= __('assessment.start') ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-globe"></i> <?= strtoupper($_SESSION['lang'] ?? 'FR') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item <?= ($_SESSION['lang'] ?? '') === 'fr' ? 'active' : '' ?>" href="/lang/fr">Français</a></li>
                            <li><a class="dropdown-item <?= ($_SESSION['lang'] ?? '') === 'en' ? 'active' : '' ?>" href="/lang/en">English</a></li>
                            <li><a class="dropdown-item <?= ($_SESSION['lang'] ?? '') === 'ar' ? 'active' : '' ?>" href="/lang/ar">العربية</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Premium Footer -->
    <footer class="aqmi-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="aqmi-footer-brand"><span>A</span>QMI</div>
                    <p class="aqmi-footer-desc">Automotive Quality Maturity Index. Plateforme d'évaluation de la maturité qualité pour les fabricants de pièces automobiles.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="aqmi-footer-title">Navigation</div>
                    <ul class="aqmi-footer-links">
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/assessment/start">Questionnaire</a></li>
                        <li><a href="/#methodology">Méthodologie</a></li>
                        <li><a href="/#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="aqmi-footer-title">Plateforme</div>
                    <ul class="aqmi-footer-links">
                        <li><a href="/login"><i class="fas fa-lock" style="font-size:0.65rem;"></i> Connexion AQMI</a></li>
                        <li><a href="/assessment/start"><i class="fas fa-clipboard-check" style="font-size:0.65rem;"></i> Démarrer une évaluation</a></li>
                        <li><a href="/forgot"><i class="fas fa-key" style="font-size:0.65rem;"></i> Mot de passe oublié</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="aqmi-footer-title">Contact</div>
                    <ul class="aqmi-footer-contact">
                        <li><i class="fas fa-envelope"></i> contact@aqmi.com</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-location-dot"></i> Paris, France</li>
                    </ul>
                </div>
            </div>
            <div class="aqmi-footer-bottom">
                <div class="aqmi-footer-copy">&copy; <?= date('Y') ?> AQMI. Tous droits réservés.</div>
                <div class="aqmi-footer-socials">
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/mobile.js') ?>"></script>
    <script src="<?= asset('js/effects.js') ?>"></script>
    <?= $extraScripts ?? '' ?>
</body>
</html>