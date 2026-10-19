<?php
$title = 'Paramètres';
ob_start();
?>
<style>
.auto-settings-wrap { max-width:800px; }
.auto-settings-header { margin-bottom: 1.5rem; }
.auto-settings-header h5 { color: var(--auto-text-primary); font-weight: 700; font-size: 0.95rem; margin-bottom: 0; }
.auto-settings-table td:first-child code { color: var(--auto-cyan); font-family: var(--auto-font-mono); font-size: 0.75rem; }
.form-switch .form-check-input { background-color: var(--auto-border); border-color: var(--auto-border); cursor: pointer; }
.form-switch .form-check-input:checked { background-color: var(--auto-cyan); border-color: var(--auto-cyan); }
</style>

<div class="auto-settings-wrap auto-fade-in">
  <div class="auto-settings-header">
    <h5><i class="fas fa-cog me-2" style="color:var(--auto-cyan);"></i>Paramètres</h5>
  </div>

  <div class="auto-glass-card p-4">
    <form method="POST" action="/admin/settings/save">
      <?= csrf_field() ?>
      <div style="overflow-x:auto;">
        <table class="auto-table auto-settings-table">
          <thead><tr><th>Clé</th><th>Valeur</th><th>Type</th></tr></thead>
          <tbody>
            <?php if (!empty($settings)): ?>
              <?php foreach ($settings as $s): ?>
                <tr>
                  <td><code><?= e($s['setting_key']) ?></code></td>
                  <td>
                    <?php if ($s['setting_type'] === 'boolean'): ?>
                      <div class="form-check form-switch" style="padding-left:2.5rem;">
                        <input class="form-check-input" type="checkbox" name="settings[<?= e($s['setting_key']) ?>]" value="1" <?= $s['setting_value'] === '1' ? 'checked' : '' ?>>
                      </div>
                    <?php else: ?>
                      <input type="text" name="settings[<?= e($s['setting_key']) ?>]" class="auto-input" value="<?= e($s['setting_value'] ?? '') ?>">
                    <?php endif; ?>
                  </td>
                  <td><span class="auto-badge auto-badge-cyan"><?= e($s['setting_type']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" style="text-align:center;color:var(--auto-text-muted);padding:2rem;">Aucun paramètre</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div style="border-top:1px solid var(--auto-border);padding-top:1.25rem;margin-top:1rem;">
        <button type="submit" class="auto-btn auto-btn-primary"><i class="fas fa-save me-1"></i>Enregistrer les paramètres</button>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>