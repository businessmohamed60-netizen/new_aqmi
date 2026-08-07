-- =============================================================
-- AQMI — Lead form: automotive industry fields
-- Adds technical/industrial fields relevant to automotive parts
-- manufacturers (OEM tier, product category, quality KPIs, etc.)
-- All columns are nullable so existing leads remain valid.
-- =============================================================

ALTER TABLE `leads`
  -- Étape 1 : Informations globales (compléments entreprise)
  ADD COLUMN `activity_category`   VARCHAR(50)   DEFAULT NULL AFTER `sector`,
  ADD COLUMN `oem_tier`            VARCHAR(20)   DEFAULT NULL AFTER `activity_category`,
  ADD COLUMN `product_category`    VARCHAR(100)  DEFAULT NULL AFTER `oem_tier`,
  ADD COLUMN `main_clients`       VARCHAR(255)  DEFAULT NULL AFTER `product_category`,
  ADD COLUMN `annual_revenue`      VARCHAR(50)   DEFAULT NULL AFTER `main_clients`,
  ADD COLUMN `export_percentage`   INT           DEFAULT NULL AFTER `annual_revenue`,
  ADD COLUMN `production_sites`    INT UNSIGNED  DEFAULT NULL AFTER `export_percentage`,
  ADD COLUMN `workforce_production` INT UNSIGNED DEFAULT NULL AFTER `production_sites`,
  ADD COLUMN `workforce_engineering` INT UNSIGNED DEFAULT NULL AFTER `workforce_production`,

  -- Étape 2 : Informations techniques industrielles
  ADD COLUMN `main_materials`      VARCHAR(255)  DEFAULT NULL AFTER `machine_types`,
  ADD COLUMN `process_technologies` VARCHAR(255) DEFAULT NULL AFTER `main_materials`,
  ADD COLUMN `ppm_target`           INT UNSIGNED  DEFAULT NULL AFTER `process_technologies`,
  ADD COLUMN `otd_rate`            DECIMAL(5,2)  DEFAULT NULL AFTER `ppm_target`,
  ADD COLUMN `fta_rate`            DECIMAL(5,2)  DEFAULT NULL AFTER `otd_rate`,
  ADD COLUMN `scrap_rate`          DECIMAL(5,2)  DEFAULT NULL AFTER `fta_rate`,
  ADD COLUMN `traceability_system` VARCHAR(100)  DEFAULT NULL AFTER `scrap_rate`,
  ADD COLUMN `logistics_system`    VARCHAR(100)  DEFAULT NULL AFTER `traceability_system`,
  ADD COLUMN `rd_budget_percent`   DECIMAL(5,2)  DEFAULT NULL AFTER `logistics_system`,
  ADD COLUMN `current_erp`         VARCHAR(100)  DEFAULT NULL AFTER `rd_budget_percent`;
