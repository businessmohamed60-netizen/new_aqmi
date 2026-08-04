<?php
$title = 'Fiche Prospect : ' . e($lead['firstname'] ?? '') . ' ' . e($lead['lastname'] ?? '');
ob_start();
$scoreColor = '#486581';
if ($assessment && isset($assessment['total_score'])) {
    $s = (float)$assessment['total_score'];
    if ($s >= 71) $scoreColor = '#00f5a0';
    elseif ($s >= 51) $scoreColor = '#00d4ff';
    elseif ($s >= 31) $scoreColor = '#ff8c00';
    else $scoreColor = '#ff3366';
}
$groupedFields = [];
foreach ($customFields as $cf) {
    $groupedFields[$cf['section']][] = $cf;
}
?>
<style>
.auto-detail-header { margin-bottom: 1.5rem; }
.auto-detail-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-profile-card {
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  text-align: center;
  padding: 1.5rem;
}
.auto-profile-card .avatar-circle {
  width: 72px; height: 72px;
  background: linear-gradient(135deg, var(--auto-cyan), var(--auto-purple));
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #080c18; font-size: 1.5rem; font-weight: 800;
  margin: 0 auto 0.75rem;
}
.auto-profile-card h5 { color: var(--auto-text-primary); font-weight: 700; }
.auto-profile-card .job-title { color: var(--auto-text-muted); font-size: 0.78rem; margin-bottom: 0.75rem; }
.auto-profile-card .info-line { margin-bottom: 0.5rem; font-size: 0.78rem; }
.auto-profile-card .info-line i { color: var(--auto-text-muted); width: 18px; margin-right: 0.5rem; }
.auto-profile-card .info-line strong { color: var(--auto-text-primary); }
.auto-section-card {
  background: var(--auto-bg-card);
  backdrop-filter: blur(20px);
  border: 1px solid var(--auto-border);
  border-radius: var(--auto-radius-lg);
  padding: 1.25rem;
  margin-bottom: 1rem;
}
.auto-section-card h6 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem; }
.auto-section-card h6 i { color: var(--auto-text-muted); margin-right: 0.5rem; }
.auto-section-card .field-label { color: var(--auto-text-muted); font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.5px; }
.auto-section-card .field-value { color: var(--auto-text-primary); font-weight: 600; font-size: 0.78rem; }
.auto-section-card .form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.auto-section-card .form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
.auto-section-card .form-switch .form-check-label { color: var(--auto-text-secondary); font-size: 0.8rem; }
.auto-score-value { font-size: 2.5rem; font-weight: 900; line-height: 1; }
.auto-section-divider { border-top: 1px solid var(--auto-border); margin: 1.5rem 0; }
</style>

