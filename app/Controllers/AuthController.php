<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Auth;
use App\Helpers\Mailer;
use App\Models\User;
use App\Models\OTP;

class AuthController extends Controller
{
    /** ---------------- LOGIN ---------------- **/
    public function loginForm(): void
    {
        $this->renderAuth("Auth/login");
    }

    public function login(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /login");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission", "danger");
            header("Location:/login");
            exit();
        }

        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if ($email === "" || $password === "") {
            Session::flash("error", "Email and password are required.", "danger");
            header("Location: /login");
            exit();
        }

        $m = new User();
        $user = $m->findByEmail($email);
        if (!$user || !password_verify($password, $user["password"])) {
            Session::flash("error", "Invalid email or password.", "danger");
            header("Location: /login");
            exit();
        }

        if ((int) $user["is_active"] !== 1) {
            Session::flash(
                "error",
                "Account not activated. Please verify your email with the OTP sent during registration.",
                "danger",
            );
            header("Location: /verify-otp?email=" . urlencode($email));
            exit();
        }

        Auth::login($user);

        if ((int) $user["role_id"] === 1) {
            header("Location: /admin/dashboard");
            exit();
        }
        header("Location: /book");
        exit();
    }

    /** ---------------- REGISTER ---------------- **/
    public function registerForm(): void
    {
        $this->renderAuth("Auth/register");
    }

    public function register(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /register");
            exit();
        }
        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission", "danger");
            header("Location:/register");
            exit();
        }

        $first_name = trim($_POST["first_name"] ?? "");
        $last_name = trim($_POST["last_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $contact = trim($_POST["contact_number"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $confirm = trim($_POST["confirm_password"] ?? "");

        if (
            $first_name === "" ||
            $last_name === "" ||
            $email === "" ||
            $password === "" ||
            $confirm === ""
        ) {
            Session::flash("error", "Please fill in all required fields.", "danger");
            header("Location: /register");
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash("error", "Invalid email.", "danger");
            header("Location: /register");
            exit();
        }
        if ($password !== $confirm) {
            Session::flash("error", "Passwords do not match.", "danger");
            header("Location: /register");
            exit();
        }
        if (strlen($password) < 6) {
            Session::flash("error", "Password must be at least 6 characters.", "danger");
            header("Location: /register");
            exit();
        }

        $m = new User();
        if ($m->findByEmail($email)) {
            Session::flash("error", "Email already registered.", "danger");
            header("Location: /register");
            exit();
        }

        // create inactive user
        $userId = $m->createWithRole(
            [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "contact_number" => $contact ?: null,
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "date_created" => date("Y-m-d H:i:s"),
                "is_active" => 0,
            ],
            3,
        );

        // generate OTP
        try {
            $code = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
        } catch (\Exception $e) {
            $code = mt_rand(100000, 999999);
        }

        $otpModel = new OTP();
        $otpModel->createOtp($userId, $code, 600, $email); // valid 10min

        $mailer = new Mailer();
        $body = "<p>Hi {$first_name},</p><p>Your AppointMe verification code is <strong>{$code}</strong>. It expires in 10 minutes.</p>";
        $mailer->send($email, "Verify your AppointMe account", $body);

        Session::flash(
            "success",
            "Account created. A verification code has been sent to your email. Please verify to activate your account.",
            "success",
        );
        header("Location: /verify-otp?email=" . urlencode($email));
        exit();
    }

    /** ---------------- OTP ---------------- **/
    public function verifyOtpForm(): void
    {
        $this->renderAuth("Auth/verify_otp");
    }

    public function verifyOtp(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /verify-otp");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission.", "danger");
            header("Location:/verify-otp");
            exit();
        }

        $email = strtolower(trim($_POST["email"] ?? ""));
        $code = str_pad(trim($_POST["code"] ?? ""), 6, "0", STR_PAD_LEFT);

        if ($email === "" || $code === "") {
            Session::flash("error", "Please enter both email and code.", "danger");
            header("Location: /verify-otp?email=" . urlencode($email));
            exit();
        }

        $otpModel = new OTP();
        $otp = $otpModel->getValidOtp($code, null, $email);

        if (!$otp) {
            Session::flash("error", "Invalid or expired code.", "danger");
            header("Location: /verify-otp?email=" . urlencode($email));
            exit();
        }

        $otpModel->markUsed((int) $otp["otp_id"]);

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $userModel->activate((int) $user["user_id"]);
            Session::flash(
                "success",
                "Your account has been verified! You can now log in.",
                "success",
            );
            header("Location: /login");
            exit();
        }

        Session::flash("error", "User not found.", "danger");
        header("Location: /register");
        exit();
    }

    /** ---------------- FORGOT PASSWORD ---------------- **/
    public function forgotPasswordForm(): void
    {
        $this->renderAuth("Auth/forgot_password");
    }

    public function sendResetLink(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /forgot-password");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission.", "danger");
            header("Location: /forgot-password");
            exit();
        }

        $email = trim($_POST["email"] ?? "");
        if ($email === "") {
            Session::flash("error", "Please enter your email.", "danger");
            header("Location: /forgot-password");
            exit();
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user) {
            Session::flash("error", "No account found with that email.", "danger");
            header("Location: /forgot-password");
            exit();
        }

        $resetToken = bin2hex(random_bytes(16));
        $resetExpires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $userModel->update($user["user_id"], [
            "reset_token" => $resetToken,
            "token_expiry" => $resetExpires,
        ]);

        $resetLink = sprintf(
            "%s/reset-password?token=%s",
            $_ENV["APP_URL"] ?? "http://localhost/prototype",
            $resetToken,
        );
        $subject = "AppointMe Password Reset";
        $body = "
            <p>Hi {$user["first_name"]},</p>
            <p>You requested a password reset. Click below to reset your password:</p>
            <p><a href='{$resetLink}'>Reset Password</a></p>
            <p>This link expires in 1 hour.</p>
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $body);
            Session::flash("success", "Password reset link sent to your email.", "success");
        } catch (\Throwable $e) {
            Session::flash("error", "Failed to send email: " . $e->getMessage(), "danger");
        }

        header("Location: /forgot-password");
        exit();
    }

    public function resetPasswordForm(): void
    {
        $token = $_GET["token"] ?? "";
        $this->renderAuth("Auth/reset_password", ["token" => $token]);
    }

    public function resetPassword(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /reset-password");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission.", "danger");
            header("Location: /reset-password");
            exit();
        }

        $resetToken = $_POST["token"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";

        if ($password === "" || $confirm === "") {
            Session::flash("error", "Please enter and confirm your new password.", "danger");
            header("Location: /reset-password?token=" . urlencode($resetToken));
            exit();
        }

        if ($password !== $confirm) {
            Session::flash("error", "Passwords do not match.", "danger");
            header("Location: /reset-password?token=" . urlencode($resetToken));
            exit();
        }

        if (strlen($password) < 6) {
            Session::flash("error", "Password must be at least 6 characters.", "danger");
            header("Location: /reset-password?token=" . urlencode($resetToken));
            exit();
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($resetToken);

        if (!$user || strtotime($user["token_expiry"]) < time()) {
            Session::flash("error", "Invalid or expired reset link.", "danger");
            header("Location: /forgot-password");
            exit();
        }

        $userModel->update($user["user_id"], [
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "reset_token" => null,
            "token_expiry" => null,
        ]);

        $this->renderAuth("Auth/reset_success");
        exit();
    }

    /** ---------------- LOGOUT ---------------- **/
    public function logout(): void
    {
        Auth::logout();
        header("Location: /");
        exit();
    }
}
