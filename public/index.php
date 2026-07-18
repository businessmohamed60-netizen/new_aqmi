<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly (for built-in PHP server)
$staticFile = __DIR__ . $requestUri;
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot|webp|html)$/', $requestUri) && file_exists($staticFile)) {
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'webp' => 'image/webp',
    ];
    $ext = pathinfo($requestUri, PATHINFO_EXTENSION);
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($staticFile);
    exit;
}

// Serve the immersive 360° hero for the homepage
if ($requestUri === '/' || $requestUri === '') {
    $heroFile = __DIR__ . '/hero360.html';
    if (file_exists($heroFile)) {
        readfile($heroFile);
        exit;
    }
}

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

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

$appConfig = require BASE_PATH . '/app/Config/app.php';
$sessionConfig = require BASE_PATH . '/app/Config/session.php';

if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

ini_set('session.cookie_lifetime', $sessionConfig['lifetime']);
ini_set('session.cookie_httponly', $sessionConfig['http_only']);
session_name($sessionConfig['cookie_name']);
session_start();

$lang = $_SESSION['lang'] ?? $appConfig['default_language'];
$_SESSION['lang'] = $lang;

require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Router;

$router = new Router();
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/admin.php';

$router->setNotFound(function() {
    http_response_code(404);
    echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;"><h1 style="font-size:4rem;color:#1a56db;">404</h1><p style="color:#6b7280;">Page non trouvée</p><a href="/" style="color:#1a56db;">Retour à l\'accueil</a></div>';
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);