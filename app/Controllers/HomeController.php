<?php
namespace App\Controllers;

use App\Helpers\ViewHelper;

class HomeController
{
    public function index(): void
    {
        view('home.index');
    }

    public function contact(): void
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        // Simple contact handling
        $_SESSION['flash_message'] = __('general.contact_success');
        back();
    }

    public function switchLang(array $params): void
    {
        $lang = $params['lang'] ?? 'fr';
        if (in_array($lang, ['fr', 'en', 'ar'])) {
            $_SESSION['lang'] = $lang;
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($redirect);
    }
}