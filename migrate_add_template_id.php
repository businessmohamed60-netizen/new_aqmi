<?php
/**
 * Migration: Add template_id column to reports table.
 *
 * Adds an INT NULL column `template_id` with a foreign key to
 * report_templates(id) ON DELETE SET NULL. Idempotent — safe to
 * run multiple times.
 *
 * Usage: php migrate_add_template_id.php
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/Database.php';
require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Check if column already exists
    $colCheck = $pdo->prepare(
        "SELECT COUNT(*) as cnt
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'reports'
           AND COLUMN_NAME = 'template_id'"
    );
    $colCheck->execute();
    $colExists = (int) $colCheck->fetch()['cnt'];

    if ($colExists === 0) {
        $pdo->exec("ALTER TABLE reports ADD COLUMN template_id INT NULL DEFAULT NULL AFTER assessment_id");
        echo "Added column `template_id` to `reports`.\n";
    } else {
        echo "Column `template_id` already exists on `reports` — skipping.\n";
    }

    // 2. Check if foreign key already exists
    $fkCheck = $pdo->prepare(
        "SELECT COUNT(*) as cnt
         FROM information_schema.TABLE_CONSTRAINTS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'reports'
           AND CONSTRAINT_TYPE = 'FOREIGN KEY'
           AND CONSTRAINT_NAME = 'fk_reports_template_id'"
    );
    $fkCheck->execute();
    $fkExists = (int) $fkCheck->fetch()['cnt'];

    if ($fkExists === 0) {
        $pdo->exec(
            "ALTER TABLE reports
             ADD CONSTRAINT fk_reports_template_id
             FOREIGN KEY (template_id) REFERENCES report_templates(id)
             ON DELETE SET NULL"
        );
        echo "Added foreign key `fk_reports_template_id`.\n";
    } else {
        echo "Foreign key `fk_reports_template_id` already exists — skipping.\n";
    }

    echo "\nMigration completed successfully.\n";

} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
