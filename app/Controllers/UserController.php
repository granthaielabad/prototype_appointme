<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\User;
use App\Models\Appointment;
use FontLib\Table\Type\head;

class UserController extends Controller
{
    public function index(): void
    {
        $users = (new User())->findAll();
        $this->renderPublic("Home/users", [
            "users" => $users,
            "pageTitle" => "Users"
        ]);
    }

    public function show($id): void
    {
        $user = (new User())->find($id);
        $this->renderPublic("Home/user_show", [
            "user" => $user,
            "pageTitle" => "User"
        ]);
    }

    public function profile(): void
    {
        Auth::requireRole(3);

        // Always fetch a fresh user row (session can be stale)
        $sessionUser = Auth::user();
        $user = $sessionUser ? (new User())->find((int) $sessionUser['user_id']) : null;

        $apptModel = new Appointment();
        $today = date('Y-m-d');
        $todayAppointment = $user ? $apptModel->findForUserOnDate((int) $user['user_id'], $today) : null;

        $this->renderCustomer("Customer/profile", [
            "user" => $user,
            "todayAppointment" => $todayAppointment,
            "pageTitle" => "My Profile"
        ]);
    }

    public function updateProfile(): void
    {
        Auth::requireRole(3);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /profile");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission", "danger");
            header("Location: /profile");
            exit();
        }

        $sessionUser = Auth::user();
        $userId = (int) ($sessionUser['user_id'] ?? 0);
        if ($userId <= 0) {
            Session::flash("error", "User not found.", "danger");
            header("Location: /login");
            exit();
        }

        $updates = [
            "first_name"         => trim($_POST["first_name"] ?? ""),
            "last_name"          => trim($_POST["last_name"] ?? ""),
            "contact_number"     => trim($_POST["contact_number"] ?? ""),
            "bio"                => trim($_POST["bio"] ?? ""),
            "address"            => trim($_POST["address"] ?? ""),
            "emergency_name"     => trim($_POST["emergency_name"] ?? ""),
            "emergency_relation" => trim($_POST["emergency_relation"] ?? ""),
            "emergency_phone"    => trim($_POST["emergency_phone"] ?? "")
        ];

        $m = new User();
        $m->update($userId, $updates);

        // refresh session with latest data
        $freshUser = $m->find($userId);
        if ($freshUser) {
            Session::set('user', $freshUser);
        }

        Session::flash("success", "Profile updated successfully.", "success");
        header("Location: /profile");
        exit();
    }


        public function changePassword(): void
    {
        Auth::requireRole(3);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit();
        }

        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::verify($token)) {
            Session::flash('error', 'Invalid form submission.', 'danger');
            header('Location: /profile');
            exit();
        }

        $current = trim($_POST['current_password'] ?? '');
        $new     = trim($_POST['new_password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($current === '' || $new === '' || $confirm === '') {
            Session::flash('error', 'All password fields are required.', 'danger');
            header('Location: /profile');
            exit();
        }

        if ($new !== $confirm) {
            Session::flash('error', 'New passwords do not match.', 'danger');
            header('Location: /profile');
            exit();
        }

        if (strlen($new) < 8) {
            Session::flash('error', 'New password must be at least 8 characters.', 'danger');
            header('Location: /profile');
            exit();
        }

        $sessionUser = Auth::user();
        $userId = (int) ($sessionUser['user_id'] ?? 0);
        if ($userId <= 0) {
            Session::flash('error', 'User not found.', 'danger');
            header('Location: /login');
            exit();
        }

        $m = new User();
        $user = $m->find($userId);
        if (!$user) {
            Session::flash('error', 'User record missing.', 'danger');
            header('Location: /profile');
            exit();
        }

        if (!password_verify($current, $user['password'])) {
            Session::flash('error', 'Current password is incorrect.', 'danger');
            header('Location: /profile');
            exit();
        }

        $updated = $m->update($userId, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        if (!$updated) {
            Session::flash('error', 'Could not update password. Please try again.', 'danger');
            header('Location: /profile');
            exit();
        }

        // refresh session
        $freshUser = $m->find($userId);
        if ($freshUser) {
            Session::set('user', $freshUser);
        }

        Session::flash('success', 'Password updated successfully.', 'success');
        header('Location: /profile');
        exit();
    }



}
