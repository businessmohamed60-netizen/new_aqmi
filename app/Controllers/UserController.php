<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Mailer;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Models\LoginHistory;
use App\Models\OtpCode;
use App\Models\PasswordReset;
use App\Models\User;

class UserController
{
    public function login(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user['role_slug'] === 'client') {
                redirect('/user/dashboard');
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $this->processLogin($email, $password);
            return;
        }

        view('user.login');
    }

    /**
     * Étape 1 : email + mot de passe. Si valide, envoie un code OTP
     * et redirige vers /user/otp (aucune session ouverte à ce stade).
     */
    private function processLogin(string $email, string $password): void
    {
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            redirect('/user/login');
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = Mailer::detectUserAgent();

        // Protection anti brute-force : max 5 échecs / 15 min par IP
        $failures = LoginHistory::countRecentFailures($ip, 15);
        if ($failures >= 5) {
            $_SESSION['error'] = 'Trop de tentatives. Veuillez réessayer dans 15 minutes.';
            redirect('/user/login');
            return;
        }

        $user = User::findByEmail($email);

        // Message générique — ne jamais révéler si l'email existe ou non
        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            LoginHistory::record($user['id'] ?? null, $email, 'failed', $ip, $ua['browser'], $ua['os']);
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            redirect('/user/login');
            return;
        }

        if ($user['role_slug'] !== 'client') {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            redirect('/user/login');
            return;
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            LoginHistory::record($user['id'], $email, 'failed', $ip, $ua['browser'], $ua['os']);
            $_SESSION['error'] = 'Votre compte est désactivé. Contactez l\'administration.';
            redirect('/user/login');
            return;
        }

        // Étape 1 réussie -> générer et envoyer le code OTP
        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $otp = OtpCode::create($user['id'], $ip, $ua['browser'], $ua['device'] ?? '');
        $deviceInfo = $ua['browser'] . ' · ' . $ua['os'];

        $emailBody = Mailer::otpTemplate($otp['code'], $fullname);
        $emailSent = Mailer::send($email, 'Votre code de sécurité - Espace Client NOVAQYS', $emailBody);

        if (!$emailSent) {
            $_SESSION['error'] = 'Erreur d\'envoi du code de vérification. Veuillez réessayer.';
            redirect('/user/login');
            return;
        }

        Session::set('user_otp_id', $user['id']);
        Session::set('user_otp_email', $email);
        Session::set('user_otp_device', $deviceInfo);
        Session::set('user_otp_expire', $otp['expire_at']);

        redirect('/user/otp');
    }

    /**
     * Étape 2 : affiche le formulaire de saisie du code OTP.
     */
    public function otp(): void
    {
        if (!Session::has('user_otp_id')) {
            redirect('/user/login');
            return;
        }
        view('user.otp', [
            'email' => Session::get('user_otp_email'),
            'device' => Session::get('user_otp_device'),
            'expire' => Session::get('user_otp_expire'),
        ]);
    }

    /**
     * Vérifie le code OTP et ouvre la session utilisateur si valide.
     */
    public function doOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/login');
            return;
        }

        $userId = Session::get('user_otp_id');
        if (!$userId) {
            $_SESSION['error'] = 'Session expirée. Veuillez vous reconnecter.';
            redirect('/user/login');
            return;
        }

        $code = trim($_POST['otp_code'] ?? '');
        if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
            $_SESSION['error'] = 'Veuillez entrer un code valide à 6 chiffres.';
            redirect('/user/otp');
            return;
        }

        $result = OtpCode::verify($userId, $code);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$result['valid']) {
            $_SESSION['error'] = $result['message'];
            redirect('/user/otp');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            redirect('/user/login');
            return;
        }

        // Ouverture de la session utilisateur (même mécanisme que l'ancien
        // AuthController pour rester compatible avec Auth::check()/Auth::user())
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role_id']);

        LoginHistory::record($user['id'], $user['email'], 'success', $ip, $ua['browser'], $ua['os']);
        Database::execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);

        Session::remove('user_otp_id');
        Session::remove('user_otp_email');
        Session::remove('user_otp_device');
        Session::remove('user_otp_expire');

        $_SESSION['success'] = 'Bienvenue ' . ($user['firstname'] ?? '') . ' !';
        redirect('/user/dashboard');
    }

    /**
     * Renvoie un nouveau code OTP (même session en attente de vérification).
     */
    public function resendOtp(): void
    {
        $userId = Session::get('user_otp_id');
        $email = Session::get('user_otp_email');

        if (!$userId || !$email) {
            $_SESSION['error'] = 'Session expirée.';
            redirect('/user/login');
            return;
        }

        $user = User::find($userId);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $otp = OtpCode::create($userId, $ip, $ua['browser'], $ua['device'] ?? '');
        $deviceInfo = $ua['browser'] . ' · ' . $ua['os'];

        $emailBody = Mailer::otpTemplate($otp['code'], $fullname);
        $emailSent = Mailer::send($email, 'Votre nouveau code de sécurité - Espace Client NOVAQYS', $emailBody);

        if ($emailSent) {
            Session::set('user_otp_device', $deviceInfo);
            Session::set('user_otp_expire', $otp['expire_at']);
            $_SESSION['success'] = 'Un nouveau code vous a été envoyé par email.';
        } else {
            $_SESSION['error'] = 'Erreur d\'envoi. Veuillez réessayer.';
        }

        redirect('/user/otp');
    }

    public function register(): void
    {
        if (Auth::check()) {
            redirect('/user/dashboard');
            return;
        }
        view('user.register');
    }

    public function doRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/register');
            return;
        }

        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $company   = trim($_POST['company'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['password_confirm'] ?? '';
        $terms     = $_POST['terms'] ?? null;

        if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($company)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires.';
            redirect('/user/register');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Veuillez entrer une adresse email valide.';
            redirect('/user/register');
            return;
        }
        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères.';
            redirect('/user/register');
            return;
        }
        if ($password !== $confirm) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
            redirect('/user/register');
            return;
        }
        if (!$terms) {
            $_SESSION['error'] = 'Vous devez accepter les conditions d\'utilisation.';
            redirect('/user/register');
            return;
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            redirect('/user/register');
            return;
        }

        $clientRole = Database::fetch("SELECT id FROM roles WHERE slug = 'client' LIMIT 1");
        if (!$clientRole) {
            $_SESSION['error'] = 'Erreur de configuration. Contactez l\'administration.';
            redirect('/user/register');
            return;
        }

        $userId = User::create([
            'role_id'    => $clientRole['id'],
            'firstname'  => $firstname,
            'lastname'   => $lastname,
            'email'      => $email,
            'password'   => Security::hashPassword($password),
            'phone'      => $phone,
            'is_active'  => 1,
        ]);

        if (!$userId) {
            $_SESSION['error'] = 'Erreur lors de la création du compte. Veuillez réessayer.';
            redirect('/user/register');
            return;
        }

        // Note : l'inscription connecte directement (sans OTP), contrairement
        // au login. À harmoniser plus tard si vous voulez le même niveau de
        // vérification à l'inscription.
        Auth::attempt($email, $password);

        $_SESSION['success'] = 'Bienvenue ' . $firstname . ' ! Votre compte a été créé avec succès.';
        redirect('/user/dashboard');
    }

    public function forgot(): void
    {
        view('user.forgot');
    }

    public function doForgot(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/forgot');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Veuillez entrer une adresse email valide.';
            redirect('/user/forgot');
            return;
        }

        $user = User::findByEmail($email);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$user || $user['role_slug'] !== 'client') {
            $_SESSION['success'] = 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.';
            redirect('/user/forgot');
            return;
        }

        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $reset = PasswordReset::create($user['id'], $ip);
        $appUrl = $_ENV['APP_URL'] ?? 'https://novaqys.com';
        $resetLink = $appUrl . '/user/reset?token=' . $reset['token'];

        $emailBody = Mailer::resetTemplate($resetLink, $fullname);
        Mailer::send($email, 'Réinitialisation de votre mot de passe - Espace Client NOVAQYS', $emailBody);

        $_SESSION['success'] = 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.';
        redirect('/user/forgot');
    }

    public function reset(): void
    {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $_SESSION['error'] = 'Lien de réinitialisation invalide.';
            redirect('/user/login');
            return;
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            $_SESSION['error'] = 'Lien de réinitialisation invalide ou expiré.';
            redirect('/user/forgot');
            return;
        }

        view('user.reset', ['token' => $token]);
    }

    public function doReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/login');
            return;
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($token) || empty($password) || empty($confirm)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            redirect('/user/reset?token=' . urlencode($token));
            return;
        }
        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères.';
            redirect('/user/reset?token=' . urlencode($token));
            return;
        }
        if ($password !== $confirm) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
            redirect('/user/reset?token=' . urlencode($token));
            return;
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            $_SESSION['error'] = 'Lien de réinitialisation invalide ou expiré.';
            redirect('/user/forgot');
            return;
        }

        $hash = Security::hashPassword($password);
        Database::execute("UPDATE users SET password = ? WHERE id = ?", [$hash, $reset['user_id']]);
        PasswordReset::markUsed($reset['id']);

        $_SESSION['success'] = 'Votre mot de passe a été réinitialisé avec succès. Connectez-vous.';
        redirect('/user/login');
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
