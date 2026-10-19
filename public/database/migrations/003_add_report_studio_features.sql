-- Report Studio: add report metadata + page settings columns to templates

ALTER TABLE `report_templates`
    ADD COLUMN `orientation` VARCHAR(10) NOT NULL DEFAULT 'portrait' AFTER `status`,
    ADD COLUMN `watermark_text` VARCHAR(200) NULL DEFAULT NULL AFTER `orientation`,
    ADD COLUMN `watermark_opacity` DECIMAL(4,2) NOT NULL DEFAULT 0.08 AFTER `watermark_text`,
    ADD COLUMN `report_number_prefix` VARCHAR(20) NOT NULL DEFAULT 'AQMI-RPT-' AFTER `watermark_opacity`,
    ADD COLUMN `certification_date` DATE NULL DEFAULT NULL AFTER `report_number_prefix`,
    ADD COLUMN `expiration_date` DATE NULL DEFAULT NULL AFTER `certification_date`;
