-- Report Studio: add chart block types (bar, line, donut, area)

INSERT INTO `report_blocks` (`block_key`, `block_name`, `category`, `icon`, `default_config`, `is_active`, `is_system`, `sort_order`) VALUES
('bar_chart',   'Bar Chart',   'charts', 'bi-bar-chart',         '{"series":[],"horizontal":false,"legend":true,"stacked":false}', 1, 1, 11),
('line_chart',  'Line Chart',  'charts', 'bi-graph-up-arrow',    '{"series":[],"smooth":false,"legend":true,"show_markers":true,"fill_area":false}', 1, 1, 12),
('donut_chart', 'Donut Chart', 'charts', 'bi-pie-chart',         '{"series":[],"show_percent":true,"show_label":true}', 1, 1, 13),
('area_chart',  'Area Chart',  'charts', 'bi-graph-up-arrow',    '{"series":[],"smooth":false,"legend":true,"fill_area":true,"show_markers":false}', 1, 1, 14)
ON DUPLICATE KEY UPDATE `block_name` = VALUES(`block_name`);
