-- Report Studio: core tables (themes, blocks, templates, template_blocks)

CREATE TABLE IF NOT EXISTS `report_themes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `css_variables` TEXT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `report_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `block_key` VARCHAR(50) NOT NULL UNIQUE,
    `block_name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'content',
    `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-square',
    `default_config` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_system` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `report_templates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'general',
    `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `settings` TEXT NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `report_template_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT NOT NULL,
    `block_id` INT NULL,
    `block_key` VARCHAR(50) NOT NULL,
    `title` VARCHAR(200) NULL,
    `block_config` TEXT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tblocks_template` FOREIGN KEY (`template_id`) REFERENCES `report_templates`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tblocks_block` FOREIGN KEY (`block_id`) REFERENCES `report_blocks`(`id`) ON DELETE SET NULL,
    INDEX `idx_tblocks_template` (`template_id`),
    INDEX `idx_tblocks_sort` (`template_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
