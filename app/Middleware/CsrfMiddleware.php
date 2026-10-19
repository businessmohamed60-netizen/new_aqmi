<?php
namespace App\Middleware;

use App\Helpers\Security;
use App\Helpers\Session;

class CsrfMiddleware
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!Security::validateCsrfToken($token)) {
                Session::setFlash('error', 'Token CSRF invalide.');
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    jsonResponse(['error' => 'CSRF token mismatch'], 419);
                }
                back();
            }
        }
    }
}