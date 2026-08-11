<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>" dir="<?= ($_SESSION['lang'] ?? 'fr') === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($title ?? 'NOVAQYS - Administration') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/nova-admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/automotive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/effects.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-light.css') ?>">
    <?= $extraStyles ?? '' ?>
</head>
<body class="nova-admin-body">

<div class="nova-admin-wrap">
    <!-- Sidebar -->
    <aside class="nova-sidebar" id="novaSidebar">
        <!-- Brand -->
        <div class="nova-sidebar-brand">
            <div class="nova-sidebar-brand-icon">N</div>
            <span class="nova-sidebar-brand-text">NOVAQYS</span>
            <span class="nova-sidebar-brand-badge">Admin</span>
        </div>

        <!-- Navigation -->
        <nav class="nova-sidebar-nav">
            <div class="nova-sidebar-section">Navigation</div>
            <a class="nova-sidebar-link <?= isActive('/admin/dashboard') ?>" href="/admin/dashboard">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>

            <div class="nova-sidebar-section">Évaluations</div>
            <a class="nova-sidebar-link <?= isActive('/admin/evaluation-models') ?>" href="/admin/evaluation-models">
                <i class="fas fa-layer-group"></i><span>Modèles</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/domains') ?>" href="/admin/domains">
                <i class="fas fa-folder"></i><span>Domaines</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/questions') ?>" href="/admin/questions">
                <i class="fas fa-question-circle"></i><span>Questions</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/score-levels') ?>" href="/admin/score-levels">
                <i class="fas fa-layer-group"></i><span>Niveaux</span>
            </a>

            <div class="nova-sidebar-section">Amélioration</div>
            <a class="nova-sidebar-link <?= isActive('/admin/recommendations') ?>" href="/admin/recommendations">
                <i class="fas fa-lightbulb"></i><span>Recommandations</span>
            </a>

            <div class="nova-sidebar-section">Données</div>
            <a class="nova-sidebar-link <?= isActive('/admin/leads') ?>" href="/admin/leads">
                <i class="fas fa-users"></i><span>Prospects</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/lead-fields') ?>" href="/admin/lead-fields">
                <i class="fas fa-id-card"></i><span>Champs</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/reports') ?>" href="/admin/reports">
                <i class="fas fa-file-alt"></i><span>Rapports</span>
            </a>

            <div class="nova-sidebar-section">Studio</div>
            <a class="nova-sidebar-link <?= isActive('/admin/reportstudio') ?>" href="/admin/reportstudio">
                <i class="fas fa-palette"></i><span>Report Studio</span>
            </a>

            <div class="nova-sidebar-section">Système</div>
            <a class="nova-sidebar-link <?= isActive('/admin/users') ?>" href="/admin/users">
                <i class="fas fa-user-shield"></i><span>Utilisateurs</span>
            </a>
            <a class="nova-sidebar-link <?= isActive('/admin/settings') ?>" href="/admin/settings">
                <i class="fas fa-cog"></i><span>Paramètres</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="nova-sidebar-footer">
            <a class="nova-sidebar-link" href="/">
                <i class="fas fa-arrow-left"></i><span>Retour au site</span>
            </a>
        </div>
    </aside>

    <!-- Sidebar Overlay (mobile) -->
    <div class="nova-sidebar-overlay" id="novaSidebarOverlay"></div>

    <!-- Main Content -->
    <main class="nova-main">
        <!-- Topbar -->
        <header class="nova-topbar">
            <div class="nova-topbar-left">
                <button class="nova-topbar-toggle d-none d-lg-flex" id="novaSidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="nova-topbar-toggle d-lg-none" id="novaMobileToggle" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="nova-topbar-title"><?= e($title ?? 'Dashboard') ?></span>
            </div>
            <div class="nova-topbar-center"></div>
            <div class="nova-topbar-right">
                <!-- User Dropdown -->
                <div class="nova-user-dropdown" id="novaUserDropdown">
                    <button class="nova-user-btn" aria-label="User menu">
                        <span class="nova-user-avatar">
                            <?= strtoupper(substr(e($_SESSION['user_firstname'] ?? 'A'), 0, 1)) ?><?= strtoupper(substr(e($_SESSION['user_lastname'] ?? 'D'), 0, 1)) ?>
                        </span>
                        <span class="nova-user-name d-none d-md-inline">
                            <?= e($_SESSION['user_firstname'] ?? 'Admin') ?>
                        </span>
                        <i class="fas fa-chevron-down nova-user-chevron"></i>
                    </button>
                    <div class="nova-dropdown-menu">
                        <div class="nova-dropdown-header">
                            <span class="nova-user-avatar">
                                <?= strtoupper(substr(e($_SESSION['user_firstname'] ?? 'A'), 0, 1)) ?><?= strtoupper(substr(e($_SESSION['user_lastname'] ?? 'D'), 0, 1)) ?>
                            </span>
                            <div class="nova-dropdown-header-info">
                                <div class="nova-dropdown-header-name"><?= e($_SESSION['user_firstname'] ?? '') ?> <?= e($_SESSION['user_lastname'] ?? '') ?></div>
                                <div class="nova-dropdown-header-role">Administrateur</div>
                            </div>
                        </div>
                        <a class="nova-dropdown-item" href="/admin/settings">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        <div class="nova-dropdown-divider"></div>
                        <a class="nova-dropdown-item danger" href="/admin/logout">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Alerts -->
        <div style="padding: 1rem 1.5rem 0;">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="nova-alert nova-alert-success"><?= e($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="nova-alert nova-alert-error"><?= e($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="nova-content-area">
            <?= $content ?? '' ?>
        </div>
    </main>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="d-lg-none" style="position:fixed;bottom:0;left:0;right:0;z-index:1050;background:var(--vx-card-bg);border-top:1px solid var(--vx-card-border);display:flex;padding:0.35rem 0;justify-content:space-around;backdrop-filter:blur(12px);">
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:<?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/dashboard') !== false ? 'var(--vx-primary)' : 'var(--vx-text-muted)' ?>;padding:0.25rem 0.5rem;gap:0.15rem;" href="/admin/dashboard">
        <i class="fas fa-chart-pie" style="font-size:0.9rem;"></i><span>Dashboard</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:<?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/questions') !== false ? 'var(--vx-primary)' : 'var(--vx-text-muted)' ?>;padding:0.25rem 0.5rem;gap:0.15rem;" href="/admin/questions">
        <i class="fas fa-question-circle" style="font-size:0.9rem;"></i><span>Questions</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:<?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/leads') !== false ? 'var(--vx-primary)' : 'var(--vx-text-muted)' ?>;padding:0.25rem 0.5rem;gap:0.15rem;" href="/admin/leads">
        <i class="fas fa-users" style="font-size:0.9rem;"></i><span>Prospects</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:<?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/reports') !== false ? 'var(--vx-primary)' : 'var(--vx-text-muted)' ?>;padding:0.25rem 0.5rem;gap:0.15rem;" href="/admin/reports">
        <i class="fas fa-file-alt" style="font-size:0.9rem;"></i><span>Rapports</span>
    </a>
    <a class="d-flex flex-column align-items-center text-decoration-none" style="font-size:0.55rem;color:var(--vx-danger);padding:0.25rem 0.5rem;gap:0.15rem;" href="/admin/logout">
        <i class="fas fa-sign-out-alt" style="font-size:0.9rem;"></i><span>Quitter</span>
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
<script src="<?= asset('js/nova-admin.js') ?>"></script>
<script src="<?= asset('js/effects.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>