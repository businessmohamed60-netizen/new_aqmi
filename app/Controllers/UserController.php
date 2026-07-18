<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Models\Assessment;

class UserController
{
    public function login(): void
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user['role_slug'] === 'client') {
                redirect('/user/dashboard');
            }
        }

        // Handle GET-based login (proxy workaround: Cloudflare blocks POST)
        $email = $_GET['email'] ?? '';
        $password = $_GET['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $this->processLogin($email, $password);
            return;
        }

        view('user.login');
    }

    private function processLogin(string $email, string $password): void
    {
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            redirect('/user/login');
            return;
        }

        $user = \App\Models\User::findByEmail($email);
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            redirect('/user/login');
            return;
        }

        if (Auth::attempt($email, $password)) {
            $_SESSION['success'] = 'Bienvenue ' . ($user['firstname'] ?? '') . ' !';
            redirect('/user/dashboard');
        } else {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            redirect('/user/login');
        }
    }

    public function dashboard(): void
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à votre espace.';
            redirect('/user/login');
            return;
        }
        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Accès non autorisé.';
            redirect('/');
            return;
        }

        $userId = Auth::id();
        $assessments = Database::fetchAll(
            "SELECT a.*, l.company, l.firstname as lead_firstname, l.lastname as lead_lastname,
                    r.status as report_status, r.id as report_id
             FROM assessments a
             LEFT JOIN leads l ON a.id = l.assessment_id
             LEFT JOIN reports r ON r.assessment_id = a.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC",
            [$userId]
        );

        $totalAssessments = count($assessments);
        $completedCount = 0;
        foreach ($assessments as $a) {
            if ($a['status'] === 'completed') $completedCount++;
        }

        view('user.dashboard', compact('assessments', 'totalAssessments', 'completedCount', 'user'));
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/user/login');
    }
}