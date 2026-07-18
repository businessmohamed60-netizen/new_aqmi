<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mailer — Envoi d'emails via PHPMailer
 * Utilise la configuration SMTP du serveur cPanel
 */
class Mailer
{
    private static ?PHPMailer $mailer = null;

    private static array $config = [];

    /**
     * Initialise et retourne l'instance PHPMailer configurée
     */
    public static function getInstance(): PHPMailer
    {
        if (self::$mailer === null) {
            self::$config = require BASE_PATH . '/app/Config/mail.php';

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = self::$config['host'];
            $mail->Username   = self::$config['username'];
            $mail->Password   = self::$config['password'];
            $mail->SMTPSecure = self::$config['encryption'];
            $mail->Port       = self::$config['port'];
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(self::$config['from_address'], self::$config['from_name']);
            $mail->isHTML(true);

            self::$mailer = $mail;
        }
        return self::$mailer;
    }

    /**
     * Envoie un email HTML (ou le loggue en mode dev)
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        if (empty(self::$config)) {
            self::$config = require BASE_PATH . '/app/Config/mail.php';
        }

        // Mode log : écrire dans un fichier au lieu d'envoyer
        if (self::$config['driver'] === 'log') {
            $logDir = BASE_PATH . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }
            $logFile = $logDir . '/mail.log';
            $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$to} | SUBJECT: {$subject}\n";
            // Extraire le code OTP du body pour faciliter le test
            if (preg_match('/<div class="code"><span>(\d{6})<\/span><\/div>/', $body, $m)) {
                $logEntry .= "OTP CODE: {$m[1]}\n";
            }
            if (preg_match('/href="([^"]+)">Réinitialiser/', $body, $m)) {
                $logEntry .= "RESET LINK: {$m[1]}\n";
            }
            $logEntry .= str_repeat('-', 60) . "\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            return true;
        }

        try {
            $mail = self::getInstance();
            $mail->clearAddresses();
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $body));
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère le template HTML responsive pour l'email OTP
     */
    public static function otpTemplate(string $otpCode, string $userName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#0a0a0f;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
  .wrapper{width:100%;max-width:600px;margin:0 auto;padding:40px 20px}
  .card{background:#14141f;border:1px solid rgba(255,255,255,0.06);border-radius:20px;padding:48px 40px;text-align:center}
  .logo{width:52px;height:52px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:22px;font-weight:800;color:#fff}
  h1{font-size:20px;font-weight:700;color:#f1f5f9;margin:0 0 8px}
  p{font-size:14px;color:#94a3b8;line-height:1.6;margin:0 0 28px}
  .code{font-size:48px;font-weight:800;letter-spacing:12px;color:#f1f5f9;background:#1a1a2e;border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:24px 32px;display:inline-block;margin:0 auto 28px;font-family:'SF Mono','Cascadia Code',monospace}
  .code span{background:linear-gradient(135deg,#3b82f6,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
  .footer-text{font-size:12px;color:#64748b;line-height:1.5;margin:0}
  .divider{height:1px;background:rgba(255,255,255,0.06);margin:24px 0}
  .alert{font-size:12px;color:#f43f5e;background:rgba(244,63,94,0.08);border:1px solid rgba(244,63,94,0.15);border-radius:8px;padding:12px 16px;margin:0 0 24px}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="logo">A</div>
      <h1>Votre code de sécurité AQMI</h1>
      <p>Bonjour <strong style="color:#f1f5f9">{$userName}</strong>,<br>Utilisez le code suivant pour vous connecter à votre espace AQMI.</p>
      <div class="code"><span>{$otpCode}</span></div>
      <div class="alert">⏱ Ce code expire dans 5 minutes</div>
      <p class="footer-text">Ne partagez jamais ce code avec personne.<br>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>
      <div class="divider"></div>
      <p class="footer-text" style="color:#64748b">NOVAQYS · Automotive Quality & Manufacturing Index</p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    /**
     * Génère le template HTML pour le reset de mot de passe
     */
    public static function resetTemplate(string $resetLink, string $userName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#0a0a0f;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
  .wrapper{width:100%;max-width:600px;margin:0 auto;padding:40px 20px}
  .card{background:#14141f;border:1px solid rgba(255,255,255,0.06);border-radius:20px;padding:48px 40px;text-align:center}
  .logo{width:52px;height:52px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:22px;font-weight:800;color:#fff}
  h1{font-size:20px;font-weight:700;color:#f1f5f9;margin:0 0 8px}
  p{font-size:14px;color:#94a3b8;line-height:1.6;margin:0 0 28px}
  .btn{display:inline-block;background:linear-gradient(135deg,#3b82f6,#8b5cf6);color:#fff;font-size:15px;font-weight:600;padding:14px 32px;border-radius:12px;text-decoration:none;margin:0 auto 28px}
  .btn:hover{background:linear-gradient(135deg,#60a5fa,#a78bfa)}
  .footer-text{font-size:12px;color:#64748b;line-height:1.5;margin:0}
  .divider{height:1px;background:rgba(255,255,255,0.06);margin:24px 0}
  .alert{font-size:12px;color:#f59e0b;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.15);border-radius:8px;padding:12px 16px;margin:0 0 24px}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="logo">A</div>
      <h1>Réinitialisation de mot de passe</h1>
      <p>Bonjour <strong style="color:#f1f5f9">{$userName}</strong>,<br>Vous avez demandé la réinitialisation de votre mot de passe.</p>
      <a href="{$resetLink}" class="btn">Réinitialiser mon mot de passe</a>
      <div class="alert">⏱ Ce lien expire dans 30 minutes</div>
      <p class="footer-text">Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.<br>Votre mot de passe reste inchangé.</p>
      <div class="divider"></div>
      <p class="footer-text" style="color:#64748b">NOVAQYS · Automotive Quality & Manufacturing Index</p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    /**
     * Détecte les infos navigateur et OS
     */
    public static function detectUserAgent(): array
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $browser = 'Unknown';
        $os = 'Unknown';

        if (preg_match('/Firefox\/([\d.]+)/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/Chrome\/([\d.]+)/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/Safari\/([\d.]+)/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/Edge\/([\d.]+)/i', $ua)) $browser = 'Edge';
        elseif (preg_match('/MSIE\s([\d.]+)/i', $ua)) $browser = 'Internet Explorer';

        if (preg_match('/Windows NT ([\d.]+)/i', $ua)) $os = 'Windows';
        elseif (preg_match('/Mac OS X ([\d_]+)/i', $ua)) $os = 'macOS';
        elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
        elseif (preg_match('/Android ([\d.]+)/i', $ua)) $os = 'Android';
        elseif (preg_match('/iPhone|iPad/i', $ua)) $os = 'iOS';

        return [
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $ua,
        ];
    }
}
