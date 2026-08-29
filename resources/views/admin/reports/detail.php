<?php
$title = 'Dossier de Certification #' . $report['id'];
ob_start();

$status = $report['status'];
$statusMeta = [
    'certification_requested' => ['label' => 'En attente de validation', 'color' => '#f59e0b', 'icon' => 'fa-hourglass'],
    'under_review'            => ['label' => 'En cours d\'examen',        'color' => '#22d3ee', 'icon' => 'fa-magnifying-glass'],
    'approved'                => ['label' => 'Approuvé',                  'color' => '#3b82f6', 'icon' => 'fa-thumbs-up'],
    'certified'               => ['label' => 'Certifié',                  'color' => '#22c55e', 'icon' => 'fa-certificate'],
    'rejected'                => ['label' => 'Rejeté',                    'color' => '#ef4444', 'icon' => 'fa-times-circle'],
];
$meta = $statusMeta[$status] ?? ['label' => $status, 'color' => '#486581', 'icon' => 'fa-circle'];

$globalScore = $analysis['global_score'] ?? 0;
$level = $analysis['maturity_level'] ?? null;
$levelColor = $level['color'] ?? '#7367f0';
$domainScores = $analysis['domain_scores'] ?? [];

$domainLabelsJson = json_encode(array_map(fn($d) => $d['domain_name_fr'] ?: $d['domain_name'], $domainScores));
$domainScoresJson = json_encode(array_map(fn($d) => round($d['percent_score']), $domainScores));
?>
<style>
.cert-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap; }
.cert-status-pill { display:inline-flex; align-items:center; gap:0.4rem; padding:0.4rem 0.9rem; border-radius:999px; font-size:0.75rem; font-weight:700; }
.cert-section { margin-bottom:1.5rem; }
.cert-section-title { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; font-weight:700; color:var(--auto-text-primary); margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:0.03em; }
.cert-info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:0.9rem; }
.cert-info-item label { display:block; font-size:0.65rem; color:var(--auto-text-muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:0.2rem; }
.cert-info-item div { font-size:0.85rem; color:var(--auto-text-primary); font-weight:600; }
.cert-gauge-wrap { display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; }
.cert-domain-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:0.75rem; margin-top:1rem; }
.cert-domain-item { background:rgba(255,255,255,0.03); border:1px solid var(--auto-border); border-radius:var(--auto-radius-sm); padding:0.75rem; text-align:center; }
.cert-answers-table { width:100%; font-size:0.78rem; }
.cert-answers-table th, .cert-answers-table td { padding:0.5rem 0.75rem; border-bottom:1px solid var(--auto-border); text-align:left; }
.cert-form label { font-size:0.75rem; font-weight:600; color:var(--auto-text-secondary); margin-bottom:0.3rem; display:block; }
.cert-form textarea, .cert-form select, .cert-form input[type=text] {
  width:100%; background:rgba(255,255,255,0.03); border:1px solid var(--auto-border); border-radius:var(--auto-radius-sm);
  color:var(--auto-text-primary); padding:0.6rem 0.8rem; font-size:0.85rem; margin-bottom:1rem;
}
.cert-actions { display:flex; gap:0.6rem; flex-wrap:wrap; margin-top:1rem; }
.cert-btn { padding:0.55rem 1.1rem; border-radius:var(--auto-radius-sm); font-size:0.8rem; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem; }
</style>

<div class="cert-header">
  <div>
    <a href="/admin/reports" style="font-size:0.75rem;color:var(--auto-text-muted);text-decoration:none;">
      <i class="fas fa-arrow-left me-1"></i> Retour aux demandes
    </a>
    <h5 style="margin:0.4rem 0 0;font-weight:700;color:var(--auto-text-primary);">
      Dossier #<?= $report['id'] ?> <?= $report['report_number'] ? '· ' . e($report['report_number']) : '' ?>
    </h5>
  </div>
  <span class="cert-status-pill" style="background:<?= $meta['color'] ?>22;color:<?= $meta['color'] ?>;">
    <i class="fas <?= $meta['icon'] ?>"></i> <?= $meta['label'] ?>
  </span>
