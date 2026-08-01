/**
 * Preview — orientation switching, page numbering, chart resize for print.
 */
(function () {
  const PREVIEW = window.RS_PREVIEW || {};
  const orientSelect = document.getElementById('rs-preview-orient');
  const container = document.querySelector('.rs-report-container');

  // Orientation toggle
  if (orientSelect && container) {
    orientSelect.addEventListener('change', function () {
      const isLandscape = this.value === 'landscape';
      container.classList.toggle('rs-landscape', isLandscape);
      container.classList.toggle('rs-portrait', !isLandscape);
      document.body.classList.toggle('landscape', isLandscape);
    });
  }

  // Page numbering — simple approximation for web preview using block count
  function updatePageNumbers() {
    const blocks = container?.querySelectorAll('.rs-report-block');
    if (!blocks) return;
    // In web preview, show "1 / 1" as a simple indicator.
    // The CSS @page counter handles real pagination in print/PDF.
    const pageNums = container.querySelectorAll('.rs-dynamic-page');
    pageNums.forEach(el => {
      if (el.textContent.indexOf('{page}') !== -1 || el.textContent.indexOf('{total_pages}') !== -1) {
        el.textContent = el.textContent
          .replace('{page}', '1')
          .replace('{total_pages}', '1');
      }
    });
  }

  // Resolve remaining dynamic vars in header/footer that weren't server-side
  function resolveVars() {
    const replacements = {
      '{report_number}': PREVIEW.reportNumber || '',
      '{certification_date}': PREVIEW.certDate || '—',
      '{expiration_date}': PREVIEW.expDate || '—',
      '{current_date}': new Date().toLocaleDateString('fr-FR'),
      '{template_name}': PREVIEW.templateName || '',
    };
    container?.querySelectorAll('.rs-report-block').forEach(block => {
      const key = block.dataset.blockKey;
      if (key !== 'header' && key !== 'footer') return;
      block.querySelectorAll('*').forEach(el => {
        if (el.children.length === 0 && el.textContent) {
          let txt = el.textContent;
          for (const [v, val] of Object.entries(replacements)) {
            txt = txt.split(v).join(val);
          }
          el.textContent = txt;
        }
      });
    });
  }

  // Resize charts before printing so they render correctly in PDF
  window.addEventListener('beforeprint', function () {
    if (window.ApexCharts) {
      document.querySelectorAll('[id^="rs-radar-"], [id^="rs-gauge-"]').forEach(function(el) {
        if (el.__apexChart) {
          try { el.__apexChart.resize(); } catch (e) {}
        }
      });
    }
  });

  // Init
  updatePageNumbers();
  resolveVars();
})();
