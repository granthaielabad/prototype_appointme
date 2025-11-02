<?php
namespace App\Core;

class Controller
{
    /**
     * Render a view and optionally wrap with navbar/footer.
     *
     * $view is path under Views (e.g. "Auth/login" or "Home/landing")
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $base = __DIR__ . '/../Views/';
        $viewFile = $base . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$viewFile}";
            return;
        }

        // auth pages that should not show navbar/footer
        $authPages = [
            'Auth/login',
            'Auth/register',
            'Auth/forgot_password',
            'Auth/reset_password'
        ];

        $isAuthPage = false;
        foreach ($authPages as $p) {
            if (strpos($view, $p) !== false) {
                $isAuthPage = true;
                break;
            }
        }

        // Always ensure session available
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Header - if you have a header file include it (optional)
        // if (!$isAuthPage) require $base . 'layouts/header.php';
        if (!$isAuthPage) {
            require_once $base . 'layouts/navbar.php';
        }

        require $viewFile;

        if (!$isAuthPage) {
            require_once $base . 'layouts/footer.php';
        }
    }
}
