<?php
namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Veuillez vous connecter pour accéder à cette page.');
            redirect('/admin/login');
        }
    }
}