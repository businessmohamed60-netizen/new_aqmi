-- =============================================================
-- AQMI Report Studio — Migration SQL unique pour phpMyAdmin
-- Importez ce fichier directement dans phpMyAdmin (onglet "Importer")
-- Idempotent : peut être ré-exécuté sans erreur (utilise IF NOT EXISTS / ON DUPLICATE KEY)
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================
-- 1. CRÉATION DES TABLES
-- =============================================================

-- 1a. Themes
CREATE TABLE IF NOT EXISTS report_themes (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(100) NOT NULL,
  description       VARCHAR(255) NULL,
  primary_color     VARCHAR(20)  NOT NULL DEFAULT '#0d47a1',
  secondary_color   VARCHAR(20)  NOT NULL DEFAULT '#546e7a',
  accent_color      VARCHAR(20)  NOT NULL DEFAULT '#00897b',
  heading_color     VARCHAR(20)  NULL,
  body_color        VARCHAR(20)  NULL,
  background_color  VARCHAR(20)  NOT NULL DEFAULT '#ffffff',
  font_family       VARCHAR(100) NOT NULL DEFAULT 'Inter, Arial, sans-serif',
  css_variables     JSON         NULL,
  is_default        TINYINT(1)   NOT NULL DEFAULT 0,
  is_active         TINYINT(1)   NOT NULL DEFAULT 1,
  created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_report_themes_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1b. Block library
CREATE TABLE IF NOT EXISTS report_blocks (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  block_key       VARCHAR(50)  NOT NULL,
  name            VARCHAR(100) NOT NULL,
  category        VARCHAR(50)  NOT NULL,
  icon            VARCHAR(50)  NULL,
  description     VARCHAR(255) NULL,
  default_config  JSON         NULL,
  is_active       TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order      INT          NOT NULL DEFAULT 0,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_report_blocks_key (block_key),
  INDEX idx_report_blocks_category (category),
  INDEX idx_report_blocks_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1c. Templates
CREATE TABLE IF NOT EXISTS report_templates (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(150) NOT NULL,
  description  TEXT         NULL,
  theme_id     INT          NULL,
  category     VARCHAR(50)  NULL,
  status       ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  thumbnail    VARCHAR(255) NULL,
  settings     JSON         NULL,
  is_system    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_templates_theme FOREIGN KEY (theme_id)
      REFERENCES report_themes(id) ON DELETE SET NULL,
  INDEX idx_report_templates_status (status),
  INDEX idx_report_templates_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1d. Template blocks
CREATE TABLE IF NOT EXISTS report_template_blocks (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  template_id   INT          NOT NULL,
  block_id      INT          NULL,
  block_key     VARCHAR(50)  NOT NULL,
  title         VARCHAR(150) NULL,
  block_config  JSON         NULL,
  sort_order    INT          NOT NULL DEFAULT 0,
  is_enabled    TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tblocks_template FOREIGN KEY (template_id)
      REFERENCES report_templates(id) ON DELETE CASCADE,
  CONSTRAINT fk_tblocks_block FOREIGN KEY (block_id)
      REFERENCES report_blocks(id) ON DELETE SET NULL,
  INDEX idx_report_tblocks_template (template_id),
  INDEX idx_report_tblocks_sort (template_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 2. AJOUT DES COLONNES (migration 003 + 005 + visibilité)
--     Utilise des procédures stockées pour vérifier l'existence
-- =============================================================

-- Procédure utilitaire : ajoute une colonne si elle n'existe pas
DROP PROCEDURE IF EXISTS rs_add_column_if_missing;
DELIMITER //
CREATE PROCEDURE rs_add_column_if_missing(
  IN p_table  VARCHAR(100),
  IN p_column VARCHAR(100),
  IN p_def    VARCHAR(500)
)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_def);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END //
DELIMITER ;

-- Migration 003 : métadonnées du template
CALL rs_add_column_if_missing('report_templates', 'report_number_prefix', "VARCHAR(20) NULL DEFAULT 'AQMI-RPT-' AFTER category");
CALL rs_add_column_if_missing('report_templates', 'orientation',          "ENUM('portrait','landscape') NOT NULL DEFAULT 'portrait' AFTER report_number_prefix");
CALL rs_add_column_if_missing('report_templates', 'watermark_text',       'VARCHAR(100) NULL AFTER orientation');
CALL rs_add_column_if_missing('report_templates', 'watermark_opacity',     'DECIMAL(3,2) NOT NULL DEFAULT 0.08 AFTER watermark_text');
CALL rs_add_column_if_missing('report_templates', 'certification_date',   'DATE NULL AFTER watermark_opacity');
CALL rs_add_column_if_missing('report_templates', 'expiration_date',      'DATE NULL AFTER certification_date');

-- Migration 005 : column_span + row_id
CALL rs_add_column_if_missing('report_template_blocks', 'column_span', 'INT DEFAULT 12');
CALL rs_add_column_if_missing('report_template_blocks', 'row_id',      'INT DEFAULT 0');

-- Visibilité
CALL rs_add_column_if_missing('report_template_blocks', 'visibility', "ENUM('web_pdf','web_only','pdf_only') NOT NULL DEFAULT 'web_pdf' AFTER is_enabled");

-- Index pour report_number_prefix (si n'existe pas)
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'report_templates' AND INDEX_NAME = 'idx_report_templates_number');
SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE report_templates ADD INDEX idx_report_templates_number (report_number_prefix)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Mettre à jour column_span pour les lignes existantes
UPDATE report_template_blocks SET column_span = 12 WHERE column_span IS NULL;

-- Nettoyer la procédure utilitaire
DROP PROCEDURE IF EXISTS rs_add_column_if_missing;

-- =============================================================
-- 3. INSERTION DES DONNÉES (seed)
-- =============================================================

-- 3a. Thèmes (idempotent)
INSERT INTO report_themes
  (name, description, primary_color, secondary_color, accent_color, heading_color, body_color, background_color, font_family, is_default, is_active)
VALUES
  ('AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', 1, 1),
  ('Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', 0, 1),
  ('Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', 0, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3b. Blocks de base (idempotent)
INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('global_score',   'Global Score',        'metrics',    'bi-speedometer',     'Overall AQMI score with rating', JSON_OBJECT('score', 0, 'max', 100, 'show_rating', true), 1, 10),
  ('radar_chart',    'Radar Chart',         'charts',     'bi-graph-up',        'Multi-axis radar chart',          JSON_OBJECT('axes', JSON_ARRAY(), 'legend', true), 1, 20),
  ('gauge',          'Gauge',               'metrics',    'bi-dial',            'Single-value gauge indicator',    JSON_OBJECT('value', 0, 'min', 0, 'max', 100), 1, 30),
  ('recommendations','Recommendations',    'content',    'bi-list-check',      'List of recommendations',         JSON_OBJECT('items', JSON_ARRAY()), 1, 40),
  ('company_info',   'Company Information','content',    'bi-building',        'Company details block',           JSON_OBJECT('fields', JSON_ARRAY()), 1, 50),
  ('aqmi_logo',      'AQMI Logo',           'branding',   'bi-award',           'Official AQMI logo',              JSON_OBJECT('size', 'md', 'align', 'left'), 1, 60),
  ('company_logo',   'Company Logo',        'branding',   'bi-image',           'Client company logo',             JSON_OBJECT('size', 'md', 'align', 'left'), 1, 70),
  ('qr_code',        'QR Code',             'utility',    'bi-qr-code',         'Generated QR code',               JSON_OBJECT('value', '', 'size', 120), 1, 80),
  ('signature',      'Signature',           'utility',    'bi-pen',             'Signature line block',            JSON_OBJECT('label', '', 'role', ''), 1, 90),
  ('header',         'Header',              'structure',  'bi-text-left',       'Page header block',               JSON_OBJECT('text', '', 'level', 1), 1, 100),
  ('footer',         'Footer',              'structure',  'bi-text-right',      'Page footer block',               JSON_OBJECT('text', '', 'align', 'center'), 1, 110),
  ('rich_text',      'Rich Text',           'content',    'bi-fonts',           'Editable rich text content',      JSON_OBJECT('html', ''), 1, 120),
  ('image',          'Image',               'media',      'bi-card-image',      'Image block',                     JSON_OBJECT('url', '', 'alt', '', 'width', '100%'), 1, 130)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3c. Block official_stamp (migration 003)
INSERT INTO report_blocks (block_key, name, category, icon, description, default_config, is_active, sort_order) VALUES
  ('official_stamp', 'Official Stamp', 'branding', 'bi-patch-check-fill',
   'Official AQMI certification stamp/seal',
   JSON_OBJECT('style', 'circular', 'text', 'CERTIFIÉ', 'subtext', 'AQMI', 'color', '#0d47a1', 'size', 100, 'align', 'right'),
   1, 65)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3d. Blocks de graphiques (migration 006)
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

-- 3e. Mise à jour des configs par défaut (header / footer / signature)
UPDATE report_blocks SET default_config = JSON_OBJECT('text', '', 'align', 'left', 'show_page_number', false, 'show_report_number', false, 'show_date', false)
  WHERE block_key = 'header';
UPDATE report_blocks SET default_config = JSON_OBJECT('text', '', 'align', 'center', 'show_page_number', true, 'show_report_number', false, 'show_date', false)
  WHERE block_key = 'footer';
UPDATE report_blocks SET default_config = JSON_OBJECT('label', '', 'role', '', 'show_date', true, 'show_stamp', false)
  WHERE block_key = 'signature';

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- FIN — Toutes les tables Report Studio sont prêtes.
-- =============================================================
