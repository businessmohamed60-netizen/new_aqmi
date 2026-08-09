-- Add production-related fields to the leads table.
ALTER TABLE `leads`
  ADD COLUMN `production_capacity` VARCHAR(100) DEFAULT NULL AFTER `production_type`,
  ADD COLUMN `machine_count` INT UNSIGNED DEFAULT NULL AFTER `production_capacity`,
  ADD COLUMN `machine_types` VARCHAR(255) DEFAULT NULL AFTER `machine_count`;
