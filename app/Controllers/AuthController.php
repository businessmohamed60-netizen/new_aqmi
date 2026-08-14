<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use App\Helpers\Mailer;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Models\User;
use App\Models\OtpCode;
use App\Models\LoginHistory;
use App\Models\PasswordReset;

/**
 * AuthController — Système d'authentification sécurisé AQMI
 *
 * Connexion → OTP → Dashboard
 * Mot de passe oublié → Reset
 * Historique des connexions
 */
class AuthController
{
    /**
     * Affiche la page de connexion
     */
    public function login(): void
    {
        // Rediriger si déjà connecté
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && in_array($user['role_slug'], ['admin', 'super_admin', 'manager'], true)) {
                redirect('/admin');
            }
            redirect('/user/dashboard');
        }
        view('auth.aqmi-login');
    }

    /**
     * Traite la tentative de connexion
     * Étape 1 : Vérification email + mot de passe
     * Étape 2 : Envoi du code OTP
     */
    public function doLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            redirect('/login');
        }

        // Vérifier l'utilisateur
        $user = User::findByEmail($email);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Message générique — ne pas révéler si l'email existe
        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            LoginHistory::record(null, $email, 'failed', $ip, $ua['browser'], $ua['os']);
            Session::setFlash('error', 'Email ou mot de passe incorrect.');
            redirect('/login');
        }

        // Vérifier si le compte est actif
        if (!$user['is_active']) {
            LoginHistory::record($user['id'], $email, 'failed', $ip, $ua['browser'], $ua['os']);
            Session::setFlash('error', 'Votre compte est désactivé. Contactez l\'administration.');
            redirect('/login');
        }

        // Protection force brute : limiter à 5 échecs en 15 min
        $failures = LoginHistory::countRecentFailures($ip, 15);
        if ($failures >= 5) {
            Session::setFlash('error', 'Trop de tentatives. Veuillez réessayer dans 15 minutes.');
            redirect('/login');
        }

        // Étape 1 réussie → Générer et envoyer le code OTP
        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $otp = OtpCode::create($user['id'], $ip, $ua['browser'], $ua['device'] ?? '');
        $deviceInfo = $ua['browser'] . ' · ' . $ua['os'];

        // Envoyer l'email OTP
        $emailBody = Mailer::otpTemplate($otp['code'], $fullname);
        $emailSent = Mailer::send($email, 'Votre code de sécurité AQMI', $emailBody);

        if (!$emailSent) {
            Session::setFlash('error', 'Erreur d\'envoi du code de vérification. Veuillez réessayer.');
            redirect('/login');
        }

        // Stocker l'état en session
        Session::set('otp_user_id', $user['id']);
        Session::set('otp_email', $email);
        Session::set('otp_device', $deviceInfo);
        Session::set('otp_expire', $otp['expire_at']);

        redirect('/otp');
    }

    /**
     * Affiche la page de vérification OTP
     */
    public function otp(): void
    {
        if (!Session::has('otp_user_id')) {
            redirect('/login');
        }
        $expireAt = Session::get('otp_expire');
        $remaining = 0;
        if ($expireAt) {
            $row = Database::fetch(
                "SELECT TIMESTAMPDIFF(SECOND, NOW(), ?) AS remaining",
                [$expireAt]
            );
            $remaining = max(0, (int)($row['remaining'] ?? 0));
        }

        view('auth.aqmi-otp', [
            'email' => Session::get('otp_email'),
            'device' => Session::get('otp_device'),
            'expire' => $expireAt,
            'expire_seconds' => $remaining,
        ]);
    }

    /**
     * Traite la vérification OTP
     */
    public function doOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $userId = Session::get('otp_user_id');
        if (!$userId) {
            Session::setFlash('error', 'Session expirée. Veuillez vous reconnecter.');
            redirect('/login');
        }

        $code = trim($_POST['otp_code'] ?? '');
        if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
            Session::setFlash('error', 'Veuillez entrer un code valide à 6 chiffres.');
            redirect('/otp');
        }

        $result = OtpCode::verify($userId, $code);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$result['valid']) {
            Session::setFlash('error', $result['message']);
            redirect('/otp');
        }

        // OTP validé — connexion réussie
        $user = User::find($userId);
        if (!$user) {
            Session::setFlash('error', 'Utilisateur introuvable.');
            redirect('/login');
        }

        // Établir la session utilisateur
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role_id']);
        Session::set('role_slug', $user['role_slug']);

        // Enregistrer la connexion
        LoginHistory::record($user['id'], $user['email'], 'success', $ip, $ua['browser'], $ua['os']);
        Database::execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);

        // Nettoyer la session OTP
        Session::remove('otp_user_id');
        Session::remove('otp_email');
        Session::remove('otp_device');
        Session::remove('otp_expire');

        Session::setFlash('success', 'Bienvenue ' . ($user['firstname'] ?? '') . ' !');

        // Redirection selon le rôle
        if (in_array($user['role_slug'], ['admin', 'super_admin', 'manager'], true)) {
            redirect('/admin');
        } else {
            redirect('/user/dashboard');
        }
    }

    /**
     * Renvoie un nouveau code OTP
     */
    public function resendOtp(): void
    {
        $userId = Session::get('otp_user_id');
        $email = Session::get('otp_email');

        if (!$userId || !$email) {
            Session::setFlash('error', 'Session expirée.');
            redirect('/login');
        }

        $user = User::find($userId);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $otp = OtpCode::create($userId, $ip, $ua['browser'], $ua['device'] ?? '');
        $deviceInfo = $ua['browser'] . ' · ' . $ua['os'];

        $emailBody = Mailer::otpTemplate($otp['code'], $fullname);
        $emailSent = Mailer::send($email, 'Votre nouveau code de sécurité AQMI', $emailBody);

        if ($emailSent) {
            Session::set('otp_device', $deviceInfo);
            Session::set('otp_expire', $otp['expire_at']);
            Session::setFlash('success', 'Un nouveau code vous a été envoyé par email.');
        } else {
            Session::setFlash('error', 'Erreur d\'envoi. Veuillez réessayer.');
        }

        redirect('/otp');
    }

    /**
     * Affiche la page mot de passe oublié
     */
    public function forgot(): void
    {
        view('auth.aqmi-forgot');
    }

    /**
     * Traite la demande de réinitialisation
     */
    public function doForgot(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/forgot');
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez entrer une adresse email valide.');
            redirect('/forgot');
        }

        $user = User::findByEmail($email);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Toujours afficher un message de succès (ne pas révéler si l'email existe)
        if (!$user) {
            Session::setFlash('success', 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.');
            redirect('/forgot');
        }

        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $reset = PasswordReset::create($user['id'], $ip);
        $appUrl = $_ENV['APP_URL'] ?? 'https://novaqys.com';
        $resetLink = $appUrl . '/reset?token=' . $reset['token'];

        $emailBody = Mailer::resetTemplate($resetLink, $fullname);
        Mailer::send($email, 'Réinitialisation de votre mot de passe AQMI', $emailBody);

        Session::setFlash('success', 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.');
        redirect('/forgot');
    }

    /**
     * Affiche la page de réinitialisation du mot de passe
     */
    public function reset(): void
    {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            Session::setFlash('error', 'Lien de réinitialisation invalide.');
            redirect('/login');
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            Session::setFlash('error', 'Lien de réinitialisation invalide ou expiré.');
            redirect('/forgot');
        }

        view('auth.aqmi-reset', ['token' => $token]);
    }

    /**
     * Traite le nouveau mot de passe
     */
    public function doReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($token) || empty($password) || empty($confirm)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            redirect('/reset?token=' . $token);
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            redirect('/reset?token=' . $token);
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Les mots de passe ne correspondent pas.');
            redirect('/reset?token=' . $token);
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            Session::setFlash('error', 'Lien de réinitialisation invalide ou expiré.');
            redirect('/forgot');
        }

        // Mettre à jour le mot de passe
        $hash = Security::hashPassword($password);
        Database::execute("UPDATE users SET password = ? WHERE id = ?", [$hash, $reset['user_id']]);
        PasswordReset::markUsed($reset['id']);

        Session::setFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Connectez-vous.');
        redirect('/login');
    }

    /**
     * Redirige /dashboard (legacy) vers /user/dashboard
     */
    public function dashboardRedirect(): void
    {
        redirect('/user/dashboard');
    }

    /**
     * Affiche le tableau de bord utilisateur (legacy — redirige)
     */
    public function dashboard(): void
    {
        redirect('/user/dashboard');
    }

    /**
     * Affiche l'historique des connexions
     */
    public function history(): void
    {
        Auth::requireAuth();

        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            Session::setFlash('error', 'Accès non autorisé.');
            redirect('/');
        }

        $history = LoginHistory::getByUser($user['id']);

        view('auth.aqmi-history', compact('user', 'history'));
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        Auth::logout();
        Session::setFlash('success', 'Vous avez été déconnecté.');
        redirect('/login');
    }

    /**
     * Affiche la page d'inscription
     */
    public function register(): void
    {
        if (Auth::check()) {
            redirect('/user/dashboard');
        }
        view('auth.aqmi-register');
    }

    /**
     * Traite l'inscription
     */
    public function doRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $company   = trim($_POST['company'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['password_confirm'] ?? '';
        $terms     = $_POST['terms'] ?? null;

        // Validation
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($company)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez entrer une adresse email valide.');
            redirect('/register');
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            redirect('/register');
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Les mots de passe ne correspondent pas.');
            redirect('/register');
        }

        if (!$terms) {
            Session::setFlash('error', 'Vous devez accepter les conditions d\'utilisation.');
            redirect('/register');
        }

        // Vérifier si l'email existe déjà
        $existing = User::findByEmail($email);
        if ($existing) {
            Session::setFlash('error', 'Cette adresse email est déjà utilisée.');
            redirect('/register');
        }

        // Récupérer le rôle client
        $clientRole = Database::fetch("SELECT id FROM roles WHERE slug = 'client' LIMIT 1");
        if (!$clientRole) {
            Session::setFlash('error', 'Erreur de configuration. Contactez l\'administration.');
            redirect('/register');
        }

        // Créer l'utilisateur
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
            Session::setFlash('error', 'Erreur lors de la création du compte. Veuillez réessayer.');
            redirect('/register');
        }

        // Connexion automatique
        Auth::attempt($email, $password);

        Session::setFlash('success', 'Bienvenue ' . $firstname . ' ! Votre compte a été créé avec succès.');
        redirect('/user/dashboard');
    }
}