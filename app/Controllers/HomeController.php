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

    public function accountRequest(): void
    {
        $data = [
            'company' => trim($_POST['company'] ?? ''),
            'fullname' => trim($_POST['fullname'] ?? ''),
            'job_title' => trim($_POST['job_title'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'company_size' => trim($_POST['company_size'] ?? ''),
            'activity' => trim($_POST['activity'] ?? ''),
            'platforms' => $_POST['platforms'] ?? [],
            'message' => trim($_POST['message'] ?? ''),
        ];

        $validator = new \App\Helpers\Validator();
        $valid = $validator->validate($data, [
            'company' => 'required|min:2|max:150',
            'fullname' => 'required|min:2|max:100',
            'job_title' => 'required|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required|min:6|max:30',
            'country' => 'required|min:2|max:80',
            'company_size' => 'required',
            'activity' => 'required|min:3|max:200',
            'message' => 'max:1000',
        ]);

        if (!$valid) {
            \App\Helpers\Session::setFlash('account_request_error', $validator->firstError());
            \App\Helpers\Session::setFlash('old_input.account', $data);
            back();
        }

        $platformsList = is_array($data['platforms']) ? implode(', ', $data['platforms']) : '';
        $body = \App\Helpers\Mailer::accountRequestTemplate($data, $platformsList);

        $sent = \App\Helpers\Mailer::send(
            'contact@novaqys.com',
            'Nouvelle demande de compte — ' . $data['company'],
            $body
        );

        if ($sent) {
            \App\Helpers\Session::setFlash('account_request_success', true);
        } else {
            \App\Helpers\Session::setFlash('account_request_error', 'Erreur lors de l\'envoi. Veuillez réessayer.');
            \App\Helpers\Session::setFlash('old_input.account', $data);
        }

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