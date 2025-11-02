<?php
namespace App\Core;

/**
 * Session management with flash message support.
 * Provides safe helpers for storing, retrieving, and destroying session data.
 */
class Session
{
    /**
     * Start the session if not already active.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a session value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Remove a specific session key.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the session completely.
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
    }

    /**
     * Set a flash message (disappears after next request).
     */
    public static function flash(string $key, string $message, string $type = 'info'): void
    {
        self::start();
        $_SESSION['_flash'][$key] = ['msg' => $message, 'type' => $type];
    }

    /**
     * Retrieve and clear a flash message.
     */
    public static function getFlash(string $key): ?array
    {
        self::start();
        if (isset($_SESSION['_flash'][$key])) {
            $flash = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $flash;
        }
        return null;
    }

    /**
     * Check if a flash message exists.
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
}