</div>

<!-- SECTION 1 : Dossier entreprise -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-building" style="color:var(--auto-cyan);"></i> Dossier entreprise</div>
  <div class="cert-info-grid">
    <div class="cert-info-item"><label>Entreprise</label><div><?= e($lead['company'] ?? 'N/A') ?></div></div>
    <div class="cert-info-item"><label>Secteur</label><div><?= e($lead['sector'] ?? '—') ?></div></div>
    <div class="cert-info-item"><label>Pays</label><div><?= e($lead['country'] ?? '—') ?></div></div>
    <div class="cert-info-item"><label>Contact</label><div><?= e(trim(($lead['firstname'] ?? '') . ' ' . ($lead['lastname'] ?? ''))) ?: '—' ?></div></div>
    <div class="cert-info-item"><label>Téléphone</label><div><?= e($lead['phone'] ?? '—') ?></div></div>
    <div class="cert-info-item"><label>Email</label><div><?= e($lead['email'] ?? '—') ?></div></div>
    <div class="cert-info-item"><label>Date de demande</label><div><?= $report['certification_requested_at'] ? formatDate($report['certification_requested_at']) : '—' ?></div></div>
    <div class="cert-info-item"><label>Numéro de rapport</label><div><?= e($report['report_number'] ?? 'Pas encore attribué') ?></div></div>
    <?php foreach ($customFields as $cf): if (empty($cf['value'])) continue; ?>
      <div class="cert-info-item"><label><?= e($cf['label_fr'] ?: $cf['label']) ?></label><div><?= e($cf['value']) ?></div></div>
    <?php endforeach; ?>
  </div>
</div>

<!-- SECTION 2 : Résultats AQMI -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-chart-pie" style="color:var(--auto-cyan);"></i> Résultats AQMI</div>
  <div class="cert-gauge-wrap">
    <div style="position:relative;width:140px;height:140px;">
      <canvas id="gaugeChart"></canvas>
      <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <div style="font-size:1.6rem;font-weight:800;color:<?= $levelColor ?>;"><?= round($globalScore) ?>%</div>
        <div style="font-size:0.65rem;color:var(--auto-text-muted);"><?= e($level['name_fr'] ?? $level['name'] ?? 'Non défini') ?></div>
      </div>
    </div>
    <div style="flex:1;min-width:280px;position:relative;height:220px;">
      <canvas id="domainRadarChart"></canvas>
    </div>
  </div>
  <div class="cert-domain-grid">
    <?php foreach ($domainScores as $d): $pct = round($d['percent_score']); $c = $pct >= 70 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444'); ?>
      <div class="cert-domain-item">
        <div style="font-weight:800;font-size:1.1rem;color:<?= $c ?>;"><?= $pct ?>%</div>
        <div style="font-size:0.68rem;color:var(--auto-text-secondary);"><?= e($d['domain_name_fr'] ?: $d['domain_name']) ?></div>
        <div style="height:4px;background:rgba(255,255,255,0.06);border-radius:2px;margin-top:0.4rem;overflow:hidden;">
          <div style="height:100%;width:<?= $pct ?>%;background:<?= $c ?>;"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- SECTION 3 : Réponses -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-list-check" style="color:var(--auto-cyan);"></i> Réponses détaillées (<?= count($answers) ?>)</div>
  <div style="max-height:420px;overflow-y:auto;">
    <table class="cert-answers-table">
      <thead><tr><th>Domaine</th><th>Question</th><th>Réponse</th></tr></thead>
      <tbody>
        <?php foreach ($answers as $a):
          $score = (int)$a['score'];
          $label = match(true) {
            $score >= 4 => ['Oui', '#22c55e'],
            $score >= 2 => ['Partiellement', '#f59e0b'],
            $score === 0 => ['Non', '#ef4444'],
            default => ['Non concerné', '#486581'],
          };
        ?>
          <tr>
            <td style="color:var(--auto-text-muted);white-space:nowrap;"><?= e($a['domain_name_fr'] ?: $a['domain_name']) ?></td>
            <td><?= e($a['title_fr'] ?: $a['title']) ?></td>
            <td><span style="color:<?= $label[1] ?>;font-weight:700;"><?= $label[0] ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- SECTION 4 : Recommandations -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-lightbulb" style="color:var(--auto-cyan);"></i> Recommandations générées</div>
  <?php if (!empty($recommendations)): ?>
    <ul style="padding-left:1.1rem;font-size:0.82rem;color:var(--auto-text-secondary);">
      <?php foreach ($recommendations as $rec): ?>
        <li style="margin-bottom:0.5rem;"><?= e($rec['text']) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p style="color:var(--auto-text-muted);font-size:0.8rem;">Aucune recommandation générée.</p>
  <?php endif; ?>
