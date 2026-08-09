-- =============================================================
-- AQMI Report Studio — Add new chart block types
-- Adds: bar_chart, line_chart, donut_chart, area_chart
-- =============================================================

INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('bar_chart',   'Bar Chart',   'charts', 'bi-bar-chart',
   'Vertical or horizontal bar chart',
   JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'A', 'value', 0)))), 'horizontal', false, 'legend', true),
   1, 25)
  ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('line_chart',  'Line Chart',  'charts', 'bi-graph-up-arrow',
   'Trend line chart with multiple series',
   JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'Jan', 'value', 0)))), 'legend', true, 'smooth', true),
   1, 26)
  ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('donut_chart', 'Donut Chart', 'charts', 'bi-pie-chart',
   'Donut/pie chart for proportional data',
   JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'A', 'value', 1)), 'legend', true),
   1, 27)
  ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('area_chart',  'Area Chart',  'charts', 'bi-graph-up-arrow',
   'Stacked area chart for trends',
   JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'Jan', 'value', 0)))), 'legend', true, 'smooth', true),
   1, 28)
  ON DUPLICATE KEY UPDATE name = VALUES(name);
