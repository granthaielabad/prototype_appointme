<?php
namespace App\Core;

abstract class Controller
{
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

        $authPages = [
            'Auth/login',
            'Auth/register',
            'Auth/forgot_password',
            'Auth/reset_password',
            'Auth/verify_otp'
        ];

        $isAuthPage = false;
        foreach ($authPages as $p) {
            if (strpos($view, $p) !== false) {
                $isAuthPage = true;
                break;
            }
        }

        if (!$isAuthPage) {
            require_once $base . 'layouts/navbar.php';
            require_once $base . 'layouts/alerts.php';
        }

        require $viewFile;

        if (!$isAuthPage) {
            require_once $base . 'layouts/footer.php';
        }
    }
}
