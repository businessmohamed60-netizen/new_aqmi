-- 011_create_consolidated_reports.sql
-- Consolidation de plusieurs évaluations (par modèle) en un seul rapport consolidé
-- avec workflow de certification côté admin.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `consolidated_reports` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `consolidated_score` DECIMAL(10,2) DEFAULT NULL,
    `maturity_level` VARCHAR(50) DEFAULT NULL,
    `status` ENUM('draft','certification_requested','under_review','approved','rejected','certified') NOT NULL DEFAULT 'draft',
    `admin_comment` TEXT DEFAULT NULL,
    `observations` TEXT DEFAULT NULL,
    `action_plan` TEXT DEFAULT NULL,
    `aqmi_level_assigned` VARCHAR(100) DEFAULT NULL,
    `report_number` VARCHAR(50) DEFAULT NULL UNIQUE,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `validated_at` TIMESTAMP NULL DEFAULT NULL,
    `validated_by` VARCHAR(255) DEFAULT NULL,
    `certification_requested_at` TIMESTAMP NULL DEFAULT NULL,
    `certified_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consolidated_report_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `consolidated_report_id` INT UNSIGNED NOT NULL,
    `assessment_id` INT UNSIGNED NOT NULL,
    `model_id` INT UNSIGNED DEFAULT NULL,
    `model_name` VARCHAR(255) DEFAULT NULL,
    `score` DECIMAL(10,2) DEFAULT NULL,
    `maturity_level` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`consolidated_report_id`) REFERENCES `consolidated_reports`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uq_consolidated_assessment` (`consolidated_report_id`, `assessment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS idx_consolidated_user ON consolidated_reports(user_id);
CREATE INDEX IF NOT EXISTS idx_consolidated_status ON consolidated_reports(status);
CREATE INDEX IF NOT EXISTS idx_consolidated_items_report ON consolidated_report_items(consolidated_report_id);
