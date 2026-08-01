/**
 * Report Studio Builder — React App
 * CDN React 18 + ApexCharts + SortableJS
 * Features: grid column layout, live ApexCharts previews, property panel, undo/redo, save
 */
(function () {
  'use strict';

  const { useState, useEffect, useRef, useCallback, useMemo } = React;
  const h = React.createElement;

  // ---- Block metadata (mirrors BlockRegistry::BLOCKS) ----
  const BLOCK_META = {
    global_score:    { category: 'metrics',   label: 'Global Score',        icon: 'bi-speedometer' },
    radar_chart:     { category: 'charts',    label: 'Radar Chart',         icon: 'bi-graph-up' },
    gauge:           { category: 'metrics',   label: 'Gauge',               icon: 'bi-dial' },
    recommendations: { category: 'content',  label: 'Recommendations',     icon: 'bi-list-check' },
    company_info:    { category: 'content',  label: 'Company Information', icon: 'bi-building' },
    aqmi_logo:       { category: 'branding',  label: 'AQMI Logo',           icon: 'bi-award' },
    company_logo:    { category: 'branding',  label: 'Company Logo',        icon: 'bi-image' },
    official_stamp:  { category: 'branding',  label: 'Official Stamp',      icon: 'bi-patch-check-fill' },
    qr_code:         { category: 'utility',   label: 'QR Code',             icon: 'bi-qr-code' },
    signature:       { category: 'utility',   label: 'Signature',           icon: 'bi-pen' },
    header:          { category: 'structure', label: 'Header',             icon: 'bi-text-left' },
    footer:          { category: 'structure', label: 'Footer',             icon: 'bi-text-right' },
    rich_text:       { category: 'content',   label: 'Rich Text',           icon: 'bi-fonts' },
    image:           { category: 'media',     label: 'Image',               icon: 'bi-card-image' },
  };

  const DEFAULT_CONFIGS = {
    global_score:    { label: 'Score global', score: 0, max: 100, show_rating: true },
    radar_chart:     { axes: [{ label: 'Domaine 1', value: 0 }], legend: true },
    gauge:           { label: 'Indicateur', value: 0, min: 0, max: 100, unit: '%' },
    recommendations: { title: 'Recommandations', items: [{ text: '' }] },
    company_info:    { fields: [{ key: '', label: '' }], show_logo: true },
    aqmi_logo:       { size: 'md', align: 'left' },
    company_logo:    { url: '', size: 'md', align: 'left' },
    official_stamp:  { style: 'circular', text: 'CERTIFIÉ', subtext: 'AQMI', color: '#0d47a1', size: 100, align: 'right' },
    qr_code:         { value: '', size: 120, label: '' },
    signature:       { label: '', role: '', show_date: true, show_stamp: false },
    header:          { text: 'Titre', level: 1, align: 'left', show_report_number: false, show_date: false, show_page_number: false },
    footer:          { text: '', align: 'center', show_report_number: false, show_date: false, show_page_number: true },
    rich_text:       { html: '' },
    image:           { url: '', alt: '', width: '100%', align: 'center' },
  };

  // ---- Utility ----
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const escAttr = (s) => String(s ?? '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
  const uid = () => 'b_' + Math.random().toString(36).slice(2, 10);

  // ---- ApexCharts Live Preview Components ----
  function RadarPreview({ cfg }) {
    const ref = useRef(null);
    const chartRef = useRef(null);
    useEffect(() => {
      if (!ref.current || !window.ApexCharts) return;
      const axes = cfg.axes || [];
      const labels = axes.map(a => a.label || '');
      const values = axes.map(a => +a.value || 0);
      if (chartRef.current) { try { chartRef.current.destroy(); } catch (e) {} }
      chartRef.current = new ApexCharts(ref.current, {
        chart: { type: 'radar', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: [{ name: 'Radar', data: values }],
        xaxis: { categories: labels },
        yaxis: { min: 0, max: 100 },
        legend: { show: cfg.legend !== false },
        colors: ['#0d47a1'],
        fill: { opacity: 0.2 },
        markers: { size: 4, colors: ['#0d47a1'] },
      });
      chartRef.current.render();
      return () => { try { chartRef.current.destroy(); } catch (e) {} };
    }, [cfg]);
    return h('div', { ref });
  }

  function GaugePreview({ cfg }) {
    const ref = useRef(null);
    const chartRef = useRef(null);
    useEffect(() => {
      if (!ref.current || !window.ApexCharts) return;
      const v = +cfg.value || 0, max = +cfg.max || 100, min = +cfg.min || 0;
      const range = Math.max(1, max - min);
      const pct = Math.min(100, Math.max(0, Math.round(((v - min) / range) * 100)));
      if (chartRef.current) { try { chartRef.current.destroy(); } catch (e) {} }
      chartRef.current = new ApexCharts(ref.current, {
        chart: { type: 'radialBar', height: 160, sparkline: { enabled: true } },
        series: [pct],
        plotOptions: { radialBar: { startAngle: -135, endAngle: 135, hollow: { size: '62%' }, dataLabels: { name: { show: false }, value: { show: false } }, track: { background: '#e2e8f0' } } },
        fill: { colors: ['#00897b'] },
        stroke: { lineCap: 'round' },
      });
      chartRef.current.render();
      return () => { try { chartRef.current.destroy(); } catch (e) {} };
    }, [cfg]);
    return h('div', { ref, style: { display: 'inline-block' } });
  }

  // ---- Block Live Preview ----
  function BlockPreview({ blockKey, cfg }) {
    const label = BLOCK_META[blockKey]?.label || blockKey;
    switch (blockKey) {
      case 'global_score': {
        const score = +cfg.score || 0, max = +cfg.max || 100;
        const pct = max > 0 ? Math.min(100, Math.round((score / max) * 100)) : 0;
        return h('div', { className: 'text-center py-2' },
          h('div', { className: 'rs-score-ring', style: { '--rs-pct': pct } },
            h('div', { className: 'rs-score-ring-inner' },
              h('span', { className: 'rs-score-value' }, score, h('small', null, '/' + max)))),
          h('h6', { className: 'mt-1 mb-0' }, esc(cfg.label || 'Score global')));
      }
      case 'radar_chart':
        return h(RadarPreview, { cfg });
      case 'gauge':
        return h('div', { className: 'text-center py-2' },
          h(GaugePreview, { cfg }),
          h('div', { className: 'fs-5 fw-bold' }, +cfg.value || 0, h('small', { className: 'text-muted' }, esc(cfg.unit || '%'))),
          h('small', { className: 'text-muted' }, esc(cfg.label || 'Indicateur')));
      case 'recommendations': {
        const items = cfg.items || [];
        return h('div', { className: 'py-1' },
          h('ol', { className: 'rs-reco-list mb-0' },
            items.slice(0, 3).map((i, idx) => h('li', { key: idx, className: 'small' }, esc((typeof i === 'object' ? i.text : i) || ''))),
            items.length > 3 ? h('li', { className: 'small text-muted' }, '… +' + (items.length - 3) + ' autre(s)') : null));
      }
      case 'company_info': {
        const f = cfg.fields || [];
        return h('div', { className: 'py-1' },
          h('dl', { className: 'row mb-0 small' },
            f.slice(0, 4).map((x, i) => h(React.Fragment, { key: i },
              h('dt', { className: 'col-5 text-muted' }, esc(x.label || '')),
              h('dd', { className: 'col-7' }, esc(x.key || '')))));
      }
      case 'aqmi_logo': {
        const sizes = { sm: '1.1rem', md: '1.6rem', lg: '2.4rem' };
        return h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' },
          h('span', { className: 'rs-aqmi-mark', style: { fontSize: sizes[cfg.size || 'md'] } }, 'AQMI'));
      }
      case 'company_logo':
        return cfg.url
          ? h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' }, h('img', { src: cfg.url, style: { maxHeight: '80px' } }))
          : h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' }, h('i', { className: 'bi bi-image fs-2 text-muted' }));
      case 'official_stamp': {
        const text = esc(cfg.text || 'CERTIFIÉ'), sub = esc(cfg.subtext || 'AQMI'), color = esc(cfg.color || '#0d47a1'), size = +cfg.size || 100;
        if (cfg.style === 'badge') {
          return h('div', { className: 'text-' + (cfg.align || 'right') + ' py-2' },
            h('div', { className: 'rs-stamp rs-stamp-badge', style: { color } },
              h('i', { className: 'bi bi-patch-check-fill', style: { fontSize: Math.round(size * 0.5) + 'px' } }),
              h('span', { className: 'rs-stamp-text small d-block mt-1' }, text)));
        }
        const cls = cfg.style === 'rectangular' ? 'rs-stamp-rectangular' : 'rs-stamp-circular';
        return h('div', { className: 'text-' + (cfg.align || 'right') + ' py-2' },
          h('div', { className: 'rs-stamp ' + cls, style: { width: size + 'px', height: cfg.style === 'rectangular' ? 'auto' : size + 'px', borderColor: color, color } },
            h('div', { className: 'rs-stamp-inner' },
              h('span', { className: 'rs-stamp-text' }, text),
              h('span', { className: 'rs-stamp-subtext' }, sub))));
      }
      case 'qr_code': {
        const size = +cfg.size || 120;
        return cfg.value
          ? h('div', { className: 'text-center py-1' }, h('img', { src: 'https://api.qrserver.com/v1/create-qr-code/?size=' + size + 'x' + size + '&data=' + encodeURIComponent(cfg.value), width: size, height: size }))
          : h('div', { className: 'text-center py-1' }, h('i', { className: 'bi bi-qr-code fs-1 text-muted' }));
      }
      case 'signature':
        return h('div', { className: 'py-2' },
          h('div', { className: 'rs-sig-line' }),
          h('p', { className: 'mb-0 small fw-semibold' }, esc(cfg.label || ''), cfg.show_stamp ? ' ' : null, cfg.show_stamp ? h('i', { className: 'bi bi-patch-check-fill text-primary ms-1' }) : null),
          h('p', { className: 'text-muted small mb-0' }, esc(cfg.role || '')));
      case 'header': {
        const lvl = Math.max(1, Math.min(3, +cfg.level || 1));
        return h('div', { className: 'py-1 text-' + (cfg.align || 'left') },
          h('h' + lvl, { className: 'rs-header-title mb-0' }, esc(cfg.text || 'Titre')));
      }
      case 'footer': {
        const parts = [];
        if (cfg.text) parts.push(esc(cfg.text));
        if (cfg.show_report_number) parts.push('N° ...');
        if (cfg.show_date) parts.push(new Date().toLocaleDateString('fr-FR'));
        if (cfg.show_page_number) parts.push('Page {page}/{total_pages}');
        return h('div', { className: 'py-1 text-' + (cfg.align || 'center') + ' border-top' },
          h('small', { className: 'text-muted' }, parts.join(' · ') || 'Pied de page'));
      }
      case 'rich_text':
        return h('div', { className: 'py-1 rs-richtext-content', dangerouslySetInnerHTML: { __html: cfg.html || '<em class="text-muted small">Texte riche...</em>' } });
      case 'image':
        return cfg.url
          ? h('div', { className: 'text-' + (cfg.align || 'center') + ' py-1' }, h('img', { src: cfg.url, alt: cfg.alt || '', style: { width: cfg.width || '100%' } }))
          : h('div', { className: 'text-' + (cfg.align || 'center') + ' py-1' }, h('i', { className: 'bi bi-card-image fs-1 text-muted' }));
      default:
        return h('div', { className: 'text-muted small' }, 'Aperçu indisponible pour « ' + esc(blockKey) + ' »');
    }
  }

  // ---- Property Panel ----
  function PropertyPanel({ block, onUpdate, onDelete, onDuplicate, onToggleEnabled }) {
    if (!block) {
      return h('div', { className: 'rs-properties-empty' },
        h('i', { className: 'bi bi-hand-index' }),
        h('p', null, 'Sélectionnez un bloc pour éditer ses propriétés'));
    }

    const cfg = block.block_config || {};
    const setCfg = (key, val) => {
      const newCfg = { ...cfg, [key]: val };
      if (key === 'axes_raw') {
        newCfg.axes = val.split('\n').filter(l => l.trim()).map(line => {
          const [label, value] = line.split(',');
          return { label: (label || '').trim(), value: parseInt(value || '0', 10) };
        });
        delete newCfg.axes_raw;
      }
      if (key === 'items_raw') {
        newCfg.items = val.split('\n').filter(l => l.trim()).map(line => ({ text: line.trim() }));
        delete newCfg.items_raw;
      }
      onUpdate({ ...block, block_config: newCfg });
    };

    const renderField = (key) => {
      const val = (k, d) => cfg[k] !== undefined ? escAttr(cfg[k]) : d;
      switch (key) {
        case 'global_score':
          return h(React.Fragment, null,
            field('Libellé', 'text', 'label', val('label', 'Score global')),
            field('Score', 'number', 'score', +cfg.score || 0),
            field('Score maximum', 'number', 'max', +cfg.max || 100),
            switchField('Afficher la note', 'show_rating', cfg.show_rating !== false));
        case 'radar_chart': {
          const axes = cfg.axes || [];
          const raw = axes.map(a => (a.label || '') + ',' + (a.value || 0)).join('\n');
          return h(React.Fragment, null,
            label('Axes (Libellé, Valeur par ligne)'),
            h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('axes_raw', e.target.value) }),
            switchField('Afficher la légende', 'legend', cfg.legend !== false));
        }
        case 'gauge':
          return h(React.Fragment, null,
            field('Libellé', 'text', 'label', val('label', 'Indicateur')),
            field('Valeur', 'number', 'value', +cfg.value || 0),
            h('div', { className: 'row g-2 mb-2' },
              h('div', { className: 'col-6' }, field('Min', 'number', 'min', +cfg.min || 0)),
              h('div', { className: 'col-6' }, field('Max', 'number', 'max', +cfg.max || 100))),
            field('Unité', 'text', 'unit', val('unit', '%')));
        case 'recommendations': {
          const items = cfg.items || [];
          const raw = items.map(i => (typeof i === 'object' ? i.text : i) || '').join('\n');
          return h(React.Fragment, null,
            field('Titre de la section', 'text', 'title', val('title', 'Recommandations')),
            label('Recommandations (une par ligne)'),
            h('textarea', { className: 'form-control form-control-sm', rows: 6, value: raw, onChange: e => setCfg('items_raw', e.target.value) }));
        }
        case 'company_info': {
          const fields = cfg.fields || [];
          return h(React.Fragment, null,
            label('Champs affichés'),
            h('div', { id: 'rs-cinfo-fields' },
              fields.map((f, i) => h('div', { key: i, className: 'input-group input-group-sm mb-1' },
                h('input', { type: 'text', className: 'form-control', placeholder: 'Clé', value: f.key || '', onChange: e => { const nf = [...fields]; nf[i] = { ...f, key: e.target.value }; setCfg('fields', nf); } }),
                h('input', { type: 'text', className: 'form-control', placeholder: 'Libellé', value: f.label || '', onChange: e => { const nf = [...fields]; nf[i] = { ...f, label: e.target.value }; setCfg('fields', nf); } }),
                h('button', { type: 'button', className: 'btn btn-outline-danger', onClick: () => { const nf = fields.filter((_, j) => j !== i); setCfg('fields', nf); } }, h('i', { className: 'bi bi-x' })))),
              fields.length === 0 ? h('p', { className: 'text-muted small' }, 'Aucun champ') : null),
            h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary w-100 mb-2', onClick: () => setCfg('fields', [...(cfg.fields || []), { key: '', label: '' }]) }, h('i', { className: 'bi bi-plus' }), ' Ajouter un champ'),
            switchField('Inclure le logo entreprise', 'show_logo', cfg.show_logo !== false));
        }
        case 'aqmi_logo':
        case 'company_logo': {
          const isCompany = key === 'company_logo';
          return h(React.Fragment, null,
            isCompany ? field('URL du logo', 'text', 'url', val('url', '')) : null,
            selectField('Taille', 'size', [{ v: 'sm', l: 'Petite' }, { v: 'md', l: 'Moyenne' }, { v: 'lg', l: 'Grande' }], cfg.size || 'md'),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'left'));
        }
        case 'official_stamp':
          return h(React.Fragment, null,
            selectField('Style du tampon', 'style', [{ v: 'circular', l: 'Circulaire' }, { v: 'rectangular', l: 'Rectangulaire' }, { v: 'badge', l: 'Badge' }], cfg.style || 'circular'),
            field('Texte principal', 'text', 'text', val('text', 'CERTIFIÉ')),
            field('Sous-texte', 'text', 'subtext', val('subtext', 'AQMI')),
            colorField('Couleur', 'color', val('color', '#0d47a1')),
            field('Taille (px)', 'number', 'size', +cfg.size || 100),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'right'));
        case 'qr_code':
          return h(React.Fragment, null,
            field('Donnée encodée', 'text', 'value', val('value', '')),
            field('Taille (px)', 'number', 'size', +cfg.size || 120),
            field('Libellé sous le QR', 'text', 'label', val('label', '')));
        case 'signature':
          return h(React.Fragment, null,
            field('Nom du signataire', 'text', 'label', val('label', '')),
            field('Fonction', 'text', 'role', val('role', '')),
            switchField('Afficher la date', 'show_date', cfg.show_date !== false),
            switchField('Afficher le tampon officiel', 'show_stamp', !!cfg.show_stamp));
        case 'header':
          return h(React.Fragment, null,
            field('Texte', 'text', 'text', val('text', '')),
            h('small', { className: 'd-block text-muted mb-2' }, 'Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}'),
            selectField('Niveau de titre', 'level', [{ v: '1', l: 'H1' }, { v: '2', l: 'H2' }, { v: '3', l: 'H3' }], String(cfg.level || 1)),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'left'),
            switchField('Afficher N° rapport', 'show_report_number', !!cfg.show_report_number),
            switchField('Afficher la date', 'show_date', !!cfg.show_date),
            switchField('Afficher la pagination', 'show_page_number', !!cfg.show_page_number));
        case 'footer':
          return h(React.Fragment, null,
            field('Texte', 'text', 'text', val('text', '')),
            h('small', { className: 'd-block text-muted mb-2' }, 'Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}'),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'center'),
            switchField('Afficher N° rapport', 'show_report_number', !!cfg.show_report_number),
            switchField('Afficher la date', 'show_date', !!cfg.show_date),
            switchField('Afficher la pagination', 'show_page_number', cfg.show_page_number !== false));
        case 'rich_text':
          return h(React.Fragment, null,
            label('Contenu (HTML)'),
            h('textarea', { className: 'form-control form-control-sm', rows: 8, value: cfg.html || '', onChange: e => setCfg('html', e.target.value) }));
        case 'image':
          return h(React.Fragment, null,
            field('URL de l\'image', 'text', 'url', val('url', '')),
            field('Texte alternatif', 'text', 'alt', val('alt', '')),
            field('Largeur', 'text', 'width', val('width', '100%')),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'center'));
        default:
          return h('div', { className: 'text-muted small' }, 'Aucune propriété pour ce bloc.');
      }
    };

    // Helper components
    function label(text) { return h('label', { className: 'form-label small fw-bold' }, text); }
    function field(lbl, type, prop, def) {
      return h(React.Fragment, null, label(lbl),
        h('input', { type, className: 'form-control form-control-sm mb-2', value: def, onChange: e => setCfg(prop, type === 'number' ? (parseFloat(e.target.value) || 0) : e.target.value) }));
    }
    function selectField(lbl, prop, options, current) {
      return h(React.Fragment, null, label(lbl),
        h('select', { className: 'form-select form-select-sm mb-2', value: current, onChange: e => setCfg(prop, e.target.value) },
          options.map(o => h('option', { key: o.v, value: o.v }, o.l))));
    }
    function switchField(lbl, prop, checked) {
      return h('div', { className: 'form-check form-switch' },
        h('input', { className: 'form-check-input', type: 'checkbox', checked: checked, onChange: e => setCfg(prop, e.target.checked) }),
        h('label', { className: 'form-check-label small' }, lbl));
    }
    function colorField(lbl, prop, def) {
      return h(React.Fragment, null, label(lbl),
        h('input', { type: 'color', className: 'form-control form-control-color mb-2', value: def, onChange: e => setCfg(prop, e.target.value) }));
    }

    const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
    return h('div', { className: 'rs-prop-content' },
      h('div', { className: 'rs-prop-block-info mb-2' },
        h('span', { className: 'badge bg-primary' }, h('i', { className: 'bi ' + meta.icon }), ' ' + meta.label)),
      h('div', { className: 'mb-2' },
        h('label', { className: 'form-label small fw-bold' }, 'Titre du bloc'),
        h('input', { type: 'text', className: 'form-control form-control-sm', value: block.title || '', onChange: e => onUpdate({ ...block, title: e.target.value }) })),
      h('div', { className: 'mb-2' },
        h('label', { className: 'form-label small fw-bold' }, 'Largeur (colonnes / 12)'),
        h('input', { type: 'range', className: 'form-range', min: 1, max: 12, value: block.column_span || 12, onChange: e => onUpdate({ ...block, column_span: parseInt(e.target.value) }) }),
        h('div', { className: 'small text-muted text-center' }, (block.column_span || 12) + ' / 12')),
      h('div', { className: 'mb-2' },
        h('label', { className: 'form-label small fw-bold' }, 'Visibilité'),
        h('select', { className: 'form-select form-select-sm', value: block.visibility || 'web_pdf', onChange: e => onUpdate({ ...block, visibility: e.target.value }) },
          h('option', { value: 'web_pdf' }, 'Web + PDF'),
          h('option', { value: 'web_only' }, 'Web uniquement'),
          h('option', { value: 'pdf_only' }, 'PDF uniquement'))),
      h('hr', { className: 'rs-prop-sep' }),
      renderField(block.block_key),
      h('hr', { className: 'rs-prop-sep' }),
      h('div', { className: 'd-flex gap-2' },
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-secondary', onClick: () => onToggleEnabled(block) },
          h('i', { className: 'bi ' + (block.is_enabled ? 'bi-eye-slash' : 'bi-eye') }), block.is_enabled ? ' Désactiver' : ' Activer'),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary', onClick: () => onDuplicate(block) },
          h('i', { className: 'bi bi-files' }), ' Dupliquer'),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-danger', onClick: () => onDelete(block) },
          h('i', { className: 'bi bi-trash' }), ' Supprimer')));
  }

  // ---- Block Card on Canvas ----
  function BlockCard({ block, isSelected, onSelect, onMoveUp, onMoveDown, index, total }) {
    const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
    const visMeta = { web_pdf: { icon: 'bi-eye', label: 'Web+PDF' }, web_only: { icon: 'bi-globe', label: 'Web' }, pdf_only: { icon: 'bi-file-pdf', label: 'PDF' } }[block.visibility || 'web_pdf'] || { icon: 'bi-eye', label: 'Web+PDF' };
    const colClass = 'col-' + (block.column_span || 12);
    return h('div', { className: colClass + ' rs-block-col' },
      h('div', {
        className: 'rs-block' + (isSelected ? ' rs-selected' : '') + (!block.is_enabled ? ' rs-block-disabled' : ''),
        onClick: e => { e.stopPropagation(); onSelect(block); }
      },
        h('div', { className: 'rs-block-toolbar' },
          h('i', { className: 'bi ' + meta.icon + ' rs-block-type-icon' }),
          h('span', { className: 'rs-block-type' }, esc(block.title || meta.label)),
          h('span', { className: 'badge rs-vis-badge bg-light text-dark' }, h('i', { className: 'bi ' + visMeta.icon }), ' ' + visMeta.label),
          h('span', { className: 'badge bg-secondary rs-col-badge' }, (block.column_span || 12) + '/12'),
          h('div', { className: 'rs-block-actions ms-auto' },
            h('button', { type: 'button', className: 'btn btn-sm', title: 'Monter', onClick: e => { e.stopPropagation(); onMoveUp(index); } }, h('i', { className: 'bi bi-arrow-up' })),
            h('button', { type: 'button', className: 'btn btn-sm', title: 'Descendre', onClick: e => { e.stopPropagation(); onMoveDown(index); } }, h('i', { className: 'bi bi-arrow-down' })))),
        h('div', { className: 'rs-block-preview' },
          h('div', { className: 'rs-live-render' },
            h(BlockPreview, { blockKey: block.block_key, cfg: block.block_config || {} })))));
  }

  // ---- Palette ----
  function Palette({ palette, onAdd }) {
    const [collapsed, setCollapsed] = useState({});
    return h('div', { className: 'rs-pane-body' },
      Object.entries(palette).map(([category, items]) =>
        h('div', { key: category, className: 'rs-palette-group' + (collapsed[category] ? ' collapsed' : '') },
          h('div', { className: 'rs-palette-cat', onClick: () => setCollapsed(c => ({ ...c, [category]: !c[category] })) },
            h('i', { className: 'bi bi-chevron-down rs-toggle' }),
            esc(category.charAt(0).toUpperCase() + category.slice(1)),
            h('span', { className: 'rs-count' }, items.length)),
          h('div', { className: 'rs-palette-items' },
            items.map(block =>
              h('div', { key: block.block_key, className: 'rs-palette-item', onClick: () => onAdd(block.block_key) },
                h('i', { className: 'bi ' + block.icon }),
                h('span', null, esc(block.label))))))));
  }

  // ---- Settings Bar ----
  function SettingsBar({ settings, onUpdate }) {
    const set = (key, val) => onUpdate({ ...settings, [key]: val });
    return h('div', { className: 'rs-settings-bar d-flex align-items-center gap-2 px-3 py-1' },
      h('span', { className: 'small text-muted fw-bold' }, h('i', { className: 'bi bi-gear' }), ' Paramètres:'),
      h('select', { className: 'form-select form-select-sm rs-setting', style: { width: 'auto' }, value: settings.orientation, onChange: e => set('orientation', e.target.value) },
        h('option', { value: 'portrait' }, 'A4 Portrait'),
        h('option', { value: 'landscape' }, 'A4 Paysage')),
      h('div', { className: 'vr' }),
      h('span', { className: 'small text-muted' }, 'N° rapport:'),
      h('input', { type: 'text', className: 'form-control form-control-sm rs-setting', style: { width: '120px' }, value: settings.report_number_prefix || '', onChange: e => set('report_number_prefix', e.target.value) }),
      h('div', { className: 'vr' }),
      h('span', { className: 'small text-muted' }, 'Date cert.:'),
      h('input', { type: 'date', className: 'form-control form-control-sm rs-setting', style: { width: '140px' }, value: settings.certification_date || '', onChange: e => set('certification_date', e.target.value) }),
      h('span', { className: 'small text-muted' }, 'Expiration:'),
      h('input', { type: 'date', className: 'form-control form-control-sm rs-setting', style: { width: '140px' }, value: settings.expiration_date || '', onChange: e => set('expiration_date', e.target.value) }),
      h('div', { className: 'vr' }),
      h('span', { className: 'small text-muted' }, 'Filigrane:'),
      h('input', { type: 'text', className: 'form-control form-control-sm rs-setting', style: { width: '120px' }, value: settings.watermark_text || '', onChange: e => set('watermark_text', e.target.value) }));
  }

  // ---- Main Builder App ----
  function BuilderApp() {
    const templateData = window.RS_TEMPLATE_DATA || { template: {}, blocks: [], settings: {} };
    const palette = window.RS_PALETTE || {};
    const templateId = window.RS_TEMPLATE_ID || 0;

    const [blocks, setBlocks] = useState((templateData.blocks || []).map(b => ({
      ...b,
      _uid: b._uid || uid(),
      block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : (b.block_config || {}),
      column_span: b.column_span || 12,
    })));
    const [settings, setSettings] = useState(templateData.settings || { orientation: 'portrait' });
    const [selectedUid, setSelectedUid] = useState(null);
    const [dirty, setDirty] = useState(false);
    const [saving, setSaving] = useState(false);
    const [statusMsg, setStatusMsg] = useState('Prêt');
    const [undoStack, setUndoStack] = useState([]);
    const [redoStack, setRedoStack] = useState([]);
    const [toast, setToast] = useState(null);

    const selectedBlock = blocks.find(b => b._uid === selectedUid);

    const pushUndo = useCallback((prev) => {
      setUndoStack(s => [...s.slice(-49), JSON.stringify(prev)]);
      setRedoStack([]);
    }, []);

    const updateBlocks = (newBlocks, recordUndo = true) => {
      if (recordUndo) pushUndo(blocks);
      setBlocks(newBlocks);
      setDirty(true);
      setStatusMsg('Modifications non enregistrées');
    };

    const addBlock = (blockKey) => {
      const newBlock = {
        _uid: uid(),
        block_key: blockKey,
        title: BLOCK_META[blockKey]?.label || blockKey,
        block_config: JSON.parse(JSON.stringify(DEFAULT_CONFIGS[blockKey] || {})),
        sort_order: blocks.length,
        is_enabled: true,
        visibility: 'web_pdf',
        column_span: 12,
      };
      updateBlocks([...blocks, newBlock]);
    };

    const updateBlock = (updated) => {
      pushUndo(blocks);
      setBlocks(blocks.map(b => b._uid === updated._uid ? updated : b));
      setDirty(true);
      setStatusMsg('Modifications non enregistrées');
    };

    const deleteBlock = (block) => {
      updateBlocks(blocks.filter(b => b._uid !== block._uid));
      if (selectedUid === block._uid) setSelectedUid(null);
    };

    const duplicateBlock = (block) => {
      const copy = { ...JSON.parse(JSON.stringify(block)), _uid: uid(), title: (block.title || '') + ' (copie)' };
      const idx = blocks.findIndex(b => b._uid === block._uid);
      const newBlocks = [...blocks];
      newBlocks.splice(idx + 1, 0, copy);
      updateBlocks(newBlocks);
    };

    const toggleEnabled = (block) => {
      updateBlocks(blocks.map(b => b._uid === block._uid ? { ...b, is_enabled: !b.is_enabled } : b));
    };

    const moveBlock = (index, dir) => {
      const newBlocks = [...blocks];
      const target = index + dir;
      if (target < 0 || target >= newBlocks.length) return;
      [newBlocks[index], newBlocks[target]] = [newBlocks[target], newBlocks[index]];
      updateBlocks(newBlocks);
    };

    const undo = () => {
      if (undoStack.length === 0) return;
      const prev = JSON.parse(undoStack[undoStack.length - 1]);
      setRedoStack(s => [...s, JSON.stringify(blocks)]);
      setUndoStack(s => s.slice(0, -1));
      setBlocks(prev.map(b => ({ ...b, block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : b.block_config })));
      setDirty(true);
    };

    const redo = () => {
      if (redoStack.length === 0) return;
      const next = JSON.parse(redoStack[redoStack.length - 1]);
      setUndoStack(s => [...s, JSON.stringify(blocks)]);
      setRedoStack(s => s.slice(0, -1));
      setBlocks(next.map(b => ({ ...b, block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : b.block_config })));
      setDirty(true);
    };

    const save = async () => {
      setSaving(true);
      setStatusMsg('Enregistrement...');
      try {
        const payload = {
          blocks: blocks.map((b, i) => ({
            block_key: b.block_key,
            title: b.title || null,
            block_config: b.block_config,
            sort_order: i,
            is_enabled: b.is_enabled !== false,
            visibility: b.visibility || 'web_pdf',
            column_span: b.column_span || 12,
          })),
          settings,
        };
        const res = await fetch('/admin/reportstudio/builder/' + templateId, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.ok) {
          setDirty(false);
          setStatusMsg('Enregistré à ' + new Date().toLocaleTimeString('fr-FR'));
          setToast({ type: 'success', msg: 'Modifications enregistrées' });
        } else {
          setStatusMsg('Erreur: ' + (data.error || 'inconnue'));
          setToast({ type: 'danger', msg: 'Erreur: ' + (data.error || 'inconnue') });
        }
      } catch (err) {
        setStatusMsg('Erreur réseau');
        setToast({ type: 'danger', msg: 'Erreur réseau' });
      }
      setSaving(false);
      setTimeout(() => setToast(null), 3000);
    };

    useEffect(() => {
      const handler = (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); save(); }
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); }
        if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); }
      };
      window.addEventListener('keydown', handler);
      return () => window.removeEventListener('keydown', handler);
    });

    const orientationClass = settings.orientation === 'landscape' ? ' rs-landscape' : '';

    return h(React.Fragment, null,
      // Topbar
      h('div', { className: 'rs-topbar d-flex align-items-center justify-content-between px-3' },
        h('div', { className: 'd-flex align-items-center gap-2' },
          h('a', { href: window.RS_DASHBOARD_URL, className: 'btn btn-sm btn-outline-light' }, h('i', { className: 'bi bi-arrow-left' }), ' Dashboard'),
          h('span', { className: 'text-white-50 small' }, '|'),
          h('span', { className: 'text-white fw-semibold' }, esc(templateData.template?.name || 'Template')),
          h('span', { className: 'badge bg-light text-dark ms-2' }, esc(templateData.template?.status || 'draft'))),
        h('div', { className: 'd-flex align-items-center gap-2' },
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: undo, title: 'Annuler (Ctrl+Z)' }, h('i', { className: 'bi bi-arrow-counterclockwise' })),
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: redo, title: 'Refaire (Ctrl+Y)' }, h('i', { className: 'bi bi-arrow-clockwise' })),
          h('a', { href: window.RS_PREVIEW_URL, target: '_blank', className: 'btn btn-sm btn-light' }, h('i', { className: 'bi bi-eye' }), ' Aperçu'),
          h('button', { type: 'button', className: 'btn btn-sm btn-success', onClick: save, disabled: saving }, h('i', { className: 'bi bi-check-lg' }), saving ? ' ...' : ' Enregistrer'))),
      // Settings bar
      h(SettingsBar, { settings, onUpdate: (s) => { setSettings(s); setDirty(true); setStatusMsg('Modifications non enregistrées'); } }),
      // 3 panes
      h('div', { className: 'rs-panes' },
        h('aside', { className: 'rs-pane rs-palette' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-blocks' }), h('span', null, 'Bibliothèque de blocs')),
          h(Palette, { palette, onAdd: addBlock })),
        h('main', { className: 'rs-pane rs-canvas-pane', onClick: () => setSelectedUid(null) },
          h('div', { className: 'rs-pane-header rs-canvas-header' },
            h('i', { className: 'bi bi-file-earmark-text' }),
            h('span', null, 'Canvas du rapport'),
            h('span', { className: 'rs-canvas-count ms-auto badge bg-secondary' }, blocks.length + ' bloc(s)')),
          h('div', { className: 'rs-canvas-scroll' },
            h('div', { className: 'rs-canvas' + orientationClass, onClick: e => e.stopPropagation() },
              blocks.length === 0
                ? h('div', { className: 'rs-canvas-empty' },
                    h('i', { className: 'bi bi-arrows-move' }),
                    h('p', null, 'Cliquez sur un bloc dans la bibliothèque pour l\'ajouter'))
                : h('div', { className: 'row g-2' },
                    blocks.map((block, i) =>
                      h(BlockCard, { key: block._uid, block, isSelected: block._uid === selectedUid, onSelect: setSelectedUid, onMoveUp: moveBlock, onMoveDown: moveBlock, index: i, total: blocks.length })))))),
        h('aside', { className: 'rs-pane rs-properties' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-sliders' }), h('span', null, 'Propriétés')),
          h('div', { className: 'rs-pane-body' },
            h(PropertyPanel, { block: selectedBlock, onUpdate: updateBlock, onDelete: deleteBlock, onDuplicate: duplicateBlock, onToggleEnabled: toggleEnabled })))),
      // Status bar
      h('div', { className: 'rs-statusbar' },
        h('span', { id: 'rs-status-msg' }, statusMsg),
        h('span', { className: 'ms-auto small text-muted' }, dirty ? 'Non enregistré' : '')),
      // Toast
      toast && h('div', { className: 'position-fixed bottom-0 end-0 p-3', style: { zIndex: 11 } },
        h('div', { className: 'toast align-items-center text-white bg-' + toast.type + ' border-0 show', role: 'alert' },
          h('div', { className: 'toast-body' }, toast.msg))));
  }

  // Mount
  const root = document.getElementById('rs-builder-root');
  if (root) {
    ReactDOM.createRoot(root).render(h(BuilderApp));
  }
})();
