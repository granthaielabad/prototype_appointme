<?php
namespace App\Core;

class CSRF
{
    public static function generate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(24));
        $_SESSION['_csrf_token'] = $token;        // overwrite always
        $_SESSION['_csrf_token_time'] = time();   // overwrite always
        return $token;
    }


    public static function getToken(): ?string
    {
        Session::start();
        return $_SESSION['_csrf_token'] ?? null;
    }

    public static function verify(string $token, int $maxAgeSeconds = 3600): bool
    {
        Session::start();

        // No token?
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            self::clear();
            return false;
        }

        // Token mismatch?
        if (!hash_equals($_SESSION['_csrf_token'], $token)) {
            self::clear();
            return false;
        }

        // Token expired?
        $time = $_SESSION['_csrf_token_time'] ?? 0;
        if ($maxAgeSeconds > 0 && time() - $time > $maxAgeSeconds) {
            self::clear();
            return false;
        }

        // Valid → rotate token
        self::clear();
        return true;
    }

    private static function clear(): void
    {
        unset($_SESSION['_csrf_token'], $_SESSION['_csrf_token_time']);
    }

}
