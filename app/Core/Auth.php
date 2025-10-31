<?php
namespace App\Core;

class Auth
{
    public static function user()
    {
        return Session::get('user');
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please log in first.', 'danger');
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole(int|array $allowedRoles): void
    {
        self::requireLogin();
        $user = self::user();
        $roles = (array)$allowedRoles; // supports single or multiple roles

        if (!in_array($user['role_id'], $roles)) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user']) && ($_SESSION['user']['role_id'] == 1);
    }

    public static function isCustomer(): bool
    {
        return isset($_SESSION['user']) && ($_SESSION['user']['role_id'] == 3);
    }


    public static function logout(): void
    {
        Session::remove('user');
        Session::flash('success', 'You have been logged out.', 'success');
        header('Location: /');
        exit;
    }
}
