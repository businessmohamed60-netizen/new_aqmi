/**
 * Builder orchestration — SortableJS drag & drop, block lifecycle, save, settings.
 * Depends on: SortableJS, window.RS_Renderers, window.RS_Properties
 */
(function () {
  const canvas = document.getElementById('rs-canvas');
  const palettePane = document.getElementById('rs-palette-pane');
  const emptyState = document.getElementById('rs-canvas-empty');
  const countBadge = document.getElementById('rs-canvas-count');
  const statusMsg = document.getElementById('rs-status-msg');
  const statusSave = document.getElementById('rs-status-save');
  const saveBtn = document.getElementById('rs-btn-save');
  const toastEl = document.getElementById('rs-toast');
  const toastBody = document.getElementById('rs-toast-body');
  const TEMPLATE_ID = window.RS_TEMPLATE_ID || 0;
  const TEMPLATE_SETTINGS = window.RS_TEMPLATE_SETTINGS || {};
  const VIS_CYCLE = ['web_pdf', 'web_only', 'pdf_only'];
  const VIS_META = {
    web_pdf:  { icon: 'bi-eye',       label: 'Web+PDF' },
    web_only: { icon: 'bi-globe',     label: 'Web' },
    pdf_only: { icon: 'bi-file-pdf',  label: 'PDF' },
  };

  let dirty = false;
  const undoStack = [];
  const redoStack = [];

  /* ---------- helpers ---------- */
  function markDirty() {
    dirty = true;
    statusMsg.textContent = 'Modifications non enregistrées';
    statusSave.textContent = '';
  }

  function snapshot() {
    undoStack.push(canvas.innerHTML);
    if (undoStack.length > 50) undoStack.shift();
    redoStack.length = 0;
  }

  function showToast(msg, ok) {
    toastBody.textContent = msg;
    toastEl.className = 'toast align-items-center text-white border-0 ' + (ok ? 'bg-success' : 'bg-danger');
    new bootstrap.Toast(toastEl, { delay: 2500 }).show();
  }

  function updateCount() {
    const blocks = canvas.querySelectorAll('.rs-block');
    countBadge.textContent = blocks.length + ' bloc(s)';
    if (emptyState) emptyState.style.display = blocks.length === 0 ? '' : 'none';
  }

  function renderPreview(blockEl) {
    const key = blockEl.dataset.blockKey;
    let cfg = {};
    try { cfg = JSON.parse(blockEl.dataset.config || '{}'); } catch (e) { cfg = {}; }
    const target = blockEl.querySelector('.rs-live-render');
    if (target) target.innerHTML = window.RS_Renderers.render(key, cfg);
  }

  function renderAllPreviews() {
    canvas.querySelectorAll('.rs-block').forEach(renderPreview);
  }

  function selectBlock(blockEl) {
    canvas.querySelectorAll('.rs-block').forEach(b => b.classList.remove('rs-selected'));
    blockEl.classList.add('rs-selected');
    if (window.RS_Properties && window.RS_Properties.show) {
      window.RS_Properties.show(blockEl);
    }
  }

  function deselectAll() {
    canvas.querySelectorAll('.rs-block').forEach(b => b.classList.remove('rs-selected'));
    if (window.RS_Properties && window.RS_Properties.clear) {
      window.RS_Properties.clear();
    }
  }

  function updateVisBadge(blockEl) {
    const vis = blockEl.dataset.visibility || 'web_pdf';
    const meta = VIS_META[vis] || VIS_META.web_pdf;
    const badge = blockEl.querySelector('.rs-vis-badge');
    if (badge) {
      badge.innerHTML = '<i class="bi ' + meta.icon + '"></i> ' + meta.label;
    }
  }

  /* ---------- create block from palette ---------- */
  function createBlockElement(key, label, icon, cfg, visibility) {
    const el = document.createElement('div');
    const vis = visibility || 'web_pdf';
    const meta = VIS_META[vis];
    el.className = 'rs-block';
    el.dataset.blockKey = key;
    el.dataset.blockId = '';
    el.dataset.config = JSON.stringify(cfg || {});
    el.dataset.title = label;
    el.dataset.visibility = vis;
    el.innerHTML = `
      <div class="rs-block-toolbar">
        <span class="rs-block-handle" title="Déplacer"><i class="bi bi-grip-vertical"></i></span>
        <span class="rs-block-type"><i class="bi ${icon}"></i> ${label}</span>
        <span class="rs-vis-badge badge bg-info text-dark" title="Visibilité (clic pour changer)">
          <i class="bi ${meta.icon}"></i> ${meta.label}
        </span>
        <div class="rs-block-actions ms-auto">
          <button type="button" class="btn btn-sm rs-block-toggle" title="Activer/Désactiver"><i class="bi bi-eye"></i></button>
          <button type="button" class="btn btn-sm rs-block-edit" title="Propriétés"><i class="bi bi-sliders"></i></button>
          <button type="button" class="btn btn-sm rs-block-duplicate" title="Dupliquer"><i class="bi bi-files"></i></button>
          <button type="button" class="btn btn-sm rs-block-delete" title="Supprimer"><i class="bi bi-trash"></i></button>
        </div>
      </div>
      <div class="rs-block-preview"><div class="rs-live-render"></div></div>`;
    renderPreview(el);
    return el;
  }

  /* ---------- SortableJS setup ---------- */
  // Canvas: receive blocks, reorder
  const canvasSortable = new Sortable(canvas, {
    group: { name: 'rs-blocks', pull: false, put: true },
    animation: 180,
    handle: '.rs-block-handle',
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    onAdd(evt) { snapshot(); markDirty(); },
    onUpdate(evt) { snapshot(); markDirty(); },
    onSort() { updateCount(); },
  });

  // Palette: one Sortable on the container, clone into canvas
  const paletteContainer = palettePane?.querySelector('.rs-pane-body');
  if (paletteContainer) {
    new Sortable(paletteContainer, {
      group: { name: 'rs-blocks', pull: 'clone', put: false },
      sort: false,
      animation: 180,
      filter: '.rs-palette-cat',
      preventOnFilter: true,
      onEnd(evt) {
        const item = evt.item;
        if (!item.classList.contains('rs-palette-item')) return;
        // Remove the cloned palette item from canvas, replace with a real block
        item.remove();
        if (evt.to === canvas) {
          const block = createBlockElement(
            item.dataset.blockKey,
            item.dataset.blockLabel,
            item.dataset.blockIcon,
            {}
          );
          canvas.insertBefore(block, evt.to.childNodes[evt.newIndex] || null);
          snapshot();
          markDirty();
          updateCount();
        }
      },
    });
  }

  // Click-to-add fallback (more reliable in some browsers)
  document.querySelectorAll('.rs-palette-item').forEach(item => {
    item.addEventListener('click', function (e) {
      // Prevent SortableJS drag-click conflict
      if (e.detail === 0) return;
      const block = createBlockElement(
        item.dataset.blockKey,
        item.dataset.blockLabel,
        item.dataset.blockIcon,
        {}
      );
      canvas.appendChild(block);
      snapshot();
      markDirty();
      updateCount();
      selectBlock(block);
    });
  });

  /* ---------- canvas event delegation ---------- */
  canvas.addEventListener('click', function (e) {
    const block = e.target.closest('.rs-block');
    if (!block) return;

    if (e.target.closest('.rs-block-delete')) {
      snapshot();
      block.remove();
      markDirty();
      updateCount();
      if (block.classList.contains('rs-selected')) deselectAll();
      return;
    }
    if (e.target.closest('.rs-block-duplicate')) {
      snapshot();
      const clone = block.cloneNode(true);
      clone.dataset.blockId = '';
      block.after(clone);
      renderPreview(clone);
      updateVisBadge(clone);
      markDirty();
      updateCount();
      return;
    }
    if (e.target.closest('.rs-block-toggle')) {
      snapshot();
      block.classList.toggle('rs-block-disabled');
      const enabled = !block.classList.contains('rs-block-disabled');
      block.querySelector('.rs-block-toggle i').className = enabled ? 'bi bi-eye' : 'bi bi-eye-slash';
      markDirty();
      return;
    }
    if (e.target.closest('.rs-vis-badge')) {
      snapshot();
      const cur = block.dataset.visibility || 'web_pdf';
      const next = VIS_CYCLE[(VIS_CYCLE.indexOf(cur) + 1) % VIS_CYCLE.length];
      block.dataset.visibility = next;
      updateVisBadge(block);
      markDirty();
      return;
    }
    selectBlock(block);
  });

  /* ---------- palette group collapse ---------- */
  document.querySelectorAll('.rs-palette-cat').forEach(cat => {
    cat.addEventListener('click', () => cat.parentElement.classList.toggle('collapsed'));
  });

  /* ---------- settings bar ---------- */
  const settingsBar = document.querySelector('.rs-settings-bar');
  if (settingsBar) {
    // Initialize from template settings
    settingsBar.querySelectorAll('.rs-setting').forEach(input => {
      const key = input.dataset.setting;
      if (TEMPLATE_SETTINGS[key] !== undefined && TEMPLATE_SETTINGS[key] !== null) {
        input.value = TEMPLATE_SETTINGS[key];
      }
    });

    // Orientation toggle also changes canvas width
    const orientSel = document.getElementById('rs-set-orientation');
    if (orientSel) {
      orientSel.addEventListener('change', function () {
        canvas.classList.toggle('rs-landscape', this.value === 'landscape');
        markDirty();
      });
      canvas.classList.toggle('rs-landscape', orientSel.value === 'landscape');
    }

    settingsBar.querySelectorAll('.rs-setting').forEach(input => {
      input.addEventListener('input', markDirty);
      input.addEventListener('change', markDirty);
    });
  }

  /* ---------- undo / redo ---------- */
  document.getElementById('rs-btn-undo').addEventListener('click', () => {
    if (!undoStack.length) return;
    redoStack.push(canvas.innerHTML);
    canvas.innerHTML = undoStack.pop();
    renderAllPreviews();
    canvas.querySelectorAll('.rs-block').forEach(updateVisBadge);
    updateCount();
    dirty = true;
    statusMsg.textContent = 'Annulé';
  });
  document.getElementById('rs-btn-redo').addEventListener('click', () => {
    if (!redoStack.length) return;
    undoStack.push(canvas.innerHTML);
    canvas.innerHTML = redoStack.pop();
    renderAllPreviews();
    canvas.querySelectorAll('.rs-block').forEach(updateVisBadge);
    updateCount();
    dirty = true;
    statusMsg.textContent = 'Refaire';
  });

  /* ---------- save ---------- */
  function collectBlocks() {
    const blocks = [];
    canvas.querySelectorAll('.rs-block').forEach((el, i) => {
      let cfg = {};
      try { cfg = JSON.parse(el.dataset.config || '{}'); } catch (e) { cfg = {}; }
      blocks.push({
        block_key: el.dataset.blockKey,
        title: el.dataset.title || '',
        block_config: cfg,
        is_enabled: !el.classList.contains('rs-block-disabled'),
        visibility: el.dataset.visibility || 'web_pdf',
      });
    });
    return blocks;
  }

  function collectSettings() {
    const settings = {};
    settingsBar?.querySelectorAll('.rs-setting').forEach(input => {
      settings[input.dataset.setting] = input.value;
    });
    return settings;
  }

  saveBtn.addEventListener('click', () => {
    const blocks = collectBlocks();
    const settings = collectSettings();
    statusMsg.textContent = 'Enregistrement...';
    fetch(`/admin/reportstudio/builder/${TEMPLATE_ID}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ blocks, settings }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          dirty = false;
          statusMsg.textContent = 'Enregistré';
          statusSave.textContent = new Date().toLocaleTimeString('fr-FR');
          showToast('Modifications enregistrées (' + data.count + ' blocs)', true);
        } else {
          showToast('Erreur: ' + (data.error || 'inconnue'), false);
          statusMsg.textContent = 'Erreur';
        }
      })
      .catch(() => { showToast('Erreur réseau', false); statusMsg.textContent = 'Erreur réseau'; });
  });

  window.addEventListener('beforeunload', (e) => {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
  });

  // Expose dirty flag so the property panel can mark the builder dirty.
  window.RS_Builder_MarkDirty = markDirty;

  /* ---------- init ---------- */
  renderAllPreviews();
  canvas.querySelectorAll('.rs-block').forEach(updateVisBadge);
  updateCount();
})();
