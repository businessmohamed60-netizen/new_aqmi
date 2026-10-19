-- Migration: Add certificate verification columns to reports table
-- Adds a random verify_token (unguessable URL identifier), certificate_status
-- (active/revoked/expired), issued_at and expires_at dates for QR verification.
-- Idempotent: checks information_schema before adding each column.

-- verify_token: 64-char hex token used in public /verify/{token} URL
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'verify_token'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN verify_token VARCHAR(64) NULL DEFAULT NULL AFTER report_number',
    'SELECT \'verify_token already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- certificate_status: active, revoked, expired
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'certificate_status'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN certificate_status VARCHAR(20) NULL DEFAULT NULL AFTER verify_token',
    'SELECT \'certificate_status already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- issued_at: date the certificate was issued (distinct from certified_at)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'issued_at'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN issued_at DATE NULL DEFAULT NULL AFTER certificate_status',
    'SELECT \'issued_at already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- expires_at: date the certificate expires
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'expires_at'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN expires_at DATE NULL DEFAULT NULL AFTER issued_at',
    'SELECT \'expires_at already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- revoked_at: timestamp when certificate was revoked (audit trail)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'revoked_at'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN revoked_at DATETIME NULL DEFAULT NULL AFTER expires_at',
    'SELECT \'revoked_at already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- revoked_reason: why the certificate was revoked
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND COLUMN_NAME = 'revoked_reason'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE reports ADD COLUMN revoked_reason TEXT NULL DEFAULT NULL AFTER revoked_at',
    'SELECT \'revoked_reason already exists, skipping\' AS msg'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index for fast token lookups (public verification endpoint)
SET @idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND INDEX_NAME = 'idx_reports_verify_token'
);
SET @idx_sql = IF(@idx_exists = 0,
    'CREATE INDEX idx_reports_verify_token ON reports (verify_token)',
    'SELECT \'idx_reports_verify_token already exists, skipping\' AS msg'
);
PREPARE idx_stmt FROM @idx_sql; EXECUTE idx_stmt; DEALLOCATE PREPARE idx_stmt;

-- Backfill: set certificate_status='active' and issued_at for already-certified reports
UPDATE reports
SET certificate_status = 'active',
    issued_at = COALESCE(issued_at, DATE(certified_at))
WHERE status = 'certified'
  AND certificate_status IS NULL;
