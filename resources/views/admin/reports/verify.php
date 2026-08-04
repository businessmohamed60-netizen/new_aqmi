<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification Certificat AQMI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background:#0a0a0f; color:#e5e7eb; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; }
        .verify-card { max-width:520px; margin:0 auto; background:#12121a; border:1px solid #23232f; border-radius:16px; padding:2.5rem; }
        .verify-icon { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.8rem; margin:0 auto 1.25rem; }
        .verify-row { display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #23232f; font-size:0.9rem; }
        .verify-row span:first-child { color:#9ca3af; }
        .verify-row span:last-child { font-weight:600; }
    </style>
</head>
<body>
<div class="container">
    <div class="verify-card text-center">
        <?php if ($isValid): ?>
            <div class="verify-icon" style="background:rgba(34,197,94,0.12);color:#22c55e;">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h4 class="fw-bold mb-1">Certificat authentique</h4>
            <p class="text-secondary small mb-4">Ce certificat AQMI a été vérifié et est valide.</p>

            <div class="text-start">
                <div class="verify-row"><span>Numéro</span><span><?= e($report['report_number']) ?></span></div>
                <div class="verify-row"><span>Entreprise</span><span><?= e($report['company'] ?? '—') ?></span></div>
                <div class="verify-row"><span>Pays</span><span><?= e($report['country'] ?? '—') ?></span></div>
                <div class="verify-row"><span>Niveau AQMI</span><span><?= e($report['aqmi_level_assigned'] ?? '—') ?></span></div>
                <div class="verify-row"><span>Date de certification</span><span><?= $report['certified_at'] ? date('d/m/Y', strtotime($report['certified_at'])) : '—' ?></span></div>
                <div class="verify-row" style="border-bottom:none;"><span>Statut</span><span style="color:#22c55e;"><i class="fas fa-check-circle me-1"></i>Certifié</span></div>
            </div>
        <?php else: ?>
            <div class="verify-icon" style="background:rgba(239,68,68,0.12);color:#ef4444;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <h4 class="fw-bold mb-1">Certificat introuvable</h4>
            <p class="text-secondary small mb-0">
                Aucun certificat AQMI valide ne correspond au numéro
                <strong><?= e($reportNumber) ?></strong>. Vérifiez le numéro ou contactez AQMI by NOVAQYS.
            </p>
        <?php endif; ?>

        <a href="/" class="btn btn-outline-light btn-sm rounded-pill mt-4 px-4">
            <i class="fas fa-home me-1"></i> Retour à l'accueil
        </a>
    </div>
</div>
</body>
</html>
