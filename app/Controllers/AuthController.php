<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Session;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('pages/login');
    }

    // Handle POST /login
    public function handleLogin(): void
    {
        $data = $_POST;
        $errors = Validator::required(['email', 'password'], $data);

        if ($errors) {
            Session::flash('error', 'All fields are required.', 'danger');
            header('Location: /login');
            exit;
        }

        $user = (new User())->findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Session::flash('error', 'Invalid email or password.', 'danger');
            header('Location: /login');
            exit;
        }

        Session::set('user', $user);

        // Redirect by role
        if ($user['role_id'] == 1) {
            Session::flash('success', 'Welcome back, Admin!', 'success');
            header('Location: /admin/dashboard');
        } elseif ($user['role_id'] == 2) {
            Session::flash('success', 'Welcome back, Staff!', 'success');
            header('Location: /staff/dashboard'); // optional staff route if added later
        } else {
            Session::flash('success', 'Welcome back, ' . $user['first_name'] . '!', 'success');
            header('Location: /home');
        }
        exit;
    }

    public function register(): void
    {
        $this->view('pages/register');
    }

    public function handleRegister(): void {
        $data = $_POST;
        // required fields
        $errors = [];
        foreach (['first_name','last_name','email','password','confirm_password','phone'] as $f) {
            if (in_array($f, ['phone'])) continue; // phone optional? you asked to include contact number; adjust if required
            if (empty(trim($data[$f] ?? ''))) $errors[] = ucfirst(str_replace('_',' ',$f)).' required';
        }
        // confirm password match
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $errors[] = 'Passwords do not match';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
        if ($errors) {
            \App\Core\Session::flash('error', implode('. ',$errors), 'danger');
            header('Location: /register'); exit;
        }

        // create user (make sure user model expects password column name)
        $um = new \App\Models\User();
        // if your DB column is 'password' use this; if 'user_password', change accordingly
        $payload = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'email'=>$data['email'],
            'password'=>password_hash($data['password'], PASSWORD_DEFAULT),
            'phone'=>$data['phone'] ?? null,
            'role_id'=>3
        ];
        $um->create($payload);
        Session::flash('success','Account created, please log in.','success');
        header('Location: /login');
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
