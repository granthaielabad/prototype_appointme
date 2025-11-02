<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth;
use App\Models\User;
use App\Helpers\Mailer;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('Auth/login');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            Session::flash('error', 'Email and password are required.', 'danger');
            header('Location: /login');
            exit;
        }

        $m = new User();
        $user = $m->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.', 'danger');
            header('Location: /login');
            exit;
        }

        Auth::login($user);

        // redirect by role
        if ($user['role_id'] == 1) {
            header('Location: /admin/dashboard');
            exit;
        }

        header('Location: /my-appointments');
        exit;
    }

    public function registerForm(): void
    {
        $this->view('Auth/register');
    }

    public function register(): void
    {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contact = trim($_POST['contact_number'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $confirm === '') {
            Session::flash('error', 'Please fill in all required fields.', 'danger');
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Invalid email.', 'danger');
            header('Location: /register');
            exit;
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.', 'danger');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters.', 'danger');
            header('Location: /register');
            exit;
        }

        $m = new User();
        if ($m->findByEmail($email)) {
            Session::flash('error', 'Email already registered.', 'danger');
            header('Location: /register');
            exit;
        }

        $m->create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'contact_number' => $contact ?: null,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        Session::flash('success', 'Account created. Please login.', 'success');
        header('Location: /login');
        exit;
    }

    public function forgotPasswordForm(): void
    {
        $this->view('Auth/forgot_password');
    }

    public function sendResetLink(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            Session::flash('error', 'Please provide an email.', 'danger');
            header('Location: /forgot-password');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Invalid email.', 'danger');
            header('Location: /forgot-password');
            exit;
        }

        $m = new User();
        $user = $m->findByEmail($email);
        // do not reveal whether email exists
        if ($user) {
            $token = bin2hex(random_bytes(24));
            $m->saveResetToken($user['user_id'], $token);

            $link = ($_ENV['APP_URL'] ?? 'http://localhost:8000') . '/reset-password?token=' . urlencode($token);

            $mailer = new Mailer();
            $body = "Hi {$user['first_name']},<br><br>Click <a href=\"{$link}\">this link</a> to reset your password. The link expires in 1 hour.";
            $mailer->send($user['email'], 'Password Reset', $body);
        }

        Session::flash('success', 'If that email exists, a reset link was sent.', 'success');
        header('Location: /forgot-password');
        exit;
    }

    public function resetPasswordForm(): void
    {
        $this->view('Auth/reset_password', ['token' => $_GET['token'] ?? null]);
    }

    public function resetPassword(): void
    {
        $token = trim($_POST['token'] ?? '');
        $pw = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($token === '' || $pw === '' || $confirm === '') {
            Session::flash('error', 'Please fill all fields.', 'danger');
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }
        if ($pw !== $confirm) {
            Session::flash('error', 'Passwords do not match.', 'danger');
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }

        $m = new User();
        $user = $m->findByResetToken($token);
        if (!$user) {
            Session::flash('error', 'Invalid or expired token.', 'danger');
            header('Location: /forgot-password');
            exit;
        }

        $m->update($user['user_id'], [
            'password' => password_hash($pw, PASSWORD_DEFAULT),
            'reset_token' => null,
            'token_expiry' => null
        ]);

        Session::flash('success', 'Password reset. Please login.', 'success');
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}
