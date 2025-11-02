<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        Session::start();
        return Session::get('user', null);
    }

    public static function login(array $user): void
    {
        Session::start();
        unset($user['password']);
        Session::set('user', $user);
    }

    public static function logout(): void
    {
        Session::start();
        Session::remove('user');
    }

    public static function check(): bool
    {
        Session::start();
        return (bool) Session::get('user', false);
    }

    /**
     * Check login only (no role). If not logged in, redirect to /login
     * If $roles provided, enforce role(s) as well.
     */
    public static function requireLogin(int|array|null $roles = null): void
    {
        Session::start();
        if (!self::check()) {
            Session::flash('error', 'Please login to continue', 'danger');
            header('Location: /login');
            exit;
        }
        if ($roles !== null) {
            self::requireRole($roles);
        }
    }

    /**
     * Require specific role(s) to access page.
     * $roles may be int or array of ints.
     */
    public static function requireRole(int|array $roles): void
    {
        Session::start();
        if (!self::check()) {
            Session::flash('error', 'Please login to continue', 'danger');
            header('Location: /login');
            exit;
        }
        $user = self::user();
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array((int)($user['role_id'] ?? 0), $allowed, true)) {
            if (($user['role_id'] ?? 0) == 1) {
                header('Location: /admin/dashboard');
                exit;
            }
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You don't have access to this page.</p>";
            exit;
        }
    }
}
