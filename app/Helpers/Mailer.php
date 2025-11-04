<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // load environment safely and trim possible surrounding quotes
        $host = $this->env('MAIL_HOST', 'smtp.gmail.com');
        $username = $this->env('MAIL_USERNAME', '');
        $password = $this->env('MAIL_PASSWORD', '');
        $port = (int) $this->env('MAIL_PORT', 587);
        $encryption = strtolower($this->env('MAIL_ENCRYPTION', 'tls'));
        $fromAddress = $this->env('MAIL_FROM_ADDRESS', 'noreply@example.com');
        $fromName = $this->env('MAIL_FROM_NAME', 'AppointMe');

        // map encryption string to PHPMailer constant
        $encConst = PHPMailer::ENCRYPTION_STARTTLS;
        if ($encryption === 'ssl') {
            $encConst = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $encConst = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // configure mailer
        $this->mail->isSMTP();
        $this->mail->Host       = $host;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $username;
        $this->mail->Password   = $password;
        $this->mail->SMTPSecure = $encConst;
        $this->mail->Port       = $port;

        // allow TLS options (avoid failures with certain local setups)
        $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ];

        // Set debug if app is in debug mode
        $appDebug = strtolower($this->env('APP_DEBUG', 'false'));
        if ($appDebug === 'true' || $appDebug === '1') {
            // enable verbose debug (logs to error_log via Debugoutput)
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF; // keep off by default; enable below if needed
            // To log SMTP debug to error_log, set Debugoutput closure
            $this->mail->Debugoutput = function($str, $level) {
                error_log("[PHPMailer][SMTPDebug][$level] $str");
            };
            // You can set SMTPDebug>0 during deep troubleshooting, e.g. SMTP::DEBUG_SERVER
            // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        $this->mail->setFrom($fromAddress, $fromName);
        $this->mail->isHTML(true);
    }

    /**
     * Helper to read env with trimming quotes
     */
    protected function env(string $key, $default = null)
    {
        $val = $_ENV[$key] ?? getenv($key) ?: $default;
        if (!is_string($val)) return $val;
        // trim whitespace and surrounding single/double quotes
        return trim($val, " \t\n\r\0\x0B\"'");
    }

    /**
     * Public send wrapper.
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        return $this->sendMail($to, $subject, $body, $options);
    }

    /**
     * Actual sending implementation with logging.
     */
    public function sendMail(string $to, string $subject, string $body, array $options = []): bool
    {
        try {
            // clear previous recipients and attachments if any
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            $this->mail->addAddress($to);

            if (!empty($options['replyTo'])) {
                $this->mail->addReplyTo($options['replyTo']);
            }

            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);

            $sent = $this->mail->send();
            if (!$sent) {
                error_log('Mailer: send() returned false with no Exception; lastError: ' . $this->mail->ErrorInfo);
            }
            return (bool)$sent;
        } catch (Exception $e) {
            // log verbose error
            error_log('Mailer Exception: ' . $e->getMessage());
            // also include PHPMailer ErrorInfo if provided
            try {
                error_log('Mailer ErrorInfo: ' . ($this->mail->ErrorInfo ?? 'n/a'));
            } catch (\Throwable $t) {
                // ignore
            }
            return false;
        }
    }
}
