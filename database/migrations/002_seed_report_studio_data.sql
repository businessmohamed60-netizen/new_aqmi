-- Report Studio: seed initial blocks, a default theme, and a starter template

-- Default theme
INSERT INTO `report_themes` (`name`, `css_variables`, `is_default`) VALUES
('AQMI Default', '{"--rs-primary":"#0d9488","--rs-heading":"#0b1f4d","--rs-body":"#102A43","--rs-font":"DejaVu Sans, sans-serif","--rs-background":"#ffffff","--rs-accent":"#9d8fd1"}', 1);

-- Built-in blocks (ordered by category)
INSERT INTO `report_blocks` (`block_key`, `block_name`, `category`, `icon`, `default_config`, `is_active`, `is_system`, `sort_order`) VALUES
('global_score',    'Global Score',        'metrics',   'bi-speedometer',          '{"score":0,"max":100,"show_rating":true}', 1, 1, 1),
('gauge',           'Gauge',               'metrics',   'bi-dial',                 '{"value":0,"min":0,"max":100,"label":""}', 1, 1, 2),
('kpi_card',        'KPI Card',            'metrics',   'bi-calendar2-check',      '{"label":"","value":"","icon":"bi-check-circle"}', 1, 1, 3),
('domain_scores',   'Domain Scores Table', 'metrics',   'bi-table',                '{"domains":[]}', 1, 1, 4),
('radar_chart',     'Radar Chart',         'charts',    'bi-graph-up',             '{"axes":[],"legend":true}', 1, 1, 10),
('recommendations', 'Recommendations',     'content',   'bi-list-check',           '{"items":[]}', 1, 1, 20),
('company_info',    'Company Information', 'content',   'bi-building',             '{"fields":[]}', 1, 1, 21),
('rich_text',       'Rich Text',           'content',   'bi-fonts',                '{"html":""}', 1, 1, 22),
('aqmi_logo',       'AQMI Logo',           'branding',  'bi-award',                '{"show_stamp":true}', 1, 1, 30),
('company_logo',    'Company Logo',        'branding',  'bi-image',                '{"src":"","width":120}', 1, 1, 31),
('official_stamp',  'Official Stamp',      'branding',  'bi-patch-check-fill',     '{"show_date":true,"show_number":true}', 1, 1, 32),
('qr_code',         'QR Code',             'utility',   'bi-qr-code',              '{"data":"","size":80}', 1, 1, 40),
('signature',       'Signature',           'utility',   'bi-pen',                  '{"label":"","name":""}', 1, 1, 41),
('header',          'Header',              'structure', 'bi-text-left',            '{"text":"","show_page_number":true}', 1, 1, 50),
('footer',          'Footer',              'structure', 'bi-text-right',           '{"text":"","show_page_number":true}', 1, 1, 51),
('cover_page',      'Cover Page',          'structure', 'bi-bookmark-star',        '{"title":"","subtitle":""}', 1, 1, 52),
('page_break',      'Page Break',          'structure', 'bi-file-earmark-break',   '{}', 1, 1, 53),
('image',           'Image',               'media',     'bi-card-image',           '{"src":"","width":"100%","alt":""}', 1, 1, 60),
('background',      'Background',          'media',     'bi-image-alt',            '{"src":"","opacity":0.1}', 1, 1, 61);

-- Starter template
INSERT INTO `report_templates` (`name`, `description`, `category`, `status`, `settings`, `is_system`) VALUES
('AQMI Standard Report', 'Default report template with cover page, global score, radar chart, domain scores, and recommendations.', 'aqmi', 'published', '{"orientation":"portrait","watermark_text":"","watermark_opacity":0.08,"report_number_prefix":"AQMI-RPT-"}', 1);

-- Starter template blocks
INSERT INTO `report_template_blocks` (`template_id`, `block_id`, `block_key`, `title`, `block_config`, `sort_order`, `is_enabled`) VALUES
(1, (SELECT id FROM report_blocks WHERE block_key='cover_page'),      'cover_page',      'Cover Page',      '{"title":"AQMI Assessment Report","subtitle":"Quality Maturity Evaluation"}', 0, 1),
(1, (SELECT id FROM report_blocks WHERE block_key='global_score'),    'global_score',    'Global Score',    '{"score":0,"max":100,"show_rating":true}', 1, 1),
(1, (SELECT id FROM report_blocks WHERE block_key='radar_chart'),     'radar_chart',     'Radar Chart',     '{"axes":[],"legend":true}', 2, 1),
(1, (SELECT id FROM report_blocks WHERE block_key='domain_scores'),   'domain_scores',   'Domain Scores',   '{"domains":[]}', 3, 1),
(1, (SELECT id FROM report_blocks WHERE block_key='recommendations'), 'recommendations', 'Recommendations', '{"items":[]}', 4, 1),
(1, (SELECT id FROM report_blocks WHERE block_key='official_stamp'),  'official_stamp',  'Official Stamp',  '{"show_date":true,"show_number":true}', 5, 1);
