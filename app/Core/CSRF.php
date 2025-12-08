<?php
namespace App\Core;

class CSRF
{
    public static function generate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(24));
        $_SESSION['_csrf_token'] = $token;
        $_SESSION['_csrf_token_time'] = time();
        return $token;
    }

    public static function getToken(): ?string
    {
        Session::start();
        
        // If no token exists, generate one automatically
        if (empty($_SESSION['_csrf_token'])) {
            return self::generate();
        }
        
        return $_SESSION['_csrf_token'];
    }

    public static function verify(string $token, int $maxAgeSeconds = 3600): bool
    {
        Session::start();

        // No token in session or no token provided?
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            return false;
        }

        // Token mismatch?
        if (!hash_equals($_SESSION['_csrf_token'], $token)) {
            return false;
        }

        // Token expired?
        $time = $_SESSION['_csrf_token_time'] ?? 0;
        if ($maxAgeSeconds > 0 && time() - $time > $maxAgeSeconds) {
            self::clear();
            return false;
        }

        // Valid → regenerate token for next request (token rotation)
        self::generate();
        return true;
    }

    private static function clear(): void
    {
        unset($_SESSION['_csrf_token'], $_SESSION['_csrf_token_time']);
    }
}