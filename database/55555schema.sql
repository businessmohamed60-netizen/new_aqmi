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

-- Reports (Certification AQMI workflow)
CREATE TABLE `reports` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `report_number` VARCHAR(50) DEFAULT NULL UNIQUE,
    `assessment_id` INT UNSIGNED DEFAULT NULL,
    `lead_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM(
        'certification_requested',
        'under_review',
        'approved',
        'rejected',
        'certified'
    ) NOT NULL DEFAULT 'certification_requested',
    `admin_comment` TEXT DEFAULT NULL,
    `observations` TEXT DEFAULT NULL,
    `action_plan` TEXT DEFAULT NULL,
    `aqmi_level_assigned` VARCHAR(100) DEFAULT NULL,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `qr_code_path` VARCHAR(255) DEFAULT NULL,
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
CREATE INDEX idx_audit_logs_action ON audit_logs(action);