<?php
return [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_NAME'] ?? 'aqmi',
    'username' => $_ENV['DB_USER'] ?? 'aqmi_user',
    'password' => $_ENV['DB_PASS'] ?? 'Aqmi@2024#Secure',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
