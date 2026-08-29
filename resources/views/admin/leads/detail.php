<?php
$title = 'Fiche Prospect : ' . e($lead['firstname'] ?? '') . ' ' . e($lead['lastname'] ?? '');
ob_start();
$scoreColor = '#7d8794';
$scoreBg = 'rgba(125,135,148,.1)';
if ($assessment && isset($assessment['total_score'])) {
    $s = (float)$assessment['total_score'];
    if ($s >= 71) { $scoreColor = '#0d6e3e'; $scoreBg = 'rgba(13,110,62,.1)'; }
    elseif ($s >= 51) { $scoreColor = '#1F6FEB'; $scoreBg = 'rgba(31,111,235,.1)'; }
    elseif ($s >= 31) { $scoreColor = '#d97706'; $scoreBg = 'rgba(217,119,6,.1)'; }
    else { $scoreColor = '#dc2626'; $scoreBg = 'rgba(220,38,38,.1)'; }
}
$groupedFields = [];
foreach ($customFields as $cf) {
    $groupedFields[$cf['section']][] = $cf;
}
$initials = strtoupper(substr($lead['firstname'] ?? '', 0, 1)) . strtoupper(substr($lead['lastname'] ?? '', 0, 1));
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
  .fiche-page { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }

  /* ── Header ── */
  .fiche-topbar {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
  }
  .fiche-topbar h4 {
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;
    font-size: 1.4rem; color: var(--auto-text-primary); margin: 0; letter-spacing: -0.02em;
  }
  .fiche-topbar .fiche-back-btn {
    padding: 0.5rem 1.1rem; border-radius: 8px; font-size: 0.78rem;
    font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    background: transparent; border: 1px solid var(--vx-input-border);
    color: var(--auto-text-secondary); text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.4rem; transition: var(--auto-transition);
  }
  .fiche-topbar .fiche-back-btn:hover { border-color: var(--auto-cyan); color: var(--auto-cyan); background: var(--auto-cyan-dim); }

  /* ── Cards ── */
  .fiche-card {
    background: var(--auto-bg-card); border: 1px solid var(--auto-border);
    border-radius: var(--auto-radius-md); padding: 1.5rem;
    margin-bottom: 1.25rem; box-shadow: 0 4px 16px rgba(80,64,42,.06);
  }
  .fiche-card-title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
    font-size: 0.85rem; color: var(--auto-text-primary);
    margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;
    padding-bottom: 0.85rem; border-bottom: 1px solid var(--vx-divider);
  }
  .fiche-card-title i { color: var(--auto-cyan); font-size: 0.9rem; }

  /* ── Profile Card ── */
  .fiche-profile {
    text-align: center; padding: 2rem 1.5rem;
  }
  .fiche-profile .fiche-avatar {
    width: 80px; height: 80px; border-radius: 20px;
    background: linear-gradient(135deg, var(--auto-cyan), #4f46e5);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.6rem; font-weight: 800;
    margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(31,111,235,.25);
  }
  .fiche-profile h5 {
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;
    font-size: 1.15rem; color: var(--auto-text-primary); margin: 0 0 0.25rem;
  }
  .fiche-profile .fiche-job {
    font-size: 0.78rem; color: var(--auto-text-muted); margin-bottom: 0.85rem;
  }
  .fiche-profile .fiche-badges {
    display: flex; gap: 0.4rem; flex-wrap: wrap; justify-content: center; margin-bottom: 1.25rem;
  }
  .fiche-profile .fiche-badge {
    padding: 0.25rem 0.7rem; border-radius: 20px; font-size: 0.68rem;
    font-weight: 600; background: var(--auto-cyan-dim); color: var(--auto-cyan);
  }
  .fiche-profile .fiche-contact-list { text-align: left; }
  .fiche-profile .fiche-contact-line {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.55rem 0; border-bottom: 1px solid var(--vx-divider);
    font-size: 0.8rem;
  }
  .fiche-profile .fiche-contact-line:last-child { border-bottom: none; }
  .fiche-profile .fiche-contact-line i {
    width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,.03); color: var(--auto-text-muted); font-size: 0.78rem;
  }
  .fiche-profile .fiche-contact-line strong { color: var(--auto-text-primary); font-weight: 600; }

  /* ── Score Card ── */
  .fiche-score-card { text-align: center; padding: 1.75rem 1.5rem; }
  .fiche-score-circle {
    width: 130px; height: 130px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem; position: relative;
    background: conic-gradient(<?= $scoreColor ?> <?= ($assessment['total_score'] ?? 0) * 3.6 ?>deg, <?= $scoreBg ?> 0deg);
  }
  .fiche-score-circle::before {
    content: ''; position: absolute; inset: 8px; border-radius: 50%; background: var(--auto-bg-card);
  }
  .fiche-score-circle .fiche-score-num {
    position: relative; font-size: 2.2rem; font-weight: 800;
    color: <?= $scoreColor ?>; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.02em;
  }
  .fiche-score-label { font-size: 0.72rem; color: var(--auto-text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
  .fiche-maturity-badge {
    display: inline-block; margin-top: 0.75rem; padding: 0.3rem 0.85rem;
    border-radius: 20px; font-size: 0.72rem; font-weight: 700;
    background: <?= $scoreBg ?>; color: <?= $scoreColor ?>;
  }

  /* ── Info Grid ── */
  .fiche-info-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;
  }
  .fiche-info-item .fiche-field-label {
    font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--auto-text-muted); font-weight: 600; margin-bottom: 0.3rem;
  }
  .fiche-info-item .fiche-field-value {
    font-size: 0.82rem; color: var(--auto-text-primary); font-weight: 600;
  }
  .fiche-info-item .fiche-field-value.empty { color: var(--auto-text-muted); font-weight: 400; font-style: italic; }
  .fiche-info-item.full { grid-column: 1 / -1; }

  /* ── Edit Form ── */
  .fiche-form-section { margin-top: 0.5rem; }
  .fiche-form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
  .fiche-form-grid .full { grid-column: 1 / -1; }
  .fiche-form-group label {
    display: block; font-size: 0.72rem; font-weight: 600;
    color: var(--auto-text-secondary); margin-bottom: 0.35rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .fiche-form-group label .req { color: #dc2626; }
  .fiche-form-group input,
  .fiche-form-group select,
  .fiche-form-group textarea {
    width: 100%; padding: 0.55rem 0.85rem; font-size: 0.8rem;
    border-radius: 8px; border: 1px solid var(--vx-input-border);
    background: var(--vx-input-bg); color: var(--auto-text-primary);
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 500;
    transition: var(--auto-transition);
  }
  .fiche-form-group input:hover,
  .fiche-form-group select:hover,
  .fiche-form-group textarea:hover { border-color: var(--auto-cyan-glow); }
  .fiche-form-group input:focus,
  .fiche-form-group select:focus,
  .fiche-form-group textarea:focus {
    border-color: var(--auto-cyan); outline: none;
    box-shadow: 0 0 0 3px var(--auto-cyan-dim);
  }
  .fiche-form-group input:invalid:not(:placeholder-shown),
  .fiche-form-group select:invalid,
  .fiche-form-group textarea:invalid:not(:placeholder-shown) {
    border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.08);
  }
  .fiche-form-actions {
    display: flex; gap: 0.75rem; margin-top: 1.5rem;
    padding-top: 1.25rem; border-top: 1px solid var(--vx-divider);
  }
  .fiche-btn-save {
    padding: 0.6rem 1.5rem; border-radius: 8px; font-size: 0.8rem;
    font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--auto-cyan); color: #fff; border: none; cursor: pointer;
    transition: var(--auto-transition); display: inline-flex; align-items: center; gap: 0.4rem;
  }
  .fiche-btn-save:hover { background: #1558c4; transform: translateY(-1px); box-shadow: 0 4px 12px var(--auto-cyan-glow); }
  .fiche-btn-cancel {
    padding: 0.6rem 1.5rem; border-radius: 8px; font-size: 0.8rem;
    font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    background: transparent; border: 1px solid var(--vx-input-border);
    color: var(--auto-text-secondary); text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.4rem; transition: var(--auto-transition);
  }
  .fiche-btn-cancel:hover { border-color: var(--auto-cyan); color: var(--auto-cyan); }
  .fiche-section-divider {
    border: none; border-top: 1px solid var(--vx-divider); margin: 1.5rem 0;
  }

  /* ── Documents ── */
  .fiche-doc-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.65rem 0; border-bottom: 1px solid var(--vx-divider);
  }
  .fiche-doc-item:last-child { border-bottom: none; }
  .fiche-doc-item .doc-info { display: flex; align-items: center; gap: 0.6rem; }
  .fiche-doc-item .doc-icon {
    width: 36px; height: 36px; border-radius: 8px;
    background: rgba(31,111,235,.08); color: var(--auto-cyan);
    display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
  }
  .fiche-doc-item .doc-name { font-size: 0.8rem; color: var(--auto-text-primary); font-weight: 600; }
  .fiche-doc-item .doc-size { font-size: 0.7rem; color: var(--auto-text-muted); }

  @media (max-width: 768px) {
    .fiche-info-grid, .fiche-form-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="fiche-page auto-fade-in">
  <!-- Topbar -->
  <div class="fiche-topbar">
    <h4><i class="fas fa-id-card me-2" style="color:var(--auto-cyan);"></i>Fiche Prospect</h4>
    <a href="/admin/leads" class="fiche-back-btn"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
  </div>

  <div class="row">
    <!-- Left Column -->
    <div class="col-lg-4">
      <!-- Profile Card -->
      <div class="fiche-card fiche-profile">
        <div class="fiche-avatar"><?= $initials ?></div>
        <h5><?= e($lead['firstname'] ?? '') ?> <?= e($lead['lastname'] ?? '') ?></h5>
        <p class="fiche-job"><?= e($lead['job_title'] ?? 'Fonction non renseignée') ?></p>
        <div class="fiche-badges">
          <?php if ($lead['sector']): ?><span class="fiche-badge"><?= e($lead['sector']) ?></span><?php endif; ?>
          <?php if ($lead['country']): ?><span class="fiche-badge"><?= e($lead['country']) ?></span><?php endif; ?>
        </div>
        <div class="fiche-contact-list">
          <div class="fiche-contact-line">
            <i class="fas fa-building"></i>
            <div><div style="font-size:0.65rem;color:var(--auto-text-muted);">Entreprise</div><strong><?= e($lead['company'] ?? '—') ?></strong></div>
          </div>
          <div class="fiche-contact-line">
            <i class="fas fa-envelope"></i>
            <div><div style="font-size:0.65rem;color:var(--auto-text-muted);">Email</div><strong><?= e($lead['email'] ?? '—') ?></strong></div>
          </div>
          <div class="fiche-contact-line">
            <i class="fas fa-phone"></i>
            <div><div style="font-size:0.65rem;color:var(--auto-text-muted);">Téléphone</div><strong><?= e($lead['phone'] ?? 'Non renseigné') ?></strong></div>
          </div>
          <?php if ($lead['website']): ?>
          <div class="fiche-contact-line">
            <i class="fas fa-globe"></i>
            <div><div style="font-size:0.65rem;color:var(--auto-text-muted);">Site web</div><strong><?= e($lead['website']) ?></strong></div>
          </div>
          <?php endif; ?>
          <div class="fiche-contact-line">
            <i class="fas fa-calendar"></i>
            <div><div style="font-size:0.65rem;color:var(--auto-text-muted);">Inscrit le</div><strong><?= date('d/m/Y', strtotime($lead['created_at'])) ?></strong></div>
          </div>
        </div>
      </div>

      <!-- Score Card -->
      <?php if ($assessment && isset($assessment['total_score'])): ?>
      <div class="fiche-card fiche-score-card">
        <div class="fiche-card-title" style="justify-content:center;border:none;padding-bottom:0;">
          <i class="fas fa-chart-pie"></i> Évaluation AQMI
        </div>
        <div class="fiche-score-circle">
          <span class="fiche-score-num"><?= (float)$assessment['total_score'] ?>%</span>
        </div>
        <div class="fiche-score-label">Score Global</div>
        <?php if ($assessment['maturity_level']): ?>
          <div class="fiche-maturity-badge"><?= e($assessment['maturity_level']) ?></div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
      <!-- Fiche Technique: Identité & Entreprise -->
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-building"></i> Informations Entreprise</div>
        <div class="fiche-info-grid">
          <div class="fiche-info-item">
            <div class="fiche-field-label">Entreprise</div>
            <div class="fiche-field-value"><?= e($lead['company'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Taille de l'entreprise</div>
            <div class="fiche-field-value"><?= e($lead['company_size'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Secteur d'activité</div>
            <div class="fiche-field-value"><?= e($lead['sector'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Catégorie d'activité</div>
            <div class="fiche-field-value"><?= e($lead['activity_category'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Niveau OEM</div>
            <div class="fiche-field-value"><?= e($lead['oem_tier'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Catégorie de produits</div>
            <div class="fiche-field-value"><?= e($lead['product_category'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Pays</div>
            <div class="fiche-field-value"><?= e($lead['country'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Année de création</div>
            <div class="fiche-field-value"><?= e($lead['founded_year'] ?? '') ?: '<span class="empty">Non renseignée</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Site web</div>
            <div class="fiche-field-value"><?= e($lead['website'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">CA annuel</div>
            <div class="fiche-field-value"><?= e($lead['annual_revenue'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Part à l'export (%)</div>
            <div class="fiche-field-value"><?= e($lead['export_percentage'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Principaux clients / OEM</div>
            <div class="fiche-field-value"><?= e($lead['main_clients'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Sites de production</div>
            <div class="fiche-field-value"><?= e($lead['production_sites'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Effectif production</div>
            <div class="fiche-field-value"><?= e($lead['workforce_production'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Effectif ingénierie / R&D</div>
            <div class="fiche-field-value"><?= e($lead['workforce_engineering'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
        </div>
      </div>

      <!-- Fiche Technique: Production -->
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-industry"></i> Production & Équipement</div>
        <div class="fiche-info-grid">
          <div class="fiche-info-item">
            <div class="fiche-field-label">Type de production</div>
            <div class="fiche-field-value"><?= e($lead['production_type'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Capacité de production</div>
            <div class="fiche-field-value"><?= e($lead['production_capacity'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Nombre de machines</div>
            <div class="fiche-field-value"><?= e($lead['machine_count'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Types de machines</div>
            <div class="fiche-field-value"><?= e($lead['machine_types'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Matières principales</div>
            <div class="fiche-field-value"><?= e($lead['main_materials'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Technologies de procédé</div>
            <div class="fiche-field-value"><?= e($lead['process_technologies'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
        </div>
      </div>

      <!-- Fiche Technique: Qualité & Performance -->
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-clipboard-check"></i> Qualité & Performance</div>
        <div class="fiche-info-grid">
          <div class="fiche-info-item full">
            <div class="fiche-field-label">Certifications détenues</div>
            <div class="fiche-field-value"><?= e($lead['certifications'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Objectif PPM</div>
            <div class="fiche-field-value"><?= e($lead['ppm_target'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Taux OTD (%)</div>
            <div class="fiche-field-value"><?= e($lead['otd_rate'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Taux FTA (%)</div>
            <div class="fiche-field-value"><?= e($lead['fta_rate'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Taux de rebut (%)</div>
            <div class="fiche-field-value"><?= e($lead['scrap_rate'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
        </div>
      </div>

      <!-- Fiche Technique: Systèmes & Ingénierie -->
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-laptop-code"></i> Systèmes & Ingénierie</div>
        <div class="fiche-info-grid">
          <div class="fiche-info-item">
            <div class="fiche-field-label">Système de traçabilité</div>
            <div class="fiche-field-value"><?= e($lead['traceability_system'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Système logistique</div>
            <div class="fiche-field-value"><?= e($lead['logistics_system'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">Budget R&D (% du CA)</div>
            <div class="fiche-field-value"><?= e($lead['rd_budget_percent'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
          <div class="fiche-info-item">
            <div class="fiche-field-label">ERP actuel</div>
            <div class="fiche-field-value"><?= e($lead['current_erp'] ?? '') ?: '<span class="empty">Non renseigné</span>' ?></div>
          </div>
        </div>
      </div>

      <!-- Custom Fields by Section -->
      <?php foreach ($groupedFields as $section => $fields): ?>
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-id-card"></i>
          <?php
          $sectionLabels = [
            'General' => 'Général', 'Company' => 'Entreprise', 'Industrial' => 'Industriel',
            'Certifications' => 'Certifications', 'Contact' => 'Contact', 'Engagement' => 'Engagement',
          ];
          echo e($sectionLabels[ucfirst($section)] ?? $section);
          ?>
        </div>
        <div class="fiche-info-grid">
          <?php foreach ($fields as $cf): ?>
            <?php
            $val = $cf['value'] ?? '';
            if ($cf['field_type'] === 'multiselect' && $val) {
                $decoded = json_decode($val, true);
                if (is_array($decoded)) $val = implode(', ', $decoded);
            }
            if ($cf['field_type'] === 'file' && $val) {
                $val = '<a href="/storage/leads/' . $lead['id'] . '/' . e($val) . '" target="_blank" style="color:var(--auto-cyan);font-weight:600;text-decoration:none;"><i class="fas fa-download me-1"></i>Voir le fichier</a>';
            }
            ?>
            <div class="fiche-info-item">
              <div class="fiche-field-label"><?= e($cf['label_fr'] ?: $cf['label']) ?></div>
              <div class="fiche-field-value"><?= $val ?: '<span class="empty">Non renseigné</span>' ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Documents -->
      <?php if (!empty($documents)): ?>
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-paperclip"></i> Documents joints</div>
        <?php foreach ($documents as $doc): ?>
          <div class="fiche-doc-item">
            <div class="doc-info">
              <div class="doc-icon"><i class="fas fa-file"></i></div>
              <div>
                <div class="doc-name"><?= e($doc['original_name']) ?></div>
                <div class="doc-size"><?= round($doc['file_size'] / 1024) ?> KB</div>
              </div>
            </div>
            <a href="/storage/leads/<?= $lead['id'] ?>/<?= e($doc['filename']) ?>" class="fiche-btn-cancel" download>
              <i class="fas fa-download"></i> Télécharger
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Edit Form -->
      <div class="fiche-card">
        <div class="fiche-card-title"><i class="fas fa-edit"></i> Modifier les informations</div>
        <form method="POST" action="/admin/leads/save-fields/<?= $lead['id'] ?>" enctype="multipart/form-data" id="leadEditForm" novalidate>
          <?= csrf_field() ?>

          <div class="fiche-form-grid">
            <div class="fiche-form-group">
              <label>Taille de l'entreprise <span class="req">*</span></label>
              <select name="company_size" required>
                <option value="">Sélectionnez...</option>
                <option value="1-10" <?= ($lead['company_size'] ?? '') === '1-10' ? 'selected' : '' ?>>1-10 employés</option>
                <option value="11-50" <?= ($lead['company_size'] ?? '') === '11-50' ? 'selected' : '' ?>>11-50 employés</option>
                <option value="51-200" <?= ($lead['company_size'] ?? '') === '51-200' ? 'selected' : '' ?>>51-200 employés</option>
                <option value="201-500" <?= ($lead['company_size'] ?? '') === '201-500' ? 'selected' : '' ?>>201-500 employés</option>
                <option value="501-1000" <?= ($lead['company_size'] ?? '') === '501-1000' ? 'selected' : '' ?>>501-1000 employés</option>
                <option value="1000+" <?= ($lead['company_size'] ?? '') === '1000+' ? 'selected' : '' ?>>1000+ employés</option>
              </select>
            </div>
            <div class="fiche-form-group">
              <label>Site web <span class="req">*</span></label>
              <input type="url" name="website" placeholder="https://www.example.com" value="<?= e($lead['website'] ?? '') ?>" required>
            </div>
            <div class="fiche-form-group">
              <label>Année de création <span class="req">*</span></label>
              <input type="number" name="founded_year" placeholder="Ex: 2010" min="1900" max="2030" value="<?= e($lead['founded_year'] ?? '') ?>" required>
            </div>
            <div class="fiche-form-group">
              <label>Type de production <span class="req">*</span></label>
              <select name="production_type" required>
                <option value="">Sélectionnez...</option>
                <option value="mass" <?= ($lead['production_type'] ?? '') === 'mass' ? 'selected' : '' ?>>Production en série</option>
                <option value="batch" <?= ($lead['production_type'] ?? '') === 'batch' ? 'selected' : '' ?>>Production par lots</option>
                <option value="unit" <?= ($lead['production_type'] ?? '') === 'unit' ? 'selected' : '' ?>>Production unitaire</option>
                <option value="mixed" <?= ($lead['production_type'] ?? '') === 'mixed' ? 'selected' : '' ?>>Mixte</option>
              </select>
            </div>
            <div class="fiche-form-group full">
              <label>Certifications <span class="req">*</span></label>
              <input type="text" name="certifications" placeholder="Ex: ISO 9001, IATF 16949, ISO 14001" value="<?= e($lead['certifications'] ?? '') ?>" required>
            </div>
            <div class="fiche-form-group full">
              <label>Notes</label>
              <textarea name="notes" rows="3" placeholder="Notes internes sur ce prospect..."><?= e($lead['notes'] ?? '') ?></textarea>
            </div>
          </div>

          <?php if (!empty($customFields)): ?>
          <hr class="fiche-section-divider">
          <div class="fiche-card-title" style="border:none;padding-bottom:0;margin-bottom:1rem;"><i class="fas fa-sliders-h"></i> Champs personnalisés</div>
          <div class="fiche-form-grid">
            <?php foreach ($customFields as $cf): ?>
              <?php
              $val = $cf['value'] ?? '';
              $fieldName = 'custom_fields[' . $cf['field_id'] . ']';
              $fieldId = 'cf_' . $cf['field_id'];
              $placeholder = $cf['placeholder_fr'] ?: $cf['placeholder'] ?: '';
              $required = $cf['is_required'] ? 'required' : '';
              ?>
              <div class="fiche-form-group">
                <label for="<?= $fieldId ?>">
                  <?= e($cf['label_fr'] ?: $cf['label']) ?>
                  <?php if ($cf['is_required']): ?><span class="req">*</span><?php endif; ?>
                </label>
                <?php if ($cf['field_type'] === 'text'): ?>
                  <input type="text" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e($val) ?>" <?= $required ?>>
                <?php elseif ($cf['field_type'] === 'textarea'): ?>
                  <textarea name="<?= $fieldName ?>" id="<?= $fieldId ?>" rows="2" placeholder="<?= e($placeholder) ?>" <?= $required ?>><?= e($val) ?></textarea>
                <?php elseif ($cf['field_type'] === 'number'): ?>
                  <input type="number" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e($val) ?>" <?= $required ?>>
                <?php elseif ($cf['field_type'] === 'phone'): ?>
                  <input type="tel" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" value="<?= e($val) ?>" <?= $required ?>>
                <?php elseif ($cf['field_type'] === 'date'): ?>
                  <input type="date" name="<?= $fieldName ?>" id="<?= $fieldId ?>" value="<?= e($val) ?>" <?= $required ?>>
                <?php elseif ($cf['field_type'] === 'select'): ?>
                  <select name="<?= $fieldName ?>" id="<?= $fieldId ?>" <?= $required ?>>
                    <option value=""><?= e($placeholder ?: 'Sélectionnez...') ?></option>
                    <?php $opts = json_decode($cf['options'] ?? '[]', true); ?>
                    <?php foreach ($opts as $opt): ?>
                      <option value="<?= e($opt['value']) ?>" <?= $val === $opt['value'] ? 'selected' : '' ?>><?= e($opt['label']) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php elseif ($cf['field_type'] === 'multiselect'): ?>
                  <?php $selectedVals = json_decode($val, true) ?: []; ?>
                  <div style="max-height:120px;overflow-y:auto;border:1px solid var(--vx-input-border);border-radius:8px;padding:0.5rem;">
                    <?php $opts = json_decode($cf['options'] ?? '[]', true); ?>
                    <?php foreach ($opts as $i => $opt): ?>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="<?= $fieldName ?>[]" value="<?= e($opt['value']) ?>" id="<?= $fieldId . '_' . $i ?>"
                          <?= in_array($opt['value'], $selectedVals) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="<?= $fieldId . '_' . $i ?>" style="font-size:0.8rem;"><?= e($opt['label']) ?></label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php elseif ($cf['field_type'] === 'file'): ?>
                  <input type="file" name="documents[]" <?= $cf['is_required'] && !$val ? 'required' : '' ?>>
                  <input type="hidden" name="doc_field_id" value="<?= $cf['field_id'] ?>">
                  <?php if ($val): ?>
                    <small style="color:var(--auto-text-muted);font-size:0.7rem;">Fichier actuel : <?= e($val) ?></small>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <div class="fiche-form-actions">
            <button type="submit" class="fiche-btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            <a href="/admin/leads" class="fiche-btn-cancel"><i class="fas fa-arrow-left"></i> Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  var form = document.getElementById('leadEditForm');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    var requiredFields = form.querySelectorAll('[required]');
    var valid = true;
    requiredFields.forEach(function(field) {
      if (!field.value || (field.type === 'checkbox' && !field.checked)) {
        field.style.borderColor = '#dc2626';
        valid = false;
      }
    });
    if (!valid) {
      e.preventDefault();
      var firstInvalid = form.querySelector('[required]:invalid');
      if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
