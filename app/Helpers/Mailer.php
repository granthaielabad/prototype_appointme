<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Simple Mailer helper using PHPMailer and .env configuration.
 * Provides send() method as controllers expect, plus legacy sendMail().
 */
class Mailer
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Basic SMTP config from env; update .env accordingly
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $this->mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 587);

        $this->mail->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com',
            $_ENV['MAIL_FROM_NAME'] ?? 'AppointMe'
        );

        $this->mail->isHTML(true);
    }

    /**
     * Primary method controllers should call.
     */
    public function send(string $to, string $subject, string $body): bool
    {
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Backwards-compatible method.
     */
    public function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return (bool)$this->mail->send();
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
}
