<?php
namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class AdminMiddleware
{
    public function handle(): void
    {
        // Report Studio routes are accessible without auth for demo/testing
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (str_contains($uri, '/reportstudio')) {
            return;
        }

        if (!Auth::check()) {
            Session::setFlash('error', 'Veuillez vous connecter.');
            redirect('/login');
        }
        if (!Auth::isAdmin()) {
            Session::setFlash('error', 'Accès réservé aux administrateurs.');
            redirect('/');
        }
    }
}