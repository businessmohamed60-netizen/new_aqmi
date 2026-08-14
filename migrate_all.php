<?php
/**
 * Migration runner — executes all pending SQL migrations in order.
 * Run this on the server: php migrate_all.php
 * Safe to re-run: skips already-applied migrations.
 */
define('BASE_PATH', __DIR__);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) return;
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    // Create migrations tracking table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `_migrations` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `filename` VARCHAR(255) NOT NULL UNIQUE,
        `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Get already-applied migrations
    $applied = [];
    $stmt = $pdo->query("SELECT filename FROM _migrations");
    while ($row = $stmt->fetch()) {
        $applied[] = $row['filename'];
    }

    // Find all migration files
    $dir = BASE_PATH . '/database/migrations';
    $files = glob($dir . '/*.sql');
    sort($files);

    if (empty($files)) {
        echo "No migration files found.\n";
        exit(0);
    }

    $count = 0;
    foreach ($files as $filepath) {
        $filename = basename($filepath);

        if (in_array($filename, $applied)) {
            echo "SKIP  $filename (already applied)\n";
            continue;
        }

        echo "APPLY $filename ... ";
        $sql = file_get_contents($filepath);
        if ($sql === false) {
            echo "ERROR (cannot read file)\n";
            continue;
        }

        // Split into statements on semicolons at end of lines, skip comment lines
        $statements = [];
        $current = '';
        foreach (explode("\n", $sql) as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || strpos($trimmed, '--') === 0) continue;
            $current .= $line . "\n";
            if (substr(rtrim($trimmed), -1) === ';') {
                $statements[] = rtrim(trim($current), ';');
                $current = '';
            }
        }
        if (!empty(trim($current))) $statements[] = trim($current);

        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($statements as $s) {
                $s = trim($s);
                if (empty($s)) continue;
                $pdo->exec($s);
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

            $pdo->prepare("INSERT INTO _migrations (filename) VALUES (?)")->execute([$filename]);
            echo "OK\n";
            $count++;
        } catch (Exception $e) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    echo "\nDone. $count migration(s) applied.\n";

    // Verify key tables/columns
    echo "\nVerification:\n";
    $cols = $pdo->query("SHOW COLUMNS FROM assessments LIKE 'model_id'");
    echo "  assessments.model_id: " . ($cols->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'consolidated_reports'");
    echo "  consolidated_reports: " . ($tables->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'consolidated_report_items'");
    echo "  consolidated_report_items: " . ($tables->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";

} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
