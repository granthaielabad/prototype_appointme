<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Helpers\Mailer;
use App\Models\OTP;

class OTPController extends Controller
{
    private const COOLDOWN_SECONDS = 60; // 1 minute cooldown
    private const MAX_PER_HOUR = 3;      // max 3 per hour

    /**
     * Send OTP to an email (used by registration verify and resend).
     * Returns JSON when request is AJAX (X-Requested-With: XMLHttpRequest).
     */
    public function send(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondRedirect('/', 'Invalid request method.', 'danger');
        }

        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::verify($token)) {
            $this->respondRedirect('/verify-otp', 'Invalid form submission.', 'danger');
        }

        $email = trim($_POST['email'] ?? '');
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respondRedirect('/verify-otp', 'Please enter a valid email.', 'danger');
        }

        $otpModel = new OTP();

        // Check cooldown
        $lastOtp = $otpModel->lastOtpByEmail($email);
        if ($lastOtp) {
            $lastTime = strtotime($lastOtp['created_at']);
            $remaining = self::COOLDOWN_SECONDS - (time() - $lastTime);
            if ($remaining > 0) {
                $this->respondRedirect("/verify-otp?email=" . urlencode($email), "Please wait {$remaining} seconds before requesting another code.", 'danger');
            }
        }

        // Check hourly limit
        $countRecent = $otpModel->countRecentSendsByEmail($email, 3600);
        if ($countRecent >= self::MAX_PER_HOUR) {
            $this->respondRedirect("/verify-otp?email=" . urlencode($email), 'You have reached the maximum number of OTP requests for this hour. Try again later.', 'danger');
        }

        // Create OTP in DB (always create so DB reflects attempt)
        try {
            $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            $code = (string)mt_rand(100000, 999999);
        }

        $otpModel->createOtp($userId, $code, 600, $email);

        // Attempt to send email
        $mailer = new Mailer();
        $body = "
            <p>Your <strong>AppointMe</strong> verification code is:</p>
            <h2 style='text-align:center; letter-spacing:2px;'>{$code}</h2>
            <p>This code will expire in 10 minutes. Do not share it with anyone.</p>
        ";
        $sent = $mailer->send($email, 'AppointMe verification code', $body);

        // If AJAX, respond JSON
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            if ($sent) {
                echo json_encode(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
            } else {
                // Log already performed by Mailer; provide user-friendly message
                echo json_encode(['success' => false, 'message' => 'Failed to send verification code. Please check your email address or contact support.']);
            }
            exit;
        }

        // Non-AJAX flow (flash + redirect)
        if ($sent) {
            Session::flash('success', 'A new verification code has been sent to your email.', 'success');
        } else {
            Session::flash('error', 'Failed to send verification code. Please contact the administrator.', 'danger');
        }
        header('Location: /verify-otp?email=' . urlencode($email));
        exit;
    }

    public function verify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /verify-otp'); exit;
        }

        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::verify($token)) {
            Session::flash('error','Invalid form submission','danger');
            header('Location:/verify-otp'); exit;
        }

        $email = trim($_POST['email'] ?? '');
        $code  = trim($_POST['code'] ?? '');

        if ($email === '' || $code === '') {
            Session::flash('error', 'Please enter both email and code.', 'danger');
            header('Location: /verify-otp?email=' . urlencode($email)); exit;
        }

        $otpModel = new OTP();
        $otp = $otpModel->getValidOtp($code, null, $email);

        if (!$otp) {
            Session::flash('error', 'Invalid or expired code.', 'danger');
            header('Location: /verify-otp?email=' . urlencode($email)); exit;
        }

        $otpModel->markUsed((int)$otp['otp_id']);
        Session::flash('success', 'Verification successful. You may now log in.', 'success');
        header('Location: /login'); exit;
    }

    /**
     * Helper: determine if request is AJAX.
     */
    protected function isAjax(): bool
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }

    /**
     * Helper: respond with flash+redirect OR JSON depending on request type.
     */
    protected function respondRedirect(string $location, string $message, string $level = 'danger'): void
    {
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $message]);
            exit;
        }
        Session::flash($level === 'success' ? 'success' : 'error', $message, $level);
        header('Location: ' . $location);
        exit;
    }
}
