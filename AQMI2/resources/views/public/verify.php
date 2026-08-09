<?php
declare(strict_types=1);
/**
 * Public certificate verification page.
 * @var array|null  $report
 * @var string      $reportNumber
 * @var bool        $isValid
 */
$title = 'Vérification de Certificat AQMI';
ob_start();
?>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0a0e1a,#1a1d23);padding:2rem 1rem;font-family:'Inter',sans-serif;">
  <div style="max-width:520px;width:100%;background:#1e293b;border:1px solid rgba(148,163,184,0.12);border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
    <div style="padding:2rem 2rem 1.5rem;text-align:center;background:linear-gradient(135deg,rgba(31,111,235,0.08),rgba(6,182,212,0.05));border-bottom:1px solid rgba(148,163,184,0.1);">
      <div style="width:64px;height:64px;border-radius:50%;background:<?= $isValid ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
        <i class="fas <?= $isValid ? 'fa-circle-check' : 'fa-circle-xmark' ?>" style="font-size:1.8rem;color:<?= $isValid ? '#10B981' : '#EF4444' ?>;"></i>
      </div>
      <h1 style="font-size:1.4rem;font-weight:800;color:#F1F5F9;margin:0 0 0.35rem;">
        <?= $isValid ? 'Certificat Valide' : 'Certificat Introuvable' ?>
      </h1>
      <p style="font-size:0.82rem;color:#94A3B8;margin:0;">
        <?= $isValid ? 'Ce certificat AQMI a été vérifié avec succès.' : 'Aucun certificat ne correspond à ce numéro.' ?>
      </p>
    </div>
    <?php if ($isValid && $report): ?>
    <div style="padding:1.5rem 2rem;">
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">N° Rapport</span>
        <span style="font-size:0.78rem;font-weight:700;color:#F1F5F9;font-family:monospace;"><?= e($report['report_number'] ?? $reportNumber) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Entreprise</span>
        <span style="font-size:0.78rem;font-weight:600;color:#F1F5F9;"><?= e($report['company'] ?? 'N/A') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Pays</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= e($report['country'] ?? '-') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;">
        <span style="font-size:0.78rem;color:#64748B;">Date de certification</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= formatDate($report['certified_at'] ?? $report['generated_at'] ?? null) ?></span>
      </div>
    </div>
    <?php else: ?>
    <div style="padding:1.5rem 2rem;text-align:center;">
      <p style="font-size:0.78rem;color:#64748B;margin:0;">Numéro recherché : <code style="color:#94A3B8;font-family:monospace;"><?= e($reportNumber) ?></code></p>
    </div>
    <?php endif; ?>
    <div style="padding:1rem 2rem 1.5rem;text-align:center;background:rgba(15,23,42,0.4);border-top:1px solid rgba(148,163,184,0.08);">
      <a href="/" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.75rem;font-weight:600;color:#3B82F6;text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
      </a>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?= $content ?>
</body>
</html>
