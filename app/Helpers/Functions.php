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
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
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