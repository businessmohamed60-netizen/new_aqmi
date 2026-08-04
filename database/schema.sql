-- AQMI Database Schema
-- Automotive Quality Maturity Index

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if exist
DROP TABLE IF EXISTS `model_domains`;
DROP TABLE IF EXISTS `evaluation_models`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `login_logs`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `recommendations`;
DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `lead_custom_fields`;
DROP TABLE IF EXISTS `assessment_answers`;
DROP TABLE IF EXISTS `score_levels`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `assessments`;
DROP TABLE IF EXISTS `domains`;
DROP TABLE IF EXISTS `role_permission`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `settings`;

-- Roles
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions
CREATE TABLE `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role-Permission pivot
CREATE TABLE `role_permission` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT UNSIGNED NOT NULL,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Domains
CREATE TABLE `domains` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `name_fr` VARCHAR(100) DEFAULT NULL,
    `name_ar` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `description_fr` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-folder',
    `weight` DECIMAL(5,2) DEFAULT 1.00,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Evaluation Models
CREATE TABLE `evaluation_models` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `name_fr` VARCHAR(255) DEFAULT '',
    `name_ar` VARCHAR(255) DEFAULT '',
    `description` TEXT DEFAULT NULL,
    `description_fr` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-clipboard-check',
    `color` VARCHAR(20) DEFAULT '#7367f0',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Model-Domain pivot
CREATE TABLE `model_domains` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `model_id` INT UNSIGNED NOT NULL,
    `domain_id` INT UNSIGNED NOT NULL,
    `sort_order` INT DEFAULT 0,
    FOREIGN KEY (`model_id`) REFERENCES `evaluation_models`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lead Custom Fields
CREATE TABLE `lead_custom_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `label` VARCHAR(255) NOT NULL,
    `field_key` VARCHAR(100) NOT NULL,
    `field_type` VARCHAR(50) DEFAULT 'text',
    `is_required` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questions
CREATE TABLE `questions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `domain_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) DEFAULT NULL,
    `title_fr` VARCHAR(255) DEFAULT NULL,
    `title_ar` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `description_fr` TEXT DEFAULT NULL,
    `description_ar` TEXT DEFAULT NULL,
    `weight` DECIMAL(5,2) DEFAULT 1.00,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessments
CREATE TABLE `assessments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `session_id` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('in_progress','completed') DEFAULT 'in_progress',
    `current_step` INT DEFAULT 0,
    `total_score` DECIMAL(10,2) DEFAULT NULL,
    `maturity_level` VARCHAR(50) DEFAULT NULL,
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment Answers
CREATE TABLE `assessment_answers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `assessment_id` INT UNSIGNED NOT NULL,
    `question_id` INT UNSIGNED NOT NULL,
    `score` INT DEFAULT NULL CHECK (`score` >= 0 AND `score` <= 5),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uq_assessment_question` (`assessment_id`, `question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Score Levels
CREATE TABLE `score_levels` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `name_fr` VARCHAR(100) DEFAULT NULL,
    `name_ar` VARCHAR(100) DEFAULT NULL,
    `min_percent` DECIMAL(5,2) NOT NULL,
    `max_percent` DECIMAL(5,2) NOT NULL,
    `color` VARCHAR(20) DEFAULT '#6c757d',
    `icon` VARCHAR(50) DEFAULT 'fa-chart-bar',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads
CREATE TABLE `leads` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `assessment_id` INT UNSIGNED DEFAULT NULL,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `company` VARCHAR(255) NOT NULL,
    `sector` VARCHAR(100) DEFAULT NULL,
    `job_title` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `company_size` VARCHAR(20) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `certifications` VARCHAR(255) DEFAULT NULL,
    `founded_year` VARCHAR(4) DEFAULT NULL,
    `production_type` VARCHAR(20) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `consent_contact` TINYINT(1) NOT NULL DEFAULT 0,
    `consent_share_industry` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Recommendations
CREATE TABLE `recommendations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `domain_id` INT UNSIGNED DEFAULT NULL,
    `condition_field` VARCHAR(100) DEFAULT NULL,
    `condition_operator` ENUM('<','>','<=','>=','==') DEFAULT '<',
    `condition_value` DECIMAL(5,2) DEFAULT NULL,
    `recommendation_text` TEXT DEFAULT NULL,
    `recommendation_text_fr` TEXT DEFAULT NULL,
    `recommendation_text_ar` TEXT DEFAULT NULL,
    `priority` ENUM('low','medium','high','critical') DEFAULT 'medium',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reports
CREATE TABLE `reports` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `report_number` VARCHAR(50) DEFAULT NULL UNIQUE,
    `assessment_id` INT UNSIGNED DEFAULT NULL,
    `lead_id` INT UNSIGNED DEFAULT NULL,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `qr_code_path` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('certification_requested','under_review','approved','rejected','certified') NOT NULL DEFAULT 'certification_requested',
    `admin_comment` TEXT DEFAULT NULL,
    `observations` TEXT DEFAULT NULL,
    `action_plan` TEXT DEFAULT NULL,
    `aqmi_level_assigned` VARCHAR(100) DEFAULT NULL,
    `validated_at` TIMESTAMP NULL DEFAULT NULL,
    `validated_by` VARCHAR(255) DEFAULT NULL,
    `admin_signature` VARCHAR(255) DEFAULT NULL,
    `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `certification_requested_at` TIMESTAMP NULL DEFAULT NULL,
    `certified_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_type` VARCHAR(50) DEFAULT 'string',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs
