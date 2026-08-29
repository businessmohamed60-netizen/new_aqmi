<?php
$title = 'Informations - AQMI Premium';
ob_start();
$customFields = [];
try { $customFields = \App\Models\LeadCustomField::allActive(); } catch (\Exception $e) {}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<div class="aqmi-lead-wrap">
  <div class="aqmi-lead-card aqmi-lead-card-wide">
    <div class="aqmi-lead-header">
      <div class="aqmi-lead-badge">
        <i class="fas fa-check"></i>
      </div>
      <h1 class="aqmi-lead-title">Demande de rapport certifié</h1>
      <p class="aqmi-lead-subtitle">Renseignez vos informations pour transmettre votre demande de rapport de maturité qualité certifié à notre équipe.</p>
    </div>

    <!-- Steps indicator -->
    <div class="aqmi-lead-steps" id="leadSteps">
      <div class="aqmi-lead-step done"><i class="fas fa-check"></i></div>
      <div class="aqmi-lead-step-bar done"></div>
      <div class="aqmi-lead-step current" data-step="1">1</div>
      <div class="aqmi-lead-step-bar" id="bar12"></div>
      <div class="aqmi-lead-step" data-step="2">2</div>
      <div class="aqmi-lead-step-bar" id="bar23"></div>
      <div class="aqmi-lead-step">3</div>
    </div>

    <div class="aqmi-lead-step-labels">
      <span class="aqmi-lead-step-label active" id="label1">Informations globales</span>
      <span class="aqmi-lead-step-label" id="label2">Informations techniques industrielles</span>
    </div>

    <?php if ($error): ?>
      <div class="aqmi-lead-error" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="/assessment/save-lead" novalidate id="leadForm">
      <?= csrf_field() ?>
      <input type="hidden" name="assessment_id" value="<?= $assessment['id'] ?>">

      <!-- ════════ ÉTAPE 1 : Informations globales ════════ -->
      <div class="aqmi-lead-step-panel active" id="step1">

        <!-- Identité -->
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-user-circle"></i> Identité
          </div>
          <p style="font-size:0.8rem;color:var(--auto-text-tertiary,#6b7280);margin-bottom:1rem;">
            <i class="fas fa-lock"></i> Ces informations proviennent de votre compte et ne peuvent pas être modifiées ici.
          </p>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Prénom <span class="aqmi-req">*</span></label>
              <input type="text" name="firstname" required value="<?= e($user['firstname'] ?? '') ?>" readonly>
            </div>
            <div class="aqmi-lead-field">
              <label>Nom <span class="aqmi-req">*</span></label>
              <input type="text" name="lastname" required value="<?= e($user['lastname'] ?? '') ?>" readonly>
            </div>
          </div>
          <div class="aqmi-lead-field">
            <label>Fonction / Poste <span class="aqmi-req">*</span></label>
            <input type="text" name="job_title" required placeholder="Ex: Directeur Qualité, Responsable Production">
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Email <span class="aqmi-req">*</span></label>
              <input type="email" name="email" required value="<?= e($user['email'] ?? '') ?>" readonly>
            </div>
            <div class="aqmi-lead-field">
              <label>Téléphone <span class="aqmi-req">*</span></label>
              <input type="tel" name="phone" required value="<?= e($user['phone'] ?? '') ?>" readonly>
            </div>
          </div>
        </div>

        <!-- Entreprise -->
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-building"></i> Entreprise
          </div>
          <div class="aqmi-lead-field">
            <label>Entreprise / Société <span class="aqmi-req">*</span></label>
            <input type="text" name="company" required placeholder="Nom de votre entreprise">
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Secteur d'activité <span class="aqmi-req">*</span></label>
              <select name="sector" required>
                <option value="">Sélectionnez...</option>
                <option>Automobile</option>
                <option>Pièces automobiles</option>
                <option>Aéronautique</option>
                <option>Agroalimentaire</option>
                <option>Pharmaceutique</option>
                <option>Électronique</option>
                <option>Mécanique</option>
                <option>Chimie</option>
                <option>Énergie</option>
                <option>Logistique</option>
                <option>Construction</option>
                <option>Textile</option>
                <option>Autre</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Catégorie d'activité <span class="aqmi-req">*</span></label>
              <select name="activity_category" required>
                <option value="">Sélectionnez...</option>
                <option value="fabricant">Fabricant de pièces</option>
                <option value="sous-traitant">Sous-traitant</option>
                <option value="distributeur">Distributeur</option>
                <option value="importateur">Importateur</option>
                <option value="equipementier">Équipementier</option>
                <option value="prestation">Prestation de services</option>
              </select>
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Niveau de la chaîne OEM <span class="aqmi-req">*</span></label>
              <select name="oem_tier" required>
                <option value="">Sélectionnez...</option>
                <option value="tier1">Tier 1 — Équipementier direct</option>
                <option value="tier2">Tier 2 — Sous-traitant direct</option>
                <option value="tier3">Tier 3 — Fournisseur de matière</option>
                <option value="aftermarket">Aftermarket / Rechange</option>
                <option value="na">Non applicable</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Catégorie de produits <span class="aqmi-req">*</span></label>
              <select name="product_category" required>
                <option value="">Sélectionnez...</option>
                <option value="moteur">Pièces moteur</option>
                <option value="transmission">Transmission / Boîte</option>
                <option value="chassis">Châssis / Suspension</option>
                <option value="carrosserie">Carrosserie</option>
                <option value="interieur">Intérieur / Habitacle</option>
                <option value="electric">Composants électriques</option>
                <option value="plastique">Pièces plastiques</option>
                <option value="metal_decoupe">Métal découpé / embouti</option>
                <option value="usinage">Usinage</option>
                <option value="fonderie">Fonderie</option>
                <option value="caoutchouc">Caoutchouc / Joints</option>
                <option value="verre">Verre</option>
                <option value="autre">Autre</option>
              </select>
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Pays <span class="aqmi-req">*</span></label>
              <select name="country" required>
                <option value="">Sélectionnez...</option>
                <option>France</option>
                <option>Belgique</option>
                <option>Suisse</option>
                <option>Canada</option>
                <option>Maroc</option>
                <option>Tunisie</option>
                <option>Algérie</option>
                <option>Allemagne</option>
                <option>Italie</option>
                <option>Espagne</option>
                <option>Royaume-Uni</option>
                <option>États-Unis</option>
                <option>Autre</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Taille de l'entreprise <span class="aqmi-req">*</span></label>
              <select name="company_size" required>
                <option value="">Sélectionnez...</option>
                <option value="1-10">1-10 employés</option>
                <option value="11-50">11-50 employés</option>
                <option value="51-200">51-200 employés</option>
                <option value="201-500">201-500 employés</option>
                <option value="1000+">Plus de 1000 employés</option>
              </select>
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Année de création <span class="aqmi-req">*</span></label>
              <input type="number" name="founded_year" required placeholder="Ex: 2010" min="1900" max="2030">
            </div>
            <div class="aqmi-lead-field">
              <label>Site web <span class="aqmi-req">*</span></label>
              <input type="url" name="website" required placeholder="https://www.example.com">
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>CA annuel (€) <span class="aqmi-req">*</span></label>
              <select name="annual_revenue" required>
                <option value="">Sélectionnez...</option>
                <option value="<2M">Moins de 2 M€</option>
                <option value="2-10M">2 à 10 M€</option>
                <option value="10-50M">10 à 50 M€</option>
                <option value="50-200M">50 à 200 M€</option>
                <option value=">200M">Plus de 200 M€</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Part à l'export (%) <span class="aqmi-req">*</span></label>
              <input type="number" name="export_percentage" required min="0" max="100" placeholder="Ex: 35">
            </div>
          </div>
          <div class="aqmi-lead-field">
            <label>Principaux clients / OEM <span class="aqmi-req">*</span></label>
            <input type="text" name="main_clients" required placeholder="Ex: Renault, Stellantis, Volkswagen, Bosch">
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Nombre de sites de production <span class="aqmi-req">*</span></label>
              <input type="number" name="production_sites" required min="0" max="999" placeholder="Ex: 2">
            </div>
            <div class="aqmi-lead-field">
              <label>Effectif production <span class="aqmi-req">*</span></label>
              <input type="number" name="workforce_production" required min="0" max="99999" placeholder="Ex: 120">
            </div>
          </div>
          <div class="aqmi-lead-field">
            <label>Effectif ingénierie / R&D <span class="aqmi-req">*</span></label>
            <input type="number" name="workforce_engineering" required min="0" max="99999" placeholder="Ex: 15">
          </div>
        </div>

        <!-- Consent (step 1) -->
        <div class="aqmi-lead-section aqmi-lead-consent">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-shield-alt"></i> Consentement et protection des données
          </div>
          <div class="aqmi-consent-block">
            <p class="aqmi-consent-text">
              J'accepte que mes données personnelles soient utilisées pour me contacter au sujet de mon rapport de maturité qualité.
            </p>
            <div class="aqmi-consent-toggle" data-name="consent_contact">
              <label class="aqmi-consent-option">
                <input type="radio" name="consent_contact" value="yes" required>
                <span class="aqmi-consent-pill">Oui</span>
              </label>
              <label class="aqmi-consent-option">
                <input type="radio" name="consent_contact" value="no">
                <span class="aqmi-consent-pill">Non</span>
              </label>
            </div>
            <span class="aqmi-consent-required">Obligatoire</span>
          </div>
          <div class="aqmi-consent-block">
            <p class="aqmi-consent-text">
              J'accepte que mon score soit partagé avec des industriels qui pourraient être intéressés par mon profil qualité.
            </p>
            <div class="aqmi-consent-toggle" data-name="consent_share_industry">
              <label class="aqmi-consent-option">
                <input type="radio" name="consent_share_industry" value="yes" required>
                <span class="aqmi-consent-pill">Oui</span>
              </label>
              <label class="aqmi-consent-option">
                <input type="radio" name="consent_share_industry" value="no">
                <span class="aqmi-consent-pill">Non</span>
              </label>
            </div>
            <span class="aqmi-consent-required">Obligatoire</span>
          </div>
        </div>

        <div class="aqmi-lead-nav">
          <button type="button" class="aqmi-lead-btn-next" id="btnNext1">
            Étape suivante <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ════════ ÉTAPE 2 : Informations techniques industrielles ════════ -->
      <div class="aqmi-lead-step-panel" id="step2">

        <!-- Production -->
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-industry"></i> Production
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Type de production <span class="aqmi-req">*</span></label>
              <select name="production_type" required>
                <option value="">Sélectionnez...</option>
                <option value="unitaire">Unitaire</option>
                <option value="petite_serie">Petite série</option>
                <option value="grande_serie">Grande série</option>
                <option value="masse">Production de masse</option>
                <option value="continue">Continue</option>
                <option value="projet">Par projet</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Capacité de production <span class="aqmi-req">*</span></label>
              <input type="text" name="production_capacity" required placeholder="Ex: 5000 unités/mois">
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Nombre de machines <span class="aqmi-req">*</span></label>
              <input type="number" name="machine_count" required placeholder="Ex: 25" min="0" max="9999">
            </div>
            <div class="aqmi-lead-field">
              <label>Type de machines <span class="aqmi-req">*</span></label>
              <input type="text" name="machine_types" required placeholder="Ex: CNC, injection plastique">
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Matières principales <span class="aqmi-req">*</span></label>
              <input type="text" name="main_materials" required placeholder="Ex: Acier, aluminium, plastique PA66">
            </div>
            <div class="aqmi-lead-field">
              <label>Technologies de procédé <span class="aqmi-req">*</span></label>
              <input type="text" name="process_technologies" required placeholder="Ex: Usinage, emboutissage, injection, soudage">
            </div>
          </div>
        </div>

        <!-- Qualité -->
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-clipboard-check"></i> Qualité & Performance
          </div>
          <div class="aqmi-lead-field">
            <label>Certifications détenues <span class="aqmi-req">*</span></label>
            <input type="text" name="certifications" required placeholder="Ex: ISO 9001, IATF 16949, ISO 14001">
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Objectif PPM (défauts) <span class="aqmi-req">*</span></label>
              <input type="number" name="ppm_target" required min="0" max="999999" placeholder="Ex: 50">
            </div>
            <div class="aqmi-lead-field">
              <label>Taux OTD (%) <span class="aqmi-req">*</span></label>
              <input type="number" step="0.01" name="otd_rate" required min="0" max="100" placeholder="Ex: 98.5">
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Taux FTA — First Time Through (%) <span class="aqmi-req">*</span></label>
              <input type="number" step="0.01" name="fta_rate" required min="0" max="100" placeholder="Ex: 95.2">
            </div>
            <div class="aqmi-lead-field">
              <label>Taux de rebut (%) <span class="aqmi-req">*</span></label>
              <input type="number" step="0.01" name="scrap_rate" required min="0" max="100" placeholder="Ex: 1.8">
            </div>
          </div>
        </div>

        <!-- Systèmes & R&D -->
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-laptop-code"></i> Systèmes & Ingénierie
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Système de traçabilité <span class="aqmi-req">*</span></label>
              <select name="traceability_system" required>
                <option value="">Sélectionnez...</option>
                <option value="aucun">Aucun</option>
                <option value="manuel">Manuel / papier</option>
                <option value="excel">Excel / tableur</option>
                <option value="mes">MES dédié</option>
                <option value="erp">Intégré ERP</option>
                <option value="rfid">RFID / IoT</option>
              </select>
            </div>
            <div class="aqmi-lead-field">
              <label>Système logistique <span class="aqmi-req">*</span></label>
              <select name="logistics_system" required>
                <option value="">Sélectionnez...</option>
                <option value="kanban">Kanban</option>
                <option value="mrp">MRP</option>
                <option value="lean">Lean / Juste-à-temps</option>
                <option value="wms">WMS dédié</option>
                <option value="mixte">Mixte</option>
                <option value="aucun">Non structuré</option>
              </select>
            </div>
          </div>
          <div class="aqmi-lead-split">
            <div class="aqmi-lead-field">
              <label>Budget R&D (% du CA) <span class="aqmi-req">*</span></label>
              <input type="number" step="0.1" name="rd_budget_percent" required min="0" max="100" placeholder="Ex: 4.5">
            </div>
            <div class="aqmi-lead-field">
              <label>ERP actuel <span class="aqmi-req">*</span></label>
              <select name="current_erp" required>
                <option value="">Sélectionnez...</option>
                <option>SAP</option>
                <option>Oracle</option>
                <option>Dynamics 365</option>
                <option>Infor</option>
                <option>Pegase</option>
                <option>Sage</option>
                <option>Odoo</option>
                <option>ERP propriétaire</option>
                <option>Aucun</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Custom Fields -->
        <?php if (!empty($customFields)): ?>
        <div class="aqmi-lead-section">
          <div class="aqmi-lead-section-title">
            <i class="fas fa-chart-bar"></i> Informations complémentaires
          </div>
          <div class="aqmi-lead-split">
            <?php foreach ($customFields as $cf):
              $fieldName = 'custom_fields[' . $cf['id'] . ']';
              $fieldId = 'cf_' . $cf['id'];
              $placeholder = $cf['placeholder_fr'] ?: $cf['placeholder'] ?: '';
              $opts = json_decode($cf['options'] ?? '[]', true);
            ?>
              <div class="aqmi-lead-field">
                <label for="<?= $fieldId ?>">
                  <?= e($cf['label_fr'] ?: $cf['label']) ?>
                  <?php if ($cf['is_required']): ?><span class="aqmi-req">*</span><?php endif; ?>
                </label>
                <?php if ($cf['field_type'] === 'text' || $cf['field_type'] === 'number' || $cf['field_type'] === 'phone'): ?>
                  <input type="<?= $cf['field_type'] === 'number' ? 'number' : ($cf['field_type'] === 'phone' ? 'tel' : 'text') ?>" name="<?= $fieldName ?>" id="<?= $fieldId ?>" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                <?php elseif ($cf['field_type'] === 'textarea'): ?>
                  <textarea name="<?= $fieldName ?>" id="<?= $fieldId ?>" rows="2" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>></textarea>
                <?php elseif ($cf['field_type'] === 'date'): ?>
                  <input type="date" name="<?= $fieldName ?>" id="<?= $fieldId ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                <?php elseif ($cf['field_type'] === 'select'): ?>
                  <select name="<?= $fieldName ?>" id="<?= $fieldId ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                    <option value=""><?= e($placeholder ?: 'Sélectionnez...') ?></option>
                    <?php foreach ($opts as $opt): ?>
                      <option value="<?= e($opt['value']) ?>"><?= e($opt['label']) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="aqmi-lead-nav">
          <button type="button" class="aqmi-lead-btn-back" id="btnBack2">
            <i class="fas fa-arrow-left"></i> Retour
          </button>
          <button type="submit" class="aqmi-lead-submit">
            <i class="fas fa-paper-plane"></i> Envoyer ma demande
          </button>
        </div>
      </div>

      <p class="aqmi-lead-footer">
        <i class="fas fa-lock"></i>Vos données sont confidentielles et traitées conformément au RGPD
      </p>
    </form>
  </div>
