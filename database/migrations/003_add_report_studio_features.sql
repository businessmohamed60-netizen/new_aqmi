-- =============================================================
-- AQMI Report Studio — Feature completion migration
-- Adds: block visibility (web/pdf), official stamp block,
--        page settings columns (orientation, watermark, metadata)
-- =============================================================

-- 1. Add visibility column to template blocks -------------------
ALTER TABLE report_template_blocks
  ADD COLUMN visibility ENUM('web_pdf','web_only','pdf_only') NOT NULL DEFAULT 'web_pdf'
  AFTER is_enabled;

-- 2. Add report metadata + page settings to templates -----------
ALTER TABLE report_templates
  ADD COLUMN report_number_prefix  VARCHAR(20)  NULL DEFAULT 'AQMI-RPT-'  AFTER category,
  ADD COLUMN orientation           ENUM('portrait','landscape') NOT NULL DEFAULT 'portrait'  AFTER report_number_prefix,
  ADD COLUMN watermark_text        VARCHAR(100) NULL  AFTER orientation,
  ADD COLUMN watermark_opacity     DECIMAL(3,2) NOT NULL DEFAULT 0.08  AFTER watermark_text,
  ADD COLUMN certification_date    DATE         NULL  AFTER watermark_opacity,
  ADD COLUMN expiration_date       DATE         NULL  AFTER certification_date,
  ADD INDEX idx_report_templates_number (report_number_prefix);

-- 3. Seed the official stamp/seal block -------------------------
INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('official_stamp', 'Official Stamp', 'branding', 'bi-patch-check-fill',
   'Official AQMI certification stamp/seal',
   JSON_OBJECT('style', 'circular', 'text', 'CERTIFIÉ', 'subtext', 'AQMI', 'color', '#0d47a1', 'size', 100, 'align', 'right'),
   1, 65)
  ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 4. Enrich header/footer default configs with dynamic var support
UPDATE report_blocks SET default_config = JSON_OBJECT('text', '', 'align', 'left', 'show_page_number', false, 'show_report_number', false, 'show_date', false)
  WHERE block_key = 'header';
UPDATE report_blocks SET default_config = JSON_OBJECT('text', '', 'align', 'center', 'show_page_number', true, 'show_report_number', false, 'show_date', false)
  WHERE block_key = 'footer';

-- 5. Update signature default config to include stamp option -----
UPDATE report_blocks SET default_config = JSON_OBJECT('label', '', 'role', '', 'show_date', true, 'show_stamp', false)
  WHERE block_key = 'signature';
