-- Migration: Add template_id column to reports table
-- Links a certification request to the Report Studio template chosen by the admin.
-- Idempotent: checks information_schema before adding the column.
-- FK ON DELETE SET NULL so deleting a template doesn't lose the report row.

SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'template_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN template_id INT NULL DEFAULT NULL AFTER assessment_id',
    'SELECT \'template_id already exists, skipping\' AS msg'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key only if it doesn't already exist
SET @fk_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND CONSTRAINT_NAME = 'fk_reports_template_id'
);

SET @fk_sql = IF(@fk_exists = 0,
    'ALTER TABLE reports ADD CONSTRAINT fk_reports_template_id FOREIGN KEY (template_id) REFERENCES report_templates(id) ON DELETE SET NULL',
    'SELECT \'fk_reports_template_id already exists, skipping\' AS msg'
);

PREPARE fk_stmt FROM @fk_sql;
EXECUTE fk_stmt;
DEALLOCATE PREPARE fk_stmt;
