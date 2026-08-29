<?php
declare(strict_types=1);
/**
 * Public certificate verification page.
 * Accessible via QR code on the official PDF (/c/{token}) or legacy /verify/{report_number}.
 * Shows ONLY non-sensitive verification info — never detailed results or contact data.
 *
 * @var array|null  $report
 * @var string      $verifyToken
 * @var string      $effectiveStatus   active|revoked|expired|not_found
 * @var bool        $isLegacy
 */
$title = 'Vérification de Certificat AQMI';

$statusConfig = [
    'active'    => ['label' => 'Certificat Valide',       'icon' => 'fa-circle-check',       'color' => '#10B981', 'bg' => 'rgba(16,185,129,0.12)'],
    'revoked'   => ['label' => 'Certificat Révoqué',      'icon' => 'fa-circle-xmark',       'color' => '#EF4444', 'bg' => 'rgba(239,68,68,0.12)'],
    'expired'   => ['label' => 'Certificat Expiré',       'icon' => 'fa-clock',              'color' => '#F59E0B', 'bg' => 'rgba(245,158,11,0.12)'],
    'not_found' => ['label' => 'Certificat Introuvable',  'icon' => 'fa-circle-question',    'color' => '#94A3B8', 'bg' => 'rgba(148,163,184,0.12)'],
];
$sc = $statusConfig[$effectiveStatus] ?? $statusConfig['not_found'];