<div class="auto-fade-in">
  <div class="auto-detail-header">
    <h5><i class="fas fa-user me-2" style="color:var(--auto-cyan);"></i>Fiche Prospect : <?= e($lead['firstname'] ?? '') ?> <?= e($lead['lastname'] ?? '') ?></h5>
  </div>

  <div class="row">
    <!-- Left Column -->
    <div class="col-lg-4">
      <!-- Identity Card -->
      <div class="auto-profile-card mb-3">
        <div class="avatar-circle">
          <?= strtoupper(substr($lead['firstname'] ?? '', 0, 1)) . strtoupper(substr($lead['lastname'] ?? '', 0, 1)) ?>
        </div>
        <h5><?= e($lead['firstname'] ?? '') ?> <?= e($lead['lastname'] ?? '') ?></h5>
        <p class="job-title"><?= e($lead['job_title'] ?? 'Fonction non renseignée') ?></p>
        <div class="mb-3" style="display:flex;gap:0.35rem;flex-wrap:wrap;justify-content:center;">
          <span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($lead['sector'] ?? 'Non spécifié') ?></span>
          <span class="auto-badge auto-badge-cyan" style="font-size:0.6rem;"><?= e($lead['country'] ?? 'Non spécifié') ?></span>
        </div>
        <hr style="border-color:var(--auto-border);">
        <div class="text-start">
          <div class="info-line"><i class="fas fa-building"></i><strong><?= e($lead['company'] ?? '') ?></strong></div>
          <div class="info-line"><i class="fas fa-envelope"></i><?= e($lead['email'] ?? '') ?></div>
          <div class="info-line"><i class="fas fa-phone"></i><?= e($lead['phone'] ?? 'Non renseigné') ?></div>
          <?php if ($lead['website']): ?>
            <div class="info-line"><i class="fas fa-globe"></i><?= e($lead['website']) ?></div>
          <?php endif; ?>
          <div class="info-line"><i class="fas fa-calendar"></i>Inscrit le <?= date('d/m/Y', strtotime($lead['created_at'])) ?></div>
        </div>
      </div>

      <!-- Assessment Score -->
      <?php if ($assessment && isset($assessment['total_score'])): ?>
        <div class="auto-section-card text-center">
          <h6><i class="fas fa-chart-bar"></i>Évaluation AQMI</h6>
          <div class="auto-score-value" style="color:<?= $scoreColor ?>;"><?= (float)$assessment['total_score'] ?>%</div>
          <div style="color:var(--auto-text-muted);font-size:0.75rem;">Score Global</div>
          <?php if ($assessment['maturity_level']): ?>
            <span class="auto-badge mt-2" style="background:<?= $scoreColor ?>20;border-color:<?= $scoreColor ?>35;color:<?= $scoreColor ?>;"><?= e($assessment['maturity_level']) ?></span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
      <!-- Company Info -->
      <div class="auto-section-card">
        <h6><i class="fas fa-building"></i>Informations Entreprise</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <span class="field-label">Taille de l'entreprise</span>
            <span class="field-value"><?= e($lead['company_size'] ?? 'Non renseigné') ?></span>
          </div>
          <div class="col-md-6">
            <span class="field-label">Site web</span>
            <span class="field-value"><?= e($lead['website'] ?? 'Non renseigné') ?></span>
          </div>
          <div class="col-md-6">
            <span class="field-label">Année de création</span>
            <span class="field-value"><?= e($lead['founded_year'] ?? 'Non renseignée') ?></span>
          </div>
          <div class="col-md-6">
            <span class="field-label">Type de production</span>
            <span class="field-value"><?= e($lead['production_type'] ?? 'Non renseigné') ?></span>
          </div>
          <div class="col-12">
            <span class="field-label">Certifications</span>
            <span class="field-value"><?= e($lead['certifications'] ?? 'Non renseigné') ?></span>
          </div>
          <?php if ($lead['notes']): ?>
          <div class="col-12">
            <span class="field-label">Notes</span>
            <span class="field-value"><?= nl2br(e($lead['notes'])) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Custom Fields by Section -->
      <?php foreach ($groupedFields as $section => $fields): ?>
        <div class="auto-section-card">
          <h6><i class="fas fa-id-card"></i>
            <?= ucfirst($section) === 'General' ? 'Général' : (ucfirst($section) === 'Company' ? 'Entreprise' : (ucfirst($section) === 'Industrial' ? 'Industriel' : (ucfirst($section) === 'Certifications' ? 'Certifications' : (ucfirst($section) === 'Contact' ? 'Contact' : (ucfirst($section) === 'Engagement' ? 'Engagement' : $section))))) ?>
          </h6>
          <div class="row g-3">
            <?php foreach ($fields as $cf): ?>
              <?php
              $val = $cf['value'] ?? '';
              if ($cf['field_type'] === 'multiselect' && $val) {
                  $decoded = json_decode($val, true);
                  if (is_array($decoded)) $val = implode(', ', $decoded);
              }
              if ($cf['field_type'] === 'file' && $val) {
                  $val = '<a href="/storage/leads/' . $lead['id'] . '/' . e($val) . '" target="_blank" class="auto-btn auto-btn-outline auto-btn-sm"><i class="fas fa-download me-1"></i>Voir le fichier</a>';
              }
              ?>
              <div class="col-md-6">
                <span class="field-label"><?= e($cf['label_fr'] ?: $cf['label']) ?></span>
                <span class="field-value"><?= $val ?: '<span style="color:var(--auto-text-muted);font-weight:400;">Non renseigné</span>' ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Documents -->
      <?php if (!empty($documents)): ?>
        <div class="auto-section-card">
          <h6><i class="fas fa-paperclip"></i>Documents</h6>
          <?php foreach ($documents as $doc): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--auto-border);">
              <div>
                <i class="fas fa-file me-2" style="color:var(--auto-text-muted);"></i>
                <span style="color:var(--auto-text-secondary);font-size:0.78rem;"><?= e($doc['original_name']) ?></span>
                <small style="color:var(--auto-text-muted);margin-left:0.5rem;">(<?= round($doc['file_size'] / 1024) ?> KB)</small>
              </div>
              <a href="/storage/leads/<?= $lead['id'] ?>/<?= e($doc['filename']) ?>" class="auto-btn auto-btn-outline auto-btn-sm" download><i class="fas fa-download"></i></a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Edit Form -->
      <div class="auto-section-card">
        <h6><i class="fas fa-edit"></i>Modifier les informations</h6>
        <form method="POST" action="/admin/leads/save-fields/<?= $lead['id'] ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="auto-label">Taille de l'entreprise</label>
              <select name="company_size" class="auto-select">
                <option value="">Non spécifié</option>
                <option value="1-10" <?= $lead['company_size'] === '1-10' ? 'selected' : '' ?>>1-10 employés</option>
                <option value="11-50" <?= $lead['company_size'] === '11-50' ? 'selected' : '' ?>>11-50 employés</option>
                <option value="51-200" <?= $lead['company_size'] === '51-200' ? 'selected' : '' ?>>51-200 employés</option>
                <option value="201-500" <?= $lead['company_size'] === '201-500' ? 'selected' : '' ?>>201-500 employés</option>
                <option value="501-1000" <?= $lead['company_size'] === '501-1000' ? 'selected' : '' ?>>501-1000 employés</option>
                <option value="1000+" <?= $lead['company_size'] === '1000+' ? 'selected' : '' ?>>1000+ employés</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="auto-label">Site web</label>
              <input type="text" name="website" class="auto-input" value="<?= e($lead['website'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="auto-label">Année de création</label>
              <input type="number" name="founded_year" class="auto-input" value="<?= e($lead['founded_year'] ?? '') ?>" min="1900" max="2030">
            </div>
            <div class="col-md-4">
              <label class="auto-label">Type de production</label>
              <select name="production_type" class="auto-select">
                <option value="">Non spécifié</option>
                <option value="mass" <?= $lead['production_type'] === 'mass' ? 'selected' : '' ?>>Production en série</option>
                <option value="batch" <?= $lead['production_type'] === 'batch' ? 'selected' : '' ?>>Production par lots</option>
                <option value="unit" <?= $lead['production_type'] === 'unit' ? 'selected' : '' ?>>Production unitaire</option>
                <option value="mixed" <?= $lead['production_type'] === 'mixed' ? 'selected' : '' ?>>Mixte</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="auto-label">Certifications</label>
              <input type="text" name="certifications" class="auto-input" value="<?= e($lead['certifications'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="auto-label">Notes</label>
              <textarea name="notes" class="auto-textarea" rows="3"><?= e($lead['notes'] ?? '') ?></textarea>
            </div>
          </div>

          <?php if (!empty($customFields)): ?>
            <div class="auto-section-divider"></div>
            <h6 style="color:var(--auto-text-primary);font-weight:700;font-size:0.8rem;margin-bottom:1rem;">Champs personnalisés</h6>
            <div class="row g-3 mb-3">
              <?php foreach ($customFields as $cf): ?>
                <?php
                $val = $cf['value'] ?? '';
                $fieldName = 'custom_fields[' . $cf['field_id'] . ']';
                $fieldId = 'cf_' . $cf['field_id'];
                $placeholder = $cf['placeholder_fr'] ?: $cf['placeholder'] ?: '';
                ?>
                <div class="col-md-6">
                  <label class="auto-label" for="<?= $fieldId ?>">
                    <?= e($cf['label_fr'] ?: $cf['label']) ?>
                    <?php if ($cf['is_required']): ?><span style="color:var(--auto-red);">*</span><?php endif; ?>
                  </label>
                  <?php if ($cf['field_type'] === 'text'): ?>
                    <input type="text" name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-input" value="<?= e($val) ?>" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                  <?php elseif ($cf['field_type'] === 'textarea'): ?>
                    <textarea name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-textarea" rows="2" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>><?= e($val) ?></textarea>
                  <?php elseif ($cf['field_type'] === 'number'): ?>
                    <input type="number" name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-input" value="<?= e($val) ?>" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                  <?php elseif ($cf['field_type'] === 'phone'): ?>
                    <input type="tel" name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-input" value="<?= e($val) ?>" placeholder="<?= e($placeholder) ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                  <?php elseif ($cf['field_type'] === 'date'): ?>
                    <input type="date" name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-input" value="<?= e($val) ?>" <?= $cf['is_required'] ? 'required' : '' ?>>
                  <?php elseif ($cf['field_type'] === 'select'): ?>
                    <select name="<?= $fieldName ?>" id="<?= $fieldId ?>" class="auto-select" <?= $cf['is_required'] ? 'required' : '' ?>>
                      <option value=""><?= e($placeholder ?: 'Sélectionnez...') ?></option>
                      <?php $opts = json_decode($cf['options'] ?? '[]', true); ?>
                      <?php foreach ($opts as $opt): ?>
                        <option value="<?= e($opt['value']) ?>" <?= $val === $opt['value'] ? 'selected' : '' ?>><?= e($opt['label']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  <?php elseif ($cf['field_type'] === 'multiselect'): ?>
                    <?php $selectedVals = json_decode($val, true) ?: []; ?>
                    <div style="max-height:120px;overflow-y:auto;border:1px solid var(--auto-border);border-radius:var(--auto-radius-sm);padding:0.5rem;">
                      <?php $opts = json_decode($cf['options'] ?? '[]', true); ?>
                      <?php foreach ($opts as $i => $opt): ?>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="<?= $fieldName ?>[]" value="<?= e($opt['value']) ?>" id="<?= $fieldId . '_' . $i ?>"
                            <?= in_array($opt['value'], $selectedVals) ? 'checked' : '' ?>>
                          <label class="form-check-label small" for="<?= $fieldId . '_' . $i ?>"><?= e($opt['label']) ?></label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php elseif ($cf['field_type'] === 'file'): ?>
                    <input type="file" name="documents[]" class="auto-input" <?= $cf['is_required'] && !$val ? 'required' : '' ?>>
                    <input type="hidden" name="doc_field_id" value="<?= $cf['field_id'] ?>">
                    <?php if ($val): ?>
                      <small style="color:var(--auto-text-muted);">Fichier actuel : <?= e($val) ?></small>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div style="display:flex;gap:0.75rem;border-top:1px solid var(--auto-border);padding-top:1.25rem;margin-top:1rem;">
            <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
            <a href="/admin/leads" class="auto-btn auto-btn-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>