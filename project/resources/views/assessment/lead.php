<?php
$title = 'Informations - AQMI Premium';
ob_start();
$customFields = [];
try { $customFields = \App\Models\LeadCustomField::allActive(); } catch (\Exception $e) {}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<div class="aqmi-lead-wrap">
  <div class="aqmi-lead-card">
    <div class="aqmi-lead-header">
      <div class="aqmi-lead-badge">
        <i class="fas fa-check"></i>
      </div>
      <h1 class="aqmi-lead-title">Demande de rapport certifié</h1>
      <p class="aqmi-lead-subtitle">Renseignez vos informations pour transmettre votre demande de rapport de maturité qualité certifié à notre équipe.</p>
    </div>

    <!-- Steps indicator -->
    <div class="aqmi-lead-steps">
      <div class="aqmi-lead-step done"><i class="fas fa-check"></i></div>
      <div class="aqmi-lead-step-bar done"></div>
      <div class="aqmi-lead-step current">2</div>
      <div class="aqmi-lead-step-bar"></div>
      <div class="aqmi-lead-step">3</div>
    </div>

    <?php if ($error): ?>
      <div class="aqmi-lead-error" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="/assessment/save-lead" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="assessment_id" value="<?= $assessment['id'] ?>">

      <!-- Identity -->
      <div class="aqmi-lead-section">
        <div class="aqmi-lead-section-title">
          <i class="fas fa-user-circle"></i> Identité
        </div>
        <div class="aqmi-lead-split">
          <div class="aqmi-lead-field">
            <label>Prénom <span class="aqmi-req">*</span></label>
            <input type="text" name="firstname" required placeholder="Votre prénom">
          </div>
          <div class="aqmi-lead-field">
            <label>Nom <span class="aqmi-req">*</span></label>
            <input type="text" name="lastname" required placeholder="Votre nom">
          </div>
        </div>
        <div class="aqmi-lead-field">
          <label>Fonction / Poste</label>
          <input type="text" name="job_title" placeholder="Votre fonction dans l'entreprise">
        </div>
      </div>

      <!-- Company -->
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
            <label>Secteur d'activité</label>
            <select name="sector">
              <option value="">Sélectionnez...</option>
              <option>Automobile</option>
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
            <label>Pays</label>
            <select name="country">
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
        </div>
        <div class="aqmi-lead-split">
          <div class="aqmi-lead-field">
            <label>Site web</label>
            <input type="url" name="website" placeholder="https://www.example.com">
          </div>
          <div class="aqmi-lead-field">
            <label>Taille de l'entreprise</label>
            <select name="company_size">
              <option value="">Sélectionnez...</option>
              <option value="1-10">1-10 employés</option>
              <option value="11-50">11-50 employés</option>
              <option value="51-200">51-200 employés</option>
              <option value="201-500">201-500 employés</option>
              <option value="1000+">Plus de 1000 employés</option>
            </select>
          </div>
        </div>
        <div class="aqmi-lead-field">
          <label>Année de création</label>
          <input type="number" name="founded_year" placeholder="Ex: 2010" min="1900" max="2030">
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

      <!-- Contact -->
      <div class="aqmi-lead-section">
        <div class="aqmi-lead-section-title">
          <i class="fas fa-envelope"></i> Contact
        </div>
        <div class="aqmi-lead-split">
          <div class="aqmi-lead-field">
            <label>Email <span class="aqmi-req">*</span></label>
            <input type="email" name="email" required placeholder="votre@email.com">
          </div>
          <div class="aqmi-lead-field">
            <label>Téléphone</label>
            <input type="tel" name="phone" placeholder="+33 6 12 34 56 78">
          </div>
        </div>
      </div>

      <!-- Consent -->
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

      <button type="submit" class="aqmi-lead-submit">
        <i class="fas fa-paper-plane"></i> Envoyer ma demande
      </button>
      <p class="aqmi-lead-footer">
        <i class="fas fa-lock"></i>Vos données sont confidentielles et traitées conformément au RGPD
      </p>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>
