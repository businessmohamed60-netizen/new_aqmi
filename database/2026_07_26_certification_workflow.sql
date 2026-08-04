-- ============================================================
-- Migration : Workflow de certification AQMI
-- Étend la table `reports` existante.
-- Ne modifie aucune autre table.
-- ============================================================

-- 1. Nouveau jeu de statuts pour le cycle de certification.
--    (Le statut "Draft"/"Completed" du questionnaire reste porté par
--     `assessments.status` (in_progress/completed) - non modifié ici.)
ALTER TABLE `reports`
    MODIFY COLUMN `status` ENUM(
        'certification_requested',
        'under_review',
        'approved',
        'rejected',
        'certified'
    ) NOT NULL DEFAULT 'certification_requested';

-- 2. Migration des données existantes vers les nouveaux statuts
UPDATE `reports` SET `status` = 'certification_requested' WHERE `status` = 'pending';
UPDATE `reports` SET `status` = 'certified' WHERE `status` = 'validated';
-- 'rejected' ne change pas de nom, rien à faire.

-- 3. Nouvelles colonnes pour le dossier de certification admin
ALTER TABLE `reports`
    ADD COLUMN `report_number` VARCHAR(50) DEFAULT NULL UNIQUE AFTER `id`,
    ADD COLUMN `admin_comment` TEXT DEFAULT NULL AFTER `status`,
    ADD COLUMN `observations` TEXT DEFAULT NULL AFTER `admin_comment`,
    ADD COLUMN `action_plan` TEXT DEFAULT NULL AFTER `observations`,
    ADD COLUMN `aqmi_level_assigned` VARCHAR(100) DEFAULT NULL AFTER `action_plan`,
    ADD COLUMN `qr_code_path` VARCHAR(255) DEFAULT NULL AFTER `file_path`,
    ADD COLUMN `admin_signature` VARCHAR(255) DEFAULT NULL AFTER `validated_by`,
    ADD COLUMN `certification_requested_at` TIMESTAMP NULL DEFAULT NULL AFTER `generated_at`,
    ADD COLUMN `certified_at` TIMESTAMP NULL DEFAULT NULL AFTER `certification_requested_at`;

-- 4. Index pour les filtres admin (statut, recherche par numéro)
CREATE INDEX idx_reports_status ON reports(status);
CREATE INDEX idx_reports_number ON reports(report_number);
