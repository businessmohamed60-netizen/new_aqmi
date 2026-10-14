<?php
$title = __('profile.title');
$activeSection = 'profile';
$pageTitle = __('profile.nav_label') ?: 'Mon profil';
$pageSubtitle = __('profile.subtitle') ?: 'Gérez vos informations personnelles et votre sécurité';
ob_start();
$langCode = $_SESSION['lang'] ?? 'fr';
?>
<style>
/* ====== Profile page styles ====== */
.ud-profile-header {
  background: linear-gradient(135deg, rgba(31,111,235,0.08) 0%, rgba(255,253,248,0.94) 45%, #fffdf8 100%);
  backdrop-filter: blur(20px);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-xl);
  padding: 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.ud-profile-header::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--vx-primary-glow) 0%, transparent 60%);
  border-radius: 50%;
  animation: udGlowPulse 6s ease-in-out infinite;
}
@keyframes udGlowPulse {
  0%,100% { opacity: 0.4; transform: scale(0.95); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
.ud-profile-avatar-lg {
  width: 80px; height: 80px;
  border-radius: 50%;
  background: var(--vx-primary-gradient);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem; font-weight: 900; color: #fff;
  flex-shrink: 0;
  box-shadow: 0 8px 24px var(--vx-primary-glow);
  position: relative; z-index: 1;
}
.ud-profile-header-info { position: relative; z-index: 1; }
.ud-profile-header-info h2 { font-size: 1.35rem; font-weight: 800; color: var(--vx-text-primary); margin-bottom: 0.25rem; }
.ud-profile-header-info p { font-size: 0.8rem; color: var(--vx-text-muted); margin: 0; }
.ud-profile-role-badge {
  display: inline-flex; align-items: center; gap: 0.3rem;
  font-size: 0.65rem; font-weight: 700;
  padding: 0.2rem 0.6rem; border-radius: 0.375rem;
  background: var(--vx-primary-light); color: var(--vx-primary);
  margin-top: 0.5rem;
}

.ud-profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
@media (max-width: 768px) { .ud-profile-grid { grid-template-columns: 1fr; } }

.ud-profile-card {
  background: var(--vx-card-bg);
  border: 1px solid var(--vx-card-border);
  border-radius: var(--vx-radius-lg);
  box-shadow: var(--vx-shadow-md);
  padding: 1.5rem;
}
.ud-profile-card h3 {
  font-size: 0.8125rem; font-weight: 700; color: var(--vx-text-primary);
  margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;
  padding-bottom: 0.875rem; border-bottom: 1px solid var(--vx-divider);
}
.ud-profile-card h3 i { color: var(--vx-primary); }

.ud-form-group { margin-bottom: 1.25rem; }
.ud-form-group:last-child { margin-bottom: 0; }
.ud-form-label {
  display: block; font-size: 0.75rem; font-weight: 600;
  color: var(--vx-text-secondary); margin-bottom: 0.4rem;
}
.ud-form-label .req { color: #dc2626; font-weight: 700; }
.ud-form-input,
.ud-form-select,
.ud-form-textarea {
  width: 100%; padding: 0.625rem 0.875rem;
  border: 1px solid var(--vx-card-border); border-radius: var(--vx-radius-md);
  font-family: var(--ud-font); font-size: 0.8125rem; font-weight: 500;
  color: var(--vx-text-primary); background: #fff;
  transition: all var(--vx-transition);
}
.ud-form-input:focus,
.ud-form-select:focus,
.ud-form-textarea:focus {
  outline: none; border-color: var(--vx-primary);
  box-shadow: 0 0 0 3px var(--vx-primary-light);
}
.ud-form-input:disabled { background: var(--vx-body-bg); color: var(--vx-text-muted); }
.ud-form-input:invalid:not(:placeholder-shown),
.ud-form-select:invalid,
.ud-form-textarea:invalid:not(:placeholder-shown) {
  border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.08);
}
.ud-form-textarea { resize: vertical; min-height: 70px; }
.ud-form-hint { font-size: 0.65rem; color: var(--vx-text-muted); margin-top: 0.3rem; }

.ud-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 576px) { .ud-form-row { grid-template-columns: 1fr; } }

.ud-info-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.625rem 0; border-bottom: 1px solid var(--vx-divider);
}
.ud-info-row:last-child { border-bottom: none; }
.ud-info-label { font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 500; }
.ud-info-value { font-size: 0.8125rem; color: var(--vx-text-primary); font-weight: 600; }