</div>

<script>
(function () {
  var step1 = document.getElementById('step1');
  var step2 = document.getElementById('step2');
  var btnNext1 = document.getElementById('btnNext1');
  var btnBack2 = document.getElementById('btnBack2');
  var bar12 = document.getElementById('bar12');
  var stepDot1 = document.querySelector('.aqmi-lead-step[data-step="1"]');
  var stepDot2 = document.querySelector('.aqmi-lead-step[data-step="2"]');
  var label1 = document.getElementById('label1');
  var label2 = document.getElementById('label2');

  function showStep(n) {
    if (n === 1) {
      step1.classList.add('active');
      step2.classList.remove('active');
      stepDot1.classList.add('current');
      stepDot2.classList.remove('current');
      bar12.classList.remove('done');
      label1.classList.add('active');
      label2.classList.remove('active');
    } else {
      step1.classList.remove('active');
      step2.classList.add('active');
      stepDot1.classList.remove('current');
      stepDot1.classList.add('done');
      stepDot2.classList.add('current');
      bar12.classList.add('done');
      label1.classList.remove('active');
      label2.classList.add('active');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  btnNext1.addEventListener('click', function () {
    var required = step1.querySelectorAll('[required]');
    for (var i = 0; i < required.length; i++) {
      if (!required[i].value || (required[i].type === 'radio' && !step1.querySelector('[name="' + required[i].name + '"]:checked'))) {
        required[i].focus();
        required[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
    }
    showStep(2);
  });

  btnBack2.addEventListener('click', function () {
    showStep(1);
  });
})();
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>
