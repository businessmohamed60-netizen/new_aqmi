<?php
// ===== MARQUEUR DE TEST — cherchez "TEST-MARKER-9F3K2" dans le code source de la page =====
echo '<!-- TEST-MARKER-9F3K2 -->';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$normalizedUri = rtrim($requestUri, '/');
if ($normalizedUri === '') {
    $normalizedUri = '/';
}

if ($normalizedUri === '/' || $normalizedUri === '/index.html' || $normalizedUri === '/index.php') {
    $landingFile = __DIR__ . '/Landing.html';
    if (file_exists($landingFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($landingFile);
        exit;
    }
    http_response_code(500);
    echo 'Landing.html introuvable a cet emplacement: ' . htmlspecialchars($landingFile);
    exit;
}

$staticFile = __DIR__ . $requestUri;
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot|webp|html)$/i', $requestUri) && is_file($staticFile)) {
    $mimeTypes = [
        'css' => 'text/css', 'js' => 'application/javascript', 'png' => 'image/png',
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif',
        'ico' => 'image/x-icon', 'svg' => 'image/svg+xml', 'woff' => 'font/woff',
        'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'eot' => 'application/vnd.ms-fontobject',
        'webp' => 'image/webp', 'html' => 'text/html; charset=UTF-8',
    ];
    $ext = strtolower(pathinfo($requestUri, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) header('Content-Type: ' . $mimeTypes[$ext]);
    readfile($staticFile);
    exit;
}

define('BASE_PATH', dirname(__DIR__));
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        if (strpos($class, $prefix) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) require_once $file;
        }
    });
}

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (substr($trimmed, 0, 1) === '#' || strpos($line, '=') === false) continue;
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

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $appConfig['default_language'];
$_SESSION['lang'] = $lang;

require_once BASE_PATH . '/app/Helpers/Functions.php';

use App\Helpers\Router;

$router = new Router();
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/admin.php';

$router->setNotFound(function() {
    http_response_code(404);
    echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;"><h1 style="font-size:4rem;color:#1F6FEB;">404</h1><p style="color:#486831;">Page non trouvee</p><a href="/" style="color:#1F6FEB;">Retour a l\'accueil</a></div>';
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);