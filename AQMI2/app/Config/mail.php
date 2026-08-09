<?php
return [
    'driver' => $_ENV['MAIL_DRIVER'] ?? (($_ENV['MAIL_HOST'] ?? 'smtp.example.com') === 'smtp.example.com' ? 'log' : 'smtp'),
    'host' => $_ENV['MAIL_HOST'] ?? 'smtp.example.com',
    'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
    'username' => $_ENV['MAIL_USERNAME'] ?? '',
    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@aqmi.com',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'AQMI',
];