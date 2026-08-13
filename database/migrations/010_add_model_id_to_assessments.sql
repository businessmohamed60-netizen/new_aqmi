-- Add model_id column to assessments table
ALTER TABLE `assessments` ADD COLUMN `model_id` INT UNSIGNED DEFAULT NULL AFTER `session_id`;
ALTER TABLE `assessments` ADD CONSTRAINT `fk_assessments_model` FOREIGN KEY (`model_id`) REFERENCES `evaluation_models`(`id`) ON DELETE SET NULL;
CREATE INDEX `idx_assessments_model` ON `assessments`(`model_id`);
