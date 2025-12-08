<?php
namespace App\Core;

class Auth
{
    /**
     * Log in a user by storing their info in the session
     */
    public static function login(array $user): void
    {
        Session::start();
        Session::set('user_id', $user['user_id']);
        Session::set('user_email', $user['email']);
        Session::set('user_first_name', $user['first_name']);
        Session::set('user_last_name', $user['last_name']);
        Session::set('user_role_id', $user['role_id']);
        Session::set('logged_in', true);
    }

    /**
     * Log out the current user
     */
    public static function logout(): void
    {
        Session::start();
        Session::destroy();
    }

    /**
     * Check if a user is logged in
     */
    public static function check(): bool
    {
        Session::start();
        return Session::get('logged_in', false) === true;
    }

    /**
     * Get the currently logged-in user's data
     */
    public static function user(): ?array
    {
        Session::start();
        
        if (!self::check()) {
            return null;
        }

        return [
            'user_id' => Session::get('user_id'),
            'email' => Session::get('user_email'),
            'first_name' => Session::get('user_first_name'),
            'last_name' => Session::get('user_last_name'),
            'role_id' => Session::get('user_role_id'),
        ];
    }

    /**
     * Get the current user's ID
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['user_id'] : null;
    }

    /**
     * Require authentication (redirect to login if not authenticated)
     */
    public static function require(): void
    {
        Session::start();
        
        if (!self::check()) {
            Session::flash('error', 'Please login to access this page.', 'danger');
            
            // ✅ Prevent caching
            Session::preventCache();
            
            header('Location: /login');
            exit();
        }
        
        // ✅ Prevent caching of authenticated pages
        Session::preventCache();
    }

    /**
     * Require a specific role (redirect if user doesn't have it)
     */
    public static function requireRole(int $roleId): void
    {
        // First check if authenticated
        self::require();
        
        $user = self::user();
        
        if (!$user || (int)$user['role_id'] !== $roleId) {
            Session::flash('error', 'Access denied. Insufficient permissions.', 'danger');
            
            // ✅ Prevent caching
            Session::preventCache();
            
            // Redirect based on their actual role
            if ($user && (int)$user['role_id'] === 1) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /book');
            }
            exit();
        }
    }

    /**
     * Require admin role (role_id = 1)
     */
    public static function requireAdmin(): void
    {
        self::requireRole(1);
    }

    /**
     * Require customer role (role_id = 2)
     */
    public static function requireCustomer(): void
    {
        self::requireRole(2);
    }

    /**
     * Check if current user has a specific role
     */
    public static function hasRole(int $roleId): bool
    {
        $user = self::user();
        return $user && (int)$user['role_id'] === $roleId;
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole(1);
    }

    /**
     * Check if current user is customer
     */
    public static function isCustomer(): bool
    {
        return self::hasRole(2);
    }

    /**
     * Redirect if already authenticated (for login/register pages)
     */
    public static function redirectIfAuthenticated(string $defaultPath = '/'): void
    {
        Session::start();
        
        if (self::check()) {
            $user = self::user();
            
            // ✅ Prevent caching
            Session::preventCache();
            
            if ($user && (int)$user['role_id'] === 1) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: ' . $defaultPath);
            }
            exit();
        }
    }
}