.ud-login-history-item {
  display: flex; align-items: center; gap: 0.75rem;
  padding: 0.625rem 0; border-bottom: 1px solid var(--vx-divider);
}
.ud-login-history-item:last-child { border-bottom: none; }
.ud-login-status-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0; }
.ud-login-status-success { background: var(--vx-success); }
.ud-login-status-failed { background: var(--vx-danger); }
.ud-login-history-info { flex: 1; }
.ud-login-history-ip { font-size: 0.75rem; color: var(--vx-text-primary); font-weight: 500; }
.ud-login-history-date { font-size: 0.65rem; color: var(--vx-text-muted); }

.ud-alert { padding: 0.75rem 1rem; border-radius: var(--vx-radius-md); font-size: 0.75rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.ud-alert-success { background: var(--vx-success-light); color: var(--vx-success); border: 1px solid rgba(16,185,129,0.20); }
.ud-alert-error { background: var(--vx-danger-light); color: var(--vx-danger); border: 1px solid rgba(239,68,68,0.20); }

.ud-section-gap { margin-top: 1.5rem; }

@media (max-width: 768px) {
  .ud-profile-header { flex-direction: column; text-align: center; padding: 1.5rem; }
  .ud-profile-avatar-lg { width: 64px; height: 64px; font-size: 1.5rem; }
}
</style>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="ud-alert ud-alert-success">
    <i class="fas fa-check-circle"></i>
    <?= e($_SESSION['success']); unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="ud-alert ud-alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= e($_SESSION['error']); unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

<!-- Profile Header -->
<div class="ud-profile-header">
  <div class="ud-profile-avatar-lg"><?= e(strtoupper(mb_substr($user['firstname'] ?? 'U', 0, 1))) ?></div>
  <div class="ud-profile-header-info">
    <h2><?= e(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?></h2>
    <p><?= e($user['email'] ?? '') ?></p>
    <span class="ud-profile-role-badge">
      <i class="fas fa-user-tag"></i>
      <?= e(__('dashboard.nav.client') ?: 'Client') ?>
    </span>
  </div>
</div>

<form method="POST" action="/user/profile" id="profileForm" novalidate>
<?= csrf_field() ?>

<!-- Personal Information -->
<div class="ud-profile-card">
  <h3><i class="fas fa-id-card"></i> Informations personnelles</h3>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="firstname">Prénom <span class="req">*</span></label>
      <input type="text" id="firstname" name="firstname" class="ud-form-input" placeholder="Votre prénom" value="<?= e($user['firstname'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="lastname">Nom <span class="req">*</span></label>
      <input type="text" id="lastname" name="lastname" class="ud-form-input" placeholder="Votre nom" value="<?= e($user['lastname'] ?? '') ?>" required>
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="email">Email <span class="req">*</span></label>
      <input type="email" id="email" name="email" class="ud-form-input" placeholder="vous@entreprise.com" value="<?= e($user['email'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="phone">Téléphone <span class="req">*</span></label>
      <input type="tel" id="phone" name="phone" class="ud-form-input" placeholder="+33 6 12 34 56 78" value="<?= e($user['phone'] ?? '') ?>" required>
    </div>
  </div>
  <div class="ud-form-group">
    <label class="ud-form-label" for="job_title">Fonction / Poste <span class="req">*</span></label>
    <input type="text" id="job_title" name="job_title" class="ud-form-input" placeholder="Ex: Responsable Qualité" value="<?= e($user['job_title'] ?? '') ?>" required>
  </div>
</div>

<!-- Company Information -->
<div class="ud-profile-card ud-section-gap">
  <h3><i class="fas fa-building"></i> Entreprise</h3>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="company">Nom de l'entreprise <span class="req">*</span></label>
      <input type="text" id="company" name="company" class="ud-form-input" placeholder="Ex: AutoParts SA" value="<?= e($user['company'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="company_size">Taille de l'entreprise <span class="req">*</span></label>
      <select id="company_size" name="company_size" class="ud-form-select" required>
        <option value="">Sélectionnez...</option>
        <option value="1-10" <?= ($user['company_size'] ?? '') === '1-10' ? 'selected' : '' ?>>1-10 employés</option>
        <option value="11-50" <?= ($user['company_size'] ?? '') === '11-50' ? 'selected' : '' ?>>11-50 employés</option>
        <option value="51-200" <?= ($user['company_size'] ?? '') === '51-200' ? 'selected' : '' ?>>51-200 employés</option>
        <option value="201-500" <?= ($user['company_size'] ?? '') === '201-500' ? 'selected' : '' ?>>201-500 employés</option>
        <option value="501-1000" <?= ($user['company_size'] ?? '') === '501-1000' ? 'selected' : '' ?>>501-1000 employés</option>
        <option value="1000+" <?= ($user['company_size'] ?? '') === '1000+' ? 'selected' : '' ?>>1000+ employés</option>
      </select>
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="sector">Secteur d'activité <span class="req">*</span></label>
      <input type="text" id="sector" name="sector" class="ud-form-input" placeholder="Ex: Automobile, Aéronautique..." value="<?= e($user['sector'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="activity_category">Catégorie d'activité <span class="req">*</span></label>
      <input type="text" id="activity_category" name="activity_category" class="ud-form-input" placeholder="Ex: Fabrication, Assemblage..." value="<?= e($user['activity_category'] ?? '') ?>" required>
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="oem_tier">Niveau OEM <span class="req">*</span></label>
      <select id="oem_tier" name="oem_tier" class="ud-form-select" required>
        <option value="">Sélectionnez...</option>
        <option value="Tier 1" <?= ($user['oem_tier'] ?? '') === 'Tier 1' ? 'selected' : '' ?>>Tier 1</option>
        <option value="Tier 2" <?= ($user['oem_tier'] ?? '') === 'Tier 2' ? 'selected' : '' ?>>Tier 2</option>
        <option value="Tier 3" <?= ($user['oem_tier'] ?? '') === 'Tier 3' ? 'selected' : '' ?>>Tier 3</option>
        <option value="Non-tier" <?= ($user['oem_tier'] ?? '') === 'Non-tier' ? 'selected' : '' ?>>Non-tier</option>
      </select>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="product_category">Catégorie de produits <span class="req">*</span></label>
      <input type="text" id="product_category" name="product_category" class="ud-form-input" placeholder="Ex: Pièces de moteur, Électronique..." value="<?= e($user['product_category'] ?? '') ?>" required>
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="country">Pays <span class="req">*</span></label>
      <input type="text" id="country" name="country" class="ud-form-input" placeholder="Ex: France, Maroc, Tunisie..." value="<?= e($user['country'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="founded_year">Année de création <span class="req">*</span></label>
      <input type="number" id="founded_year" name="founded_year" class="ud-form-input" placeholder="Ex: 2010" min="1900" max="2030" value="<?= e($user['founded_year'] ?? '') ?>" required>
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="website">Site web <span class="req">*</span></label>
      <input type="url" id="website" name="website" class="ud-form-input" placeholder="https://www.example.com" value="<?= e($user['website'] ?? '') ?>" required>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="annual_revenue">CA annuel</label>
      <input type="text" id="annual_revenue" name="annual_revenue" class="ud-form-input" placeholder="Ex: 5M€, 20M€..." value="<?= e($user['annual_revenue'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="export_percentage">Part à l'export (%)</label>
      <input type="number" id="export_percentage" name="export_percentage" class="ud-form-input" placeholder="Ex: 45" min="0" max="100" value="<?= e($user['export_percentage'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="main_clients">Principaux clients / OEM</label>
      <input type="text" id="main_clients" name="main_clients" class="ud-form-input" placeholder="Ex: Renault, PSA, BMW..." value="<?= e($user['main_clients'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="production_sites">Sites de production</label>
      <input type="number" id="production_sites" name="production_sites" class="ud-form-input" placeholder="Ex: 3" min="0" value="<?= e($user['production_sites'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="workforce_production">Effectif production</label>
      <input type="number" id="workforce_production" name="workforce_production" class="ud-form-input" placeholder="Ex: 120" min="0" value="<?= e($user['workforce_production'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-group">
    <label class="ud-form-label" for="workforce_engineering">Effectif ingénierie / R&D</label>
    <input type="number" id="workforce_engineering" name="workforce_engineering" class="ud-form-input" placeholder="Ex: 25" min="0" value="<?= e($user['workforce_engineering'] ?? '') ?>">
  </div>
</div>

<!-- Production & Equipment -->
<div class="ud-profile-card ud-section-gap">
  <h3><i class="fas fa-industry"></i> Production & Équipement</h3>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="production_type">Type de production <span class="req">*</span></label>
      <select id="production_type" name="production_type" class="ud-form-select" required>
        <option value="">Sélectionnez...</option>
        <option value="mass" <?= ($user['production_type'] ?? '') === 'mass' ? 'selected' : '' ?>>Production en série</option>
        <option value="batch" <?= ($user['production_type'] ?? '') === 'batch' ? 'selected' : '' ?>>Production par lots</option>
        <option value="unit" <?= ($user['production_type'] ?? '') === 'unit' ? 'selected' : '' ?>>Production unitaire</option>
        <option value="mixed" <?= ($user['production_type'] ?? '') === 'mixed' ? 'selected' : '' ?>>Mixte</option>
      </select>
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="production_capacity">Capacité de production</label>
      <input type="text" id="production_capacity" name="production_capacity" class="ud-form-input" placeholder="Ex: 50000 unités/an" value="<?= e($user['production_capacity'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="machine_count">Nombre de machines</label>
      <input type="number" id="machine_count" name="machine_count" class="ud-form-input" placeholder="Ex: 45" min="0" value="<?= e($user['machine_count'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="machine_types">Types de machines</label>
      <input type="text" id="machine_types" name="machine_types" class="ud-form-input" placeholder="Ex: CNC, Injection, Découpe laser..." value="<?= e($user['machine_types'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="main_materials">Matières principales</label>
      <input type="text" id="main_materials" name="main_materials" class="ud-form-input" placeholder="Ex: Acier, Aluminium, Plastique..." value="<?= e($user['main_materials'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="process_technologies">Technologies de procédé</label>
      <input type="text" id="process_technologies" name="process_technologies" class="ud-form-input" placeholder="Ex: Soudage, Usinage, Fonderie..." value="<?= e($user['process_technologies'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- Quality & Performance -->
<div class="ud-profile-card ud-section-gap">
  <h3><i class="fas fa-clipboard-check"></i> Qualité & Performance</h3>
  <div class="ud-form-group">
    <label class="ud-form-label" for="certifications">Certifications détenues <span class="req">*</span></label>
    <input type="text" id="certifications" name="certifications" class="ud-form-input" placeholder="Ex: ISO 9001, IATF 16949, ISO 14001" value="<?= e($user['certifications'] ?? '') ?>" required>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="ppm_target">Objectif PPM</label>
      <input type="number" id="ppm_target" name="ppm_target" class="ud-form-input" placeholder="Ex: 50" min="0" value="<?= e($user['ppm_target'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="otd_rate">Taux OTD (%)</label>
      <input type="number" id="otd_rate" name="otd_rate" class="ud-form-input" placeholder="Ex: 98.5" min="0" max="100" step="0.01" value="<?= e($user['otd_rate'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="fta_rate">Taux FTA (%)</label>
      <input type="number" id="fta_rate" name="fta_rate" class="ud-form-input" placeholder="Ex: 99.2" min="0" max="100" step="0.01" value="<?= e($user['fta_rate'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="scrap_rate">Taux de rebut (%)</label>
      <input type="number" id="scrap_rate" name="scrap_rate" class="ud-form-input" placeholder="Ex: 1.5" min="0" max="100" step="0.01" value="<?= e($user['scrap_rate'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- Systems & Engineering -->
<div class="ud-profile-card ud-section-gap">
  <h3><i class="fas fa-laptop-code"></i> Systèmes & Ingénierie</h3>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="traceability_system">Système de traçabilité</label>
      <input type="text" id="traceability_system" name="traceability_system" class="ud-form-input" placeholder="Ex: SAP, Oracle, Excel..." value="<?= e($user['traceability_system'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="logistics_system">Système logistique</label>
      <input type="text" id="logistics_system" name="logistics_system" class="ud-form-input" placeholder="Ex: WMS, SAP MM..." value="<?= e($user['logistics_system'] ?? '') ?>">
    </div>
  </div>
  <div class="ud-form-row">
    <div class="ud-form-group">
      <label class="ud-form-label" for="rd_budget_percent">Budget R&D (% du CA)</label>
      <input type="number" id="rd_budget_percent" name="rd_budget_percent" class="ud-form-input" placeholder="Ex: 5.5" min="0" max="100" step="0.01" value="<?= e($user['rd_budget_percent'] ?? '') ?>">
    </div>
    <div class="ud-form-group">
      <label class="ud-form-label" for="current_erp">ERP actuel</label>
      <input type="text" id="current_erp" name="current_erp" class="ud-form-input" placeholder="Ex: SAP, Oracle, Microsoft Dynamics..." value="<?= e($user['current_erp'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- Submit -->
<div style="margin-top:1.5rem;text-align:right;">
  <button type="submit" class="nova-btn nova-btn-primary" style="min-width:200px;justify-content:center;">
    <i class="fas fa-save"></i> Enregistrer le profil
  </button>
</div>
</form>

<div class="ud-profile-grid" style="margin-top:1.5rem;">
  <!-- Password Change -->
  <div class="ud-profile-card">
    <h3><i class="fas fa-shield-halved"></i> Sécurité</h3>
    <form method="POST" action="/user/profile/password">
      <?= csrf_field() ?>
      <div class="ud-form-group">
        <label class="ud-form-label" for="current_password">Mot de passe actuel</label>
        <input type="password" id="current_password" name="current_password" class="ud-form-input" required>
      </div>
      <div class="ud-form-group">
        <label class="ud-form-label" for="new_password">Nouveau mot de passe</label>
        <input type="password" id="new_password" name="new_password" class="ud-form-input" required>
        <div class="ud-form-hint">Minimum 8 caractères</div>
      </div>
      <div class="ud-form-group">
        <label class="ud-form-label" for="confirm_password">Confirmer le mot de passe</label>
        <input type="password" id="confirm_password" name="confirm_password" class="ud-form-input" required>
      </div>
      <button type="submit" class="nova-btn nova-btn-primary" style="width:100%;justify-content:center;">
        <i class="fas fa-key"></i> Changer le mot de passe
      </button>
    </form>
  </div>

  <!-- Account Info -->
  <div class="ud-profile-card">
    <h3><i class="fas fa-circle-info"></i> Compte</h3>
    <div class="ud-info-row">
      <span class="ud-info-label">Membre depuis</span>
      <span class="ud-info-value"><?= $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : '—' ?></span>
    </div>
    <div class="ud-info-row">
      <span class="ud-info-label">Dernière connexion</span>
      <span class="ud-info-value"><?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '—' ?></span>
    </div>
    <div class="ud-info-row">
      <span class="ud-info-label">Statut</span>
      <span class="ud-info-value" style="color:var(--vx-success);">
        <i class="fas fa-circle-check" style="font-size:0.7rem;"></i> Actif
      </span>
    </div>
  </div>
</div>

<!-- Login History -->
<?php if (!empty($loginHistory)): ?>
<div class="ud-profile-card" style="margin-top:1.5rem;">
  <h3><i class="fas fa-clock-rotate-left"></i> Historique de connexion</h3>
  <?php foreach ($loginHistory as $log): ?>
    <div class="ud-login-history-item">
      <div class="ud-login-status-dot <?= $log['status'] === 'success' ? 'ud-login-status-success' : 'ud-login-status-failed' ?>"></div>
      <div class="ud-login-history-info">
        <div class="ud-login-history-ip">
          <?= e($log['ip_address'] ?? '—') ?>
          <?php if ($log['status'] === 'failed'): ?>
            <span style="color:var(--vx-danger);font-size:0.65rem;margin-left:0.4rem;">Échec</span>
          <?php endif; ?>
        </div>
        <div class="ud-login-history-date"><?= e($log['created_at'] ? date('d/m/Y H:i', strtotime($log['created_at'])) : '') ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
(function() {
  var form = document.getElementById('profileForm');
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
require BASE_PATH . '/resources/views/layouts/user_dashboard.php';
