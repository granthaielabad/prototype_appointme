<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Helpers\Mailer;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mailer = new Mailer();

$sent = $mailer->sendMail(
    'granthaielpabad@gmail.com',   // change this
    'Test Email from AppointMe',
    '<h3>Hello!</h3><p>This is a test email from your AppointMe project.</p>'
);

echo $sent ? '✅ Email sent successfully!' : '❌ Failed to send email.';
