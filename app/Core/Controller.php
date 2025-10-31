<?php
namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $base = __DIR__ . '/../Views/';
        $header = $base . 'layouts/header.php';
        $navbar = $base . 'layouts/navbar.php';
        $alerts = $base . 'layouts/alerts.php';
        $footer = $base . 'layouts/footer.php';
        $viewFile = $base . "{$view}.php";

        if (file_exists($viewFile)) {
            require $header;
            require $navbar;
            require $alerts;
            require $viewFile;
            require $footer;
        } else {
            require $base . 'errors/404.php';
        }
    }
}
