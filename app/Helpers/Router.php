<?php
namespace App\Helpers;

class Router
{
    private array $routes = [];
    private $notFoundCallback = null;

    public function addRoute(string $method, string $route, $callback, array $middleware = []): void
    {
        $route = preg_quote($route, '~');
        $route = preg_replace('/\\\\\\{([a-z_]+)\\\\\\}/', '(?P<$1>[^/]+)', $route);
        $route = '~^' . $route . '$~i';
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $route,
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    public function get(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('GET', $route, $callback, $middleware);
    }

    public function post(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('POST', $route, $callback, $middleware);
    }

    public function setNotFound($callback): void
    {
        $this->notFoundCallback = $callback;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
                $_REQUEST['route_params'] = $params;

                foreach ($route['middleware'] as $middlewareClass) {
                    if (!class_exists($middlewareClass)) {
                        http_response_code(500);
                        error_log('Middleware class not found: ' . $middlewareClass);
                        echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;"><h1 style="color:#E5484D;">Erreur 500</h1><p>Erreur de configuration du serveur. Contactez l\'administrateur.</p></div>';
                        exit;
                    }
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                try {
                    if (is_callable($route['callback'])) {
                        call_user_func($route['callback'], $params);
                    } elseif (is_string($route['callback'])) {
                        $this->handleControllerCallback($route['callback'], $params);
                    }
                } catch (\Throwable $e) {
                    error_log('Route error [' . $uri . ']: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    http_response_code(500);
                    $appConfig = require BASE_PATH . '/app/Config/app.php';
                    if ($appConfig['debug']) {
                        echo '<div style="font-family:monospace;padding:20px;max-width:900px;margin:40px auto;background:#1a1a2e;color:#e2e8f0;border-radius:8px;">'
                            . '<h2 style="color:#ef4444;">Erreur ' . get_class($e) . '</h2>'
                            . '<p style="color:#486581;">' . e($e->getMessage()) . '</p>'
                            . '<p style="color:#486581;font-size:0.85rem;">' . e($e->getFile()) . ':' . $e->getLine() . '</p>'
                            . '<pre style="color:#486581;font-size:0.8rem;overflow:auto;">' . e($e->getTraceAsString()) . '</pre>'
                            . '</div>';
                    } else {
                        echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;">'
                            . '<h1 style="font-size:3rem;color:#E5484D;">Erreur 500</h1>'
                            . '<p style="color:#486581;">Une erreur est survenue. L\'équipe technique a été notifiée.</p>'
                            . '<a href="/" style="color:#1F6FEB;">Retour à l\'accueil</a></div>';
                    }
                }
                return;
            }
        }

        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo '<div style="text-align:center;padding:100px 20px;font-family:sans-serif;"><h1 style="font-size:4rem;color:#1F6FEB;">404</h1><p style="color:#486581;">Page non trouvée</p><a href="/" style="color:#1F6FEB;">Retour à l\'accueil</a></div>';
        }
    }

    private function handleControllerCallback(string $callback, array $params): void
    {
        $parts = explode('@', $callback);
        $method = $parts[1] ?? 'index';

        $namespaces = [
            'App\\Controllers\\',
            'App\\Modules\\ReportStudio\\Controllers\\',
        ];

        foreach ($namespaces as $ns) {
            $controllerName = $ns . $parts[0];
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], [$params]);
                }
                return;
            }
        }
    }
}