CREATE TABLE `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(100) DEFAULT NULL,
    `entity_id` INT DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login Logs
CREATE TABLE `login_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `status` ENUM('success','failed') DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_assessments_session ON assessments(session_id);
CREATE INDEX idx_assessments_status ON assessments(status);
CREATE INDEX idx_questions_domain ON questions(domain_id);
CREATE INDEX idx_answers_assessment ON assessment_answers(assessment_id);
CREATE INDEX idx_leads_email ON leads(email);
CREATE INDEX idx_leads_company ON leads(company);
CREATE INDEX idx_recommendations_domain ON recommendations(domain_id);
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);-- =============================================================
-- AQMI Report Studio — Module schema
-- Dialect : MySQL 8 / InnoDB / utf8mb4
-- Module  : Report Studio (templates, builder, blocks, themes)
-- Assumes : auth, router, base DB layer, PDF engine already exist
-- =============================================================

-- 1. Themes -----------------------------------------------------
CREATE TABLE report_themes (
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

-- 2. Block library (catalog of available block types) ----------
CREATE TABLE report_blocks (
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

-- 3. Templates --------------------------------------------------
CREATE TABLE report_templates (
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

-- 4. Template blocks (instances placed by the drag&drop builder)
CREATE TABLE report_template_blocks (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  template_id   INT          NOT NULL,
  block_id      INT          NULL,
  block_key     VARCHAR(50)  NOT NULL,
  title         VARCHAR(150) NULL,
  block_config  JSON         NULL,
  sort_order    INT          NOT NULL DEFAULT 0,
  is_enabled    TINYINT(1)   NOT NULL DEFAULT 1,
  column_span   INT          DEFAULT 12,
  row_id        INT          DEFAULT 0,
  visibility    ENUM('web_pdf','web_only','pdf_only') NOT NULL DEFAULT 'web_pdf',
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
  ('image',         'Image',               'media',      'bi-card-image',      'Image block',                     JSON_OBJECT('url', '', 'alt', '', 'width', '100%'), 1, 130),
  ('bar_chart',     'Bar Chart',           'charts',     'bi-bar-chart',       'Vertical or horizontal bar chart',  JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'A', 'value', 0)))), 'horizontal', false, 'legend', true), 1, 25),
  ('line_chart',    'Line Chart',          'charts',     'bi-graph-up-arrow',  'Trend line chart with multiple series', JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'Jan', 'value', 0)))), 'legend', true, 'smooth', true), 1, 26),
  ('donut_chart',   'Donut Chart',         'charts',     'bi-pie-chart',       'Donut/pie chart for proportional data', JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'A', 'value', 1)), 'legend', true), 1, 27),
  ('area_chart',    'Area Chart',          'charts',     'bi-graph-up-arrow',  'Stacked area chart for trends',     JSON_OBJECT('series', JSON_ARRAY(JSON_OBJECT('label', 'Série 1', 'data', JSON_ARRAY(JSON_OBJECT('label', 'Jan', 'value', 0)))), 'legend', true, 'smooth', true), 1, 28);
-- =============================================================
-- AQMI Report Studio — Feature completion migration
-- Adds: block visibility (web/pdf), official stamp block,
--        page settings columns (orientation, watermark, metadata)
-- =============================================================

-- 1. Add report metadata + page settings to templates -----------
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

-- ============================================================
-- Authentication Tables
-- ============================================================

-- OTP Codes for 2FA verification
CREATE TABLE IF NOT EXISTS `otp_codes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `otp_code` VARCHAR(6) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expire_at` DATETIME NOT NULL,
    `attempts` TINYINT UNSIGNED DEFAULT 0,
    `used` TINYINT(1) DEFAULT 0,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `browser` VARCHAR(255) DEFAULT NULL,
    `device` VARCHAR(255) DEFAULT NULL,
    INDEX `idx_otp_user` (`user_id`),
    INDEX `idx_otp_code` (`otp_code`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `expire_at` DATETIME NOT NULL,
    `used` TINYINT(1) DEFAULT 0,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_reset_user` (`user_id`),
    INDEX `idx_reset_token` (`token`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login History
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `login_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `browser` VARCHAR(255) DEFAULT NULL,
    `operating_system` VARCHAR(100) DEFAULT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `result` ENUM('success','failed') DEFAULT NULL,
    INDEX `idx_history_user` (`user_id`),
    INDEX `idx_history_date` (`login_date`),
    INDEX `idx_history_result` (`result`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
