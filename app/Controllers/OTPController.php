<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\Mailer;
use App\Models\OTP;
use App\Core\Auth;

class OTPController extends Controller
{
    /**
     * Generate & send OTP to email for given user (or guest email)
     */
    public function send(): void
    {
        // Expect POST: email (optional user_id)
        $email = trim($_POST['email'] ?? '');
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        if ($email === '') {
            Session::flash('error', 'Email is required to send OTP.', 'danger');
            header('Location: /#contact'); exit;
        }

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpModel = new OTP();
        $otpId = $otpModel->createOtp($userId, $code, 300); // 5-min OTP

        $mailer = new Mailer();
        $body = "<p>Your AppointMe verification code is <strong>{$code}</strong>. It expires in 5 minutes.</p>";
        $mailer->send($email, 'Your AppointMe verification code', $body);

        Session::flash('success', 'OTP sent to email (if deliverable).', 'success');
        header('Location: /verify-otp'); exit;
    }

    public function verify(): void
    {
        // Expect POST: code and optionally user_id
        $code = trim($_POST['code'] ?? '');
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        if ($code === '') {
            Session::flash('error', 'Please enter the verification code.', 'danger');
            header('Location: /verify-otp'); exit;
        }

        $otpModel = new OTP();
        $row = $otpModel->getValidOtp($code, $userId);
        if (!$row) {
            Session::flash('error', 'Invalid or expired code.', 'danger');
            header('Location: /verify-otp'); exit;
        }

        $otpModel->markUsed((int)$row['otp_id']);
        // you can now set a verified flag or proceed with registration/login
        Session::flash('success', 'Verification successful!', 'success');
        header('Location: /login'); exit;
    }
}
