-- Migration: Add column_span to report_template_blocks for grid layout support
-- Allows blocks to span multiple columns (1-12 grid system) in Report Studio

ALTER TABLE report_template_blocks
  ADD COLUMN column_span INT DEFAULT 12;

ALTER TABLE report_template_blocks
  ADD COLUMN row_id INT DEFAULT 0;

-- Update existing blocks to default full width
UPDATE report_template_blocks SET column_span = 12 WHERE column_span IS NULL;
