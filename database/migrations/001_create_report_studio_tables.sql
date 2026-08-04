-- =============================================================
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
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tblocks_template FOREIGN KEY (template_id)
      REFERENCES report_templates(id) ON DELETE CASCADE,
  CONSTRAINT fk_tblocks_block FOREIGN KEY (block_id)
      REFERENCES report_blocks(id) ON DELETE SET NULL,
  INDEX idx_report_tblocks_template (template_id),
  INDEX idx_report_tblocks_sort (template_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
