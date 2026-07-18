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
            redirect('/aqmi/dashboard');
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
            redirect('/aqmi/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            redirect('/aqmi/login');
        }

        // Vérifier l'utilisateur
        $user = User::findByEmail($email);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Message générique — ne pas révéler si l'email existe
        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            LoginHistory::record(null, $email, 'failed', $ip, $ua['browser'], $ua['os']);
            Session::setFlash('error', 'Email ou mot de passe incorrect.');
            redirect('/aqmi/login');
        }

        // Vérifier si le compte est actif
        if (!$user['is_active']) {
            LoginHistory::record($user['id'], $email, 'failed', $ip, $ua['browser'], $ua['os']);
            Session::setFlash('error', 'Votre compte est désactivé. Contactez l\'administration.');
            redirect('/aqmi/login');
        }

        // Vérifier le rôle client
        if ($user['role_slug'] !== 'client') {
            Session::setFlash('error', 'Accès non autorisé.');
            redirect('/aqmi/login');
        }

        // Protection force brute : limiter à 5 échecs en 15 min
        $failures = LoginHistory::countRecentFailures($ip, 15);
        if ($failures >= 5) {
            Session::setFlash('error', 'Trop de tentatives. Veuillez réessayer dans 15 minutes.');
            redirect('/aqmi/login');
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
            redirect('/aqmi/login');
        }

        // Stocker l'état en session
        Session::set('otp_user_id', $user['id']);
        Session::set('otp_email', $email);
        Session::set('otp_device', $deviceInfo);
        Session::set('otp_expire', $otp['expire_at']);

        redirect('/aqmi/otp');
    }

    /**
     * Affiche la page de vérification OTP
     */
    public function otp(): void
    {
        if (!Session::has('otp_user_id')) {
            redirect('/aqmi/login');
        }
        view('auth.aqmi-otp', [
            'email' => Session::get('otp_email'),
            'device' => Session::get('otp_device'),
            'expire' => Session::get('otp_expire'),
        ]);
    }

    /**
     * Traite la vérification OTP
     */
    public function doOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/aqmi/login');
        }

        $userId = Session::get('otp_user_id');
        if (!$userId) {
            Session::setFlash('error', 'Session expirée. Veuillez vous reconnecter.');
            redirect('/aqmi/login');
        }

        $code = trim($_POST['otp_code'] ?? '');
        if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
            Session::setFlash('error', 'Veuillez entrer un code valide à 6 chiffres.');
            redirect('/aqmi/otp');
        }

        $result = OtpCode::verify($userId, $code);
        $ua = Mailer::detectUserAgent();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$result['valid']) {
            Session::setFlash('error', $result['message']);
            redirect('/aqmi/otp');
        }

        // OTP validé — connexion réussie
        $user = User::find($userId);
        if (!$user) {
            Session::setFlash('error', 'Utilisateur introuvable.');
            redirect('/aqmi/login');
        }

        // Établir la session utilisateur
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role_id']);

        // Enregistrer la connexion
        LoginHistory::record($user['id'], $user['email'], 'success', $ip, $ua['browser'], $ua['os']);
        Database::execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);

        // Nettoyer la session OTP
        Session::remove('otp_user_id');
        Session::remove('otp_email');
        Session::remove('otp_device');
        Session::remove('otp_expire');

        Session::setFlash('success', 'Bienvenue ' . ($user['firstname'] ?? '') . ' !');
        redirect('/aqmi/dashboard');
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
            redirect('/aqmi/login');
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

        redirect('/aqmi/otp');
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
            redirect('/aqmi/forgot');
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez entrer une adresse email valide.');
            redirect('/aqmi/forgot');
        }

        $user = User::findByEmail($email);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Toujours afficher un message de succès (ne pas révéler si l'email existe)
        if (!$user) {
            Session::setFlash('success', 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.');
            redirect('/aqmi/forgot');
        }

        $fullname = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $reset = PasswordReset::create($user['id'], $ip);
        $appUrl = $_ENV['APP_URL'] ?? 'https://novaqys.com';
        $resetLink = $appUrl . '/aqmi/reset?token=' . $reset['token'];

        $emailBody = Mailer::resetTemplate($resetLink, $fullname);
        Mailer::send($email, 'Réinitialisation de votre mot de passe AQMI', $emailBody);

        Session::setFlash('success', 'Si cette adresse email existe, vous recevrez un lien de réinitialisation.');
        redirect('/aqmi/forgot');
    }

    /**
     * Affiche la page de réinitialisation du mot de passe
     */
    public function reset(): void
    {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            Session::setFlash('error', 'Lien de réinitialisation invalide.');
            redirect('/aqmi/login');
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            Session::setFlash('error', 'Lien de réinitialisation invalide ou expiré.');
            redirect('/aqmi/forgot');
        }

        view('auth.aqmi-reset', ['token' => $token]);
    }

    /**
     * Traite le nouveau mot de passe
     */
    public function doReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/aqmi/login');
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($token) || empty($password) || empty($confirm)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            redirect('/aqmi/reset?token=' . $token);
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            redirect('/aqmi/reset?token=' . $token);
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Les mots de passe ne correspondent pas.');
            redirect('/aqmi/reset?token=' . $token);
        }

        $reset = PasswordReset::verify($token);
        if (!$reset) {
            Session::setFlash('error', 'Lien de réinitialisation invalide ou expiré.');
            redirect('/aqmi/forgot');
        }

        // Mettre à jour le mot de passe
        $hash = Security::hashPassword($password);
        Database::execute("UPDATE users SET password = ? WHERE id = ?", [$hash, $reset['user_id']]);
        PasswordReset::markUsed($reset['id']);

        Session::setFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Connectez-vous.');
        redirect('/aqmi/login');
    }

    /**
     * Affiche le tableau de bord utilisateur
     */
    public function dashboard(): void
    {
        Auth::requireAuth();

        $user = Auth::user();
        if (!$user || $user['role_slug'] !== 'client') {
            Session::setFlash('error', 'Accès non autorisé.');
            redirect('/');
        }

        // Statistiques
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

        // Dernière connexion
        $lastLogin = LoginHistory::getLastSuccess($userId);

        view('auth.aqmi-dashboard', compact('user', 'assessments', 'totalAssessments', 'completedCount', 'lastLogin'));
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
        redirect('/aqmi/login');
    }

    /**
     * Affiche la page d'inscription
     */
    public function register(): void
    {
        if (Auth::check()) {
            redirect('/aqmi/dashboard');
        }
        view('auth.aqmi-register');
    }

    /**
     * Traite l'inscription
     */
    public function doRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/aqmi/register');
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
            redirect('/aqmi/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez entrer une adresse email valide.');
            redirect('/aqmi/register');
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            redirect('/aqmi/register');
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Les mots de passe ne correspondent pas.');
            redirect('/aqmi/register');
        }

        if (!$terms) {
            Session::setFlash('error', 'Vous devez accepter les conditions d\'utilisation.');
            redirect('/aqmi/register');
        }

        // Vérifier si l'email existe déjà
        $existing = User::findByEmail($email);
        if ($existing) {
            Session::setFlash('error', 'Cette adresse email est déjà utilisée.');
            redirect('/aqmi/register');
        }

        // Récupérer le rôle client
        $clientRole = Database::fetch("SELECT id FROM roles WHERE slug = 'client' LIMIT 1");
        if (!$clientRole) {
            Session::setFlash('error', 'Erreur de configuration. Contactez l\'administration.');
            redirect('/aqmi/register');
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
            redirect('/aqmi/register');
        }

        // Connexion automatique
        Auth::attempt($email, $password);

        Session::setFlash('success', 'Bienvenue ' . $firstname . ' ! Votre compte a été créé avec succès.');
        redirect('/aqmi/dashboard');
    }
}