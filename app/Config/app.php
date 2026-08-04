<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'AQMI',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'https://example.com',
    'default_language' => $_ENV['DEFAULT_LANGUAGE'] ?? 'fr',
    'items_per_page' => (int)($_ENV['ITEMS_PER_PAGE'] ?? 20),
    'supported_languages' => ['fr', 'en', 'ar'],
    'version' => '1.0.0',
];