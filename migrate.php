<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/Database.php';
require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Read and execute migration SQL
    $sql = file_get_contents(BASE_PATH . '/database/migration_aqmi_auth.sql');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $statements = explode(';', $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "Migration executed successfully\n";
    
    // Verify tables
    $tables = $pdo->query("SHOW TABLES LIKE 'otp_codes'");
    echo "otp_codes: " . ($tables->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'password_resets'");
    echo "password_resets: " . ($tables->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'login_history'");
    echo "login_history: " . ($tables->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    
    // Check existing login_logs table structure
    $cols = $pdo->query("DESCRIBE login_logs");
    echo "\nlogin_logs columns:\n";
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
