<?php
/**
 * AQMI Report Studio — Database migration script
 * Run this once on your production server via: php migrate_reportstudio.php
 * It applies all Report Studio migrations safely (idempotent).
 */
define('BASE_PATH', __DIR__);

// Load autoloader
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) require_once $file;
        }
    });
}

// Load .env
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Database;

echo "AQMI Report Studio — Migration\n";
echo "================================\n\n";

try {
    $pdo = Database::getInstance()->getConnection();

    // Check if report_themes table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'report_%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing Report Studio tables: " . (empty($tables) ? 'NONE' : implode(', ', $tables)) . "\n\n";

    if (empty($tables)) {
        echo "Creating all Report Studio tables from schema...\n";
        $sql = file_get_contents(BASE_PATH . '/database/migrations/001_create_report_studio_tables.sql');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if (!empty($stmt)) $pdo->exec($stmt);
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        echo "  Tables created.\n\n";

        echo "Seeding initial data...\n";
        $sql = file_get_contents(BASE_PATH . '/database/migrations/002_seed_report_studio_data.sql');
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if (!empty($stmt)) $pdo->exec($stmt);
        }
        echo "  Data seeded.\n\n";
    }

    // Migration 003: Add report metadata + page settings to templates
    $cols = $pdo->query("SHOW COLUMNS FROM report_templates LIKE 'report_number_prefix'")->fetch();
    if (!$cols) {
        echo "Applying migration 003: report template metadata...\n";
        $sql = file_get_contents(BASE_PATH . '/database/migrations/003_add_report_studio_features.sql');
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if (!empty($stmt)) $pdo->exec($stmt);
        }
        echo "  Done.\n\n";
    }

    // Migration 005: Add column_span + row_id to template blocks
    $cols = $pdo->query("SHOW COLUMNS FROM report_template_blocks LIKE 'column_span'")->fetch();
    if (!$cols) {
        echo "Applying migration 005: column_span + row_id...\n";
        $pdo->exec("ALTER TABLE report_template_blocks ADD COLUMN column_span INT DEFAULT 12");
        $pdo->exec("ALTER TABLE report_template_blocks ADD COLUMN row_id INT DEFAULT 0");
        $pdo->exec("UPDATE report_template_blocks SET column_span = 12 WHERE column_span IS NULL");
        echo "  Done.\n\n";
    }

    // Add visibility column if missing
    $cols = $pdo->query("SHOW COLUMNS FROM report_template_blocks LIKE 'visibility'")->fetch();
    if (!$cols) {
        echo "Adding visibility column to template blocks...\n";
        $pdo->exec("ALTER TABLE report_template_blocks ADD COLUMN visibility ENUM('web_pdf','web_only','pdf_only') NOT NULL DEFAULT 'web_pdf' AFTER is_enabled");
        echo "  Done.\n\n";
    }

    // Fix block_id nullable + FK
    $col = $pdo->query("SHOW COLUMNS FROM report_template_blocks LIKE 'block_id'")->fetch();
    if ($col && strtoupper($col['Null']) === 'NO') {
        echo "Fixing block_id to be nullable...\n";
        $fks = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'report_template_blocks' AND COLUMN_NAME = 'block_id' AND REFERENCED_TABLE_NAME IS NOT NULL")->fetch();
        if ($fks) {
            $pdo->exec("ALTER TABLE report_template_blocks DROP FOREIGN KEY " . $fks['CONSTRAINT_NAME']);
        }
        $pdo->exec("ALTER TABLE report_template_blocks MODIFY COLUMN block_id INT NULL");
        $pdo->exec("ALTER TABLE report_template_blocks ADD CONSTRAINT fk_tblocks_block FOREIGN KEY (block_id) REFERENCES report_blocks(id) ON DELETE SET NULL");
        echo "  Done.\n\n";
    }

    // Migration 006: Add chart blocks
    $block = $pdo->query("SELECT id FROM report_blocks WHERE block_key = 'bar_chart'")->fetch();
    if (!$block) {
        echo "Applying migration 006: chart blocks...\n";
        $sql = file_get_contents(BASE_PATH . '/database/migrations/006_add_chart_blocks.sql');
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if (!empty($stmt)) $pdo->exec($stmt);
        }
        echo "  Done.\n\n";
    }

    // Migration 007: Rename reports.template_id → reports.theme_id
    // Le champ stocke un ID de thème (report_themes), pas un ID de modèle
    // (report_templates). On renomme la colonne pour refléter la réalité.
    $col = $pdo->query("SHOW COLUMNS FROM reports LIKE 'template_id'")->fetch();
    if ($col) {
        echo "Applying migration 007: rename reports.template_id → theme_id...\n";
        $pdo->exec("ALTER TABLE reports CHANGE COLUMN template_id theme_id INT NULL");
        echo "  Done.\n\n";
    } elseif (!$pdo->query("SHOW COLUMNS FROM reports LIKE 'theme_id'")->fetch()) {
        echo "Adding reports.theme_id column (new install)...\n";
        $pdo->exec("ALTER TABLE reports ADD COLUMN theme_id INT NULL");
        echo "  Done.\n\n";
    }

    // Verify final state
    echo "Verification:\n";
    $count = $pdo->query("SELECT COUNT(*) FROM report_themes")->fetchColumn();
    echo "  report_themes: {$count} rows\n";
    $count = $pdo->query("SELECT COUNT(*) FROM report_blocks")->fetchColumn();
    echo "  report_blocks: {$count} rows\n";
    $count = $pdo->query("SELECT COUNT(*) FROM report_templates")->fetchColumn();
    echo "  report_templates: {$count} rows\n";

    $cols = $pdo->query("SHOW COLUMNS FROM report_template_blocks")->fetchAll(PDO::FETCH_ASSOC);
    echo "  report_template_blocks columns: " . implode(', ', array_column($cols, 'Field')) . "\n\n";

    echo "All migrations applied successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
