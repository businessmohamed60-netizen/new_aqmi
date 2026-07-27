<?php
$title = 'Informations - AQMI Premium';
ob_start();
$customFields = [];
try { $customFields = \App\Models\LeadCustomField::allActive(); } catch (\Exception $e) {}
$sections = [];
foreach ($customFields as $cf) {
    $sections[$cf['section']][] = $cf;
}
?>
<div class="aqmi-lead-wrap">
  <div class="aqmi-lead-card" style="animation: aqmi-fade-in 0.6s ease;">
    <div style="text-align:center;margin-bottom:1.5rem;">
      <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--aqmi-success),#16a34a);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:#fff;box-shadow:0 0 30px rgba(34,197,94,0.25);">
        <i class="fas fa-check"></i>
      </div>
      <h1 class="aqmi-lead-title">Demande de rapport certifié</h1>
      <p class="aqmi-lead-subtitle">Renseignez vos informations pour transmettre votre demande de rapport de maturité qualité certifié à notre équipe.</p>
    </div>

    <!-- Steps indicator -->
    <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:2rem;">
      <div style="width:28px;height:28px;border-radius:50%;background:var(--aqmi-success);display:flex;align-items:center;justify-content:center;font-size:0.6rem;color:#fff;"><i class="fas fa-check"></i></div>
      <div style="width:40px;height:2px;background:var(--aqmi-success);border-radius:2px;"></div>
      <div style="width:28px;height:28px;border-radius:50%;background:var(--aqmi-accent);display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;color:#fff;">2</div>
      <div style="width:40px;height:2px;background:var(--aqmi-border);border-radius:2px;"></div>
      <div style="width:28px;height:28px;border-radius:50%;border:1px solid var(--aqmi-border);display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:600;color:var(--aqmi-text-tertiary);">3</div>
    </div>

    <form method="POST" action="/assessment/save-lead" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="assessment_id" value="<?= $assessment['id'] ?>">

      <!-- Identity -->
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:0.7rem;font-weight:700;color:var(--aqmi-text);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.4rem;">
          <i class="fas fa-user-circle" style="color:var(--aqmi-accent);"></i> Identité
        </div>
        <div class="aqmi-lead-split">
          <div class="aqmi-lead-field">
            <label>Prénom <span style="color:var(--aqmi-danger);">*</span></label>
            <input type="text" name="firstname" required placeholder="Votre prénom">
          </div>
          <div class="aqmi-lead-field">
            <label>Nom <span style="color:var(--aqmi-danger);">*</span></label>
            <input type="text" name="lastname" required placeholder="Votre nom">
          </div>
        </div>
        <div class="aqmi-lead-field">
          <label>Fonction / Poste</label>
          <input type="text" name="job_title" placeholder="Votre fonction dans l'entreprise">
        </div>
      </div>

      <!-- Company -->
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:0.7rem;font-weight:700;color:var(--aqmi-text);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.4rem;">
          <i class="fas fa-building" style="color:var(--aqmi-accent);"></i> Entreprise
        </div>
        <div class="aqmi-lead-field">
          <label>Entreprise / Société <span style="color:var(--aqmi-danger);">*</span></label>
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
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:0.7rem;font-weight:700;color:var(--aqmi-text);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.4rem;">
          <i class="fas fa-chart-bar" style="color:var(--aqmi-accent);"></i> Informations complémentaires
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
                <?php if ($cf['is_required']): ?><span style="color:var(--aqmi-danger);">*</span><?php endif; ?>
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
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:0.7rem;font-weight:700;color:var(--aqmi-text);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.4rem;">
          <i class="fas fa-envelope" style="color:var(--aqmi-accent);"></i> Contact
        </div>
        <div class="aqmi-lead-split">
          <div class="aqmi-lead-field">
            <label>Email <span style="color:var(--aqmi-danger);">*</span></label>
            <input type="email" name="email" required placeholder="votre@email.com">
          </div>
          <div class="aqmi-lead-field">
            <label>Téléphone</label>
            <input type="tel" name="phone" placeholder="+33 6 12 34 56 78">
          </div>
        </div>
      </div>

      <button type="submit" class="aqmi-lead-submit">
        <i class="fas fa-paper-plane" style="margin-right:0.5rem;"></i> Envoyer ma demande
      </button>
      <p style="text-align:center;font-size:0.72rem;color:var(--aqmi-text-tertiary);margin-top:1rem;">
        <i class="fas fa-lock" style="margin-right:0.3rem;"></i>Vos données sont confidentielles et ne seront pas partagées
      </p>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/app-premium.php';
?>