<?php
namespace App\Helpers;

/**
 * Mailer — Envoi d'emails
 * Utilise PHPMailer si disponible (via vendor/), sinon fallback natif PHP mail()
 * En mode dev (MAIL_HOST=smtp.example.com), les emails sont loggués dans un fichier
 */
class Mailer
{
    private static array $config = [];

    /**
     * Charge la configuration mail
     */
    private static function loadConfig(): void
    {
        if (empty(self::$config)) {
            self::$config = require BASE_PATH . '/app/Config/mail.php';
        }
    }

    /**
     * Envoie un email HTML
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        self::loadConfig();

        // Mode log : écrire dans un fichier au lieu d'envoyer
        if (self::$config['driver'] === 'log') {
            $logDir = BASE_PATH . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }
            $logFile = $logDir . '/mail.log';
            $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$to} | SUBJECT: {$subject}\n";
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

        // Essayer PHPMailer si disponible
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return self::sendWithPHPMailer($to, $subject, $body);
        }

        // Fallback : fonction mail() native de PHP
        return self::sendWithNativeMail($to, $subject, $body);
    }

    /**
     * Envoie via PHPMailer (si le package est installé)
     */
    private static function sendWithPHPMailer(string $to, string $subject, string $body): bool
    {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
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
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $body));
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log("PHPMailer error: " . $e->getMessage());
            return self::sendWithNativeMail($to, $subject, $body);
        }
    }

    /**
     * Envoie via la fonction mail() native de PHP (fallback)
     */
    private static function sendWithNativeMail(string $to, string $subject, string $body): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::$config['from_name'] . ' <' . self::$config['from_address'] . '>',
        ];

        $sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));

        if (!$sent) {
            error_log("Native mail() failed to: {$to} subject: {$subject}");
        }

        return $sent;
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
  @media (prefers-color-scheme: dark){
    body,.bg{background-color:#05050a !important}
  }
  body{margin:0;padding:0;background-color:#f4f5fb;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;-webkit-font-smoothing:antialiased}
  .bg{background:radial-gradient(circle at top,#171a35 0%,#0a0b16 55%,#05050a 100%);padding:56px 0}
  .wrapper{width:100%;max-width:520px;margin:0 auto;padding:0 20px}
  .card{background:linear-gradient(180deg,#171a2e 0%,#12131f 100%);border:1px solid rgba(255,255,255,0.08);border-radius:24px;padding:48px 40px;text-align:center;box-shadow:0 24px 60px -20px rgba(99,102,241,0.35)}
  .logo{display:inline-block;height:52px;line-height:52px;padding:0 22px;background:linear-gradient(135deg,#818cf8,#6366f1 45%,#a855f7);border-radius:14px;margin:0 0 28px;box-shadow:0 8px 24px -6px rgba(99,102,241,0.6);mso-line-height-rule:exactly}
  .logo-mark{display:inline-block;width:8px;height:8px;border-radius:50%;background:#fff;opacity:0.9;margin-right:8px;vertical-align:middle}
  .logo-text{display:inline-block;vertical-align:middle;font-size:19px;font-weight:800;letter-spacing:1.5px;color:#fff;line-height:1;font-family:'Inter',-apple-system,sans-serif}
  .eyebrow{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#a5b4fc;margin:0 0 12px}
  h1{font-size:22px;font-weight:700;color:#f8fafc;margin:0 0 10px;letter-spacing:-0.3px}
  p{font-size:14px;color:#9ca3af;line-height:1.7;margin:0 0 30px}
  .code{font-size:42px;font-weight:800;letter-spacing:14px;color:#fff;background:linear-gradient(180deg,#1c1f36,#171a2b);border:1px solid rgba(129,140,248,0.35);border-radius:18px;padding:22px 20px;margin:0 auto 24px;font-family:'SF Mono','Cascadia Code',ui-monospace,monospace;text-indent:14px}
  .code span{background:linear-gradient(135deg,#a5b4fc,#818cf8 50%,#c084fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
  .timer{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#fbbf24;background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.25);border-radius:999px;padding:8px 18px;margin:0 0 28px}
  .footer-text{font-size:12px;color:#6b7280;line-height:1.6;margin:0}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.12),transparent);margin:28px 0 20px}
  .brand{font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#818cf8;font-weight:700;margin:0 0 4px}
</style></head>
<body>
  <div class="bg">
    <div class="wrapper">
      <div class="card">
        <div class="logo"><span class="logo-mark"></span><span class="logo-text">AQMI</span></div>
        <p class="eyebrow">Secure Sign-in</p>
        <h1>Your verification code</h1>
        <p>Hi <strong style="color:#e5e7eb">{$userName}</strong>, use the code below to sign in to your NOVAQYS workspace.</p>
        <div class="code"><span>{$otpCode}</span></div>
        <div class="timer">⏱ Expires in 5 minutes</div>
        <p class="footer-text">Never share this code with anyone — our team will never ask for it.<br>Didn't request this? You can safely ignore this email.</p>
        <div class="divider"></div>
        <p class="brand">NOVAQYS</p>
        <p class="footer-text">Automotive Quality & Manufacturing Index</p>
      </div>
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
  .logo{width:52px;height:52px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:22px;font-weight:800;color:#fff}
  h1{font-size:20px;font-weight:700;color:#f1f5f9;margin:0 0 8px}
  p{font-size:14px;color:#94a3b8;line-height:1.6;margin:0 0 28px}
  .btn{display:inline-block;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:15px;font-weight:600;padding:14px 32px;border-radius:12px;text-decoration:none;margin:0 auto 28px}
  .btn:hover{background:linear-gradient(135deg,#60a5fa,#3b82f6)}
  .footer-text{font-size:12px;color:#64748b;line-height:1.5;margin:0}
  .divider{height:1px;background:rgba(255,255,255,0.06);margin:24px 0}
  .alert{font-size:12px;color:#f59e0b;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.15);border-radius:8px;padding:12px 16px;margin:0 0 24px}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="logo">N</div>
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
     * Génère le template HTML pour une demande de compte
     */
    public static function accountRequestTemplate(array $data, string $platformsList): string
    {
        $rows = [
            ['Entreprise', $data['company']],
            ['Contact', $data['fullname']],
            ['Fonction', $data['job_title']],
            ['Email', $data['email']],
            ['Téléphone', $data['phone']],
            ['Pays', $data['country']],
            ['Taille entreprise', $data['company_size']],
            ['Activité', $data['activity']],
            ['Plateformes intéressées', $platformsList ?: 'Non précisé'],
            ['Message', $data['message'] ?: 'Aucun message'],
        ];

        $rowsHtml = '';
        foreach ($rows as [$label, $value]) {
            $safeValue = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            $rowsHtml .= "<tr><td style=\"padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.06);color:#94a3b8;font-size:13px;white-space:nowrap\">{$label}</td><td style=\"padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.06);color:#f1f5f9;font-size:13px;font-weight:500\">{$safeValue}</td></tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#0a0a0f;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
  .wrapper{width:100%;max-width:600px;margin:0 auto;padding:40px 20px}
  .card{background:#14141f;border:1px solid rgba(255,255,255,0.06);border-radius:20px;padding:40px}
  .logo{width:52px;height:52px;background:linear-gradient(135deg,#00cfe8,#ff9f43);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:22px;font-weight:800;color:#fff}
  h1{font-size:20px;font-weight:700;color:#f1f5f9;margin:0 0 8px;text-align:center}
  .subtitle{font-size:13px;color:#94a3b8;text-align:center;margin:0 0 28px}
  table{width:100%;border-collapse:collapse}
  .footer-text{font-size:12px;color:#64748b;line-height:1.5;margin:0;text-align:center}
  .divider{height:1px;background:rgba(255,255,255,0.06);margin:24px 0}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="logo">N</div>
      <h1>Nouvelle demande de compte</h1>
      <p class="subtitle">Un nouveau prospect souhaite rejoindre l'écosystème NOVAQYS</p>
      <table>{$rowsHtml}</table>
      <div class="divider"></div>
      <p class="footer-text">NOVAQYS · Automotive Quality & Manufacturing Index<br>Email généré automatiquement depuis le formulaire de demande de compte</p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    /**
     * Génère le template HTML pour une notification de connexion
     */
    public static function loginNotificationTemplate(string $userName, string $deviceInfo, string $ipAddress, string $loginDate): string
    {
        $safeName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
        $safeDevice = htmlspecialchars($deviceInfo, ENT_QUOTES, 'UTF-8');
        $safeIp = htmlspecialchars($ipAddress, ENT_QUOTES, 'UTF-8');
        $safeDate = htmlspecialchars($loginDate, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#f4f5fb;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;-webkit-font-smoothing:antialiased}
  .bg{background:radial-gradient(circle at top,#171a35 0%,#0a0b16 55%,#05050a 100%);padding:56px 0}
  .wrapper{width:100%;max-width:520px;margin:0 auto;padding:0 20px}
  .card{background:linear-gradient(180deg,#171a2e 0%,#12131f 100%);border:1px solid rgba(255,255,255,0.08);border-radius:24px;padding:48px 40px;text-align:center;box-shadow:0 24px 60px -20px rgba(99,102,241,0.35)}
  .logo{display:inline-block;height:52px;line-height:52px;padding:0 22px;background:linear-gradient(135deg,#818cf8,#6366f1 45%,#a855f7);border-radius:14px;margin:0 0 28px;box-shadow:0 8px 24px -6px rgba(99,102,241,0.6);mso-line-height-rule:exactly}
  .logo-mark{display:inline-block;width:8px;height:8px;border-radius:50%;background:#fff;opacity:0.9;margin-right:8px;vertical-align:middle}
  .logo-text{display:inline-block;vertical-align:middle;font-size:19px;font-weight:800;letter-spacing:1.5px;color:#fff;line-height:1;font-family:'Inter',-apple-system,sans-serif}
  .eyebrow{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#a5b4fc;margin:0 0 12px}
  h1{font-size:22px;font-weight:700;color:#f8fafc;margin:0 0 10px;letter-spacing:-0.3px}
  p{font-size:14px;color:#9ca3af;line-height:1.7;margin:0 0 20px}
  .info-box{background:linear-gradient(180deg,#1c1f36,#171a2b);border:1px solid rgba(129,140,248,0.25);border-radius:16px;padding:24px 20px;margin:0 auto 24px;text-align:left}
  .info-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.06)}
  .info-row:last-child{border-bottom:none}
  .info-label{font-size:12px;color:#818cf8;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
  .info-value{font-size:14px;color:#f1f5f9;font-weight:500}
  .footer-text{font-size:12px;color:#6b7280;line-height:1.6;margin:0}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.12),transparent);margin:28px 0 20px}
  .brand{font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#818cf8;font-weight:700;margin:0 0 4px}
  .alert-icon{display:inline-block;width:48px;height:48px;border-radius:50%;background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);line-height:48px;font-size:24px;margin:0 0 16px}
</style></head>
<body>
  <div class="bg">
    <div class="wrapper">
      <div class="card">
        <div class="logo"><span class="logo-mark"></span><span class="logo-text">AQMI</span></div>
        <div class="alert-icon">✓</div>
        <p class="eyebrow">New Sign-in</p>
        <h1>Connexion réussie</h1>
        <p>Bonjour <strong style="color:#e5e7eb">{$safeName}</strong>, une nouvelle connexion à votre compte NOVAQYS vient d'avoir lieu.</p>
        <div class="info-box">
          <div class="info-row"><span class="info-label">Appareil</span><span class="info-value">{$safeDevice}</span></div>
          <div class="info-row"><span class="info-label">Adresse IP</span><span class="info-value">{$safeIp}</span></div>
          <div class="info-row"><span class="info-label">Date</span><span class="info-value">{$safeDate}</span></div>
        </div>
        <p class="footer-text">Si vous êtes à l'origine de cette connexion, aucune action n'est requise.<br>Si vous ne reconnaissez pas cette activité, contactez immédiatement l'administrateur.</p>
        <div class="divider"></div>
        <p class="brand">NOVAQYS</p>
        <p class="footer-text">Automotive Quality & Manufacturing Index</p>
      </div>
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