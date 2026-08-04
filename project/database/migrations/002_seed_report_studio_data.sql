-- =============================================================
-- AQMI Report Studio — Seed data
-- Inserts the built-in block library and one default theme.
-- =============================================================

-- Default theme ------------------------------------------------
INSERT INTO report_themes
  (name, description, primary_color, secondary_color, accent_color, heading_color, body_color, background_color, font_family, is_default, is_active)
VALUES
  ('AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', 1, 1),
  ('Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', 0, 1),
  ('Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', 0, 1);

-- Block library ------------------------------------------------
INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('global_score',  'Global Score',        'metrics',    'bi-speedometer',     'Overall AQMI score with rating', JSON_OBJECT('score', 0, 'max', 100, 'show_rating', true), 1, 10),
  ('radar_chart',   'Radar Chart',         'charts',     'bi-graph-up',        'Multi-axis radar chart',          JSON_OBJECT('axes', JSON_ARRAY(), 'legend', true), 1, 20),
  ('gauge',         'Gauge',               'metrics',    'bi-dial',            'Single-value gauge indicator',    JSON_OBJECT('value', 0, 'min', 0, 'max', 100), 1, 30),
  ('recommendations','Recommendations',    'content',    'bi-list-check',      'List of recommendations',         JSON_OBJECT('items', JSON_ARRAY()), 1, 40),
  ('company_info',  'Company Information', 'content',    'bi-building',        'Company details block',           JSON_OBJECT('fields', JSON_ARRAY()), 1, 50),
  ('aqmi_logo',     'AQMI Logo',           'branding',   'bi-award',           'Official AQMI logo',              JSON_OBJECT('size', 'md', 'align', 'left'), 1, 60),
  ('company_logo',  'Company Logo',        'branding',   'bi-image',           'Client company logo',             JSON_OBJECT('size', 'md', 'align', 'left'), 1, 70),
  ('qr_code',       'QR Code',             'utility',    'bi-qr-code',         'Generated QR code',               JSON_OBJECT('value', '', 'size', 120), 1, 80),
  ('signature',     'Signature',           'utility',    'bi-pen',             'Signature line block',            JSON_OBJECT('label', '', 'role', ''), 1, 90),
  ('header',        'Header',              'structure',  'bi-text-left',       'Page header block',               JSON_OBJECT('text', '', 'level', 1), 1, 100),
  ('footer',        'Footer',              'structure',  'bi-text-right',      'Page footer block',               JSON_OBJECT('text', '', 'align', 'center'), 1, 110),
  ('rich_text',     'Rich Text',           'content',    'bi-fonts',           'Editable rich text content',      JSON_OBJECT('html', ''), 1, 120),
  ('image',         'Image',               'media',      'bi-card-image',      'Image block',                     JSON_OBJECT('url', '', 'alt', '', 'width', '100%'), 1, 130);
