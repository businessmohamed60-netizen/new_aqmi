/**
 * Property panel — loads the per-block form, syncs edits back to the
 * selected block's data-config, and triggers a live re-render.
 * Also handles per-block visibility cycling.
 * Depends on: window.RS_Renderers
 */
(function () {
  const body = document.getElementById('rs-properties-body');
  if (!body) return;

  let currentBlock = null;

  function show(blockEl) {
    currentBlock = blockEl;
    const key = blockEl.dataset.blockKey;
    const title = blockEl.dataset.title || '';
    const vis = blockEl.dataset.visibility || 'web_pdf';
    let cfg = {};
    try { cfg = JSON.parse(blockEl.dataset.config || '{}'); } catch (e) { cfg = {}; }

    body.innerHTML = `
      <input type="hidden" id="rs-prop-block-key" value="${key}">
      <div class="rs-prop-title">
        <label class="form-label small fw-bold">Titre du bloc</label>
        <input type="text" class="form-control form-control-sm" id="rs-prop-title" value="${escapeAttr(title)}" placeholder="Titre optionnel">
      </div>
      <div class="mb-2">
        <label class="form-label small fw-bold">Visibilité</label>
        <select class="form-select form-select-sm rs-prop-vis" id="rs-prop-visibility">
          <option value="web_pdf" ${vis==='web_pdf'?'selected':''}>Web + PDF</option>
          <option value="web_only" ${vis==='web_only'?'selected':''}>Web uniquement</option>
          <option value="pdf_only" ${vis==='pdf_only'?'selected':''}>PDF uniquement</option>
        </select>
      </div>
      <hr class="rs-prop-sep">
      <div class="rs-prop-fields" id="rs-prop-fields"></div>`;

    const fields = body.querySelector('#rs-prop-fields');
    fields.innerHTML = renderPropertyForm(key, cfg);
    bindFieldEvents();
  }

  function clear() {
    currentBlock = null;
    body.innerHTML = '<div class="rs-properties-empty"><i class="bi bi-hand-index"></i><p>Sélectionnez un bloc pour éditer ses propriétés</p></div>';
  }

  function escapeAttr(s) {
    return String(s ?? '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
  }
  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  /* ---------- per-block form generators ---------- */
  function renderPropertyForm(key, cfg) {
    const val = (k, d) => cfg[k] !== undefined ? escapeAttr(cfg[k]) : d;

    switch (key) {
      case 'global_score':
        return `
          <label class="form-label small fw-bold">Libellé</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="${val('label','Score global')}">
          <label class="form-label small fw-bold">Score</label>
          <input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="score" min="0" value="${+cfg.score||0}">
          <label class="form-label small fw-bold">Score maximum</label>
          <input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="max" min="1" value="${+cfg.max||100}">
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_rating" id="gs-r" ${cfg.show_rating!==false?'checked':''}><label class="form-check-label small" for="gs-r">Afficher la note</label></div>`;

      case 'radar_chart': {
        const axes = cfg.axes || [];
        const raw = axes.map(a => `${a.label||''},${a.value||0}`).join('\n');
        return `
          <label class="form-label small fw-bold">Axes (Libellé, Valeur par ligne)</label>
          <textarea class="form-control form-control-sm mb-2 rs-prop" data-prop="axes_raw" rows="5">${escapeHtml(raw)}</textarea>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="legend" id="rc-l" ${cfg.legend!==false?'checked':''}><label class="form-check-label small" for="rc-l">Afficher la légende</label></div>`;
      }

      case 'gauge':
        return `
          <label class="form-label small fw-bold">Libellé</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="${val('label','Indicateur')}">
          <label class="form-label small fw-bold">Valeur</label>
          <input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="value" value="${+cfg.value||0}">
          <div class="row g-2 mb-2"><div class="col-6"><label class="form-label small fw-bold">Min</label><input type="number" class="form-control form-control-sm rs-prop" data-prop="min" value="${+cfg.min||0}"></div><div class="col-6"><label class="form-label small fw-bold">Max</label><input type="number" class="form-control form-control-sm rs-prop" data-prop="max" value="${+cfg.max||100}"></div></div>
          <label class="form-label small fw-bold">Unité</label>
          <input type="text" class="form-control form-control-sm rs-prop" data-prop="unit" value="${val('unit','%')}">`;

      case 'recommendations': {
        const items = cfg.items || [];
        const raw = items.map(i => (typeof i === 'object' ? i.text : i) || '').join('\n');
        return `
          <label class="form-label small fw-bold">Titre de la section</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="title" value="${val('title','Recommandations')}">
          <label class="form-label small fw-bold">Recommandations (une par ligne)</label>
          <textarea class="form-control form-control-sm rs-prop" data-prop="items_raw" rows="6">${escapeHtml(raw)}</textarea>`;
      }

      case 'company_info': {
        const fields = cfg.fields || [];
        const rows = fields.map((f, i) => `<div class="input-group input-group-sm mb-1"><input type="text" class="form-control rs-cinfo-key" placeholder="Clé" value="${escapeAttr(f.key||'')}"><input type="text" class="form-control rs-cinfo-label" placeholder="Libellé" value="${escapeAttr(f.label||'')}"><button type="button" class="btn btn-outline-danger rs-cinfo-remove"><i class="bi bi-x"></i></button></div>`).join('');
        return `
          <label class="form-label small fw-bold">Champs affichés</label>
          <div id="rs-cinfo-fields">${rows || '<p class="text-muted small">Aucun champ</p>'}</div>
          <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" id="rs-cinfo-add"><i class="bi bi-plus"></i> Ajouter un champ</button>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_logo" id="ci-l" ${cfg.show_logo!==false?'checked':''}><label class="form-check-label small" for="ci-l">Inclure le logo entreprise</label></div>`;
      }

      case 'aqmi_logo':
      case 'company_logo': {
        const isCompany = key === 'company_logo';
        const urlField = isCompany ? `<label class="form-label small fw-bold">URL du logo</label><input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="url" value="${val('url','')}">` : '';
        return `${urlField}
          <label class="form-label small fw-bold">Taille</label>
          <select class="form-select form-select-sm mb-2 rs-prop" data-prop="size"><option value="sm" ${cfg.size==='sm'?'selected':''}>Petite</option><option value="md" ${cfg.size==='md'||!cfg.size?'selected':''}>Moyenne</option><option value="lg" ${cfg.size==='lg'?'selected':''}>Grande</option></select>
          <label class="form-label small fw-bold">Alignement</label>
          <select class="form-select form-select-sm rs-prop" data-prop="align"><option value="left" ${cfg.align==='left'||!cfg.align?'selected':''}>Gauche</option><option value="center" ${cfg.align==='center'?'selected':''}>Centre</option><option value="right" ${cfg.align==='right'?'selected':''}>Droite</option></select>`;
      }

      case 'official_stamp':
        return `
          <label class="form-label small fw-bold">Style du tampon</label>
          <select class="form-select form-select-sm mb-2 rs-prop" data-prop="style"><option value="circular" ${cfg.style==='circular'||!cfg.style?'selected':''}>Circulaire</option><option value="rectangular" ${cfg.style==='rectangular'?'selected':''}>Rectangulaire</option><option value="badge" ${cfg.style==='badge'?'selected':''}>Badge</option></select>
          <label class="form-label small fw-bold">Texte principal</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="text" value="${val('text','CERTIFIÉ')}">
          <label class="form-label small fw-bold">Sous-texte</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="subtext" value="${val('subtext','AQMI')}">
          <label class="form-label small fw-bold">Couleur</label>
          <input type="color" class="form-control form-control-color mb-2 rs-prop" data-prop="color" value="${val('color','#0d47a1')}">
          <label class="form-label small fw-bold">Taille (px)</label>
          <input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="size" min="60" max="300" value="${+cfg.size||100}">
          <label class="form-label small fw-bold">Alignement</label>
          <select class="form-select form-select-sm rs-prop" data-prop="align"><option value="left" ${cfg.align==='left'?'selected':''}>Gauche</option><option value="center" ${cfg.align==='center'?'selected':''}>Centre</option><option value="right" ${cfg.align==='right'||!cfg.align?'selected':''}>Droite</option></select>`;

      case 'qr_code':
        return `
          <label class="form-label small fw-bold">Donnée encodée</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="value" value="${val('url','')}">
          <label class="form-label small fw-bold">Taille (px)</label>
          <input type="number" class="form-control form-control-sm mb-2 rs-prop" data-prop="size" min="60" max="400" value="${+cfg.size||120}">
          <label class="form-label small fw-bold">Libellé sous le QR</label>
          <input type="text" class="form-control form-control-sm rs-prop" data-prop="label" value="${val('label','')}">`;

      case 'signature':
        return `
          <label class="form-label small fw-bold">Nom du signataire</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="label" value="${val('label','')}">
          <label class="form-label small fw-bold">Fonction</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="role" value="${val('role','')}">
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="sig-d" ${cfg.show_date!==false?'checked':''}><label class="form-check-label small" for="sig-d">Afficher la date</label></div>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_stamp" id="sig-s" ${cfg.show_stamp?'checked':''}><label class="form-check-label small" for="sig-s">Afficher le tampon officiel</label></div>`;

      case 'header':
        return `
          <label class="form-label small fw-bold">Texte</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="text" value="${val('text','')}">
          <small class="d-block text-muted mb-2">Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}</small>
          <label class="form-label small fw-bold">Niveau de titre</label>
          <select class="form-select form-select-sm mb-2 rs-prop" data-prop="level"><option value="1" ${+cfg.level===1||!cfg.level?'selected':''}>H1</option><option value="2" ${+cfg.level===2?'selected':''}>H2</option><option value="3" ${+cfg.level===3?'selected':''}>H3</option></select>
          <label class="form-label small fw-bold">Alignement</label>
          <select class="form-select form-select-sm mb-2 rs-prop" data-prop="align"><option value="left" ${cfg.align==='left'||!cfg.align?'selected':''}>Gauche</option><option value="center" ${cfg.align==='center'?'selected':''}>Centre</option><option value="right" ${cfg.align==='right'?'selected':''}>Droite</option></select>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_report_number" id="hd-rn" ${cfg.show_report_number?'checked':''}><label class="form-check-label small" for="hd-rn">Afficher N° rapport</label></div>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="hd-d" ${cfg.show_date?'checked':''}><label class="form-check-label small" for="hd-d">Afficher la date</label></div>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_page_number" id="hd-pn" ${cfg.show_page_number?'checked':''}><label class="form-check-label small" for="hd-pn">Afficher la pagination</label></div>`;

      case 'footer':
        return `
          <label class="form-label small fw-bold">Texte</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="text" value="${val('text','')}">
          <small class="d-block text-muted mb-2">Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}</small>
          <label class="form-label small fw-bold">Alignement</label>
          <select class="form-select form-select-sm mb-2 rs-prop" data-prop="align"><option value="left" ${cfg.align==='left'?'selected':''}>Gauche</option><option value="center" ${cfg.align==='center'||!cfg.align?'selected':''}>Centre</option><option value="right" ${cfg.align==='right'?'selected':''}>Droite</option></select>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_report_number" id="ft-rn" ${cfg.show_report_number?'checked':''}><label class="form-check-label small" for="ft-rn">Afficher N° rapport</label></div>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_date" id="ft-d" ${cfg.show_date?'checked':''}><label class="form-check-label small" for="ft-d">Afficher la date</label></div>
          <div class="form-check form-switch"><input class="form-check-input rs-prop" type="checkbox" data-prop="show_page_number" id="ft-pn" ${cfg.show_page_number!==false?'checked':''}><label class="form-check-label small" for="ft-pn">Afficher la pagination</label></div>`;

      case 'rich_text':
        return `
          <label class="form-label small fw-bold">Contenu (HTML)</label>
          <textarea class="form-control form-control-sm rs-prop" data-prop="html" rows="8">${escapeHtml(cfg.html||'')}</textarea>`;

      case 'image':
        return `
          <label class="form-label small fw-bold">URL de l'image</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="url" value="${val('url','')}">
          <label class="form-label small fw-bold">Texte alternatif</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="alt" value="${val('alt','')}">
          <label class="form-label small fw-bold">Largeur</label>
          <input type="text" class="form-control form-control-sm mb-2 rs-prop" data-prop="width" value="${val('width','100%')}">
          <label class="form-label small fw-bold">Alignement</label>
          <select class="form-select form-select-sm rs-prop" data-prop="align"><option value="left" ${cfg.align==='left'?'selected':''}>Gauche</option><option value="center" ${cfg.align==='center'||!cfg.align?'selected':''}>Centre</option><option value="right" ${cfg.align==='right'?'selected':''}>Droite</option></select>`;

      default:
        return '<div class="text-muted small">Aucune propriété pour ce bloc.</div>';
    }
  }

  /* ---------- bind field changes ---------- */
  function bindFieldEvents() {
    const titleInput = body.querySelector('#rs-prop-title');
    if (titleInput) {
      titleInput.addEventListener('input', () => {
        if (!currentBlock) return;
        currentBlock.dataset.title = titleInput.value;
        const typeEl = currentBlock.querySelector('.rs-block-type');
        if (typeEl) {
          const iconMatch = typeEl.innerHTML.match(/<i[^>]*><\/i>/);
          typeEl.innerHTML = (iconMatch ? iconMatch[0] + ' ' : '') + escapeHtml(titleInput.value || currentBlock.dataset.blockKey);
        }
        markBuilderDirty();
      });
    }

    const visSelect = body.querySelector('#rs-prop-visibility');
    if (visSelect) {
      visSelect.addEventListener('change', () => {
        if (!currentBlock) return;
        currentBlock.dataset.visibility = visSelect.value;
        updateBlockVisBadge(currentBlock);
        markBuilderDirty();
      });
    }

    body.querySelectorAll('.rs-prop').forEach(field => {
      const prop = field.dataset.prop;
      const ev = field.type === 'checkbox' ? 'change' : 'input';
      field.addEventListener(ev, () => syncField(prop, field));
    });

    // company_info dynamic fields
    const cinfoAdd = body.querySelector('#rs-cinfo-add');
    if (cinfoAdd) {
      cinfoAdd.addEventListener('click', () => {
        const wrap = body.querySelector('#rs-cinfo-fields');
        const row = document.createElement('div');
        row.className = 'input-group input-group-sm mb-1';
        row.innerHTML = '<input type="text" class="form-control rs-cinfo-key" placeholder="Clé" value=""><input type="text" class="form-control rs-cinfo-label" placeholder="Libellé" value=""><button type="button" class="btn btn-outline-danger rs-cinfo-remove"><i class="bi bi-x"></i></button>';
        wrap.appendChild(row);
        bindCinfoRow(row);
        syncCinfo();
      });
      body.querySelectorAll('.rs-cinfo-row, #rs-cinfo-fields > .input-group').forEach(bindCinfoRow);
    }
  }

  function updateBlockVisBadge(blockEl) {
    const vis = blockEl.dataset.visibility || 'web_pdf';
    const meta = { web_pdf: { icon: 'bi-eye', label: 'Web+PDF' }, web_only: { icon: 'bi-globe', label: 'Web' }, pdf_only: { icon: 'bi-file-pdf', label: 'PDF' } }[vis];
    const badge = blockEl.querySelector('.rs-vis-badge');
    if (badge && meta) badge.innerHTML = '<i class="bi ' + meta.icon + '"></i> ' + meta.label;
  }

  function bindCinfoRow(row) {
    const removeBtn = row.querySelector('.rs-cinfo-remove');
    if (removeBtn) {
      removeBtn.addEventListener('click', () => { row.remove(); syncCinfo(); markBuilderDirty(); });
    }
    row.querySelectorAll('input').forEach(inp => {
      inp.addEventListener('input', () => { syncCinfo(); markBuilderDirty(); });
    });
  }

  function syncCinfo() {
    if (!currentBlock) return;
    const cfg = readConfig();
    cfg.fields = [];
    body.querySelectorAll('#rs-cinfo-fields > .input-group').forEach(row => {
      const key = row.querySelector('.rs-cinfo-key')?.value || '';
      const label = row.querySelector('.rs-cinfo-label')?.value || '';
      if (key || label) cfg.fields.push({ key, label });
    });
    writeConfig(cfg);
    reRender();
  }

  function syncField(prop, field) {
    if (!currentBlock) return;
    const cfg = readConfig();

    if (prop === 'axes_raw') {
      cfg.axes = field.value.split('\n').filter(l => l.trim()).map(line => {
        const [label, value] = line.split(',');
        return { label: (label || '').trim(), value: parseInt(value || '0', 10) };
      });
      delete cfg.axes_raw;
    } else if (prop === 'items_raw') {
      cfg.items = field.value.split('\n').filter(l => l.trim()).map(line => ({ text: line.trim() }));
      delete cfg.items_raw;
    } else if (field.type === 'checkbox') {
      cfg[prop] = field.checked;
    } else if (field.type === 'number') {
      cfg[prop] = parseFloat(field.value) || 0;
    } else if (field.type === 'color') {
      cfg[prop] = field.value;
    } else {
      cfg[prop] = field.value;
    }

    writeConfig(cfg);
    reRender();
    markBuilderDirty();
  }

  function readConfig() {
    try { return JSON.parse(currentBlock.dataset.config || '{}'); } catch (e) { return {}; }
  }
  function writeConfig(cfg) {
    currentBlock.dataset.config = JSON.stringify(cfg);
  }
  function reRender() {
    if (!currentBlock) return;
    const key = currentBlock.dataset.blockKey;
    const cfg = readConfig();
    const target = currentBlock.querySelector('.rs-live-render');
    if (target) target.innerHTML = window.RS_Renderers.render(key, cfg);
  }

  function markBuilderDirty() {
    if (window.RS_Builder_MarkDirty) window.RS_Builder_MarkDirty();
    else {
      const msg = document.getElementById('rs-status-msg');
      if (msg) msg.textContent = 'Modifications non enregistrées';
    }
  }

  /* ---------- expose ---------- */
  window.RS_Properties = { show, clear };
})();
