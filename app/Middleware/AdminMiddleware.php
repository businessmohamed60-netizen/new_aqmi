<?php
namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\Session;

class AdminMiddleware
{
    public function handle(): void
    {
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