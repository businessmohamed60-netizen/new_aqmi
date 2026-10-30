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
        // Accepte JSON (fetch) ou formulaire classique ($_POST)
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?? $_POST;

        $data = [
            'company' => trim($input['company'] ?? ''),
            'fullname' => trim($input['fullname'] ?? ''),
            // Le formulaire HTML utilise "role", le reste de l'app utilise "job_title"
            'job_title' => trim($input['job_title'] ?? $input['role'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'country' => trim($input['country'] ?? ''),
            // Le formulaire HTML utilise "size", le reste de l'app utilise "company_size"
            'company_size' => trim($input['company_size'] ?? $input['size'] ?? ''),
            // Le formulaire HTML utilise "sector", le reste de l'app utilise "activity"
            'activity' => trim($input['activity'] ?? $input['sector'] ?? ''),
            'platforms' => $input['platforms'] ?? [],
            'message' => trim($input['message'] ?? ''),
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

        $isJson = $raw !== '' && json_decode($raw, true) !== null;

        if (!$valid) {
            if ($isJson) {
                // Remappe les clés internes vers les noms de champs du formulaire HTML
                $fieldMap = [
                    'job_title' => 'role',
                    'company_size' => 'size',
                    'activity' => 'sector',
                ];
                $errors = [];
                foreach ($validator->errors() as $field => $messages) {
                    $formField = $fieldMap[$field] ?? $field;
                    $errors[$formField] = $messages[0] ?? '';
                }
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => $validator->firstError(),
                    'errors' => $errors,
                ]);
                return;
            }
            \App\Helpers\Session::setFlash('account_request_error', $validator->firstError());
            \App\Helpers\Session::setFlash('old_input.account', $data);
            back();
        }

        $platformsList = is_array($data['platforms']) ? implode(', ', $data['platforms']) : '';
        $body = \App\Helpers\Mailer::accountRequestTemplate($data, $platformsList);

        $sent = \App\Helpers\Mailer::notifyAdmin(
            'Nouvelle demande de compte — ' . $data['company'],
            $body
        );

        if ($isJson) {
            http_response_code($sent ? 200 : 500);
            header('Content-Type: application/json');
            echo json_encode($sent
                ? ['status' => 'ok', 'reference' => 'NOVA-' . date('ymd') . '-' . substr(uniqid(), -5)]
                : ['status' => 'error', 'message' => 'Erreur lors de l\'envoi. Veuillez réessayer.']
            );
            return;
        }

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
        if (in_array($lang, ['fr', 'en', 'ar', 'es'])) {
            $_SESSION['lang'] = $lang;
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($redirect);
    }

    public function cgu(): void
    {
        view('legal.cgu');
    }

    public function privacy(): void
    {
        view('legal.privacy');
    }
}