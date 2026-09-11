-- Migration: Add assessment_limit column to users table
-- Allows the admin to control how many assessments each user can create.
-- NULL means unlimited; 0 means no new assessments allowed; N means up to N.

SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'assessment_limit'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN assessment_limit INT NULL DEFAULT NULL AFTER is_active',
    'SELECT \'assessment_limit already exists, skipping\' AS msg'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