</div>

<!-- SECTION 5 : Documents -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-paperclip" style="color:var(--auto-cyan);"></i> Documents</div>
  <p style="color:var(--auto-text-muted);font-size:0.8rem;">
    Aucun document joint pour l'instant — il n'existe pas encore de table dédiée aux pièces jointes dans le projet.
    Cette section s'activera automatiquement dès qu'un système d'upload de documents sera ajouté.
  </p>
</div>

<!-- SECTION 6 : Certification AQMI -->
<div class="auto-glass-card cert-section" style="padding:1.25rem;">
  <div class="cert-section-title"><i class="fas fa-stamp" style="color:var(--auto-cyan);"></i> Certification AQMI</div>

  <?php if ($status === 'rejected'): ?>
    <p style="color:#ef4444;font-size:0.82rem;"><i class="fas fa-circle-info me-1"></i> Cette demande a été rejetée. Motif : <?= e($report['admin_comment'] ?? '—') ?></p>

  <?php elseif ($status === 'certified'): ?>
    <p style="color:#22c55e;font-size:0.85rem;">
      <i class="fas fa-certificate me-1"></i> Certifié le <?= formatDate($report['certified_at']) ?> par <?= e($report['admin_signature'] ?? '—') ?>.
    </p>
    <div class="cert-info-grid">
      <div class="cert-info-item"><label>Niveau AQMI attribué</label><div><?= e($report['aqmi_level_assigned'] ?? '—') ?></div></div>
      <div class="cert-info-item"><label>Numéro de rapport</label><div><?= e($report['report_number']) ?></div></div>
    </div>
    <?php if ($report['observations']): ?><p style="margin-top:0.8rem;"><strong>Observations :</strong> <?= nl2br(e($report['observations'])) ?></p><?php endif; ?>
    <?php if ($report['action_plan']): ?><p><strong>Plan d'action :</strong> <?= nl2br(e($report['action_plan'])) ?></p><?php endif; ?>

    <!-- Sous-section : Vérification publique du certificat -->
    <div style="margin-top:1.2rem;padding:1rem 1.1rem;border:1px solid rgba(148,163,184,0.15);border-radius:12px;background:rgba(15,23,42,0.35);">
      <div style="font-size:0.8rem;font-weight:700;color:var(--auto-text-primary);margin-bottom:0.7rem;">
        <i class="fas fa-qrcode" style="color:var(--auto-cyan);margin-right:0.35rem;"></i> Vérification publique du certificat
      </div>

      <?php if (!empty($verifyUrl)): ?>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;margin-bottom:0.7rem;">
          <span style="font-size:0.72rem;color:var(--auto-text-tertiary);">Lien public de vérification :</span>
          <code style="font-size:0.7rem;color:var(--auto-cyan);background:rgba(34,211,238,0.08);padding:0.2rem 0.5rem;border-radius:6px;word-break:break-all;"><?= e($verifyUrl) ?></code>
          <button type="button" onclick="navigator.clipboard.writeText('<?= e($verifyUrl) ?>');this.textContent='Copié !';setTimeout(()=>this.textContent='Copier',1500);" style="font-size:0.68rem;padding:0.2rem 0.6rem;border-radius:6px;border:1px solid rgba(148,163,184,0.2);background:transparent;color:var(--auto-text-secondary);cursor:pointer;">
            <i class="fas fa-copy"></i> Copier
          </button>
          <a href="<?= e($verifyUrl) ?>" target="_blank" rel="noopener" style="font-size:0.68rem;padding:0.2rem 0.6rem;border-radius:6px;border:1px solid rgba(59,130,246,0.3);background:rgba(59,130,246,0.1);color:#60a5fa;text-decoration:none;">
            <i class="fas fa-external-link-alt"></i> Ouvrir
          </a>
        </div>

        <!-- QR code generator: shows existing QR or a generate button -->
        <div style="display:flex;flex-wrap:wrap;gap:0.8rem;align-items:center;margin-bottom:0.7rem;padding:0.6rem 0.8rem;border-radius:10px;background:rgba(15,23,42,0.4);">
          <?php if (!empty($report['qr_code_path']) && file_exists(BASE_PATH . '/storage/' . $report['qr_code_path'])): ?>
            <img src="/storage/<?= e($report['qr_code_path']) ?>" alt="QR Code de vérification" style="width:72px;height:72px;border-radius:8px;border:1px solid rgba(148,163,184,0.15);background:#fff;padding:4px;">
            <div>
              <div style="font-size:0.7rem;color:var(--auto-text-secondary);margin-bottom:0.3rem;">
                <i class="fas fa-check-circle" style="color:#22c55e;"></i> QR code généré — scannez pour ouvrir la page de vérification publique.
              </div>
              <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                <a href="/admin/reports/<?= (int)$report['id'] ?>/qr" download style="font-size:0.68rem;padding:0.25rem 0.7rem;border-radius:6px;border:1px solid rgba(34,211,238,0.3);background:rgba(34,211,238,0.1);color:var(--auto-cyan);text-decoration:none;">
                  <i class="fas fa-download"></i> Télécharger le QR
                </a>
                <a href="/admin/reports/<?= (int)$report['id'] ?>/qr" style="font-size:0.68rem;padding:0.25rem 0.7rem;border-radius:6px;border:1px solid rgba(148,163,184,0.2);background:transparent;color:var(--auto-text-secondary);text-decoration:none;">
                  <i class="fas fa-rotate"></i> Régénérer
                </a>
              </div>
            </div>
          <?php else: ?>
            <div style="width:72px;height:72px;border-radius:8px;border:1px dashed rgba(148,163,184,0.2);display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.3);">
              <i class="fas fa-qrcode" style="font-size:1.8rem;color:rgba(148,163,184,0.3);"></i>
            </div>
            <div>
              <div style="font-size:0.7rem;color:var(--auto-text-secondary);margin-bottom:0.3rem;">
                Aucun QR code généré pour ce certificat.
              </div>
              <a href="/admin/reports/<?= (int)$report['id'] ?>/qr" style="font-size:0.68rem;padding:0.25rem 0.7rem;border-radius:6px;border:1px solid rgba(34,211,238,0.3);background:rgba(34,211,238,0.1);color:var(--auto-cyan);text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;">
                <i class="fas fa-qrcode"></i> Générer le QR code
              </a>
            </div>
          <?php endif; ?>
        </div>

        <?php
        $certStatusLabels = ['active' => 'Valide', 'revoked' => 'Révoqué', 'expired' => 'Expiré', 'not_found' => '—'];
        $certStatusColors = ['active' => '#22c55e', 'revoked' => '#ef4444', 'expired' => '#f59e0b', 'not_found' => '#64748b'];
        $csl = $certStatusLabels[$certEffectiveStatus] ?? '—';
        $csc = $certStatusColors[$certEffectiveStatus] ?? '#64748b';
        ?>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;font-size:0.72rem;margin-bottom:0.8rem;">
          <div><span style="color:var(--auto-text-tertiary);">Statut :</span> <strong style="color:<?= $csc ?>;"><?= e($csl) ?></strong></div>
          <?php if (!empty($report['issued_at'])): ?><div><span style="color:var(--auto-text-tertiary);">Émis le :</span> <span style="color:var(--auto-text-secondary);"><?= formatDate($report['issued_at']) ?></span></div><?php endif; ?>
          <?php if (!empty($report['expires_at'])): ?><div><span style="color:var(--auto-text-tertiary);">Expire le :</span> <span style="color:var(--auto-text-secondary);"><?= formatDate($report['expires_at']) ?></span></div><?php endif; ?>
        </div>

        <?php if ($certEffectiveStatus === 'revoked'): ?>
          <?php if (!empty($report['revoked_reason'])): ?>
            <p style="font-size:0.72rem;color:#f87171;margin:0 0 0.7rem;"><i class="fas fa-ban"></i> Motif de révocation : <?= e($report['revoked_reason']) ?></p>
          <?php endif; ?>
          <form method="post" action="/admin/reports/<?= $report['id'] ?>/reactivate" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;">
            <div>
              <label style="font-size:0.68rem;color:var(--auto-text-tertiary);display:block;margin-bottom:0.2rem;">Nouvelle date d'expiration (optionnel)</label>
              <input type="date" name="expires_at" value="<?= e($report['expires_at'] ?? date('Y-m-d', strtotime('+1 year'))) ?>" style="font-size:0.72rem;padding:0.25rem 0.5rem;border-radius:6px;border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.6);color:var(--auto-text-primary);">
            </div>
            <button type="submit" class="cert-btn" style="background:#22c55e;color:#fff;font-size:0.72rem;" onclick="return confirm('Réactiver ce certificat ?');">
              <i class="fas fa-rotate-right"></i> Réactiver le certificat
            </button>
          </form>
        <?php elseif ($certEffectiveStatus === 'expired'): ?>
          <form method="post" action="/admin/reports/<?= $report['id'] ?>/reactivate" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;">
            <div>
              <label style="font-size:0.68rem;color:var(--auto-text-tertiary);display:block;margin-bottom:0.2rem;">Nouvelle date d'expiration</label>
              <input type="date" name="expires_at" value="<?= e(date('Y-m-d', strtotime('+1 year'))) ?>" style="font-size:0.72rem;padding:0.25rem 0.5rem;border-radius:6px;border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.6);color:var(--auto-text-primary);">
            </div>
            <button type="submit" class="cert-btn" style="background:#3b82f6;color:#fff;font-size:0.72rem;" onclick="return confirm('Renouveler ce certificat expiré ?');">
              <i class="fas fa-rotate-right"></i> Renouveler
            </button>
          </form>
        <?php else: ?>
          <form method="post" action="/admin/reports/<?= $report['id'] ?>/revoke" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
              <label style="font-size:0.68rem;color:var(--auto-text-tertiary);display:block;margin-bottom:0.2rem;">Motif de révocation (optionnel)</label>
              <input type="text" name="revoke_reason" placeholder="ex: Faux document, non-conformité..." style="width:100%;font-size:0.72rem;padding:0.25rem 0.5rem;border-radius:6px;border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.6);color:var(--auto-text-primary);">
            </div>
            <button type="submit" class="cert-btn" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.72rem;" onclick="return confirm('Révoquer ce certificat ? La page publique affichera « Certificat Révoqué ».');">
              <i class="fas fa-ban"></i> Révoquer le certificat
            </button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <p style="font-size:0.72rem;color:var(--auto-text-tertiary);margin:0;">
          <i class="fas fa-info-circle"></i> Le lien de vérification publique sera généré automatiquement lors de la certification.
        </p>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <form method="post" action="/admin/reports/<?= $report['id'] ?>/<?= $status === 'approved' ? 'certify' : 'approve' ?>" class="cert-form">
      <label>Commentaire administrateur</label>
      <textarea name="admin_comment" rows="2"><?= e($report['admin_comment'] ?? '') ?></textarea>

      <label>Observations</label>
      <textarea name="observations" rows="3"><?= e($report['observations'] ?? '') ?></textarea>

      <label>Plan d'action</label>
      <textarea name="action_plan" rows="3"><?= e($report['action_plan'] ?? '') ?></textarea>

      <label>Niveau AQMI attribué</label>
      <input type="text" name="aqmi_level_assigned" value="<?= e($report['aqmi_level_assigned'] ?? ($level['name_fr'] ?? '')) ?>" placeholder="ex: Niveau Or, Argent, Bronze...">

      <?php if ($status === 'approved'): ?>
        <label>Modèle de rapport <span style="color:#ef4444;">*</span></label>
        <select name="template_id" required>
          <option value="">— Sélectionnez un modèle —</option>
          <?php foreach ($publishedTemplates as $tpl): ?>
            <option value="<?= (int)$tpl->id ?>" <?= ((int)($report['template_id'] ?? 0) === (int)$tpl->id) ? 'selected' : '' ?>>
              <?= e($tpl->name ?? 'Modèle #' . $tpl->id) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($publishedTemplates)): ?>
          <p style="color:#f59e0b;font-size:0.75rem;margin-top:-0.5rem;margin-bottom:1rem;">
            <i class="fas fa-triangle-exclamation"></i> Aucun modèle publié. Publiez un modèle dans Report Studio avant de certifier.
          </p>
        <?php endif; ?>
      <?php endif; ?>

      <div class="cert-actions">
        <?php if ($status === 'certification_requested'): ?>
          <button type="submit" formaction="/admin/reports/<?= $report['id'] ?>/review" class="cert-btn" style="background:rgba(34,211,238,0.12);color:#22d3ee;">
            <i class="fas fa-magnifying-glass"></i> Passer en révision
          </button>
        <?php endif; ?>

        <?php if ($status === 'approved'): ?>
          <button type="submit" class="cert-btn" style="background:#22c55e;color:#fff;" <?= empty($publishedTemplates) ? 'disabled title="Aucun modèle publié disponible"' : '' ?>>
            <i class="fas fa-certificate"></i> Certifier
          </button>
        <?php else: ?>
          <button type="submit" class="cert-btn" style="background:#3b82f6;color:#fff;">
            <i class="fas fa-thumbs-up"></i> Approuver
          </button>
        <?php endif; ?>

        <button type="submit" formaction="/admin/reports/<?= $report['id'] ?>/reject" formnovalidate class="cert-btn" style="background:rgba(239,68,68,0.12);color:#ef4444;" onclick="return confirm('Rejeter cette demande ?');">
          <i class="fas fa-times"></i> Rejeter
        </button>
      </div>
    </form>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
  var gaugeEl = document.getElementById('gaugeChart');
  if (gaugeEl) {
    new Chart(gaugeEl, {
      type: 'doughnut',
      data: { datasets: [{ data: [<?= round($globalScore) ?>, <?= 100 - round($globalScore) ?>], backgroundColor: ['<?= $levelColor ?>', 'rgba(255,255,255,0.06)'], borderWidth: 0 }] },
      options: { responsive: true, maintainAspectRatio: false, cutout: '75%', circumference: 360, rotation: 0, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });
  }
  var radarEl = document.getElementById('domainRadarChart');
  if (radarEl) {
    new Chart(radarEl, {
      type: 'radar',
      data: { labels: <?= $domainLabelsJson ?>, datasets: [{ label: 'Score (%)', data: <?= $domainScoresJson ?>, borderColor: '<?= $levelColor ?>', backgroundColor: '<?= $levelColor ?>22', pointBackgroundColor: '<?= $levelColor ?>', borderWidth: 2 }] },
      options: {
        responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
        scales: { r: { min: 0, max: 100, ticks: { display: false }, grid: { color: 'rgba(255,255,255,0.06)' }, angleLines: { color: 'rgba(255,255,255,0.06)' }, pointLabels: { color: 'var(--auto-text-tertiary)', font: { size: 9 } } } }
      }
    });
  }
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
