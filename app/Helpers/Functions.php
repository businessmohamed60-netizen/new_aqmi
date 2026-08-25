<?php

use App\Helpers\Security;
use App\Helpers\Session;

function e($value): string
{
    return Security::sanitizeOutput($value ?? '');
}

function __(string $key, array $replace = []): string
{
    static $translations = [];
    $lang = $_SESSION['lang'] ?? 'fr';

    if (!isset($translations[$lang])) {
        $file = BASE_PATH . "/resources/lang/{$lang}.php";
        $translations[$lang] = file_exists($file) ? require $file : [];
    }

    $text = $translations[$lang][$key] ?? $key;

    foreach ($replace as $search => $value) {
        $text = str_replace(":{$search}", (string)$value, $text);
    }

    return $text;
}

function old(string $key, $default = '')
{
    return Session::getFlash('old_input.' . $key, $default);
}

function csrf_field(): string
{
    return Security::csrfField();
}

function csrf_token(): string
{
    return Security::generateCsrfToken();
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function back(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

function dd($var): void
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    exit;
}

function view(string $view, array $data = []): void
{
    extract($data);
    $viewFile = BASE_PATH . '/resources/views/' . str_replace('.', '/', $view) . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        throw new \RuntimeException("View not found: {$view}");
    }
}

/**
 * Render a view partial to a string (used by Report Studio block renderer).
 */
function view_partial(string $view, array $data = [], bool $return = false): string
{
    extract($data);
    $viewFile = BASE_PATH . '/resources/views/' . str_replace('.', '/', $view) . '.php';
    if (!file_exists($viewFile)) {
        return '';
    }
    ob_start();
    require $viewFile;
    $output = ob_get_clean();
    if ($return) {
        return $output;
    }
    echo $output;
    return $output;
}

/**
 * Generate a URL for a named Report Studio route.
 * Supports: reportstudio, reportstudio.templates.index, reportstudio.builder.edit,
 * reportstudio.themes.index, reportstudio.preview.show
 */
function route(string $name, array $params = []): string
{
    $routes = [
        'reportstudio'                  => '/admin/reportstudio',
        'reportstudio.index'            => '/admin/reportstudio',
        'reportstudio.templates.index' => '/admin/reportstudio/templates',
        'reportstudio.templates.create'=> '/admin/reportstudio/templates/create',
        'reportstudio.templates.store'  => '/admin/reportstudio/templates',
        'reportstudio.templates.show'   => '/admin/reportstudio/templates/{id}',
        'reportstudio.templates.edit'   => '/admin/reportstudio/templates/{id}/edit',
        'reportstudio.templates.update'  => '/admin/reportstudio/templates/{id}',
        'reportstudio.templates.destroy' => '/admin/reportstudio/templates/{id}/delete',
        'reportstudio.builder.edit'     => '/admin/reportstudio/builder/{id}/edit',
        'reportstudio.datasources'      => '/admin/reportstudio/datasources',
        'reportstudio.table-info'       => '/admin/reportstudio/table-info/{table}',
        'reportstudio.data-preview'     => '/admin/reportstudio/data-preview',
        'reportstudio.upload-image'     => '/admin/reportstudio/upload-image',
        'reportstudio.themes.index'     => '/admin/reportstudio/themes',
        'reportstudio.themes.create'    => '/admin/reportstudio/themes/create',
        'reportstudio.themes.edit'      => '/admin/reportstudio/themes/{id}/edit',
        'reportstudio.themes.store'     => '/admin/reportstudio/themes',
        'reportstudio.themes.update'    => '/admin/reportstudio/themes/{id}',
        'reportstudio.themes.destroy'   => '/admin/reportstudio/themes/{id}/delete',
        'reportstudio.preview.show'     => '/admin/reportstudio/preview/{id}',
    ];
    $url = $routes[$name] ?? '/';
    foreach ($params as $key => $value) {
        $url = str_replace('{' . $key . '}', (string) $value, $url);
    }
    return $url;
}

/**
 * Abort with an HTTP status code.
 */
function abort(int $code): void
{
    http_response_code($code);
    if ($code === 404) {
        echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;"><h1 style="font-size:4rem;color:#1F6FEB;">404</h1><p style="color:#486581;">Page non trouvée</p><a href="/" style="color:#1F6FEB;">Retour à l\'accueil</a></div>';
    }
    exit;
}

function jsonResponse($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function formatPercent($value): string
{
    return number_format((float)$value, 1) . '%';
}

function formatDate($date, string $format = 'd/m/Y'): string
{
    if (!$date) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

function isActive(string $path): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($path === '/') {
        return $uri === '/' ? 'active' : '';
    }
    return str_starts_with($uri, $path) ? 'active' : '';
}

function truncate(string $text, int $length = 100): string
{
    $len = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($len <= $length) return $text;
    return (function_exists('mb_substr') ? mb_substr($text, 0, $length) : substr($text, 0, $length)) . '...';
}

function generateSlug(string $string): string
{
    $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function generateUuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}