/**
 * Report Studio Builder — Professional Report Designer
 * CDN React 18 + ApexCharts
 * Features: reliable selection, tabbed properties, object explorer,
 *   floating toolbar, multi-select, alignment, copy/paste, duplicate,
 *   undo/redo, zoom, rulers, snap-to-grid, alignment guides, dark UI
 */
(function () {
  'use strict';

  const { useState, useEffect, useRef, useCallback, useMemo, useReducer } = React;
  const h = React.createElement;

  // ============================================================
  // BLOCK METADATA (mirrors BlockRegistry::BLOCKS)
  // ============================================================
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
    background:      { category: 'media',     label: 'Background',          icon: 'bi-image-alt' },
    cover_page:      { category: 'structure', label: 'Cover Page',          icon: 'bi-bookmark-star' },
    kpi_card:        { category: 'metrics',   label: 'KPI Card',            icon: 'bi-calendar2-check' },
    domain_scores:   { category: 'metrics',   label: 'Domain Scores Table', icon: 'bi-table' },
    page_break:      { category: 'structure', label: 'Page Break',          icon: 'bi-file-earmark-break' },
  };

  const DEFAULT_CONFIGS = {
    global_score:    { label: 'Score global', score: 0, max: 100, show_rating: true, color: '#102A43', size: 'md', show_progress: true, suffix: '' },
    radar_chart:     { axes: [{ label: 'Domaine 1', value: 0 }], legend: true, color: '#102A43', fill_opacity: 0.2, height: 280, show_markers: true, stroke_width: 2 },
    bar_chart:       { series: [{ label: 'Série 1', data: [{ label: 'A', value: 0 }] }], horizontal: false, legend: true, color: '#102A43', color2: '#2EC4B6', color3: '#9d8fd1', color4: '#E5484D', height: 280, bar_radius: 4, stacked: false, fill_opacity: 0.9 },
    line_chart:      { series: [{ label: 'Série 1', data: [{ label: 'Jan', value: 0 }] }], legend: true, smooth: true, color: '#102A43', color2: '#2EC4B6', color3: '#9d8fd1', color4: '#E5484D', height: 280, stroke_width: 2, show_markers: true, fill_area: false },
    donut_chart:     { series: [{ label: 'A', value: 1 }], legend: true, color: '#102A43', color2: '#2EC4B6', color3: '#486581', color4: '#9d8fd1', color5: '#E5484D', color6: '#1F6FEB', height: 280, donut_size: '65%', stroke_width: 2, show_percent: true },
    area_chart:      { series: [{ label: 'Série 1', data: [{ label: 'Jan', value: 0 }] }], legend: true, smooth: true, color: '#102A43', color2: '#2EC4B6', height: 280, stroke_width: 2, fill_opacity_from: 0.3, fill_opacity_to: 0.05, show_markers: true },
    gauge:           { label: 'Indicateur', value: 0, min: 0, max: 100, unit: '%', color: '#2EC4B6', height: 160, show_label: true, show_value: true, start_angle: -135, end_angle: 135, hollow_size: '62%' },
    recommendations: { title: 'Recommandations', items: [{ text: '' }], icon: 'bi-list-check', numbered: true, font_size: '0.9rem', color: '#102A43' },
    company_info:    { fields: [{ key: '', label: '' }], show_logo: true, layout: 'vertical', label_color: '#6b7280', value_color: '#102A43', font_size: '0.9rem' },
    aqmi_logo:       { size: 'md', align: 'left', color: '#102A43', use_custom_image: false, image_url: '', image_height: '60px', image_border_radius: '0' },
    company_logo:    { url: '', size: 'md', align: 'left', max_height: '80px', border_radius: '0' },
    official_stamp:  { style: 'circular', text: 'CERTIFIÉ', subtext: 'AQMI', color: '#102A43', size: 100, align: 'right', border_width: 3, font_size: '0.9rem', use_custom_image: false, image_url: '', image_height: '100px' },
    qr_code:         { mode: 'manual', value: '', size: 120, label: '', align: 'center', margin: 0 },
    signature:       { label: '', role: '', show_date: true, show_stamp: false, line_color: '#102A43', font_size: '0.9rem', date_format: 'fr-FR' },
    header:          { text: 'Titre', level: 1, align: 'left', show_report_number: false, show_date: false, show_page_number: false, color: '#102A43', font_size: '', uppercase: false, border_bottom: false },
    footer:          { text: '', align: 'center', show_report_number: false, show_date: false, show_page_number: true, color: '#6b7280', font_size: '0.8rem', border_top: true },
    rich_text:       { html: '', padding: '8px', font_family: 'Inter, sans-serif', font_size: '0.9rem' },
    image:           { url: '', alt: '', width: '100%', align: 'center', border_radius: '0', max_height: '', object_fit: 'contain' },
    background:      { image_url: '', bg_color: '#ffffff', opacity: 1, size: 'cover', position: 'center', repeat: 'no-repeat', min_height: '300px', padding: '24px' },
    cover_page:      { company_name: 'Nom de l\'entreprise', report_title: 'Rapport d\'Audit Qualité', subtitle: 'Automotive Quality Maturity Index', show_logo: true, show_stamp: true, show_date: true, show_number: true, accent_color: '#102A43', bg_color: '#ffffff', text_color: '#102A43', font_size_title: '1.5rem', font_size_subtitle: '0.9rem', padding: '40px', border_color: '#EEF2F7', bg_image_url: '', bg_image_opacity: 1, bg_image_size: 'cover', bg_image_position: 'center', bg_image_repeat: 'no-repeat' },
    kpi_card:        { label: 'Indicateur', value: 0, unit: '', icon: 'bi-check-circle', color: '#102A43', trend: '', trend_direction: 'up', bg_color: '#ffffff', border_color: '#EEF2F7', icon_bg: true, font_size: '1.1rem', show_trend: true },
    domain_scores:   { title: 'Scores par domaine', domains: [{ label: 'Domaine 1', score: 0, max: 100 }], color: '#102A43', alternating_rows: true, show_progress_bar: true, font_size: '0.85rem', border_color: '#EEF2F7' },
    page_break:      { label: 'Saut de page', show_label: false, spacing: '2rem' },
  };

  const CATEGORY_LABELS = {
    metrics: 'Métriques', charts: 'Graphiques', content: 'Contenu',
    branding: 'Branding', utility: 'Utilitaire', structure: 'Structure', media: 'Média',
  };

  // Map block_key -> category for color coding
  const BLOCK_CATEGORY = Object.fromEntries(
    Object.entries(BLOCK_META).map(([key, meta]) => [key, meta.category])
  );

  // ============================================================
  // UTILITIES
  // ============================================================
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const uid = () => 'b_' + Math.random().toString(36).slice(2, 10);
  const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
  const GRID_SIZE = 8; // px snap grid

  // ============================================================
  // GLOBAL STATE (reducer-based for reliability)
  // ============================================================
  const initialState = {
    blocks: [],
    settings: {},
    selectedIds: [],       // array of _uid strings — the single source of truth
    hoveredId: null,
    clipboard: null,       // array of block objects
    undoStack: [],
    redoStack: [],
    zoom: 100,
    showGrid: true,
    snapToGrid: true,
    activeTab: 'general',
    dirty: false,
    saving: false,
    statusMsg: 'Prêt',
    toast: null,
    marquee: null,         // {x0,y0,x1,y1} for marquee selection
  };

  function reducer(state, action) {
    switch (action.type) {
      case 'INIT':
        return { ...state, blocks: action.blocks, settings: action.settings };
      case 'SET_BLOCKS': {
        return { ...state, blocks: action.blocks, dirty: true, statusMsg: 'Modifications non enregistrées' };
      }
      case 'SET_SETTINGS':
        return { ...state, settings: action.settings, dirty: true, statusMsg: 'Modifications non enregistrées' };
      case 'SELECT': {
        return { ...state, selectedIds: action.ids };
      }
      case 'SELECT_ADD': {
        const ids = new Set(state.selectedIds);
        if (ids.has(action.id)) ids.delete(action.id); else ids.add(action.id);
        return { ...state, selectedIds: [...ids] };
      }
      case 'HOVER':
        return { ...state, hoveredId: action.id };
      case 'SET_TAB':
        return { ...state, activeTab: action.tab };
      case 'SET_ZOOM':
        return { ...state, zoom: clamp(action.zoom, 25, 400) };
      case 'TOGGLE_GRID':
        return { ...state, showGrid: !state.showGrid };
      case 'TOGGLE_SNAP':
        return { ...state, snapToGrid: !state.snapToGrid };
      case 'COPY':
        return { ...state, clipboard: action.blocks };
      case 'SET_DIRTY':
        return { ...state, dirty: action.dirty, statusMsg: action.msg || state.statusMsg };
      case 'SET_SAVING':
        return { ...state, saving: action.saving };
      case 'SET_STATUS':
        return { ...state, statusMsg: action.msg };
      case 'SET_TOAST':
        return { ...state, toast: action.toast };
      case 'PUSH_UNDO':
        return { ...state, undoStack: [...state.undoStack.slice(-49), action.snapshot], redoStack: [] };
      case 'SET_UNDO_REDO':
        return { ...state, undoStack: action.undo, redoStack: action.redo };
      case 'SET_MARQUEE':
        return { ...state, marquee: action.marquee };
      default:
        return state;
    }
  }

  // ============================================================
  // APEX CHART PREVIEW COMPONENTS
  // ============================================================
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
      const c = cfg.color || '#102A43';
      return {
        chart: { type: 'radar', height: +cfg.height || 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: [{ name: 'Radar', data: axes.map(a => +a.value || 0) }],
        xaxis: { categories: axes.map(a => a.label || '') },
        yaxis: { min: 0, max: 100 },
        legend: { show: cfg.legend !== false },
        colors: [c], fill: { opacity: +cfg.fill_opacity || 0.2 },
        stroke: { width: +cfg.stroke_width || 2 },
        markers: { size: cfg.show_markers !== false ? 4 : 0, colors: [c] },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function GaugePreview({ cfg }) {
    const ref = useApexChart(() => {
      const v = +cfg.value || 0, max = +cfg.max || 100, min = +cfg.min || 0;
      const pct = Math.min(100, Math.max(0, Math.round(((v - min) / Math.max(1, max - min)) * 100)));
      return {
        chart: { type: 'radialBar', height: +cfg.height || 160, sparkline: { enabled: true } },
        series: [pct],
        plotOptions: { radialBar: { startAngle: +cfg.start_angle || -135, endAngle: +cfg.end_angle || 135, hollow: { size: cfg.hollow_size || '62%' }, dataLabels: { name: { show: false }, value: { show: false } }, track: { background: '#e2e8f0' } } },
        fill: { colors: [cfg.color || '#2EC4B6'] }, stroke: { lineCap: 'round' },
      };
    }, [cfg]);
    return h('div', { ref, style: { display: 'inline-block' } });
  }

  function BarPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      const colors = [cfg.color || '#102A43', cfg.color2 || '#2EC4B6', cfg.color3 || '#9d8fd1', cfg.color4 || '#E5484D'].filter(Boolean);
      return {
        chart: { type: 'bar', height: +cfg.height || 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        plotOptions: { bar: { horizontal: !!cfg.horizontal, borderRadius: +cfg.bar_radius || 4 } },
        legend: { show: cfg.legend !== false }, colors, fill: { opacity: +cfg.fill_opacity || 0.9 },
        stroke: { width: cfg.stacked ? 1 : 0 },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function LinePreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      const colors = [cfg.color || '#102A43', cfg.color2 || '#2EC4B6', cfg.color3 || '#9d8fd1', cfg.color4 || '#E5484D'].filter(Boolean);
      return {
        chart: { type: 'line', height: +cfg.height || 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        stroke: { curve: cfg.smooth !== false ? 'smooth' : 'straight', width: +cfg.stroke_width || 2 },
        legend: { show: cfg.legend !== false }, colors,
        markers: { size: cfg.show_markers !== false ? 3 : 0 },
        fill: cfg.fill_area ? { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } } : undefined,
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function DonutPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const colors = [cfg.color || '#102A43', cfg.color2 || '#2EC4B6', cfg.color3 || '#486581', cfg.color4 || '#9d8fd1', cfg.color5 || '#E5484D', cfg.color6 || '#1F6FEB'].filter(Boolean);
      return {
        chart: { type: 'donut', height: +cfg.height || 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => +s.value || 0), labels: series.map(s => s.label || ''),
        legend: { show: cfg.legend !== false, position: 'bottom' }, colors,
        stroke: { width: +cfg.stroke_width || 2 },
        plotOptions: { pie: { donut: { size: cfg.donut_size || '65%' } } },
        dataLabels: { enabled: cfg.show_percent !== false },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  function AreaPreview({ cfg }) {
    const ref = useApexChart(() => {
      const series = cfg.series || [];
      const categories = (series[0]?.data || []).map(d => d.label || '');
      const colors = [cfg.color || '#102A43', cfg.color2 || '#2EC4B6'].filter(Boolean);
      return {
        chart: { type: 'area', height: +cfg.height || 280, toolbar: { show: false }, sparkline: { enabled: true } },
        series: series.map(s => ({ name: s.label || '', data: (s.data || []).map(d => +d.value || 0) })),
        xaxis: { categories },
        stroke: { curve: cfg.smooth !== false ? 'smooth' : 'straight', width: +cfg.stroke_width || 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: +cfg.fill_opacity_from || 0.3, opacityTo: +cfg.fill_opacity_to || 0.05 } },
        legend: { show: cfg.legend !== false }, colors, markers: { size: cfg.show_markers !== false ? 3 : 0 },
      };
    }, [cfg]);
    return h('div', { ref });
  }

  // ============================================================
  // BLOCK LIVE PREVIEW
  // ============================================================
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
      case 'radar_chart': return h(RadarPreview, { cfg });
      case 'bar_chart': return h(BarPreview, { cfg });
      case 'line_chart': return h(LinePreview, { cfg });
      case 'donut_chart': return h(DonutPreview, { cfg });
      case 'area_chart': return h(AreaPreview, { cfg });
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
        if (cfg.use_custom_image && cfg.image_url) {
          return h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' },
            h('img', { src: cfg.image_url, alt: 'AQMI Logo', style: { height: cfg.image_height || '60px', borderRadius: cfg.image_border_radius || '0' } }));
        }
        return h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' },
          h('span', { className: 'rs-aqmi-mark', style: { fontSize: sizes[cfg.size || 'md'] } }, 'AQMI'));
      }
      case 'company_logo':
        return cfg.url
          ? h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' }, h('img', { src: cfg.url, style: { maxHeight: '80px' } }))
          : h('div', { className: 'text-' + (cfg.align || 'left') + ' py-1' }, h('i', { className: 'bi bi-image fs-2 text-muted' }));
      case 'official_stamp': {
        const text = esc(cfg.text || 'CERTIFIÉ'), sub = esc(cfg.subtext || 'AQMI'), color = esc(cfg.color || '#102A43'), size = +cfg.size || 100;
        if (cfg.use_custom_image && cfg.image_url) {
          return h('div', { className: 'text-' + (cfg.align || 'right') + ' py-2' },
            h('img', { src: cfg.image_url, alt: 'Official Stamp', style: { height: cfg.image_height || '100px' } }));
        }
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
        if (cfg.mode === 'verify') {
          return h('div', { className: 'text-center py-1' },
            h('div', { style: { width: size + 'px', height: size + 'px', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', border: '2px dashed #6366f1', borderRadius: '8px', background: '#eef2ff', color: '#4338ca', gap: '6px', margin: '0 auto' } },
              h('i', { className: 'bi bi-shield-check', style: { fontSize: '2rem' } }),
              h('span', { style: { fontSize: '0.65rem', fontWeight: '600' } }, 'Vérification certificat')));
        }
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
          ? h('div', { className: 'text-' + (cfg.align || 'center') + ' py-1' }, h('img', { src: cfg.url, alt: cfg.alt || '', style: { width: cfg.width || '100%', maxHeight: cfg.max_height || undefined, borderRadius: cfg.border_radius || '0', objectFit: cfg.object_fit || 'contain' } }))
          : h('div', { className: 'text-' + (cfg.align || 'center') + ' py-1' }, h('i', { className: 'bi bi-card-image fs-1 text-muted' }));
      case 'background': {
        const bgStyle = { backgroundColor: cfg.bg_color || '#ffffff', padding: cfg.padding || '24px', minHeight: cfg.min_height || '300px' };
        if (cfg.image_url) {
          bgStyle.backgroundImage = 'url(' + cfg.image_url + ')';
          bgStyle.backgroundSize = cfg.size || 'cover';
          bgStyle.backgroundPosition = cfg.position || 'center';
          bgStyle.backgroundRepeat = cfg.repeat || 'no-repeat';
        }
        return h('div', { className: 'py-1', style: bgStyle });
      }
      case 'cover_page': {
        const cpBgStyle = { border: '2px solid #EEF2F7', borderRadius: '10px', padding: '16px' };
        if (cfg.bg_image_url) {
          cpBgStyle.backgroundImage = 'url(' + cfg.bg_image_url + ')';
          cpBgStyle.backgroundSize = cfg.bg_image_size || 'cover';
          cpBgStyle.backgroundPosition = cfg.bg_image_position || 'center';
          cpBgStyle.backgroundRepeat = cfg.bg_image_repeat || 'no-repeat';
        }
        return h('div', { className: 'py-2 text-center', style: cpBgStyle },
          h('span', { className: 'rs-aqmi-mark', style: { fontSize: '1.6rem', color: cfg.accent_color || '#102A43' } }, 'AQMI'),
          h('h5', { className: 'mt-2 mb-0', style: { color: cfg.accent_color || '#102A43' } }, esc(cfg.report_title || 'Rapport')),
          h('small', { className: 'text-muted' }, esc(cfg.subtitle || '')),
          h('hr', null),
          h('p', { className: 'small text-muted mb-0' }, 'Préparé pour '),
          h('span', { className: 'fw-bold' }, esc(cfg.company_name || '')),
          cfg.show_stamp ? h('div', { className: 'mt-2' }, h('span', { className: 'badge bg-primary' }, 'CERTIFIÉ')) : null);
      }
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

  // ============================================================
  // DATA SOURCE PANEL
  // ============================================================
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
      fetch('/admin/reportstudio/datasources').then(r => r.json()).then(data => { if (data.ok) setTables(data.tables || []); }).catch(() => {});
    }, []);

    const loadColumns = (tableName) => {
      if (!tableName) { setColumns([]); return; }
      setLoading(true);
      fetch('/admin/reportstudio/table-info/' + tableName).then(r => r.json()).then(data => { if (data.ok) setColumns(data.columns || []); }).catch(() => setColumns([])).finally(() => setLoading(false));
    };

    useEffect(() => { if (ds.table) loadColumns(ds.table); }, [ds.table]);

    const setDs = (key, val) => {
      const newDs = { ...ds, [key]: val };
      if (key === 'table') { newDs.label_column = ''; newDs.value_column = ''; newDs.series_column = ''; newDs.order_by = ''; setColumns([]); loadColumns(val); }
      setCfg('data_source', newDs);
    };

    const toggleBind = (on) => {
      if (on) setCfg('data_source', { table: '', label_column: '', value_column: '', limit: 50, order_dir: 'ASC' });
      else { const nc = { ...cfg }; delete nc.data_source; setCfg('__replace__', nc); }
    };

    const doPreview = () => {
      setShowPreview(true); setPreviewData({ loading: true });
      fetch('/admin/reportstudio/data-preview', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(ds) })
        .then(r => r.json()).then(data => { if (data.ok) setPreviewData({ labels: data.data.labels, series: data.data.series }); else setPreviewData({ error: data.error || 'Erreur' }); }).catch(() => setPreviewData({ error: 'Erreur réseau' }));
    };

    return h('div', { className: 'rs-ds-panel' },
      h('div', { className: 'd-flex align-items-center gap-2 mb-2' },
        h('i', { className: 'bi bi-database-fill text-primary' }),
        h('span', { className: 'fw-bold small' }, 'Source de données'),
        h('div', { className: 'form-check form-switch ms-auto' },
          h('input', { className: 'form-check-input', type: 'checkbox', checked: !!cfg.data_source, onChange: e => toggleBind(e.target.checked) }),
          h('label', { className: 'form-check-label small' }, 'Lier à une table'))),
      cfg.data_source && h(React.Fragment, null,
        h('label', { className: 'form-label small' }, 'Table'),
        h('select', { className: 'form-select form-select-sm mb-2', value: ds.table || '', onChange: e => setDs('table', e.target.value) },
          h('option', { value: '' }, '— Choisir —'), tables.map(t => h('option', { key: t.name, value: t.name }, t.name + ' (' + t.rows + ' lignes)'))),
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-6' }, h('label', { className: 'form-label small' }, 'Colonne libellés'), h('select', { className: 'form-select form-select-sm', value: ds.label_column || '', onChange: e => setDs('label_column', e.target.value), disabled: loading }, h('option', { value: '' }, '—'), columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-6' }, h('label', { className: 'form-label small' }, 'Colonne valeurs'), h('select', { className: 'form-select form-select-sm', value: ds.value_column || '', onChange: e => setDs('value_column', e.target.value), disabled: loading }, h('option', { value: '' }, '—'), columns.map(c => h('option', { key: c.name, value: c.name }, c.name))))),
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-6' }, h('label', { className: 'form-label small' }, 'Séries (optionnel)'), h('select', { className: 'form-select form-select-sm', value: ds.series_column || '', onChange: e => setDs('series_column', e.target.value), disabled: loading }, h('option', { value: '' }, '—'), columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-6' }, h('label', { className: 'form-label small' }, 'Limite'), h('input', { type: 'number', className: 'form-control form-control-sm', min: 1, max: 500, value: ds.limit || 50, onChange: e => setDs('limit', parseInt(e.target.value) || 50) }))),
        h('div', { className: 'row g-2 mb-2' },
          h('div', { className: 'col-7' }, h('label', { className: 'form-label small' }, 'Trier par'), h('select', { className: 'form-select form-select-sm', value: ds.order_by || '', onChange: e => setDs('order_by', e.target.value) }, h('option', { value: '' }, '— Par défaut —'), columns.map(c => h('option', { key: c.name, value: c.name }, c.name)))),
          h('div', { className: 'col-5' }, h('label', { className: 'form-label small' }, 'Direction'), h('select', { className: 'form-select form-select-sm', value: ds.order_dir || 'ASC', onChange: e => setDs('order_dir', e.target.value) }, h('option', { value: 'ASC' }, 'Ascendant'), h('option', { value: 'DESC' }, 'Descendant')))),
        h('label', { className: 'form-label small' }, 'Filtre WHERE (optionnel)'),
        h('input', { type: 'text', className: 'form-control form-control-sm mb-2', placeholder: "ex: status='published'", value: ds.where_clause || '', onChange: e => setDs('where_clause', e.target.value) }),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary w-100', onClick: doPreview, disabled: !bound }, h('i', { className: 'bi bi-eye' }), ' Prévisualiser les données'),
        showPreview && previewData && h('div', { className: 'mt-2 small' },
          previewData.loading ? h('span', { className: 'text-muted' }, 'Chargement...') :
          previewData.error ? h('span', { className: 'text-danger' }, previewData.error) :
          h(React.Fragment, null,
            h('div', { className: 'text-muted mb-1' }, (previewData.labels || []).length + ' lignes, ' + (previewData.series || []).length + ' série(s)'),
            h('table', { className: 'table table-sm table-bordered' },
              h('thead', null, h('tr', null, h('th', null, 'Libellé'), (previewData.series || []).map((s, i) => h('th', { key: i }, s.name || ('S' + (i + 1)))))),
              h('tbody', null, (previewData.labels || []).slice(0, 10).map((label, i) => h('tr', { key: i }, h('td', null, esc(label)), (previewData.series || []).map((s, j) => h('td', { key: j }, s.data[i] || 0))))))))));
  }

  // ============================================================
  // RICH TEXT EDITOR
  // ============================================================
  const RT_FONTS = [{ v: 'Arial, sans-serif', l: 'Arial' }, { v: 'Helvetica, Arial, sans-serif', l: 'Helvetica' }, { v: 'Georgia, serif', l: 'Georgia' }, { v: '"Times New Roman", Times, serif', l: 'Times New Roman' }, { v: '"Courier New", Courier, monospace', l: 'Courier New' }, { v: 'Inter, sans-serif', l: 'Inter' }, { v: 'Roboto, sans-serif', l: 'Roboto' }, { v: '"Trebuchet MS", sans-serif', l: 'Trebuchet MS' }, { v: 'Verdana, sans-serif', l: 'Verdana' }];
  const RT_SIZES = [1, 2, 3, 4, 5, 6, 7];
  const RT_SIZE_LABELS = { 1: '8pt', 2: '10pt', 3: '12pt', 4: '14pt', 5: '18pt', 6: '24pt', 7: '36pt' };

  function RichTextEditor({ html, onChange }) {
    const ref = useRef(null);
    const [active, setActive] = useState({ bold: false, italic: false, underline: false, strikeThrough: false, ul: false, ol: false });
    const [fontVal, setFontVal] = useState('');
    const [sizeVal, setSizeVal] = useState('');
    const [colorVal, setColorVal] = useState('#000000');
    const [bgVal, setBgVal] = useState('#ffff00');

    const exec = (cmd, val) => { ref.current && ref.current.focus(); document.execCommand(cmd, false, val || null); syncState(); if (ref.current) onChange(ref.current.innerHTML); };
    const syncState = () => {
      try {
        setActive({ bold: document.queryCommandState('bold'), italic: document.queryCommandState('italic'), underline: document.queryCommandState('underline'), strikeThrough: document.queryCommandState('strikeThrough'), ul: document.queryCommandState('insertUnorderedList'), ol: document.queryCommandState('insertOrderedList') });
        setFontVal(document.queryCommandValue('fontName') || ''); setColorVal(document.queryCommandValue('foreColor') || '#000000');
        const sz = document.queryCommandValue('fontSize'); setSizeVal(sz && sz !== 'false' ? sz : '');
      } catch (e) {}
    };

    useEffect(() => { if (ref.current && ref.current.innerHTML !== (html || '')) ref.current.innerHTML = html || ''; }, [html]);

    const btn = (icon, cmd, isActive, title) => h('button', { type: 'button', className: 'btn btn-sm rt-btn' + (isActive ? ' active' : ''), title, onMouseDown: e => { e.preventDefault(); exec(cmd); } }, h('i', { className: 'bi ' + icon }));

    return h('div', { className: 'rt-editor-wrap' },
      h('div', { className: 'rt-toolbar' },
        h('select', { className: 'form-select form-select-sm rt-font-select', value: fontVal, onChange: e => exec('fontName', e.target.value), onMouseDown: e => e.stopPropagation(), title: 'Police' }, RT_FONTS.map(f => h('option', { key: f.v, value: f.v, style: { fontFamily: f.v } }, f.l))),
        h('select', { className: 'form-select form-select-sm rt-size-select', value: sizeVal, onChange: e => exec('fontSize', e.target.value), onMouseDown: e => e.stopPropagation(), title: 'Taille' }, h('option', { value: '' }, 'Taille'), RT_SIZES.map(s => h('option', { key: s, value: String(s) }, RT_SIZE_LABELS[s] || s))),
        btn('bi-type-bold', 'bold', active.bold, 'Gras (Ctrl+B)'), btn('bi-type-italic', 'italic', active.italic, 'Italique (Ctrl+I)'), btn('bi-type-underline', 'underline', active.underline, 'Souligné (Ctrl+U)'), btn('bi-type-strikethrough', 'strikeThrough', active.strikeThrough, 'Barré'),
        h('span', { className: 'rt-sep' }),
        btn('bi-text-left', 'justifyLeft', false, 'Gauche'), btn('bi-text-center', 'justifyCenter', false, 'Centre'), btn('bi-text-right', 'justifyRight', false, 'Droite'), btn('bi-justify', 'justifyFull', false, 'Justifier'),
        h('span', { className: 'rt-sep' }),
        btn('bi-list-ul', 'insertUnorderedList', active.ul, 'Puces'), btn('bi-list-ol', 'insertOrderedList', active.ol, 'Numérotée'), btn('bi-x-circle', 'removeFormat', false, 'Effacer'),
        h('span', { className: 'rt-sep' }),
        h('label', { className: 'rt-color-label', title: 'Couleur texte' }, h('i', { className: 'bi bi-palette-fill' }), h('input', { type: 'color', className: 'rt-color-input', value: colorVal, onChange: e => { setColorVal(e.target.value); exec('foreColor', e.target.value); }, onMouseDown: e => e.stopPropagation() })),
        h('label', { className: 'rt-color-label', title: 'Surlignage' }, h('i', { className: 'bi bi-highlighter' }), h('input', { type: 'color', className: 'rt-color-input', value: bgVal, onChange: e => { setBgVal(e.target.value); exec('hiliteColor', e.target.value); }, onMouseDown: e => e.stopPropagation() }))),
      h('div', { ref, className: 'rt-content', contentEditable: true, suppressContentEditableWarning: true,
        onInput: () => { if (ref.current) onChange(ref.current.innerHTML); }, onKeyUp: syncState, onMouseUp: syncState,
        onKeyDown: e => { if (e.ctrlKey || e.metaKey) { if (e.key === 'b') { e.preventDefault(); exec('bold'); } if (e.key === 'i') { e.preventDefault(); exec('italic'); } if (e.key === 'u') { e.preventDefault(); exec('underline'); } } } }));
  }

  // ============================================================
  // IMAGE UPLOAD COMPONENT
  // ============================================================
  function ImageUpload({ label: lbl, url, onUpload }) {
    const [uploading, setUploading] = useState(false);
    const [error, setError] = useState('');

    const handleFile = (file) => {
      if (!file) return;
      setUploading(true);
      setError('');
      const fd = new FormData();
      fd.append('file', file);
      fetch('/admin/reportstudio/upload-image', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.ok) onUpload(data.url);
          else setError(data.error || 'Erreur');
        })
        .catch(() => setError('Erreur réseau'))
        .finally(() => setUploading(false));
    };

    return h('div', { className: 'rs-img-upload mb-2' },
      h('label', { className: 'form-label small fw-bold' }, lbl || 'Télécharger une image'),
      h('div', { className: 'rs-img-upload-zone', onDragOver: e => e.preventDefault(), onDrop: e => { e.preventDefault(); handleFile(e.dataTransfer.files[0]); } },
        uploading
          ? h('div', { className: 'text-center py-2' }, h('div', { className: 'spinner-border spinner-border-sm text-primary' }), ' Téléchargement...')
          : h(React.Fragment, null,
              h('input', { type: 'file', accept: 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml', className: 'd-none', id: 'rs-upload-' + (lbl || '').replace(/[^a-z0-9]/gi, ''), onChange: e => handleFile(e.target.files[0]) }),
              h('label', { className: 'btn btn-sm btn-outline-primary w-100', htmlFor: 'rs-upload-' + (lbl || '').replace(/[^a-z0-9]/gi, '') },
                h('i', { className: 'bi bi-upload' }), ' Choisir un fichier'),
              h('p', { className: 'text-muted small text-center mt-1 mb-0' }, 'ou glissez-déposez ici'))),
      url ? h('div', { className: 'rs-img-preview mt-1' },
        h('img', { src: url, alt: 'Aperçu', style: { maxHeight: '60px', borderRadius: '4px' } }),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-danger ms-2', onClick: () => onUpload('') }, h('i', { className: 'bi bi-x' }), ' Retirer')) : null,
      error ? h('p', { className: 'text-danger small mt-1 mb-0' }, error) : null);
  }

  // ============================================================
  // PROPERTY FIELD HELPERS
  // ============================================================
  function label(text) { return h('label', { className: 'form-label small fw-bold' }, text); }
  function field(lbl, type, prop, def, setCfg) {
    return h(React.Fragment, null, label(lbl),
      h('input', { type, className: 'form-control form-control-sm mb-2', value: def, onChange: e => setCfg(prop, type === 'number' ? (parseFloat(e.target.value) || 0) : e.target.value) }));
  }
  function selectField(lbl, prop, options, current, setCfg) {
    return h(React.Fragment, null, label(lbl),
      h('select', { className: 'form-select form-select-sm mb-2', value: current, onChange: e => setCfg(prop, e.target.value) }, options.map(o => h('option', { key: o.v, value: o.v }, o.l))));
  }
  function switchField(lbl, prop, checked, setCfg) {
    return h('div', { className: 'form-check form-switch' },
      h('input', { className: 'form-check-input', type: 'checkbox', checked: checked, onChange: e => setCfg(prop, e.target.checked) }),
      h('label', { className: 'form-check-label small' }, lbl));
  }
  function colorField(lbl, prop, def, setCfg) {
    const safeDef = (def && def.startsWith('#')) ? def : '#102A43';
    const hasValue = def && def.startsWith('#');
    return h(React.Fragment, null, label(lbl),
      h('div', { className: 'input-group input-group-sm mb-2' },
        h('input', { type: 'color', className: 'form-control form-control-color', value: safeDef, onChange: e => setCfg(prop, e.target.value) }),
        h('input', { type: 'text', className: 'form-control', value: def || '', placeholder: 'Aucune', onChange: e => setCfg(prop, e.target.value) }),
        hasValue && h('button', { type: 'button', className: 'btn btn-outline-secondary', title: 'Réinitialiser', onClick: () => setCfg(prop, '') }, h('i', { className: 'bi bi-x' }))));
  }

  // ============================================================
  // BLOCK-SPECIFIC PROPERTY RENDERERS
  // ============================================================
  function renderBlockProperties(blockKey, cfg, setCfg) {
    const val = (k, d) => cfg[k] !== undefined ? cfg[k] : d;
    switch (blockKey) {
      case 'global_score':
        return h(React.Fragment, null,
          field('Libellé', 'text', 'label', val('label', 'Score global'), setCfg),
          field('Score', 'number', 'score', +cfg.score || 0, setCfg),
          field('Score maximum', 'number', 'max', +cfg.max || 100, setCfg),
          field('Suffixe', 'text', 'suffix', val('suffix', ''), setCfg),
          colorField('Couleur principale', 'color', val('color', '#102A43'), setCfg),
          selectField('Taille', 'size', [{ v: 'sm', l: 'Petite' }, { v: 'md', l: 'Moyenne' }, { v: 'lg', l: 'Grande' }], cfg.size || 'md', setCfg),
          switchField('Afficher la note', 'show_rating', cfg.show_rating !== false, setCfg),
          switchField('Barre de progression', 'show_progress', cfg.show_progress !== false, setCfg));
      case 'radar_chart': {
        const axes = cfg.axes || [];
        const raw = axes.map(a => (a.label || '') + ',' + (a.value || 0)).join('\n');
        return h(React.Fragment, null,
          label('Axes (Libellé, Valeur par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('axes_raw', e.target.value) }),
          colorField('Couleur', 'color', val('color', '#102A43'), setCfg),
          field('Opacité remplissage', 'number', 'fill_opacity', +cfg.fill_opacity || 0.2, setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 280, setCfg),
          field('Épaisseur trait', 'number', 'stroke_width', +cfg.stroke_width || 2, setCfg),
          switchField('Légende', 'legend', cfg.legend !== false, setCfg),
          switchField('Marqueurs', 'show_markers', cfg.show_markers !== false, setCfg));
      }
      case 'bar_chart': {
        const series = cfg.series || [];
        const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
        return h(React.Fragment, null,
          label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_bar', e.target.value) }),
          colorField('Couleur série 1', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur série 2', 'color2', val('color2', '#2EC4B6'), setCfg),
          colorField('Couleur série 3', 'color3', val('color3', '#9d8fd1'), setCfg),
          colorField('Couleur série 4', 'color4', val('color4', '#E5484D'), setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 280, setCfg),
          field('Rayon barres', 'number', 'bar_radius', +cfg.bar_radius || 4, setCfg),
          field('Opacité', 'number', 'fill_opacity', +cfg.fill_opacity || 0.9, setCfg),
          switchField('Horizontal', 'horizontal', !!cfg.horizontal, setCfg),
          switchField('Empilé', 'stacked', !!cfg.stacked, setCfg),
          switchField('Légende', 'legend', cfg.legend !== false, setCfg));
      }
      case 'line_chart': {
        const series = cfg.series || [];
        const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
        return h(React.Fragment, null,
          label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_line', e.target.value) }),
          colorField('Couleur série 1', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur série 2', 'color2', val('color2', '#2EC4B6'), setCfg),
          colorField('Couleur série 3', 'color3', val('color3', '#9d8fd1'), setCfg),
          colorField('Couleur série 4', 'color4', val('color4', '#E5484D'), setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 280, setCfg),
          field('Épaisseur trait', 'number', 'stroke_width', +cfg.stroke_width || 2, setCfg),
          switchField('Courbe lisse', 'smooth', cfg.smooth !== false, setCfg),
          switchField('Marqueurs', 'show_markers', cfg.show_markers !== false, setCfg),
          switchField('Remplir zone', 'fill_area', !!cfg.fill_area, setCfg),
          switchField('Légende', 'legend', cfg.legend !== false, setCfg));
      }
      case 'area_chart': {
        const series = cfg.series || [];
        const raw = series.map(s => (s.label || '') + ',' + (s.data || []).map(d => (d.label || '') + ':' + (d.value || 0)).join(';')).join('\n');
        return h(React.Fragment, null,
          label('Séries (Libellé, Cat:Val;Cat:Val... par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_area', e.target.value) }),
          colorField('Couleur série 1', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur série 2', 'color2', val('color2', '#2EC4B6'), setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 280, setCfg),
          field('Épaisseur trait', 'number', 'stroke_width', +cfg.stroke_width || 2, setCfg),
          field('Opacité début', 'number', 'fill_opacity_from', +cfg.fill_opacity_from || 0.3, setCfg),
          field('Opacité fin', 'number', 'fill_opacity_to', +cfg.fill_opacity_to || 0.05, setCfg),
          switchField('Courbe lisse', 'smooth', cfg.smooth !== false, setCfg),
          switchField('Marqueurs', 'show_markers', cfg.show_markers !== false, setCfg),
          switchField('Légende', 'legend', cfg.legend !== false, setCfg));
      }
      case 'donut_chart': {
        const series = cfg.series || [];
        const raw = series.map(s => (s.label || '') + ',' + (s.value || 0)).join('\n');
        return h(React.Fragment, null,
          label('Segments (Libellé, Valeur par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('series_raw_donut', e.target.value) }),
          colorField('Couleur 1', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur 2', 'color2', val('color2', '#2EC4B6'), setCfg),
          colorField('Couleur 3', 'color3', val('color3', '#486581'), setCfg),
          colorField('Couleur 4', 'color4', val('color4', '#9d8fd1'), setCfg),
          colorField('Couleur 5', 'color5', val('color5', '#E5484D'), setCfg),
          colorField('Couleur 6', 'color6', val('color6', '#1F6FEB'), setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 280, setCfg),
          field('Taille du trou', 'text', 'donut_size', val('donut_size', '65%'), setCfg),
          field('Épaisseur trait', 'number', 'stroke_width', +cfg.stroke_width || 2, setCfg),
          switchField('Pourcentages', 'show_percent', cfg.show_percent !== false, setCfg),
          switchField('Légende', 'legend', cfg.legend !== false, setCfg));
      }
      case 'gauge':
        return h(React.Fragment, null,
          field('Libellé', 'text', 'label', val('label', 'Indicateur'), setCfg),
          field('Valeur', 'number', 'value', +cfg.value || 0, setCfg),
          h('div', { className: 'row g-2 mb-2' },
            h('div', { className: 'col-6' }, field('Min', 'number', 'min', +cfg.min || 0, setCfg)),
            h('div', { className: 'col-6' }, field('Max', 'number', 'max', +cfg.max || 100, setCfg))),
          field('Unité', 'text', 'unit', val('unit', '%'), setCfg),
          colorField('Couleur de l\'arc', 'color', val('color', '#2EC4B6'), setCfg),
          field('Hauteur (px)', 'number', 'height', +cfg.height || 160, setCfg),
          field('Angle départ (°)', 'number', 'start_angle', +cfg.start_angle || -135, setCfg),
          field('Angle fin (°)', 'number', 'end_angle', +cfg.end_angle || 135, setCfg),
          field('Taille du creux', 'text', 'hollow_size', val('hollow_size', '62%'), setCfg),
          switchField('Afficher libellé', 'show_label', cfg.show_label !== false, setCfg),
          switchField('Afficher valeur', 'show_value', cfg.show_value !== false, setCfg));
      case 'recommendations': {
        const items = cfg.items || [];
        const raw = items.map(i => (typeof i === 'object' ? i.text : i) || '').join('\n');
        return h(React.Fragment, null,
          field('Titre', 'text', 'title', val('title', 'Recommandations'), setCfg),
          label('Recommandations (une par ligne)'),
          h('textarea', { className: 'form-control form-control-sm', rows: 6, value: raw, onChange: e => setCfg('items_raw', e.target.value) }),
          selectField('Icône', 'icon', [{ v: 'bi-list-check', l: 'Liste' }, { v: 'bi-check-circle', l: 'Cercle' }, { v: 'bi-arrow-right-circle', l: 'Flèche' }, { v: 'bi-star', l: 'Étoile' }, { v: 'bi-lightbulb', l: 'Ampoule' }, { v: 'bi-shield-check', l: 'Bouclier' }], cfg.icon || 'bi-list-check', setCfg),
          colorField('Couleur texte', 'color', val('color', '#102A43'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.9rem'), setCfg),
          switchField('Numérotation', 'numbered', cfg.numbered !== false, setCfg));
      }
      case 'company_info': {
        const fields = cfg.fields || [];
        return h(React.Fragment, null,
          label('Champs affichés'),
          h('div', null, fields.map((f, i) => h('div', { key: i, className: 'input-group input-group-sm mb-1' },
            h('input', { type: 'text', className: 'form-control', placeholder: 'Clé', value: f.key || '', onChange: e => { const nf = [...fields]; nf[i] = { ...f, key: e.target.value }; setCfg('fields', nf); } }),
            h('input', { type: 'text', className: 'form-control', placeholder: 'Libellé', value: f.label || '', onChange: e => { const nf = [...fields]; nf[i] = { ...f, label: e.target.value }; setCfg('fields', nf); } }),
            h('button', { type: 'button', className: 'btn btn-outline-danger', onClick: () => setCfg('fields', fields.filter((_, j) => j !== i)) }, h('i', { className: 'bi bi-x' })))),
            fields.length === 0 ? h('p', { className: 'text-muted small' }, 'Aucun champ') : null),
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary w-100 mb-2', onClick: () => setCfg('fields', [...(cfg.fields || []), { key: '', label: '' }]) }, h('i', { className: 'bi bi-plus' }), ' Ajouter un champ'),
          switchField('Inclure logo', 'show_logo', cfg.show_logo !== false, setCfg),
          selectField('Disposition', 'layout', [{ v: 'vertical', l: 'Verticale' }, { v: 'horizontal', l: 'Horizontale' }, { v: 'grid', l: 'Grille' }], cfg.layout || 'vertical', setCfg),
          colorField('Couleur libellés', 'label_color', val('label_color', '#6b7280'), setCfg),
          colorField('Couleur valeurs', 'value_color', val('value_color', '#102A43'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.9rem'), setCfg));
      }
      case 'aqmi_logo':
      case 'company_logo': {
        const isCompany = blockKey === 'company_logo';
        const isAqmi = blockKey === 'aqmi_logo';
        return h(React.Fragment, null,
          isAqmi && switchField('Utiliser une image personnalisée', 'use_custom_image', !!cfg.use_custom_image, setCfg),
          isAqmi && cfg.use_custom_image ? h(React.Fragment, null,
            h(ImageUpload, { label: 'Télécharger le logo (JPG, PNG, SVG...)', url: cfg.image_url || '', onUpload: (url) => setCfg('image_url', url) }),
            field('Hauteur de l\'image', 'text', 'image_height', val('image_height', '60px'), setCfg),
            field('Arrondi coins', 'text', 'image_border_radius', val('image_border_radius', '0'), setCfg))
          : null,
          isCompany ? field('URL du logo', 'text', 'url', val('url', ''), setCfg) : null,
          isCompany ? colorField('Couleur (si SVG)', 'color', val('color', '#102A43'), setCfg) : null,
          !isAqmi || !cfg.use_custom_image ? selectField('Taille', 'size', [{ v: 'sm', l: 'Petite' }, { v: 'md', l: 'Moyenne' }, { v: 'lg', l: 'Grande' }, { v: 'xl', l: 'Très grande' }], cfg.size || 'md', setCfg) : null,
          selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'left', setCfg),
          isCompany ? field('Hauteur max', 'text', 'max_height', val('max_height', '80px'), setCfg) : null,
          isCompany ? field('Arrondi coins', 'text', 'border_radius', val('border_radius', '0'), setCfg) : null);
      }
      case 'official_stamp':
        return h(React.Fragment, null,
          switchField('Utiliser une image personnalisée', 'use_custom_image', !!cfg.use_custom_image, setCfg),
          cfg.use_custom_image ? h(React.Fragment, null,
            h(ImageUpload, { label: 'Télécharger le tampon (JPG, PNG, SVG...)', url: cfg.image_url || '', onUpload: (url) => setCfg('image_url', url) }),
            field('Hauteur de l\'image', 'text', 'image_height', val('image_height', '100px'), setCfg),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'right', setCfg))
          : h(React.Fragment, null,
            selectField('Style', 'style', [{ v: 'circular', l: 'Circulaire' }, { v: 'rectangular', l: 'Rectangulaire' }, { v: 'badge', l: 'Badge' }], cfg.style || 'circular', setCfg),
            field('Texte principal', 'text', 'text', val('text', 'CERTIFIÉ'), setCfg),
            field('Sous-texte', 'text', 'subtext', val('subtext', 'AQMI'), setCfg),
            colorField('Couleur', 'color', val('color', '#102A43'), setCfg),
            field('Taille (px)', 'number', 'size', +cfg.size || 100, setCfg),
            field('Épaisseur bordure', 'number', 'border_width', +cfg.border_width || 3, setCfg),
            field('Taille police', 'text', 'font_size', val('font_size', '0.9rem'), setCfg),
            selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'right', setCfg)));
      
      case 'qr_code':
        return h(React.Fragment, null,
          selectField('Mode', 'mode', [{ v: 'manual', l: 'Manuel (URL ou texte)' }, { v: 'verify', l: 'Vérification certificat (auto)' }], cfg.mode || 'manual', setCfg),
          (cfg.mode || 'manual') === 'verify'
            ? h('div', { className: 'alert alert-info py-1 px-2 mb-2 small' }, h('i', { className: 'bi bi-info-circle' }), ' Le QR pointera automatiquement vers la page publique de vérification /c/{token} propre à chaque certificat.')
            : field('Donnée encodée', 'text', 'value', val('value', ''), setCfg),
          field('Taille (px)', 'number', 'size', +cfg.size || 120, setCfg),
          field('Libellé sous QR', 'text', 'label', val('label', ''), setCfg),
          selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'center', setCfg),
          field('Marge (px)', 'number', 'margin', +cfg.margin || 0, setCfg));
      case 'signature':
        return h(React.Fragment, null,
          field('Nom du signataire', 'text', 'label', val('label', ''), setCfg),
          field('Fonction', 'text', 'role', val('role', ''), setCfg),
          colorField('Couleur ligne', 'line_color', val('line_color', '#102A43'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.9rem'), setCfg),
          selectField('Format date', 'date_format', [{ v: 'fr-FR', l: 'FR (jj/mm/aaaa)' }, { v: 'en-US', l: 'EN (mm/dd/yyyy)' }, { v: 'de-DE', l: 'DE (dd.mm.yyyy)' }], cfg.date_format || 'fr-FR', setCfg),
          switchField('Afficher date', 'show_date', cfg.show_date !== false, setCfg),
          switchField('Tampon officiel', 'show_stamp', !!cfg.show_stamp, setCfg));
      case 'header':
        return h(React.Fragment, null,
          field('Texte', 'text', 'text', val('text', ''), setCfg),
          h('small', { className: 'd-block text-muted mb-2' }, 'Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}'),
          selectField('Niveau', 'level', [{ v: '1', l: 'H1' }, { v: '2', l: 'H2' }, { v: '3', l: 'H3' }], String(cfg.level || 1), setCfg),
          selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'left', setCfg),
          colorField('Couleur texte', 'color', val('color', '#102A43'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', ''), setCfg),
          switchField('Majuscules', 'uppercase', !!cfg.uppercase, setCfg),
          switchField('Bordure inférieure', 'border_bottom', !!cfg.border_bottom, setCfg),
          switchField('N° rapport', 'show_report_number', !!cfg.show_report_number, setCfg),
          switchField('Date', 'show_date', !!cfg.show_date, setCfg),
          switchField('Pagination', 'show_page_number', !!cfg.show_page_number, setCfg));
      case 'footer':
        return h(React.Fragment, null,
          field('Texte', 'text', 'text', val('text', ''), setCfg),
          h('small', { className: 'd-block text-muted mb-2' }, 'Variables: {report_number} {certification_date} {expiration_date} {current_date} {template_name} {page} {total_pages}'),
          selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'center', setCfg),
          colorField('Couleur texte', 'color', val('color', '#6b7280'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.8rem'), setCfg),
          switchField('Bordure supérieure', 'border_top', cfg.border_top !== false, setCfg),
          switchField('N° rapport', 'show_report_number', !!cfg.show_report_number, setCfg),
          switchField('Date', 'show_date', !!cfg.show_date, setCfg),
          switchField('Pagination', 'show_page_number', cfg.show_page_number !== false, setCfg));
      case 'rich_text':
        return h(React.Fragment, null,
          h(RichTextEditor, { html: cfg.html || '', onChange: (html) => setCfg('html', html) }),
          field('Padding', 'text', 'padding', val('padding', '8px'), setCfg),
          field('Police', 'text', 'font_family', val('font_family', 'Inter, sans-serif'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.9rem'), setCfg));
      case 'image':
        return h(React.Fragment, null,
          h(ImageUpload, { label: 'Télécharger une image (JPG, PNG, GIF, WebP, SVG)', url: cfg.url || '', onUpload: (url) => setCfg('url', url) }),
          field('Ou URL image', 'text', 'url', val('url', ''), setCfg),
          field('Texte alternatif', 'text', 'alt', val('alt', ''), setCfg),
          field('Largeur', 'text', 'width', val('width', '100%'), setCfg),
          field('Hauteur max', 'text', 'max_height', val('max_height', ''), setCfg),
          selectField('Alignement', 'align', [{ v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg.align || 'center', setCfg),
          selectField('Ajustement', 'object_fit', [{ v: 'contain', l: 'Contenir' }, { v: 'cover', l: 'Couvrir' }, { v: 'fill', l: 'Remplir' }, { v: 'none', l: 'Aucun' }], cfg.object_fit || 'contain', setCfg),
          field('Arrondi coins', 'text', 'border_radius', val('border_radius', '0'), setCfg));
      case 'background':
        return h(React.Fragment, null,
          h(ImageUpload, { label: 'Télécharger l\'image de fond (JPG, PNG, SVG...)', url: cfg.image_url || '', onUpload: (url) => setCfg('image_url', url) }),
          field('Ou URL de l\'image de fond', 'text', 'image_url', val('image_url', ''), setCfg),
          colorField('Couleur de fond', 'bg_color', val('bg_color', '#ffffff'), setCfg),
          field('Opacité', 'number', 'opacity', +cfg.opacity ?? 1, setCfg),
          selectField('Taille', 'size', [{ v: 'cover', l: 'Couvrir' }, { v: 'contain', l: 'Contenir' }, { v: '100% 100%', l: 'Étirer' }, { v: 'auto', l: 'Auto' }], cfg.size || 'cover', setCfg),
          selectField('Position', 'position', [{ v: 'center', l: 'Centré' }, { v: 'top', l: 'Haut' }, { v: 'bottom', l: 'Bas' }, { v: 'left', l: 'Gauche' }, { v: 'right', l: 'Droite' }], cfg.position || 'center', setCfg),
          selectField('Répétition', 'repeat', [{ v: 'no-repeat', l: 'Aucune' }, { v: 'repeat', l: 'Répéter' }, { v: 'repeat-x', l: 'Horizontale' }, { v: 'repeat-y', l: 'Verticale' }], cfg.repeat || 'no-repeat', setCfg),
          field('Hauteur min', 'text', 'min_height', val('min_height', '300px'), setCfg),
          field('Padding', 'text', 'padding', val('padding', '24px'), setCfg));
      
      case 'cover_page':
        return h(React.Fragment, null,
          field('Nom entreprise', 'text', 'company_name', val('company_name', ''), setCfg),
          field('Titre rapport', 'text', 'report_title', val('report_title', ''), setCfg),
          field('Sous-titre', 'text', 'subtitle', val('subtitle', ''), setCfg),
          colorField('Couleur accent', 'accent_color', val('accent_color', '#102A43'), setCfg),
          colorField('Couleur fond', 'bg_color', val('bg_color', '#ffffff'), setCfg),
          h(ImageUpload, { label: 'Image de fond (optionnel)', url: cfg.bg_image_url || '', onUpload: (url) => setCfg('bg_image_url', url) }),
          field('Opacité image de fond', 'number', 'bg_image_opacity', +cfg.bg_image_opacity ?? 1, setCfg),
          selectField('Taille image de fond', 'bg_image_size', [{ v: 'cover', l: 'Couvrir' }, { v: 'contain', l: 'Contenir' }, { v: '100% 100%', l: 'Étirer' }, { v: 'auto', l: 'Auto' }], cfg.bg_image_size || 'cover', setCfg),
          selectField('Position image de fond', 'bg_image_position', [{ v: 'center', l: 'Centré' }, { v: 'top', l: 'Haut' }, { v: 'bottom', l: 'Bas' }, { v: 'left', l: 'Gauche' }, { v: 'right', l: 'Droite' }], cfg.bg_image_position || 'center', setCfg),
          selectField('Répétition image de fond', 'bg_image_repeat', [{ v: 'no-repeat', l: 'Aucune' }, { v: 'repeat', l: 'Répéter' }], cfg.bg_image_repeat || 'no-repeat', setCfg),
          colorField('Couleur texte', 'text_color', val('text_color', '#102A43'), setCfg),
          colorField('Couleur bordure', 'border_color', val('border_color', '#EEF2F7'), setCfg),
          field('Taille titre', 'text', 'font_size_title', val('font_size_title', '1.5rem'), setCfg),
          field('Taille sous-titre', 'text', 'font_size_subtitle', val('font_size_subtitle', '0.9rem'), setCfg),
          field('Padding', 'text', 'padding', val('padding', '40px'), setCfg),
          switchField('Logo', 'show_logo', cfg.show_logo !== false, setCfg),
          switchField('Tampon', 'show_stamp', cfg.show_stamp !== false, setCfg),
          switchField('Date', 'show_date', cfg.show_date !== false, setCfg),
          switchField('Numéro', 'show_number', cfg.show_number !== false, setCfg));
      case 'kpi_card':
        return h(React.Fragment, null,
          field('Libellé', 'text', 'label', val('label', ''), setCfg),
          field('Valeur', 'text', 'value', val('value', ''), setCfg),
          field('Unité', 'text', 'unit', val('unit', ''), setCfg),
          field('Icône', 'text', 'icon', val('icon', 'bi-check-circle'), setCfg),
          colorField('Couleur principale', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur fond', 'bg_color', val('bg_color', '#ffffff'), setCfg),
          colorField('Couleur bordure', 'border_color', val('border_color', '#EEF2F7'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '1.1rem'), setCfg),
          field('Tendance', 'text', 'trend', val('trend', ''), setCfg),
          selectField('Direction', 'trend_direction', [{ v: 'up', l: 'Hausse' }, { v: 'down', l: 'Baisse' }, { v: 'flat', l: 'Stable' }], cfg.trend_direction || 'up', setCfg),
          switchField('Fond d\'icône', 'icon_bg', !!cfg.icon_bg, setCfg),
          switchField('Afficher tendance', 'show_trend', cfg.show_trend !== false, setCfg));
      case 'domain_scores': {
        const domains = cfg.domains || [];
        const raw = domains.map(d => (d.label || '') + ',' + (d.score || 0) + ',' + (d.max || 100)).join('\n');
        return h(React.Fragment, null,
          field('Titre', 'text', 'title', val('title', 'Scores par domaine'), setCfg),
          label('Domaines (Libellé, Score, Max par ligne)'),
          h('textarea', { className: 'form-control form-control-sm mb-2', rows: 5, value: raw, onChange: e => setCfg('domains_raw', e.target.value) }),
          colorField('Couleur', 'color', val('color', '#102A43'), setCfg),
          colorField('Couleur bordure', 'border_color', val('border_color', '#EEF2F7'), setCfg),
          field('Taille police', 'text', 'font_size', val('font_size', '0.85rem'), setCfg),
          switchField('Lignes alternées', 'alternating_rows', cfg.alternating_rows !== false, setCfg),
          switchField('Barre progression', 'show_progress_bar', cfg.show_progress_bar !== false, setCfg));
      }
      case 'page_break':
        return h(React.Fragment, null,
          field('Libellé', 'text', 'label', val('label', 'Saut de page'), setCfg),
          switchField('Afficher libellé', 'show_label', !!cfg.show_label, setCfg),
          field('Espacement', 'text', 'spacing', val('spacing', '2rem'), setCfg));
      default:
        return h('div', { className: 'text-muted small' }, 'Aucune propriété pour ce bloc.');
    }
  }

  // ============================================================
  // TABBED PROPERTY PANEL
  // ============================================================
  function PropertyPanel({ block, state, dispatch, onUpdate, onDelete, onDuplicate, onToggleEnabled }) {
    const [activeTab, setLocalTab] = useState('general');

    if (!block) {
      return h('div', { className: 'rs-properties-empty' },
        h('i', { className: 'bi bi-hand-index' }),
        h('p', null, 'Sélectionnez un bloc pour éditer ses propriétés'));
    }

    const cfg = block.block_config || {};
    const setCfg = (key, val) => {
      onUpdate(prev => {
        const prevCfg = prev.block_config || {};
        const newCfg = { ...prevCfg, [key]: val };
        if (key === 'axes_raw') {
          newCfg.axes = val.split('\n').filter(l => l.trim()).map(line => { const [label, value] = line.split(','); return { label: (label || '').trim(), value: parseInt(value || '0', 10) }; });
          delete newCfg.axes_raw;
        }
        if (key === 'items_raw') { newCfg.items = val.split('\n').filter(l => l.trim()).map(line => ({ text: line.trim() })); delete newCfg.items_raw; }
        if (key === 'series_raw_bar' || key === 'series_raw_line' || key === 'series_raw_area') {
          newCfg.series = val.split('\n').filter(l => l.trim()).map(line => { const [label, ...rest] = line.split(','); const dataStr = rest.join(','); const pairs = dataStr.split(';').filter(p => p.trim()); const data = pairs.map(p => { const [dl, dv] = p.split(':'); return { label: (dl || '').trim(), value: parseFloat(dv || '0') || 0 }; }); return { label: (label || '').trim(), data }; });
          delete newCfg[key];
        }
        if (key === 'series_raw_donut') { newCfg.series = val.split('\n').filter(l => l.trim()).map(line => { const [label, value] = line.split(','); return { label: (label || '').trim(), value: parseFloat(value || '0') || 0 }; }); delete newCfg.series_raw_donut; }
        if (key === 'domains_raw') { newCfg.domains = val.split('\n').filter(l => l.trim()).map(line => { const p = line.split(','); return { label: (p[0] || '').trim(), score: parseInt(p[1] || '0', 10), max: parseInt(p[2] || '100', 10) }; }); delete newCfg.domains_raw; }
        return { ...prev, block_config: newCfg };
      });
    };

    const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
    const tab = (id, icon, label) => h('button', {
      type: 'button',
      className: 'rs-tab' + (activeTab === id ? ' active' : ''),
      onClick: () => setLocalTab(id),
    }, h('i', { className: 'bi ' + icon }), h('span', null, label));

    return h('div', { className: 'rs-prop-content' },
      h('div', { className: 'rs-prop-block-info' },
        h('span', { className: 'badge bg-primary' }, h('i', { className: 'bi ' + meta.icon }), ' ' + meta.label)),
      h('div', { className: 'rs-prop-tabs' },
        tab('general', 'bi-gear', 'General'),
        tab('appearance', 'bi-palette', 'Appearance'),
        tab('typography', 'bi-type', 'Typography'),
        tab('data', 'bi-database', 'Data'),
        tab('behavior', 'bi-sliders', 'Behavior')),
      h('div', { className: 'rs-prop-tab-content' },
        activeTab === 'general' && h(React.Fragment, null,
          h('div', { className: 'mb-2' }, label('Titre du bloc'),
            h('input', { type: 'text', className: 'form-control form-control-sm', value: block.title || '', onChange: e => onUpdate(prev => ({ ...prev, title: e.target.value })) })),
          h('div', { className: 'mb-2' }, label('Type'),
            h('input', { type: 'text', className: 'form-control form-control-sm', value: meta.label, disabled: true })),
          h('div', { className: 'mb-2' }, label('Largeur (colonnes / 12)'),
            h('input', { type: 'range', className: 'form-range', min: 1, max: 12, value: block.column_span || 12, onChange: e => onUpdate(prev => ({ ...prev, column_span: parseInt(e.target.value) })) }),
            h('div', { className: 'small text-muted text-center' }, (block.column_span || 12) + ' / 12')),
          h('div', { className: 'mb-2' }, label('Visibilité'),
            h('select', { className: 'form-select form-select-sm', value: block.visibility || 'web_pdf', onChange: e => onUpdate(prev => ({ ...prev, visibility: e.target.value })) },
              h('option', { value: 'web_pdf' }, 'Web + PDF'), h('option', { value: 'web_only' }, 'Web uniquement'), h('option', { value: 'pdf_only' }, 'PDF uniquement'))),
          h('div', { className: 'form-check form-switch mb-2' },
            h('input', { className: 'form-check-input', type: 'checkbox', checked: block.is_enabled !== false, onChange: () => onToggleEnabled(block) }),
            h('label', { className: 'form-check-label small' }, 'Bloc actif')),
          h('hr', { className: 'rs-prop-sep' }),
          renderBlockProperties(block.block_key, cfg, setCfg)),
        activeTab === 'appearance' && h(React.Fragment, null,
          colorField('Couleur de fond', '_style_bg', cfg._style_bg || '', setCfg),
          colorField('Couleur bordure', '_style_border', cfg._style_border || '', setCfg),
          field('Épaisseur bordure', 'text', '_style_border_width', cfg._style_border_width || '', setCfg),
          field('Arrondi', 'text', '_style_border_radius', cfg._style_border_radius || '', setCfg),
          field('Padding', 'text', '_style_padding', cfg._style_padding || '', setCfg),
          field('Marge', 'text', '_style_margin', cfg._style_margin || '', setCfg),
          selectField('Alignement texte', '_style_text_align', [{ v: '', l: 'Défaut' }, { v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }], cfg._style_text_align || '', setCfg)),
        activeTab === 'typography' && h(React.Fragment, null,
          selectField('Police', '_style_font_family', [{ v: '', l: 'Défaut' }, { v: 'Inter, sans-serif', l: 'Inter' }, { v: 'Arial, sans-serif', l: 'Arial' }, { v: 'Georgia, serif', l: 'Georgia' }, { v: '"Times New Roman", serif', l: 'Times New Roman' }, { v: 'Roboto, sans-serif', l: 'Roboto' }], cfg._style_font_family || '', setCfg),
          field('Taille police', 'text', '_style_font_size', cfg._style_font_size || '', setCfg),
          selectField('Graisse', '_style_font_weight', [{ v: '', l: 'Défaut' }, { v: '300', l: 'Léger' }, { v: '400', l: 'Normal' }, { v: '600', l: 'Semi-gras' }, { v: '700', l: 'Gras' }, { v: '800', l: 'Extra-gras' }], cfg._style_font_weight || '', setCfg),
          switchField('Italique', '_style_font_italic', !!cfg._style_font_italic, setCfg),
          switchField('Souligné', '_style_text_decoration', !!cfg._style_text_decoration, setCfg),
          selectField('Alignement', '_style_text_align', [{ v: '', l: 'Défaut' }, { v: 'left', l: 'Gauche' }, { v: 'center', l: 'Centre' }, { v: 'right', l: 'Droite' }, { v: 'justify', l: 'Justifié' }], cfg._style_text_align || '', setCfg),
          field('Espacement lettres', 'text', '_style_letter_spacing', cfg._style_letter_spacing || '', setCfg)),
        activeTab === 'data' && h(React.Fragment, null,
          DS_BLOCKS.includes(block.block_key) ? h(DataSourcePanel, {
            cfg, setCfg: (prop, val) => { if (prop === '__replace__') onUpdate(prev => ({ ...prev, block_config: val })); else onUpdate(prev => ({ ...prev, block_config: { ...prev.block_config, [prop]: val } })); },
          }) : h('div', { className: 'text-muted small' }, 'Aucune source de données pour ce type de bloc.')),
        activeTab === 'behavior' && h(React.Fragment, null,
          h('div', { className: 'mb-2' }, label('Visibilité'),
            h('select', { className: 'form-select form-select-sm', value: block.visibility || 'web_pdf', onChange: e => onUpdate(prev => ({ ...prev, visibility: e.target.value })) },
              h('option', { value: 'web_pdf' }, 'Web + PDF'), h('option', { value: 'web_only' }, 'Web uniquement'), h('option', { value: 'pdf_only' }, 'PDF uniquement'))),
          h('div', { className: 'form-check form-switch mb-2' },
            h('input', { className: 'form-check-input', type: 'checkbox', checked: block.is_enabled !== false, onChange: () => onToggleEnabled(block) }),
            h('label', { className: 'form-check-label small' }, 'Bloc actif')),
          h('div', { className: 'text-muted small mt-2' }, 'Conditions et expressions avancées seront disponibles dans une prochaine version.'))),
      h('hr', { className: 'rs-prop-sep' }),
      h('div', { className: 'd-flex gap-2 rs-prop-actions' },
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-primary', onClick: () => onDuplicate(block) }, h('i', { className: 'bi bi-files' }), ' Dupliquer'),
        h('button', { type: 'button', className: 'btn btn-sm btn-outline-danger', onClick: () => onDelete(block) }, h('i', { className: 'bi bi-trash' }), ' Supprimer')));
  }

  // ============================================================
  // FLOATING TOOLBAR (appears above selected block)
  // ============================================================
  function FloatingToolbar({ block, onDuplicate, onDelete, onCopy, dispatch, state }) {
    if (!block) return null;
    const cfg = block.block_config || {};
    const setStyle = (key, val) => {
      dispatch({ type: 'SET_BLOCKS', blocks: state.blocks.map(b => b._uid === block._uid ? { ...b, block_config: { ...b.block_config, [key]: val } } : b) });
    };
    const tb = (icon, title, onClick, isActive) => h('button', {
      type: 'button', className: 'rs-ftb-btn' + (isActive ? ' active' : ''), title, onClick: e => { e.stopPropagation(); onClick(); },
    }, h('i', { className: 'bi ' + icon }));

    return h('div', { className: 'rs-floating-toolbar', onClick: e => e.stopPropagation() },
      tb('bi-files', 'Dupliquer (Ctrl+D)', onDuplicate),
      tb('bi-clipboard', 'Copier (Ctrl+C)', onCopy),
      tb('bi-trash', 'Supprimer (Suppr)', onDelete),
      h('span', { className: 'rs-ftb-sep' }),
      tb('bi-type-bold', 'Gras', () => setStyle('_style_font_weight', cfg._style_font_weight === '700' ? '' : '700'), cfg._style_font_weight === '700'),
      tb('bi-type-italic', 'Italique', () => setStyle('_style_font_italic', !cfg._style_font_italic), !!cfg._style_font_italic),
      tb('bi-type-underline', 'Souligné', () => setStyle('_style_text_decoration', !cfg._style_text_decoration ? 'underline' : ''), !!cfg._style_text_decoration),
      h('span', { className: 'rs-ftb-sep' }),
      tb('bi-text-left', 'Gauche', () => setStyle('_style_text_align', 'left'), cfg._style_text_align === 'left'),
      tb('bi-text-center', 'Centre', () => setStyle('_style_text_align', 'center'), cfg._style_text_align === 'center'),
      tb('bi-text-right', 'Droite', () => setStyle('_style_text_align', 'right'), cfg._style_text_align === 'right'),
      h('span', { className: 'rs-ftb-sep' }),
      h('label', { className: 'rs-ftb-color', title: 'Couleur texte' },
        h('i', { className: 'bi bi-palette-fill' }),
        h('input', { type: 'color', value: (cfg._style_color && cfg._style_color.startsWith('#')) ? cfg._style_color : '#102A43',
          onChange: e => setStyle('_style_color', e.target.value), onClick: e => e.stopPropagation() })));
  }

  // ============================================================
  // OBJECT EXPLORER (tree of all blocks)
  // ============================================================
  function ObjectExplorer({ blocks, selectedIds, onSelect, onHover }) {
    if (blocks.length === 0) {
      return h('div', { className: 'rs-explorer-empty' },
        h('i', { className: 'bi bi-diagram-3' }),
        h('p', null, 'Aucun bloc dans le rapport'));
    }
    return h('div', { className: 'rs-explorer-list' },
      h('div', { className: 'rs-explorer-group' },
        h('div', { className: 'rs-explorer-group-header' },
          h('i', { className: 'bi bi-file-earmark-text' }),
          h('span', null, 'Report'),
          h('span', { className: 'rs-explorer-count' }, blocks.length)),
        h('div', { className: 'rs-explorer-items' },
          blocks.map((block, i) => {
            const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
            const isSelected = selectedIds.includes(block._uid);
            return h('div', {
              key: block._uid,
              className: 'rs-explorer-item' + (isSelected ? ' selected' : '') + (!block.is_enabled ? ' disabled' : ''),
              onClick: (e) => { e.stopPropagation(); onSelect(block._uid, e.shiftKey, e.ctrlKey || e.metaKey); },
              onMouseEnter: () => onHover(block._uid),
              onMouseLeave: () => onHover(null),
            },
              h('i', { className: 'bi ' + meta.icon }),
              h('span', { className: 'rs-explorer-label' }, esc(block.title || meta.label)),
              h('span', { className: 'rs-explorer-badge' }, (block.column_span || 12) + '/12'),
              !block.is_enabled && h('i', { className: 'bi bi-eye-slash rs-explorer-disabled-icon' }));
          }))));
  }

  // ============================================================
  // BLOCK CARD ON CANVAS
  // ============================================================
  function BlockCard({ block, isSelected, isHovered, onSelect, onMoveUp, onMoveDown, onResize, onDragStart, onDragEnd, onDragOver, onDrop, isDragOver, index, onHover, onEdit }) {
    const meta = BLOCK_META[block.block_key] || { label: block.block_key, icon: 'bi-box' };
    const resizeStart = (e) => {
      e.preventDefault(); e.stopPropagation();
      const column = e.currentTarget.closest('.rs-block-col');
      const row = column?.parentElement;
      if (!column || !row) return;
      const startX = e.clientX;
      const startSpan = block.column_span || 12;
      const unit = row.getBoundingClientRect().width / 12;
      const move = (event) => onResize(index, Math.round(startSpan + (event.clientX - startX) / unit));
      const stop = () => { document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', stop); };
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
      onMouseEnter: () => onHover(block._uid),
      onMouseLeave: () => onHover(null),
    },
      h('div', {
        className: 'rs-block' + (isSelected ? ' rs-selected' : '') + (isHovered && !isSelected ? ' rs-hovered' : '') + (!block.is_enabled ? ' rs-block-disabled' : ''),
        onClick: e => { e.stopPropagation(); onSelect(block._uid, e.shiftKey, e.ctrlKey || e.metaKey); },
        onDoubleClick: e => { e.stopPropagation(); onEdit(block); },
      },
        isSelected && h('div', { className: 'rs-block-selection-outline' }),
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
        isSelected && h('div', { className: 'rs-resize-handle', title: 'Glisser pour redimensionner', onPointerDown: resizeStart },
          h('i', { className: 'bi bi-arrows-angle-right' }))));
  }

  // ============================================================
  // PALETTE (block library)
  // ============================================================
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
        h('div', { key: category, className: 'rs-palette-group rs-cat-' + category + (collapsed[category] ? ' collapsed' : '') },
          h('div', { className: 'rs-palette-cat', onClick: () => setCollapsed(c => ({ ...c, [category]: !c[category] })) },
            h('i', { className: 'bi bi-chevron-down rs-toggle' }),
            esc(CATEGORY_LABELS[category] || category),
            h('span', { className: 'rs-count' }, items.length)),
          h('div', { className: 'rs-palette-items' },
            items.map(block =>
              h('div', {
                key: block.block_key,
                className: 'rs-palette-item rs-cat-' + (BLOCK_CATEGORY[block.block_key] || category),
                draggable: true,
                onDragStart: (e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('text/block-key', block.block_key); },
                onClick: () => onAdd(block.block_key),
              },
                h('i', { className: 'bi ' + block.icon }),
                h('span', null, esc(block.label))))))));
  }

  // ============================================================
  // SETTINGS BAR
  // ============================================================
  function SettingsBar({ settings, onUpdate }) {
    const set = (key, val) => onUpdate({ ...settings, [key]: val });
    return h('div', { className: 'rs-settings-bar d-flex align-items-center gap-2 px-3 py-1' },
      h('span', { className: 'small text-muted fw-bold' }, h('i', { className: 'bi bi-gear' }), ' Paramètres:'),
      h('select', { className: 'form-select form-select-sm rs-setting', style: { width: 'auto' }, value: settings.orientation, onChange: e => set('orientation', e.target.value) },
        h('option', { value: 'portrait' }, 'A4 Portrait'), h('option', { value: 'landscape' }, 'A4 Paysage')),
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

  // ============================================================
  // ALIGNMENT TOOLBAR (for multi-select)
  // ============================================================
  function AlignmentToolbar({ selectedBlocks, onAlign, onDistribute }) {
    if (selectedBlocks.length < 2) return null;
    const ab = (icon, title, action) => h('button', {
      type: 'button', className: 'btn btn-sm btn-outline-light', title, onClick: e => { e.stopPropagation(); action(); },
    }, h('i', { className: 'bi ' + icon }));

    return h('div', { className: 'rs-align-toolbar d-flex align-items-center gap-1 px-2 py-1' },
      h('span', { className: 'small text-white-50 me-1' }, 'Aligner:'),
      ab('bi-align-start', 'Aligner à gauche', () => onAlign('left')),
      ab('bi-align-center', 'Centrer horizontalement', () => onAlign('center')),
      ab('bi-align-end', 'Aligner à droite', () => onAlign('right')),
      h('span', { className: 'text-white-50' }, '|'),
      ab('bi-distribute-horizontal', 'Distribuer H', () => onDistribute('horizontal')),
      ab('bi-distribute-vertical', 'Distribuer V', () => onDistribute('vertical')),
      h('span', { className: 'badge bg-light text-dark ms-2' }, selectedBlocks.length + ' sélectionné(s)'));
  }

  // ============================================================
  // RULER COMPONENT
  // ============================================================
  function Ruler({ orientation, zoom, scroll }) {
    const ticks = [];
    const step = zoom < 50 ? 100 : zoom < 100 ? 50 : 20;
    const totalSize = orientation === 'h' ? 2000 : 3000;
    for (let i = 0; i < totalSize; i += step) {
      const pos = (i * zoom) / 100 - (scroll || 0);
      if (pos < 0 || pos > 5000) continue;
      ticks.push(h('div', { key: i, className: 'rs-ruler-tick', style: orientation === 'h' ? { left: pos + 'px' } : { top: pos + 'px' } },
        h('span', { className: 'rs-ruler-label' }, i)));
    }
    return h('div', { className: 'rs-ruler rs-ruler-' + orientation }, ticks);
  }

  // ============================================================
  // MAIN BUILDER APP
  // ============================================================
  function BuilderApp() {
    const templateData = window.RS_TEMPLATE_DATA || { template: {}, blocks: [], settings: {} };
    const palette = window.RS_PALETTE || {};
    const templateId = window.RS_TEMPLATE_ID || 0;

    const [state, dispatch] = useReducer(reducer, {
      ...initialState,
      blocks: (templateData.blocks || []).map(b => ({
        ...b,
        _uid: b._uid || uid(),
        block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : (b.block_config || {}),
        column_span: b.column_span || 12,
      })),
      settings: templateData.settings || { orientation: 'portrait' },
    });

    const blocksRef = useRef(state.blocks);
    blocksRef.current = state.blocks;
    const stateRef = useRef(state);
    stateRef.current = state;

    const selectedBlock = state.blocks.find(b => b._uid === state.selectedIds[0]) || null;
    const selectedBlocks = state.blocks.filter(b => state.selectedIds.includes(b._uid));

    // ---- Block operations ----
    const pushUndo = useCallback(() => {
      dispatch({ type: 'PUSH_UNDO', snapshot: JSON.stringify(blocksRef.current) });
    }, []);

    const updateBlocks = useCallback((newBlocks, recordUndo = true) => {
      if (recordUndo) pushUndo();
      dispatch({ type: 'SET_BLOCKS', blocks: newBlocks });
    }, [pushUndo]);

    const addBlock = useCallback((blockKey) => {
      const newBlock = { _uid: uid(), block_key: blockKey, title: BLOCK_META[blockKey]?.label || blockKey, block_config: JSON.parse(JSON.stringify(DEFAULT_CONFIGS[blockKey] || {})), sort_order: blocksRef.current.length, is_enabled: true, visibility: 'web_pdf', column_span: 12 };
      updateBlocks([...blocksRef.current, newBlock]);
      dispatch({ type: 'SELECT', ids: [newBlock._uid] });
    }, [updateBlocks]);

    const updateBlock = useCallback((updatedOrFn) => {
      pushUndo();
      const uid = stateRef.current.selectedIds[0];
      dispatch({ type: 'SET_BLOCKS', blocks: blocksRef.current.map(b => {
        if (b._uid !== uid) return b;
        return typeof updatedOrFn === 'function' ? updatedOrFn(b) : updatedOrFn;
      }) });
    }, [pushUndo]);

    const deleteBlocks = useCallback((uids) => {
      updateBlocks(blocksRef.current.filter(b => !uids.includes(b._uid)));
      const remaining = stateRef.current.selectedIds.filter(id => !uids.includes(id));
      dispatch({ type: 'SELECT', ids: remaining });
    }, [updateBlocks]);

    const duplicateBlock = useCallback((block) => {
      const copy = { ...JSON.parse(JSON.stringify(block)), _uid: uid(), title: (block.title || '') + ' (copie)' };
      const idx = blocksRef.current.findIndex(b => b._uid === block._uid);
      const newBlocks = [...blocksRef.current];
      newBlocks.splice(idx + 1, 0, copy);
      updateBlocks(newBlocks);
      dispatch({ type: 'SELECT', ids: [copy._uid] });
    }, [updateBlocks]);

    const copyBlocks = useCallback((blocksToCopy) => {
      dispatch({ type: 'COPY', blocks: blocksToCopy.map(b => ({ ...JSON.parse(JSON.stringify(b)), _uid: uid() })) });
    }, []);

    const pasteBlocks = useCallback(() => {
      if (!stateRef.current.clipboard) return;
      const newBlocks = [...blocksRef.current];
      stateRef.current.clipboard.forEach(b => {
        const copy = { ...JSON.parse(JSON.stringify(b)), _uid: uid(), title: (b.title || '') + ' (copie)' };
        newBlocks.push(copy);
      });
      updateBlocks(newBlocks);
      dispatch({ type: 'SELECT', ids: stateRef.current.clipboard.map(b => b._uid) });
    }, [updateBlocks]);

    const toggleEnabled = useCallback((block) => {
      updateBlocks(blocksRef.current.map(b => b._uid === block._uid ? { ...b, is_enabled: !b.is_enabled } : b));
    }, [updateBlocks]);

    const moveBlock = useCallback((index, dir) => {
      const newBlocks = [...blocksRef.current];
      const target = index + dir;
      if (target < 0 || target >= newBlocks.length) return;
      [newBlocks[index], newBlocks[target]] = [newBlocks[target], newBlocks[index]];
      updateBlocks(newBlocks);
    }, [updateBlocks]);

    const resizeBlock = useCallback((index, newSpan) => {
      const span = clamp(newSpan, 1, 12);
      updateBlocks(blocksRef.current.map((b, i) => i === index ? { ...b, column_span: span } : b));
    }, [updateBlocks]);

    // ---- Selection ----
    const handleSelect = useCallback((uid, shiftKey, ctrlKey) => {
      if (shiftKey || ctrlKey) {
        dispatch({ type: 'SELECT_ADD', id: uid });
      } else {
        dispatch({ type: 'SELECT', ids: [uid] });
      }
    }, []);

    const handleDeselect = useCallback(() => {
      dispatch({ type: 'SELECT', ids: [] });
    }, []);

    // ---- Drag & Drop ----
    const handleDragStart = (e, block) => { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', block._uid); };
    const handleDragEnd = () => {};
    const handleDragOver = (e, index) => { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; };
    const handleDrop = useCallback((e, index) => {
      e.preventDefault(); e.stopPropagation();
      const blockKey = e.dataTransfer.getData('text/block-key');
      if (blockKey) {
        const newBlock = { _uid: uid(), block_key: blockKey, title: BLOCK_META[blockKey]?.label || blockKey, block_config: JSON.parse(JSON.stringify(DEFAULT_CONFIGS[blockKey] || {})), sort_order: index, is_enabled: true, visibility: 'web_pdf', column_span: 12 };
        const newBlocks = [...blocksRef.current]; newBlocks.splice(index, 0, newBlock);
        updateBlocks(newBlocks); dispatch({ type: 'SELECT', ids: [newBlock._uid] });
      } else {
        const draggedUid = e.dataTransfer.getData('text/plain');
        const fromIdx = blocksRef.current.findIndex(b => b._uid === draggedUid);
        if (fromIdx === -1 || fromIdx === index) return;
        const newBlocks = [...blocksRef.current]; const [moved] = newBlocks.splice(fromIdx, 1); newBlocks.splice(index, 0, moved);
        updateBlocks(newBlocks);
      }
    }, [updateBlocks]);

    const handleCanvasDrop = (e) => {
      e.preventDefault();
      const blockKey = e.dataTransfer.getData('text/block-key');
      if (blockKey) addBlock(blockKey);
    };

    // ---- Undo/Redo ----
    const undo = useCallback(() => {
      if (stateRef.current.undoStack.length === 0) return;
      const prev = JSON.parse(stateRef.current.undoStack[stateRef.current.undoStack.length - 1]);
      dispatch({ type: 'SET_UNDO_REDO', undo: stateRef.current.undoStack.slice(0, -1), redo: [...stateRef.current.redoStack, JSON.stringify(blocksRef.current)] });
      dispatch({ type: 'SET_BLOCKS', blocks: prev.map(b => ({ ...b, block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : b.block_config })) });
    }, []);

    const redo = useCallback(() => {
      if (stateRef.current.redoStack.length === 0) return;
      const next = JSON.parse(stateRef.current.redoStack[stateRef.current.redoStack.length - 1]);
      dispatch({ type: 'SET_UNDO_REDO', undo: [...stateRef.current.undoStack, JSON.stringify(blocksRef.current)], redo: stateRef.current.redoStack.slice(0, -1) });
      dispatch({ type: 'SET_BLOCKS', blocks: next.map(b => ({ ...b, block_config: typeof b.block_config === 'string' ? JSON.parse(b.block_config || '{}') : b.block_config })) });
    }, []);

    // ---- Save ----
    const save = async () => {
      dispatch({ type: 'SET_SAVING', saving: true });
      dispatch({ type: 'SET_STATUS', msg: 'Enregistrement...' });
      try {
        const payload = {
          blocks: state.blocks.map((b, i) => ({ block_key: b.block_key, title: b.title || null, block_config: b.block_config, sort_order: i, is_enabled: b.is_enabled !== false, visibility: b.visibility || 'web_pdf', column_span: b.column_span || 12 })),
          settings: state.settings,
        };
        const res = await fetch('/admin/reportstudio/builder/' + templateId, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.ok) {
          dispatch({ type: 'SET_DIRTY', dirty: false, msg: 'Enregistré à ' + new Date().toLocaleTimeString('fr-FR') });
          dispatch({ type: 'SET_TOAST', toast: { type: 'success', msg: 'Modifications enregistrées (' + data.count + ' blocs)' } });
        } else {
          dispatch({ type: 'SET_STATUS', msg: 'Erreur: ' + (data.error || 'inconnue') });
          dispatch({ type: 'SET_TOAST', toast: { type: 'danger', msg: 'Erreur: ' + (data.error || 'inconnue') } });
        }
      } catch (err) {
        dispatch({ type: 'SET_STATUS', msg: 'Erreur réseau' });
        dispatch({ type: 'SET_TOAST', toast: { type: 'danger', msg: 'Erreur réseau' } });
      }
      dispatch({ type: 'SET_SAVING', saving: false });
      setTimeout(() => dispatch({ type: 'SET_TOAST', toast: null }), 3000);
    };

    // ---- Alignment (multi-select) ----
    const alignBlocks = useCallback((type) => {
      if (selectedBlocks.length < 2) return;
      // For column-based layout, alignment means setting all selected blocks to same column_span
      if (type === 'left') updateBlocks(blocksRef.current.map(b => stateRef.current.selectedIds.includes(b._uid) ? { ...b, column_span: 12 } : b), false);
      else if (type === 'center') updateBlocks(blocksRef.current.map(b => stateRef.current.selectedIds.includes(b._uid) ? { ...b, column_span: 6 } : b), false);
      else if (type === 'right') updateBlocks(blocksRef.current.map(b => stateRef.current.selectedIds.includes(b._uid) ? { ...b, column_span: 12 } : b), false);
    }, [selectedBlocks, updateBlocks]);

    const distributeBlocks = useCallback((type) => {
      if (selectedBlocks.length < 3) return;
      // Even distribution of column spans
      const span = Math.floor(12 / selectedBlocks.length);
      updateBlocks(blocksRef.current.map(b => stateRef.current.selectedIds.includes(b._uid) ? { ...b, column_span: Math.max(1, span) } : b), false);
    }, [selectedBlocks, updateBlocks]);

    // ---- Keyboard shortcuts ----
    useEffect(() => {
      const handler = (e) => {
        const tag = e.target.tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target.isContentEditable) return;
        const ctrl = e.ctrlKey || e.metaKey;
        if (ctrl && e.key === 's') { e.preventDefault(); save(); }
        else if (ctrl && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); }
        else if (ctrl && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); }
        else if (ctrl && e.key === 'c') { e.preventDefault(); if (selectedBlocks.length) copyBlocks(selectedBlocks); }
        else if (ctrl && e.key === 'v') { e.preventDefault(); pasteBlocks(); }
        else if (ctrl && e.key === 'd') { e.preventDefault(); if (selectedBlock) duplicateBlock(selectedBlock); }
        else if (e.key === 'Escape') { e.preventDefault(); handleDeselect(); }
        else if (e.key === 'Delete' || e.key === 'Backspace') { e.preventDefault(); if (stateRef.current.selectedIds.length) deleteBlocks(stateRef.current.selectedIds); }
      };
      window.addEventListener('keydown', handler);
      return () => window.removeEventListener('keydown', handler);
    });

    useEffect(() => {
      const handler = (e) => { if (stateRef.current.dirty) { e.preventDefault(); e.returnValue = ''; } };
      window.addEventListener('beforeunload', handler);
      return () => window.removeEventListener('beforeunload', handler);
    });

    const orientationClass = state.settings.orientation === 'landscape' ? ' rs-landscape' : '';
    const zoomStyle = { transform: 'scale(' + (state.zoom / 100) + ')', transformOrigin: 'top center' };

    return h(React.Fragment, null,
      // ---- Top bar ----
      h('div', { className: 'rs-topbar' },
        h('div', { className: 'rs-topbar-left' },
          h('a', { href: window.RS_DASHBOARD_URL, className: 'btn btn-sm btn-outline-light' }, h('i', { className: 'bi bi-arrow-left' }), ' Dashboard'),
          h('span', { className: 'rs-topbar-sep' }, '|'),
          h('span', { className: 'rs-topbar-title' }, esc(templateData.template?.name || 'Template')),
          h('span', { className: 'badge bg-light text-dark ms-2' }, esc(templateData.template?.status || 'draft'))),
        h('div', { className: 'rs-topbar-right' },
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: undo, title: 'Annuler (Ctrl+Z)' }, h('i', { className: 'bi bi-arrow-counterclockwise' })),
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: redo, title: 'Refaire (Ctrl+Y)' }, h('i', { className: 'bi bi-arrow-clockwise' })),
          // Zoom controls
          h('div', { className: 'rs-zoom-controls' },
            h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: () => dispatch({ type: 'SET_ZOOM', zoom: state.zoom - 25 }), title: 'Zoom -' }, h('i', { className: 'bi bi-zoom-out' })),
            h('span', { className: 'rs-zoom-label' }, state.zoom + '%'),
            h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: () => dispatch({ type: 'SET_ZOOM', zoom: state.zoom + 25 }), title: 'Zoom +' }, h('i', { className: 'bi bi-zoom-in' })),
            h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: () => dispatch({ type: 'SET_ZOOM', zoom: 100 }), title: 'Zoom 100%' }, h('i', { className: 'bi bi-arrows-fullscreen' }))),
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: () => dispatch({ type: 'TOGGLE_GRID' }), title: 'Grille' }, h('i', { className: 'bi bi-grid-3x3' + (state.showGrid ? '-fill' : '') })),
          h('button', { type: 'button', className: 'btn btn-sm btn-outline-light', onClick: () => dispatch({ type: 'TOGGLE_SNAP' }), title: 'Aimantation grille' }, h('i', { className: 'bi bi-magnet' + (state.snapToGrid ? '-fill' : '') })),
          h('a', { href: window.RS_PREVIEW_URL, target: '_blank', className: 'btn btn-sm btn-light' }, h('i', { className: 'bi bi-eye' }), ' Aperçu'),
          h('button', { type: 'button', className: 'btn btn-sm btn-success', onClick: save, disabled: state.saving }, h('i', { className: 'bi bi-check-lg' }), state.saving ? ' ...' : ' Enregistrer'))),
      // ---- Settings bar ----
      h(SettingsBar, { settings: state.settings, onUpdate: (s) => dispatch({ type: 'SET_SETTINGS', settings: s }) }),
      // ---- Multi-select alignment toolbar ----
      selectedBlocks.length >= 2 && h(AlignmentToolbar, { selectedBlocks, onAlign: alignBlocks, onDistribute: distributeBlocks }),
      // ---- Main 4-pane layout ----
      h('div', { className: 'rs-panes' },
        // Left: Palette
        h('aside', { className: 'rs-pane rs-palette' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-blocks' }), h('span', null, 'Bibliothèque')),
          h(Palette, { palette, onAdd: addBlock })),
        // Center-left: Object Explorer
        h('aside', { className: 'rs-pane rs-explorer-pane' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-diagram-3' }), h('span', null, 'Explorateur')),
          h(ObjectExplorer, { blocks: state.blocks, selectedIds: state.selectedIds, onSelect: handleSelect, onHover: (id) => dispatch({ type: 'HOVER', id }) })),
        // Center: Canvas
        h('main', { className: 'rs-pane rs-canvas-pane', onClick: handleDeselect, onDragOver: e => e.preventDefault(), onDrop: handleCanvasDrop },
          h('div', { className: 'rs-pane-header rs-canvas-header' },
            h('i', { className: 'bi bi-file-earmark-text' }),
            h('span', null, 'Canvas'),
            h('span', { className: 'rs-canvas-count ms-auto badge bg-secondary' }, state.blocks.length + ' bloc(s)')),
          // Rulers
          h('div', { className: 'rs-rulers' },
            h(Ruler, { orientation: 'h', zoom: state.zoom }),
            h(Ruler, { orientation: 'v', zoom: state.zoom })),
          h('div', { className: 'rs-canvas-scroll' },
            h('div', { className: 'rs-canvas-wrap' + (state.showGrid ? ' rs-show-grid' : ''), style: zoomStyle, onClick: e => e.stopPropagation() },
              h('div', { className: 'rs-canvas' + orientationClass, onClick: e => e.stopPropagation() },
                state.blocks.length === 0
                  ? h('div', { className: 'rs-canvas-empty', onDragOver: e => e.preventDefault(), onDrop: handleCanvasDrop },
                      h('i', { className: 'bi bi-arrows-move' }),
                      h('p', null, 'Glissez un bloc ici ou cliquez dans la bibliothèque'))
                  : h('div', { className: 'row g-2' },
                      state.blocks.map((block, i) =>
                        h(BlockCard, {
                          key: block._uid, block,
                          isSelected: state.selectedIds.includes(block._uid),
                          isHovered: state.hoveredId === block._uid,
                          onSelect: handleSelect,
                          onMoveUp: moveBlock, onMoveDown: moveBlock,
                          onResize: resizeBlock,
                          onDragStart: handleDragStart, onDragEnd: handleDragEnd,
                          onDragOver: handleDragOver, onDrop: handleDrop,
                          isDragOver: false,
                          index: i,
                          onHover: (id) => dispatch({ type: 'HOVER', id }),
                          onEdit: (b) => dispatch({ type: 'SELECT', ids: [b._uid] }),
                        }))))))),
        // Right: Properties
        h('aside', { className: 'rs-pane rs-properties' },
          h('div', { className: 'rs-pane-header' }, h('i', { className: 'bi bi-sliders' }), h('span', null, 'Propriétés')),
          h('div', { className: 'rs-pane-body' },
            h(PropertyPanel, {
              block: selectedBlock, state, dispatch,
              onUpdate: updateBlock,
              onDelete: (block) => deleteBlocks([block._uid]),
              onDuplicate: duplicateBlock,
              onToggleEnabled: toggleEnabled,
            }))),
      ),
      // ---- Floating toolbar ----
      selectedBlock && h(FloatingToolbar, { block: selectedBlock, onDuplicate: () => duplicateBlock(selectedBlock), onDelete: () => deleteBlocks([selectedBlock._uid]), onCopy: () => copyBlocks([selectedBlock]), dispatch, state }),
      // ---- Status bar ----
      h('div', { className: 'rs-statusbar' },
        h('span', { id: 'rs-status-msg' }, state.statusMsg),
        h('span', { className: 'ms-auto small text-muted' }, state.dirty ? 'Non enregistré' : ''),
        h('span', { className: 'small text-muted ms-2' }, state.selectedIds.length + ' sélectionné(s)'),
        h('span', { className: 'small text-muted ms-2' }, 'Zoom: ' + state.zoom + '%')),
      // ---- Toast ----
      state.toast && h('div', { className: 'position-fixed bottom-0 end-0 p-3', style: { zIndex: 11 } },
        h('div', { className: 'toast align-items-center text-white bg-' + state.toast.type + ' border-0 show', role: 'alert' },
          h('div', { className: 'toast-body' }, state.toast.msg))));
  }

  const root = document.getElementById('rs-builder-root');
  if (root) {
    ReactDOM.createRoot(root).render(h(BuilderApp));
  }
})();