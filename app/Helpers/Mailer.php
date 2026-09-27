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
        $codeDigits = implode('', array_map(
            fn($d) => '<span style="display:inline-block;width:44px;height:56px;line-height:56px;margin:0 4px;background:#181826;border:1px solid rgba(255,255,255,0.08);border-radius:12px;font-family:\'SF Mono\',\'Cascadia Code\',Consolas,monospace;font-size:26px;font-weight:700;color:#f8fafc;">' . $d . '</span>',
            str_split($otpCode)
        ));

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#07070c;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Inter,Roboto,sans-serif}
  .wrapper{width:100%;max-width:560px;margin:0 auto;padding:48px 20px}
  .card{background:linear-gradient(180deg,#14141f 0%,#101019 100%);border:1px solid rgba(255,255,255,0.07);border-radius:24px;padding:44px 36px;text-align:center;box-shadow:0 20px 60px -20px rgba(0,0,0,0.6)}
  .badge{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#60a5fa;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:999px;padding:6px 14px;margin-bottom:24px}
  .logo{width:48px;height:48px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:20px;font-weight:800;color:#fff;box-shadow:0 8px 24px -6px rgba(59,130,246,0.5)}
  h1{font-size:21px;font-weight:700;color:#f8fafc;margin:0 0 10px;letter-spacing:-0.01em}
  p{font-size:14px;color:#94a3b8;line-height:1.65;margin:0 0 30px}
  .code-row{margin:0 auto 20px}
  .expiry{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#fbbf24;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.18);border-radius:10px;padding:10px 16px;margin:0 0 28px}
  .footer-text{font-size:12px;color:#64748b;line-height:1.6;margin:0}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.08),transparent);margin:28px 0 20px}
  .brand{font-size:11px;letter-spacing:0.04em;color:#475569;text-transform:uppercase}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="badge">🔒 Connexion sécurisée</div>
      <div class="logo">N</div>
      <h1>Votre code de vérification</h1>
      <p>Bonjour <strong style="color:#e2e8f0">{$userName}</strong>, saisissez ce code pour accéder à votre espace AQMI.</p>
      <div class="code-row">{$codeDigits}</div>
      <div class="expiry">⏱ Expire dans 5 minutes</div>
      <p class="footer-text">Ne partagez jamais ce code, même avec le support NOVAQYS.<br>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
      <div class="divider"></div>
      <p class="brand">NOVAQYS · Automotive Quality &amp; Manufacturing Index</p>
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
  body{margin:0;padding:0;background-color:#07070c;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Inter,Roboto,sans-serif}
  .wrapper{width:100%;max-width:560px;margin:0 auto;padding:48px 20px}
  .card{background:linear-gradient(180deg,#14141f 0%,#101019 100%);border:1px solid rgba(255,255,255,0.07);border-radius:24px;padding:44px 36px;text-align:center;box-shadow:0 20px 60px -20px rgba(0,0,0,0.6)}
  .badge{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#60a5fa;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:999px;padding:6px 14px;margin-bottom:24px}
  .logo{width:48px;height:48px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:20px;font-weight:800;color:#fff;box-shadow:0 8px 24px -6px rgba(59,130,246,0.5)}
  h1{font-size:21px;font-weight:700;color:#f8fafc;margin:0 0 10px;letter-spacing:-0.01em}
  p{font-size:14px;color:#94a3b8;line-height:1.65;margin:0 0 30px}
  .btn{display:inline-block;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff !important;font-size:15px;font-weight:600;padding:15px 36px;border-radius:12px;text-decoration:none;margin:0 auto 24px;box-shadow:0 10px 30px -8px rgba(59,130,246,0.55)}
  .link-fallback{font-size:11px;color:#475569;word-break:break-all;margin:0 0 24px;padding:0 8px}
  .expiry{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#fbbf24;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.18);border-radius:10px;padding:10px 16px;margin:0 0 28px}
  .footer-text{font-size:12px;color:#64748b;line-height:1.6;margin:0}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.08),transparent);margin:28px 0 20px}
  .brand{font-size:11px;letter-spacing:0.04em;color:#475569;text-transform:uppercase}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="badge">🔑 Sécurité du compte</div>
      <div class="logo">N</div>
      <h1>Réinitialisation de mot de passe</h1>
      <p>Bonjour <strong style="color:#e2e8f0">{$userName}</strong>, vous avez demandé la réinitialisation de votre mot de passe NOVAQYS.</p>
      <a href="{$resetLink}" class="btn">Réinitialiser mon mot de passe</a>
      <p class="link-fallback">Ou copiez ce lien dans votre navigateur :<br>{$resetLink}</p>
      <div class="expiry">⏱ Ce lien expire dans 30 minutes</div>
      <p class="footer-text">Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.<br>Votre mot de passe restera inchangé.</p>
      <div class="divider"></div>
      <p class="brand">NOVAQYS · Automotive Quality &amp; Manufacturing Index</p>
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

        $icons = [
            'Entreprise' => '🏢', 'Contact' => '👤', 'Fonction' => '💼', 'Email' => '✉️',
            'Téléphone' => '📞', 'Pays' => '🌍', 'Taille entreprise' => '📊',
            'Activité' => '⚙️', 'Plateformes intéressées' => '🧩', 'Message' => '💬',
        ];

        $rowsHtml = '';
        foreach ($rows as [$label, $value]) {
            $safeValue = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            $icon = $icons[$label] ?? '•';
            $rowsHtml .= "<tr><td style=\"padding:14px 18px;border-bottom:1px solid rgba(255,255,255,0.05)\"><table style=\"width:100%;border-collapse:collapse\"><tr><td style=\"width:28px;font-size:15px;vertical-align:top\">{$icon}</td><td style=\"vertical-align:top\"><div style=\"font-size:11px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;color:#64748b;margin-bottom:3px\">{$label}</div><div style=\"font-size:14px;font-weight:500;color:#f1f5f9;line-height:1.5\">{$safeValue}</div></td></tr></table></td></tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background-color:#07070c;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Inter,Roboto,sans-serif}
  .wrapper{width:100%;max-width:600px;margin:0 auto;padding:48px 20px}
  .card{background:linear-gradient(180deg,#14141f 0%,#101019 100%);border:1px solid rgba(255,255,255,0.07);border-radius:24px;padding:0;overflow:hidden;box-shadow:0 20px 60px -20px rgba(0,0,0,0.6)}
  .header{padding:36px 40px 28px;text-align:center;background:radial-gradient(120% 100% at 50% 0%,rgba(0,207,232,0.12),transparent 60%)}
  .badge{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#22d3ee;background:rgba(0,207,232,0.1);border:1px solid rgba(0,207,232,0.22);border-radius:999px;padding:6px 14px;margin-bottom:18px}
  .logo{width:48px;height:48px;background:linear-gradient(135deg,#00cfe8,#ff9f43);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:20px;font-weight:800;color:#fff;box-shadow:0 8px 24px -6px rgba(0,207,232,0.4)}
  h1{font-size:20px;font-weight:700;color:#f8fafc;margin:0 0 6px;letter-spacing:-0.01em}
  .subtitle{font-size:13px;color:#94a3b8;margin:0}
  .body-pad{padding:8px 24px 32px}
  table{width:100%;border-collapse:collapse}
  .footer{padding:20px 40px 32px;text-align:center;border-top:1px solid rgba(255,255,255,0.05)}
  .footer-text{font-size:11px;color:#64748b;line-height:1.6;margin:0}
  .brand{font-size:11px;letter-spacing:0.04em;color:#94a3b8;text-transform:uppercase;font-weight:600;margin:0 0 4px}
</style></head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="header">
        <div class="badge">🆕 Nouveau lead</div>
        <div class="logo">N</div>
        <h1>Nouvelle demande de compte</h1>
        <p class="subtitle">Un nouveau prospect souhaite rejoindre l'écosystème NOVAQYS</p>
      </div>
      <div class="body-pad">
        <table>{$rowsHtml}</table>
      </div>
      <div class="footer">
        <p class="brand">NOVAQYS</p>
        <p class="footer-text">Automotive Quality &amp; Manufacturing Index<br>Email généré automatiquement depuis le formulaire de demande de compte</p>
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