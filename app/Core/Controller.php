<?php

namespace App\Core;

class Controller
{
    /*
        Renders a view with optional data
        Automatically includes navbar/footer except for auth pages.
     */
    public function view(string $view, array $data = []): void
    {
                
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            echo "❌ View not found: $viewFile";
            return;
        }
                
        // Determine if this view should hide the navbar/footer
        $authPages = ['Auth/login', 'Auth/register', 'Auth/forgot_password'];
        $isAuthPage = false;
        foreach ($authPages as $authPage) {
            if (str_contains($view, $authPage)) {
                $isAuthPage = true;
                break;
            }
        }
                
        // Include layout conditionally
        if (!$isAuthPage) {
            require __DIR__ . '/../Views/layouts/navbar.php';
        }
                
        require $viewFile;

        if (!$isAuthPage) {
            require __DIR__ . '/../Views/layouts/footer.php';
        }

    }
}
