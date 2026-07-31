/**
 * Block renderers — produce lightweight live previews for each block type
 * inside the builder canvas. These are intentionally simpler than the
 * final report partials; they give instant visual feedback while editing.
 */
window.RS_Renderers = (function () {
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  const renderers = {
    global_score(cfg) {
      const score = +cfg.score || 0, max = +cfg.max || 100;
      const pct = max > 0 ? Math.min(100, Math.round((score / max) * 100)) : 0;
      const label = esc(cfg.label || 'Score global');
      return `<div class="text-center py-2"><div class="rs-score-ring" style="--rs-pct:${pct}"><div class="rs-score-ring-inner"><span class="rs-score-value">${score}<small>/${max}</small></span></div></div><h6 class="mt-1 mb-0">${label}</h6></div>`;
    },

    radar_chart(cfg) {
      const axes = cfg.axes || [];
      const labels = axes.map(a => esc(a.label || '')).join(', ');
      return `<div class="py-2"><i class="bi bi-graph-up fs-2 text-muted"></i><p class="small text-muted mb-0">Radar — ${axes.length} axe(s): ${labels}</p></div>`;
    },

    gauge(cfg) {
      const v = +cfg.value || 0, max = +cfg.max || 100;
      const pct = max > 0 ? Math.min(100, Math.round((v / max) * 100)) : 0;
      return `<div class="text-center py-2"><div class="rs-score-ring" style="--rs-pct:${pct}"><div class="rs-score-ring-inner"><span class="rs-score-value">${v}<small>${esc(cfg.unit||'%')}</small></span></div></div><small class="text-muted">${esc(cfg.label||'Indicateur')}</small></div>`;
    },

    recommendations(cfg) {
      const items = cfg.items || [];
      const list = items.slice(0, 3).map(i => `<li class="small">${esc((typeof i === 'object' ? i.text : i) || '')}</li>`).join('');
      const more = items.length > 3 ? `<li class="small text-muted">… +${items.length - 3} autre(s)</li>` : '';
      return `<div class="py-1"><ol class="rs-reco-list mb-0">${list}${more}</ol></div>`;
    },

    company_info(cfg) {
      const f = cfg.fields || [];
      const rows = f.slice(0, 4).map(x => `<dt class="col-5 text-muted small">${esc(x.label||'')}</dt><dd class="col-7 small">${esc(x.key||'')}</dd>`).join('');
      return `<div class="py-1"><dl class="row mb-0 small">${rows}</dl></div>`;
    },

    aqmi_logo(cfg) {
      const sizes = {sm:'1.1rem',md:'1.6rem',lg:'2.4rem'};
      return `<div class="text-${esc(cfg.align||'left')} py-1"><span class="rs-aqmi-mark" style="font-size:${sizes[cfg.size||'md']}">AQMI</span></div>`;
    },

    company_logo(cfg) {
      if (cfg.url) return `<div class="text-${esc(cfg.align||'left')} py-1"><img src="${esc(cfg.url)}" style="max-height:80px"></div>`;
      return `<div class="text-${esc(cfg.align||'left')} py-1"><i class="bi bi-image fs-2 text-muted"></i></div>`;
    },

    official_stamp(cfg) {
      const style = cfg.style || 'circular';
      const text = esc(cfg.text || 'CERTIFIÉ');
      const sub = esc(cfg.subtext || 'AQMI');
      const color = esc(cfg.color || '#0d47a1');
      const size = +cfg.size || 100;
      if (style === 'badge') {
        return `<div class="text-${esc(cfg.align||'right')} py-2"><div class="rs-stamp rs-stamp-badge" style="color:${color}"><i class="bi bi-patch-check-fill" style="font-size:${Math.round(size*0.5)}px"></i><span class="rs-stamp-text small d-block mt-1">${text}</span></div></div>`;
      }
      const borderCls = style === 'rectangular' ? 'rs-stamp-rectangular' : 'rs-stamp-circular';
      return `<div class="text-${esc(cfg.align||'right')} py-2"><div class="rs-stamp ${borderCls}" style="width:${size}px;height:${style==='rectangular'?'auto':size+'px'};border-color:${color};color:${color}"><div class="rs-stamp-inner"><span class="rs-stamp-text">${text}</span><span class="rs-stamp-subtext">${sub}</span></div></div></div>`;
    },

    qr_code(cfg) {
      const size = +cfg.size || 120;
      if (cfg.value) return `<div class="text-center py-1"><img src="https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(cfg.value)}" width="${size}" height="${size}"></div>`;
      return `<div class="text-center py-1"><i class="bi bi-qr-code fs-1 text-muted"></i></div>`;
    },

    signature(cfg) {
      const stamp = cfg.show_stamp ? ' <i class="bi bi-patch-check-fill text-primary ms-1"></i>' : '';
      return `<div class="py-2"><div class="rs-sig-line"></div><p class="mb-0 small fw-semibold">${esc(cfg.label||'')}${stamp}</p><p class="text-muted small mb-0">${esc(cfg.role||'')}</p></div>`;
    },

    header(cfg) {
      const lvl = Math.max(1, Math.min(3, +cfg.level || 1));
      const meta = (cfg.show_report_number || cfg.show_page_number || cfg.show_date) ? `<div class="rs-header-meta small text-muted d-flex gap-3 mt-1">${cfg.show_report_number?'<span>N° ...</span>':''}${cfg.show_date?'<span>'+new Date().toLocaleDateString('fr-FR')+'</span>':''}${cfg.show_page_number?'<span>Page {page}/{total_pages}</span>':''}</div>` : '';
      return `<div class="py-1 text-${esc(cfg.align||'left')}"><h${lvl} class="rs-header-title mb-0">${esc(cfg.text||'Titre')}</h${lvl}>${meta}</div>`;
    },

    footer(cfg) {
      const parts = [];
      if (cfg.text) parts.push(esc(cfg.text));
      if (cfg.show_report_number) parts.push('N° ...');
      if (cfg.show_date) parts.push(new Date().toLocaleDateString('fr-FR'));
      if (cfg.show_page_number) parts.push('Page {page}/{total_pages}');
      return `<div class="py-1 text-${esc(cfg.align||'center')} border-top"><small class="text-muted">${parts.join(' · ') || 'Pied de page'}</small></div>`;
    },

    rich_text(cfg) {
      return `<div class="py-1 rs-richtext-content">${cfg.html || '<em class="text-muted small">Texte riche...</em>'}</div>`;
    },

    image(cfg) {
      if (cfg.url) return `<div class="text-${esc(cfg.align||'center')} py-1"><img src="${esc(cfg.url)}" alt="${esc(cfg.alt||'')}" style="width:${esc(cfg.width||'100%')}"></div>`;
      return `<div class="text-${esc(cfg.align||'center')} py-1"><i class="bi bi-card-image fs-1 text-muted"></i></div>`;
    },
  };

  return {
    render(key, cfg) {
      const fn = renderers[key];
      return fn ? fn(cfg || {}) : `<div class="text-muted small">Aperçu indisponible pour « ${esc(key)} »</div>`;
    },
  };
})();
