<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\PasswordReset;
use App\Models\User;

class ForgotPasswordController extends Controller {
    public function requestForm(): void {
        $this->view('pages/forgot_password_request', ['title'=>'Forgot Password']);
    }

    public function sendReset(): void {
        $email = $_POST['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error','Please enter a valid email','danger');
            header('Location: /forgot-password'); exit;
        }

        $user = (new User())->findByEmail($email);
        if (!$user) {
            // don't reveal whether email exists — still flash success so as not to disclose accounts
            Session::flash('success','If that email exists we sent reset instructions.', 'success');
            header('Location: /forgot-password'); exit;
        }

        $token = bin2hex(random_bytes(24));
        (new PasswordReset())->create(['email'=>$email,'token'=>$token]);

        // TODO: send email with link e.g. /password-reset?token=...
        // For now flash token (DEV) — in prod send via mailer
        Session::flash('success','Reset token generated. (In production we would email it.) Token: '.$token,'success');
        header('Location: /forgot-password');
    }

    public function resetForm(): void {
        $token = $_GET['token'] ?? null;
        $this->view('pages/forgot_password_reset', ['token'=>$token,'title'=>'Reset Password']);
    }

    public function handleReset(): void {
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$token || $password !== $confirm || strlen($password) < 6) {
            Session::flash('error','Invalid input or passwords do not match','danger');
            header('Location: /password-reset?token='.urlencode($token)); exit;
        }
        $pr = new PasswordReset();
        $row = $pr->findByToken($token);
        if (!$row) {
            Session::flash('error','Invalid or expired token','danger');
            header('Location: /forgot-password'); exit;
        }
        $userModel = new User();
        $user = $userModel->findByEmail($row['email']);
        if (!$user) {
            Session::flash('error','User not found','danger');
            header('Location: /forgot-password'); exit;
        }
        // update password (adjust column name accordingly)
        $userModel->update($user['user_id'], ['password'=>password_hash($password,PASSWORD_DEFAULT)]);
        $pr->deleteByEmail($row['email']);
        Session::flash('success','Password updated. Please login.','success');
        header('Location: /login');
    }
}
