/**
 * Report Studio Builder — React App
 * CDN React 18 + ApexCharts + SortableJS
 * Features: true drag & drop, visual resize handles, live ApexCharts previews,
 *   property panel, undo/redo, save, keyboard shortcuts
 */
(function () {
  'use strict';

  const { useState, useEffect, useRef, useCallback, useMemo } = React;
  const h = React.createElement;

  // ---- Block metadata (mirrors BlockRegistry::BLOCKS) ----
  const BLOCK_META = {
    global_score:    { category: 'metrics',   label: 'Global Score',        icon: 'bi-speedometer' },
    radar_chart:     { category: 'charts',    label: 'Radar Chart',         icon: 'bi-graph-up' },
    bar_chart:       { category: 'charts',    label: 'Bar Chart',           icon: 'bi-bar-chart' },
    line_chart:      { category: 'charts',    label: 'Line Chart',          icon: 'bi-graph-up-arrow' },
    donut_chart:     { category: 'charts',    label: 'Donut Chart',         icon: 'bi-pie-chart' },
    area_chart:      { category: 'charts',    label: 'Area Chart',          icon: 'bi-graph-up-arrow' },
    gauge:           { category: 'metrics',   label: 'Gauge',               icon: 'bi-dial' },
    recommendations: { category: 'content',   label: 'Recommendations',     icon: 'bi-list-check' },
    company_info:    { category: 'content',   label: 'Company Information', icon: 'bi-building' },
    aqmi_logo:       { category: 'branding',  label: 'AQMI Logo',           icon: 'bi-award' },
    company_logo:    { category: 'branding',  label: 'Company Logo',        icon: 'bi-image' },
    official_stamp:  { category: 'branding',  label: 'Official Stamp',      icon: 'bi-patch-check-fill' },
    qr_code:         { category: 'utility',   label: 'QR Code',             icon: 'bi-qr-code' },
    signature:       { category: 'utility',   label: 'Signature',           icon: 'bi-pen' },
    header:          { category: 'structure', label: 'Header',             icon: 'bi-text-left' },
    footer:          { category: 'structure', label: 'Footer',             icon: 'bi-text-right' },
    rich_text:       { category: 'content',   label: 'Rich Text',           icon: 'bi-fonts' },
    image:           { category: 'media',     label: 'Image',               icon: 'bi-card-image' },
    cover_page:      { category: 'structure', label: 'Cover Page',          icon: 'bi-bookmark-star' },
    kpi_card:        { category: 'metrics',   label: 'KPI Card',            icon: 'bi-calendar2-check' },
    domain_scores:   { category: 'metrics',   label: 'Domain Scores Table', icon: 'bi-table' },
    page_break:      { category: 'structure', label: 'Page Break',          icon: 'bi-file-earmark-break' },
  };

  const DEFAULT_CONFIGS = {
    global_score:    { label: 'Score global', score: 0, max: 100, show_rating: true },
    radar_chart:     { axes: [{ label: 'Domaine 1', value: 0 }], legend: true },
    bar_chart:       { series: [{ label: 'Série 1', data: [{ label: 'A', value: 0 }] }], horizontal: false, legend: true },
    line_chart:      { series: [{ label: 'Série 1', data: [{ label: 'Jan', value: 0 }] }], legend: true, smooth: true },
    donut_chart:     { series: [{ label: 'A', value: 1 }], legend: true },
    area_chart:      { series: [{ label: 'Série 1', data: [{ label: 'Jan', value: 0 }] }], legend: true, smooth: true },
    gauge:           { label: 'Indicateur', value: 0, min: 0, max: 100, unit: '%' },
    recommendations: { title: 'Recommandations', items: [{ text: '' }] },
    company_info:    { fields: [{ key: '', label: '' }], show_logo: true },
    aqmi_logo:       { size: 'md', align: 'left' },
    company_logo:    { url: '', size: 'md', align: 'left' },
    official_stamp:  { style: 'circular', text: 'CERTIFIÉ', subtext: 'AQMI', color: '#102A43', size: 100, align: 'right' },
    qr_code:         { value: '', size: 120, label: '' },
    signature:       { label: '', role: '', show_date: true, show_stamp: false },
    header:          { text: 'Titre', level: 1, align: 'left', show_report_number: false, show_date: false, show_page_number: false },
    footer:          { text: '', align: 'center', show_report_number: false, show_date: false, show_page_number: true },
    rich_text:       { html: '' },
    image:           { url: '', alt: '', width: '100%', align: 'center' },
    cover_page:      { company_name: 'Nom de l\'entreprise', report_title: 'Rapport d\'Audit Qualité', subtitle: 'Automotive Quality Maturity Index', show_logo: true, show_stamp: true, show_date: true, show_number: true, accent_color: '#102A43' },
    kpi_card:        { label: 'Indicateur', value: 0, unit: '', icon: 'bi-check-circle', color: '#102A43', trend: '', trend_direction: 'up' },
    domain_scores:   { title: 'Scores par domaine', domains: [{ label: 'Domaine 1', score: 0, max: 100 }] },
    page_break:      {},
  };

  // ---- Utility ----
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const escAttr = (s) => String(s ?? '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
  const uid = () => 'b_' + Math.random().toString(36).slice(2, 10);

  // ---- ApexCharts Live Preview Components ----
  function useApexChart(buildOptions, deps) {
    const ref = useRef(null);
    const chartRef = useRef(null);
    useEffect(() => {
      if (!ref.current || !window.ApexCharts) return;
      if (chartRef.current) { try { chartRef.current.destroy(); } catch (e) {} }
      chartRef.current = new ApexCharts(ref.current, buildOptions());
      chartRef.current.render();
      return () => { try { chartRef.current.destroy(); } catch (e) {} };
    }, deps);
    return ref;
  }

  function RadarPreview({ cfg }) {
    const ref = useApexChart(() => {
      const axes = cfg.axes || [];
      return {
        chart: { type: 'radar', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: [{ name: 'Radar', data: axes.map(a => +a.value || 0) }],
        xaxis: { categories: axes.map(a => a.label || '') },
        yaxis: { min: 0, max: 100 },
        legend: { show: cfg.legend !== false },
        colors: ['#102A43'],
        fill: { opacity: 0.2 },
        markers: { size: 4, colors: ['#102A43'] },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function GaugePreview({ cfg }) {
    const ref = useApexChart(() => {
      const v = +cfg.value || 0, max = +cfg.max || 100, min = +cfg.min || 0;
      const range = Math.max(1, max - min);
      const pct = Math.min(100, Math.max(0, Math.round(((v - min) / range) * 100)));
      return {
        chart: { type: 'radialBar', height: 160, sparkline: { enabled: true } },
        series: [pct],
        plotOptions: { radialBar: { startAngle: -135, endAngle: 135, hollow: { size: '62%' }, dataLabels: { name: { show: false }, value: { show: false } }, track: { background: '#e2e8f0' } } },
        fill: { colors: ['#2EC4B6'] },
        stroke: { lineCap: 'round' },
      };
    }, [cfg]);
    return h('div', { ref, style: { display: 'inline-block' } });
  }

  function BarPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      return {
        chart: { type: 'bar', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        plotOptions: { bar: { horizontal: !!cfg.horizontal, borderRadius: 4 } },
        legend: { show: cfg.legend !== false },
        colors: ['#102A43', '#2EC4B6', '#486581', '#C9A227'],
        fill: { opacity: 0.9 },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function LinePreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      return {
        chart: { type: 'line', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        stroke: { curve: cfg.smooth !== false ? 'smooth' : 'straight', width: 2 },
        legend: { show: cfg.legend !== false },
        colors: ['#102A43', '#2EC4B6', '#C9A227', '#E5484D'],
        markers: { size: 3 },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function DonutPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      return {
        chart: { type: 'donut', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => +s.value || 0),
        labels: series.map(s => s.label || ''),
        legend: { show: cfg.legend !== false, position: 'bottom' },
        colors: ['#102A43', '#2EC4B6', '#486581', '#C9A227', '#E5484D', '#7c3aed'],
        stroke: { width: 2 },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function AreaPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      return {
        chart: { type: 'area', height: 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        stroke: { curve: cfg.smooth !== false ? 'smooth' : 'straight', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
        legend: { show: cfg.legend !== false },
        colors: ['#102A43', '#2EC4B6'],
        markers: { size: 3 },
      };
    }, [cfg]);
    return h('div', { ref });
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
      case 'bar_chart':
        return h(BarPreview, { cfg });
      case 'line_chart':
        return h(LinePreview, { cfg });
      case 'donut_chart':
        return h(DonutPreview, { cfg });
      case 'area_chart':
        return h(AreaPreview, { cfg });
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
              h('dd', { className: 'col-7' }, esc(x.key || ''))))));
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
        const text = esc(cfg.text || 'CERTIFIÉ'), sub = esc(cfg.subtext || 'AQMI'), color = esc(cfg.color || '#102A43'), size = +cfg.size || 100;
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
      case 'cover_page':
        return h('div', { className: 'py-2 text-center', style: { border: '2px solid #EEF2F7', borderRadius: '10px', padding: '16px' } },
          h('span', { className: 'rs-aqmi-mark', style: { fontSize: '1.6rem', color: cfg.accent_color || '#102A43' } }, 'AQMI'),
          h('h5', { className: 'mt-2 mb-0', style: { color: cfg.accent_color || '#102A43' } }, esc(cfg.report_title || 'Rapport')),
          h('small', { className: 'text-muted' }, esc(cfg.subtitle || '')),
          h('hr', null),
          h('p', { className: 'small text-muted mb-0' }, 'Préparé pour '),
          h('span', { className: 'fw-bold' }, esc(cfg.company_name || '')),
          cfg.show_stamp ? h('div', { className: 'mt-2' }, h('span', { className: 'badge bg-primary' }, 'CERTIFIÉ')) : null);
      case 'kpi_card':
        return h('div', { className: 'd-flex align-items-center gap-2 p-2', style: { border: '1px solid #EEF2F7', borderRadius: '8px' } },
          h('div', { style: { width: '36px', height: '36px', borderRadius: '8px', background: (cfg.color || '#102A43') + '15', display: 'flex', alignItems: 'center', justifyContent: 'center' } },
            h('i', { className: 'bi ' + (cfg.icon || 'bi-check-circle'), style: { color: cfg.color || '#102A43' } })),
          h('div', null,
            h('p', { className: 'small text-muted mb-0' }, esc(cfg.label || 'Indicateur')),
            h('span', { className: 'fw-bold fs-5' }, esc(String(cfg.value || 0)), cfg.unit ? h('small', { className: 'text-muted' }, ' ' + cfg.unit) : null)));
      case 'domain_scores':
        return h('div', { className: 'py-1' },
          h('h6', { className: 'rs-block-title' }, esc(cfg.title || 'Scores par domaine')),
          h('table', { className: 'table table-sm table-bordered' },
            h('thead', null, h('tr', null, h('th', null, 'Domaine'), h('th', { className: 'text-center' }, 'Score'), h('th', { className: 'text-center' }, 'Niveau'))),
            h('tbody', null, (cfg.domains || []).slice(0, 5).map((d, i) => h('tr', { key: i },
              h('td', null, esc(d.label || '')),
              h('td', { className: 'text-center fw-bold' }, String(d.score || 0)),
              h('td', { className: 'text-center' }, h('span', { className: 'badge bg-secondary' }, '—')))))));
      case 'page_break':
        return h('div', { className: 'text-center py-3 text-muted', style: { borderTop: '2px dashed #EEF2F7' } },
          h('small', null, h('i', { className: 'bi bi-file-earmark-break' }), ' Saut de page'));
      default:
        return h('div', { className: 'text-muted small' }, 'Aperçu indisponible pour « ' + esc(blockKey) + ' »');
    }
  }

  // ---- Data Source Panel (bind a block to a DB table) ----
  const DS_BLOCKS = ['global_score', 'radar_chart', 'bar_chart', 'line_chart', 'donut_chart', 'area_chart', 'gauge'];

  function DataSourcePanel({ cfg, setCfg }) {
    const [tables, setTables] = useState([]);
    const [columns, setColumns] = useState([]);
    const [loading, setLoading] = useState(false);
    const [previewData, setPreviewData] = useState(null);
    const [showPreview, setShowPreview] = useState(false);

    const ds = cfg.data_source || {};
    const bound = !!(ds.table && ds.label_column && ds.value_column);

    useEffect(() => {
      fetch('/admin/reportstudio/datasources')
        .then(r => r.json())
        .then(data => { if (data.ok) setTables(data.tables || []); })
        .catch(() => {});
    }, []);

    const loadColumns = (tableName) => {
      if (!tableName) { setColumns([]); return; }
      setLoading(true);
      fetch('/admin/reportstudio/table-info/' + tableName)
        .then(r => r.json())
        .then(data => { if (data.ok) setColumns(data.columns || []); })
        .catch(() => setColumns([]))
        .finally(() => setLoading(false));
    };

    // Load columns when table changes or on mount if already bound
    useEffect(() => { if (ds.table) loadColumns(ds.table); }, [ds.table]);

    const setDs = (key, val) => {
      const newDs = { ...ds, [key]: val };
      if (key === 'table') {
        newDs.label_column = '';
        newDs.value_column = '';
        newDs.series_column = '';
        newDs.order_by = '';
        setColumns([]);
        loadColumns(val);
      }
      setCfg('data_source', newDs);
    };

    const toggleBind = (on) => {
      if (on) {
        setCfg('data_source', { table: '', label_column: '', value_column: '', limit: 50, order_dir: 'ASC' });
      } else {
        const newCfg = { ...cfg };
        delete newCfg.data_source;
        setCfg('__replace__', newCfg);
      }
    };

    const doPreview = () => {
      setShowPreview(true);
      setPreviewData({ loading: true });
      fetch('/admin/reportstudio/data-preview', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(ds),
      })
        .then(r => r.json())
        .then(data => {
          if (data.ok) {
            setPreviewData({ labels: data.data.labels, series: data.data.series, rows: data.data.rows });
          } else {
            setPreviewData({ error: data.error || 'Erreur' });
          }
        })
        .catch(() => setPreviewData({ error: 'Erreur réseau' }));
    };

    return h('div', { className: 'rs-ds-panel' },
      h('hr', { className: 'rs-prop-sep' }),
      h('div', { className: 'd-flex align-items-center gap-2 mb-2' },
        h('i', { className: 'bi bi-database-fill text-primary' }),
        h('span', { className: 'fw-bold small' }, 'Source de données'),
        h('div', { className: 'form-check form-switch ms-auto' },
          h('input', {
            className: 'form-check-input', type: 'checkbox',
            checked: !!cfg.data_source,
            onChange: e => toggleBind(e.target.checked),
          }),
          h('label', { className: 'form-check-label small' }, 'Lier à une table'))),
      cfg.data_source && h(React.Fragment, null,
        h('label', { className: 'form-label small' }, 'Table'),
        h('select', {
          className: 'form-select form-select-sm mb-2',
          value: ds.table || '',
          onChange: e => setDs('table', e.target.value),
        },
          h('option', { value: '' }, '— Choisir —'),
          tables.map(t => h('option', { key: t.name, value: t.name }, t.name + ' (' + t.rows + ' lignes)'))),
        // Column mappers
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-6' },
            h('label', { className: 'form-label small' }, 'Colonne libellés'),
            h('select', { className: 'form-select form-select-sm', value: ds.label_column || '', onChange: e => setDs('label_column', e.target.value), disabled: loading },
              h('option', { value: '' }, '—'),
              columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-6' },
            h('label', { className: 'form-label small' }, 'Colonne valeurs'),
            h('select', { className: 'form-select form-select-sm', value: ds.value_column || '', onChange: e => setDs('value_column', e.target.value), disabled: loading },
              h('option', { value: '' }, '—'),
              columns.map(c => h('option', { key: c.name, value: c.name }, c.name))))),
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-6' },
            h('label', { className: 'form-label small' }, 'Séries (optionnel)'),
            h('select', { className: 'form-select form-select-sm', value: ds.series_column || '', onChange: e => setDs('series_column', e.target.value), disabled: loading },
              h('option', { value: '' }, '—'),
              columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-6' },
            h('label', { className: 'form-label small' }, 'Limite'),
            h('input', { type: 'number', className: 'form-control form-control-sm', min: 1, max: 500, value: ds.limit || 50, onChange: e => setDs('limit', parseInt(e.target.value) || 50) }))),
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-7' },
            h('label', { className: 'form-label small' }, 'Trier par'),
            h('select', { className: 'form-select form-select-sm', value: ds.order_by || '', onChange: e => setDs('order_by', e.target.value) },
              h('option', { value: '' }, '— Par défaut —'),
              columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-5' },
            h('label', { className: 'form-label small' }, 'Direction'),
            h('select', { className: 'form-select form-select-sm', value: ds.order_dir || 'ASC', onChange: e => setDs('order_dir', e.target.value) },
              h('option', { value: 'ASC' }, 'Ascendant'),
              h('option', { value: 'DESC' }, 'Descendant')))),
        h('label', { className: 'form-label small' }, 'Filtre WHERE (optionnel)'),
        h('input', { type: 'text', className: 'form-control form-control-sm mb-2', placeholder: "ex: status='published'", value: ds.where_clause || '', onChange: e => setDs('where_clause', e.target.value) }),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary w-100', onClick: doPreview, disabled: !bound },
          h('i', { className: 'bi bi-eye' }), ' Prévisualiser les données'),
        showPreview && previewData && h('div', { className: 'mt-2 small' },
          previewData.loading ? h('span', { className: 'text-muted' }, 'Chargement...') :
          previewData.error ? h('span', { className: 'text-danger' }, previewData.error) :
          h(React.Fragment, null,
            h('div', { className: 'text-muted mb-1' }, (previewData.labels || []).length + ' lignes, ' + (previewData.series || []).length + ' série(s)'),
            h('table', { className: 'table table-sm table-bordered' },
              h('thead', null,
                h('tr', null,
                  h('th', null, 'Libellé'),
                  (previewData.series || []).map((s, i) => h('th', { key: i }, s.name || ('S' + (i + 1)))))),
              h('tbody', null,
                (previewData.labels || []).slice(0, 10).map((label, i) =>
                  h('tr', { key: i },
                    h('td', null, esc(label)),
                    (previewData.series || []).map((s, j) => h('td', { key: j }, s.data[i] || 0)))))))
        )
      )
    );
  }

  // ---- Rich Text Editor (Word-style formatting toolbar) ----
  const RT_FONTS = [
    { v: 'Arial, sans-serif', l: 'Arial' },
    { v: 'Helvetica, Arial, sans-serif', l: 'Helvetica' },
    { v: 'Georgia, serif', l: 'Georgia' },
    { v: '"Times New Roman", Times, serif', l: 'Times New Roman' },
    { v: '"Courier New", Courier, monospace', l: 'Courier New' },
    { v: 'Inter, sans-serif', l: 'Inter' },
    { v: 'Roboto, sans-serif', l: 'Roboto' },
    { v: '"Trebuchet MS", sans-serif', l: 'Trebuchet MS' },
    { v: 'Verdana, sans-serif', l: 'Verdana' },
  ];
  const RT_SIZES = [1, 2, 3, 4, 5, 6, 7];
  const RT_SIZE_LABELS = { 1: '8pt', 2: '10pt', 3: '12pt', 4: '14pt', 5: '18pt', 6: '24pt', 7: '36pt' };

  function RichTextEditor({ html, onChange }) {
    const ref = useRef(null);
    const [active, setActive] = useState({ bold: false, italic: false, underline: false, strikeThrough: false, ul: false, ol: false });
    const [fontVal, setFontVal] = useState('');
    const [sizeVal, setSizeVal] = useState('');
    const [colorVal, setColorVal] = useState('#000000');
    const [bgVal, setBgVal] = useState('#ffff00');

    const exec = (cmd, val) => {
      ref.current && ref.current.focus();
      document.execCommand(cmd, false, val || null);
      syncState();
      if (ref.current) onChange(ref.current.innerHTML);
    };

    const syncState = () => {
      try {
        setActive({
          bold: document.queryCommandState('bold'),
          italic: document.queryCommandState('italic'),
          underline: document.queryCommandState('underline'),
          strikeThrough: document.queryCommandState('strikeThrough'),
          ul: document.queryCommandState('insertUnorderedList'),
          ol: document.queryCommandState('insertOrderedList'),
        });
        setFontVal(document.queryCommandValue('fontName') || '');
        setColorVal(document.queryCommandValue('foreColor') || '#000000');
        const sz = document.queryCommandValue('fontSize');
        setSizeVal(sz && sz !== 'false' ? sz : '');
      } catch (e) {}
    };

    useEffect(() => {
      if (ref.current && ref.current.innerHTML !== (html || '')) {
        ref.current.innerHTML = html || '';
      }
    }, [html]);

    const btn = (icon, cmd, isActive, title) => h('button', {
      type: 'button', className: 'btn btn-sm rt-btn' + (isActive ? ' active' : ''),
      title, onMouseDown: e => { e.preventDefault(); exec(cmd); },
    }, h('i', { className: 'bi ' + icon }));

    return h('div', { className: 'rt-editor-wrap' },
      h('div', { className: 'rt-toolbar' },
        h('select', {
          className: 'form-select form-select-sm rt-font-select',
          value: fontVal,
          onChange: e => exec('fontName', e.target.value),
          onMouseDown: e => e.stopPropagation(),
          title: 'Police',
        }, RT_FONTS.map(f => h('option', { key: f.v, value: f.v, style: { fontFamily: f.v } }, f.l))),
        h('select', {
          className: 'form-select form-select-sm rt-size-select',
          value: sizeVal,
          onChange: e => exec('fontSize', e.target.value),
          onMouseDown: e => e.stopPropagation(),
          title: 'Taille',
        }, h('option', { value: '' }, 'Taille'), RT_SIZES.map(s => h('option', { key: s, value: String(s) }, RT_SIZE_LABELS[s] || s))),
        btn('bi-type-bold', 'bold', active.bold, 'Gras (Ctrl+B)'),
        btn('bi-type-italic', 'italic', active.italic, 'Italique (Ctrl+I)'),
        btn('bi-type-underline', 'underline', active.underline, 'Souligné (Ctrl+U)'),
        btn('bi-type-strikethrough', 'strikeThrough', active.strikeThrough, 'Barré'),
        h('span', { className: 'rt-sep' }),
        btn('bi-text-left', 'justifyLeft', false, 'Aligner à gauche'),
        btn('bi-text-center', 'justifyCenter', false, 'Centrer'),
        btn('bi-text-right', 'justifyRight', false, 'Aligner à droite'),
        btn('bi-justify', 'justifyFull', false, 'Justifier'),
        h('span', { className: 'rt-sep' }),
        btn('bi-list-ul', 'insertUnorderedList', active.ul, 'Liste à puces'),
        btn('bi-list-ol', 'insertOrderedList', active.ol, 'Liste numérotée'),
        btn('bi-x-circle', 'removeFormat', false, 'Effacer la mise en forme'),
        h('span', { className: 'rt-sep' }),
        h('label', { className: 'rt-color-label', title: 'Couleur du texte' },
          h('i', { className: 'bi bi-palette-fill' }),
          h('input', { type: 'color', className: 'rt-color-input', value: colorVal,
            onChange: e => { setColorVal(e.target.value); exec('foreColor', e.target.value); },
            onMouseDown: e => e.stopPropagation() })),
        h('label', { className: 'rt-color-label', title: 'Couleur de surbrillance' },
          h('i', { className: 'bi bi-highlighter' }),
          h('input', { type: 'color', className: 'rt-color-input', value: bgVal,
            onChange: e => { setBgVal(e.target.value); exec('hiliteColor', e.target.value); },
            onMouseDown: e => e.stopPropagation() })),
      ),
      h('div', {
        ref: ref,
        className: 'rt-content',
        contentEditable: true,
        suppressContentEditableWarning: true,
        onInput: () => { if (ref.current) onChange(ref.current.innerHTML); },
        onKeyUp: syncState,
        onMouseUp: syncState,
        onKeyDown: e => {
          if (e.ctrlKey || e.metaKey) {
            if (e.key === 'b') { e.preventDefault(); exec('bold'); }
            if (e.key === 'i') { e.preventDefault(); exec('italic'); }
            if (e.key === 'u') { e.preventDefault(); exec('underline'); }
          }
        },
      }));
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
      if (key === 'series_raw_bar' || key === 'series_raw_line' || key === 'series_raw_area') {
        const rawKey = key;
        newCfg.series = val.split('\n').filter(l => l.trim()).map(line => {
          const [label, ...rest] = line.split(',');
          const dataStr = rest.join(',');
          const pairs = dataStr.split(';').filter(p => p.trim());
          const data = pairs.map(p => {
            const [dl, dv] = p.split(':');
            return { label: (dl || '').trim(), value: parseFloat(dv || '0') || 0 };
          });
          return { label: (label || '').trim(), data };
        });
        delete newCfg[rawKey];
      }
      if (key === 'series_raw_donut') {
        newCfg.series = val.split('\n').filter(l => l.trim()).map(line => {
          const [label, value] = line.split(',');
          return { label: (label || '').trim(), value: parseFloat(value || '0') || 0 };
        });
        delete newCfg.series_raw_donut;
      }
      onUpdate({ ...block, block_config: newCfg });
    };

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

    const renderField = (key) => {
      const val = (k, d) => cfg[k] !== undefined ? cfg[k] : d;
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
        case 'bar_chart': {
          const series = cfg.series || [];
          const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
          return h(React.Fragment, null,
            label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
            h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_bar', e.target.value) }),
            switchField('Horizontal', 'horizontal', !!cfg.horizontal),
            switchField('Afficher la légende', 'legend', cfg.legend !== false));
        }
        case 'line_chart': {
          const series = cfg.series || [];
          const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
          return h(React.Fragment, null,
            label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
            h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_line', e.target.value) }),
            switchField('Courbe lisse', 'smooth', cfg.smooth !== false),
            switchField('Afficher la légende', 'legend', cfg.legend !== false));
        }
        case 'area_chart': {
          const series = cfg.series || [];
          const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
          return h(React.Fragment, null,
            label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
            h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_area', e.target.value) }),
            switchField('Courbe lisse', 'smooth', cfg.smooth !== false),
            switchField('Afficher la légende', 'legend', cfg.legend !== false));
        }
        case 'donut_chart': {
          const series = cfg.series || [];
          const raw = series.map(s => (s.label || '') + ',' + (s.value || 0)).join('\n');
          return h(React.Fragment, null,
            label('Segments (Libellé, Valeur par ligne)'),
            h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_donut', e.target.value) }),
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
            colorField('Couleur', 'color', val('color', '#102A43')),
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
          return h(RichTextEditor, { html: cfg.html || '', onChange: (html) => setCfg('html', html) });
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
      DS_BLOCKS.includes(block.block_key) && h(DataSourcePanel, {
        cfg: block.block_config || {},
        setCfg: (prop, val) => {
          const newCfg = prop === '__replace__' ? val : { ...block.block_config, [prop]: val };
          onUpdate({ ...block, block_config: newCfg });
        },
      }),
      h('hr', { className: 'rs-prop-sep' }),
      h('div', { className: 'd-flex gap-2' },
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-secondary', onClick: () => onToggleEnabled(block) },
          h('i', { className: 'bi ' + (block.is_enabled ? 'bi-eye-slash' : 'bi-eye') }), block.is_enabled ? ' Désactiver' : ' Activer'),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary', onClick: () => onDuplicate(block) },
          h('i', { className: 'bi bi-files' }), ' Dupliquer'),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-danger', onClick: () => onDelete(block) },
          h('i', { className: 'bi bi-trash' }), ' Supprimer')));
  }

  // ---- Block Card on Canvas (with drag handle + resize handle) ----
  function BlockCard({ block, isSelected, onSelect, onMoveUp, onMoveDown, onResize, onDragStart, onDragEnd, onDragOver, onDrop, isDragOver, index, total }) {
    const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
    const resizeStart = (e) => {
      e.preventDefault();
      e.stopPropagation();
      const column = e.currentTarget.closest('.rs-block-col');
      const row = column?.parentElement;
      if (!column || !row) return;
      const startX = e.clientX;
      const startSpan = block.column_span || 12;
      const unit = row.getBoundingClientRect().width / 12;
      const move = (event) => onResize(index, Math.round(startSpan + (event.clientX - startX) / unit));
      const stop = () => {
        document.removeEventListener('pointermove', move);
        document.removeEventListener('pointerup', stop);
      };
      document.addEventListener('pointermove', move);
      document.addEventListener('pointerup', stop, { once: true });
    };
    const visMeta = { web_pdf: { icon: 'bi-eye', label: 'Web+PDF' }, web_only: { icon: 'bi-globe', label: 'Web' }, pdf_only: { icon: 'bi-file-pdf', label: 'PDF' } }[block.visibility || 'web_pdf'] || { icon: 'bi-eye', label: 'Web+PDF' };
    const colClass = 'col-' + (block.column_span || 12);
    return h('div', {
      className: colClass + ' rs-block-col' + (isDragOver ? ' rs-drag-over' : ''),
      draggable: true,
      onDragStart: (e) => onDragStart(e, block),
      onDragEnd: onDragEnd,
      onDragOver: (e) => onDragOver(e, index),
      onDrop: (e) => onDrop(e, index),
    },
      h('div', {
        className: 'rs-block' + (isSelected ? ' rs-selected' : '') + (!block.is_enabled ? ' rs-block-disabled' : ''),
        onClick: e => { e.stopPropagation(); onSelect(block); }
      },
        h('div', { className: 'rs-block-toolbar' },
          h('i', { className: 'bi bi-grip-vertical rs-block-handle' }),
          h('i', { className: 'bi ' + meta.icon + ' rs-block-type-icon' }),
          h('span', { className: 'rs-block-type' }, esc(block.title || meta.label)),
          h('span', { className: 'badge rs-vis-badge bg-light text-dark' }, h('i', { className: 'bi ' + visMeta.icon }), ' ' + visMeta.label),
          h('span', { className: 'badge bg-secondary rs-col-badge' }, (block.column_span || 12) + '/12'),
          h('div', { className: 'rs-block-actions ms-auto' },
            h('button', { type: 'button', className: 'btn btn-sm', title: 'Monter', onClick: e => { e.stopPropagation(); onMoveUp(index); } }, h('i', { className: 'bi bi-arrow-up' })),
            h('button', { type: 'button', className: 'btn btn-sm', title: 'Descendre', onClick: e => { e.stopPropagation(); onMoveDown(index); } }, h('i', { className: 'bi bi-arrow-down' })))),
        h('div', { className: 'rs-block-preview' },
          h('div', { className: 'rs-live-render' },
            h(BlockPreview, { blockKey: block.block_key, cfg: block.block_config || {} }))),
        h('div', { className: 'rs-resize-handle', title: 'Glisser pour redimensionner', onPointerDown: resizeStart },
          h('i', { className: 'bi bi-arrows-angle-right' }))));
  }

  // ---- Palette ----
  function Palette({ palette, onAdd }) {
    const [collapsed, setCollapsed] = useState({});
    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
      if (!search.trim()) return palette;
      const q = search.toLowerCase();
      const result = {};
      for (const [category, items] of Object.entries(palette)) {
        const matched = items.filter(b => b.label.toLowerCase().includes(q) || b.block_key.toLowerCase().includes(q));
        if (matched.length) result[category] = matched;
      }
      return result;
    }, [palette, search]);

    return h('div', { className: 'rs-pane-body' },
      h('div', { className: 'rs-palette-search mb-2' },
        h('div', { className: 'input-group input-group-sm' },
          h('span', { className: 'input-group-text' }, h('i', { className: 'bi bi-search' })),
          h('input', { type: 'text', className: 'form-control', placeholder: 'Rechercher un bloc...', value: search, onChange: e => setSearch(e.target.value) }))),
      Object.entries(filtered).map(([category, items]) =>
        h('div', { key: category, className: 'rs-palette-group' + (collapsed[category] ? ' collapsed' : '') },
          h('div', { className: 'rs-palette-cat', onClick: () => setCollapsed(c => ({ ...c, [category]: !c[category] })) },
            h('i', { className: 'bi bi-chevron-down rs-toggle' }),
            esc(category.charAt(0).toUpperCase() + category.slice(1)),
            h('span', { className: 'rs-count' }, items.length)),
          h('div', { className: 'rs-palette-items' },
            items.map(block =>
              h('div', {
                key: block.block_key,
                className: 'rs-palette-item',
                draggable: true,
                onDragStart: (e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('text/block-key', block.block_key); },
                onClick: () => onAdd(block.block_key),
              },
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
    const [dragIndex, setDragIndex] = useState(null);
    const [dragOverIndex, setDragOverIndex] = useState(null);

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
      setSelectedUid(newBlock._uid);
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

    const resizeBlock = (index, newSpan) => {
      const span = Math.max(1, Math.min(12, newSpan));
      updateBlocks(blocks.map((b, i) => i === index ? { ...b, column_span: span } : b));
    };

    // ---- Drag & Drop reordering ----
    const handleDragStart = (e, block) => {
      const idx = blocks.findIndex(b => b._uid === block._uid);
      setDragIndex(idx);
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', block._uid);
    };

    const handleDragEnd = () => {
      setDragIndex(null);
      setDragOverIndex(null);
    };

    const handleDragOver = (e, index) => {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      setDragOverIndex(index);
    };

    const handleDrop = (e, index) => {
      e.preventDefault();
      e.stopPropagation();
      const blockKey = e.dataTransfer.getData('text/block-key');
      if (blockKey) {
        const newBlock = {
          _uid: uid(),
          block_key: blockKey,
          title: BLOCK_META[blockKey]?.label || blockKey,
          block_config: JSON.parse(JSON.stringify(DEFAULT_CONFIGS[blockKey] || {})),
          sort_order: index,
          is_enabled: true,
          visibility: 'web_pdf',
          column_span: 12,
        };
        const newBlocks = [...blocks];
        newBlocks.splice(index, 0, newBlock);
        updateBlocks(newBlocks);
        setSelectedUid(newBlock._uid);
      } else {
        const uid = e.dataTransfer.getData('text/plain');
        const fromIdx = blocks.findIndex(b => b._uid === uid);
        if (fromIdx === -1 || fromIdx === index) return;
        const newBlocks = [...blocks];
        const [moved] = newBlocks.splice(fromIdx, 1);
        newBlocks.splice(index, 0, moved);
        updateBlocks(newBlocks);
      }
      setDragIndex(null);
      setDragOverIndex(null);
    };

    // Drop on empty canvas
    const handleCanvasDrop = (e) => {
      e.preventDefault();
      const blockKey = e.dataTransfer.getData('text/block-key');
      if (blockKey) {
        addBlock(blockKey);
      }
      setDragIndex(null);
      setDragOverIndex(null);
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
          setToast({ type: 'success', msg: 'Modifications enregistrées (' + data.count + ' blocs)' });
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

    useEffect(() => {
      const handler = (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } };
      window.addEventListener('beforeunload', handler);
      return () => window.removeEventListener('beforeunload', handler);
    });

    const orientationClass = settings.orientation === 'landscape' ? ' rs-landscape' : '';

    return h(React.Fragment, null,
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
      h(SettingsBar, { settings, onUpdate: (s) => { setSettings(s); setDirty(true); setStatusMsg('Modifications non enregistrées'); } }),
      h('div', { className: 'rs-panes' },
        h('aside', { className: 'rs-pane rs-palette' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-blocks' }), h('span', null, 'Bibliothèque de blocs')),
          h(Palette, { palette, onAdd: addBlock })),
        h('main', { className: 'rs-pane rs-canvas-pane', onClick: () => setSelectedUid(null),
          onDragOver: (e) => e.preventDefault(),
          onDrop: handleCanvasDrop },
          h('div', { className: 'rs-pane-header rs-canvas-header' },
            h('i', { className: 'bi bi-file-earmark-text' }),
            h('span', null, 'Canvas du rapport'),
            h('span', { className: 'rs-canvas-count ms-auto badge bg-secondary' }, blocks.length + ' bloc(s)')),
          h('div', { className: 'rs-canvas-scroll' },
            h('div', { className: 'rs-canvas' + orientationClass, onClick: e => e.stopPropagation() },
              blocks.length === 0
                ? h('div', { className: 'rs-canvas-empty',
                    onDragOver: (e) => e.preventDefault(),
                    onDrop: handleCanvasDrop },
                    h('i', { className: 'bi bi-arrows-move' }),
                    h('p', null, 'Glissez un bloc ici ou cliquez dans la bibliothèque'))
                : h('div', { className: 'row g-2' },
                    blocks.map((block, i) =>
                      h(BlockCard, {
                        key: block._uid, block,
                        isSelected: block._uid === selectedUid,
                        onSelect: setSelectedUid,
                        onMoveUp: moveBlock, onMoveDown: moveBlock,
                        onResize: resizeBlock,
                        onDragStart: handleDragStart, onDragEnd: handleDragEnd,
                        onDragOver: handleDragOver, onDrop: handleDrop,
                        isDragOver: dragOverIndex === i && dragIndex !== i,
                        index: i, total: blocks.length,
                      })))))),
        h('aside', { className: 'rs-pane rs-properties' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-sliders' }), h('span', null, 'Propriétés')),
          h('div', { className: 'rs-pane-body' },
            h(PropertyPanel, { block: selectedBlock, onUpdate: updateBlock, onDelete: deleteBlock, onDuplicate: duplicateBlock, onToggleEnabled: toggleEnabled })))),
      h('div', { className: 'rs-statusbar' },
        h('span', { id: 'rs-status-msg' }, statusMsg),
        h('span', { className: 'ms-auto small text-muted' }, dirty ? 'Non enregistré' : '')),
      toast && h('div', { className: 'position-fixed bottom-0 end-0 p-3', style: { zIndex: 11 } },
        h('div', { className: 'toast align-items-center text-white bg-' + toast.type + ' border-0 show', role: 'alert' },
          h('div', { className: 'toast-body' }, toast.msg))));
  }

  const root = document.getElementById('rs-builder-root');
  if (root) {
    ReactDOM.createRoot(root).render(h(BuilderApp));
  }
})();