$showInfo = $report !== null && $effectiveStatus !== 'not_found';
ob_start();
?>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0a0e1a,#1a1d23);padding:2rem 1rem;font-family:'Inter',sans-serif;">
  <div style="max-width:560px;width:100%;background:#1e293b;border:1px solid rgba(148,163,184,0.12);border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.5);">

    <!-- Header with status -->
    <div style="padding:2rem 2rem 1.5rem;text-align:center;background:linear-gradient(135deg,<?= $sc['bg'] ?>,rgba(6,182,212,0.04));border-bottom:1px solid rgba(148,163,184,0.1);">
      <div style="width:68px;height:68px;border-radius:50%;background:<?= $sc['bg'] ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
        <i class="fas <?= $sc['icon'] ?>" style="font-size:2rem;color:<?= $sc['color'] ?>;"></i>
      </div>
      <h1 style="font-size:1.45rem;font-weight:800;color:#F1F5F9;margin:0 0 0.35rem;">
        <?= $sc['label'] ?>
      </h1>
      <p style="font-size:0.82rem;color:#94A3B8;margin:0;">
        <?php if ($effectiveStatus === 'active'): ?>
          Ce certificat AQMI a été vérifié avec succès et est actuellement valide.
        <?php elseif ($effectiveStatus === 'revoked'): ?>
          Ce certificat a été révoqué par l'autorité de certification. Il n'est plus valide.
        <?php elseif ($effectiveStatus === 'expired'): ?>
          La période de validité de ce certificat est dépassée. Il n'est plus valide.
        <?php else: ?>
          Aucun certificat ne correspond à ce code de vérification.
        <?php endif; ?>
      </p>
    </div>

    <!-- Certificate info (only if found) -->
    <?php if ($showInfo): ?>
    <div style="padding:1.5rem 2rem;">
      <!-- Status banner for revoked/expired -->
      <?php if ($effectiveStatus === 'revoked'): ?>
        <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:0.8rem 1rem;margin-bottom:1.2rem;text-align:center;">
          <i class="fas fa-ban" style="color:#EF4444;margin-right:0.4rem;"></i>
          <span style="font-size:0.8rem;font-weight:700;color:#EF4444;">CERTIFICAT RÉVOQUÉ</span>
          <?php if (!empty($report['revoked_reason'])): ?>
            <div style="font-size:0.72rem;color:#94A3B8;margin-top:0.4rem;">Motif : <?= e($report['revoked_reason']) ?></div>
          <?php endif; ?>
          <?php if (!empty($report['revoked_at'])): ?>
            <div style="font-size:0.72rem;color:#64748B;margin-top:0.2rem;">Révoqué le <?= formatDate($report['revoked_at']) ?></div>
          <?php endif; ?>
        </div>
      <?php elseif ($effectiveStatus === 'expired'): ?>
        <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:10px;padding:0.8rem 1rem;margin-bottom:1.2rem;text-align:center;">
          <i class="fas fa-clock" style="color:#F59E0B;margin-right:0.4rem;"></i>
          <span style="font-size:0.8rem;font-weight:700;color:#F59E0B;">CERTIFICAT EXPIRÉ</span>
          <?php if (!empty($report['expires_at'])): ?>
            <div style="font-size:0.72rem;color:#94A3B8;margin-top:0.4rem;">Date d'expiration : <?= formatDate($report['expires_at']) ?></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- Verification details -->
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">N° Certificat</span>
        <span style="font-size:0.78rem;font-weight:700;color:#F1F5F9;font-family:monospace;"><?= e($report['report_number'] ?? $verifyToken) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Statut</span>
        <span style="font-size:0.78rem;font-weight:700;color:<?= $sc['color'] ?>;">
          <?php
          $statusLabel = ['active' => 'Valide', 'revoked' => 'Révoqué', 'expired' => 'Expiré'];
          echo e($statusLabel[$effectiveStatus] ?? 'Inconnu');
          ?>
        </span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Organisme</span>
        <span style="font-size:0.78rem;font-weight:600;color:#F1F5F9;"><?= e($report['company'] ?? 'N/A') ?></span>
      </div>
      <?php if (!empty($report['sector'])): ?>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Secteur</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= e($report['sector']) ?></span>
      </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Pays</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= e($report['country'] ?? '-') ?></span>
      </div>
      <?php if (!empty($report['aqmi_level_assigned'])): ?>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Niveau AQMI</span>
        <span style="font-size:0.78rem;font-weight:600;color:#F1F5F9;"><?= e($report['aqmi_level_assigned']) ?></span>
      </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;border-bottom:1px solid rgba(148,163,184,0.08);">
        <span style="font-size:0.78rem;color:#64748B;">Date d'émission</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= formatDate($report['issued_at'] ?? $report['certified_at'] ?? null) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.65rem 0;">
        <span style="font-size:0.78rem;color:#64748B;">Date d'expiration</span>
        <span style="font-size:0.78rem;color:#F1F5F9;"><?= !empty($report['expires_at']) ? formatDate($report['expires_at']) : '—' ?></span>
      </div>
    </div>
    <?php else: ?>
    <div style="padding:1.5rem 2rem;text-align:center;">
      <p style="font-size:0.78rem;color:#64748B;margin:0;">
        Code recherché : <code style="color:#94A3B8;font-family:monospace;"><?= e($verifyToken) ?></code>
      </p>
      <?php if ($isLegacy): ?>
      <p style="font-size:0.72rem;color:#475569;margin-top:0.8rem;">
        Si vous disposez du QR code d'origine, scannez-le pour accéder à la page de vérification à jour.
      </p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Security note -->
    <div style="padding:1rem 2rem 0.5rem;text-align:center;">
      <p style="font-size:0.68rem;color:#475569;margin:0;line-height:1.5;">
        <i class="fas fa-shield-halved" style="margin-right:0.3rem;"></i>
        Cette vérification est publique et ne révèle aucune donnée confidentielle.
        Les résultats détaillés sont accessibles uniquement aux utilisateurs autorisés après authentification.
      </p>
    </div>

    <!-- Footer -->
    <div style="padding:1rem 2rem 1.5rem;text-align:center;background:rgba(15,23,42,0.4);border-top:1px solid rgba(148,163,184,0.08);">
      <a href="/" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.75rem;font-weight:600;color:#3B82F6;text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
      </a>
      <span style="display:inline-block;margin:0 0.6rem;color:#334155;">|</span>
      <span style="font-size:0.7rem;color:#475569;">AQMI by NOVAQYS</span>
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
