<?php
namespace App\Core;

abstract class Controller
{
    protected string $viewBase;
    protected string $layoutBase;

    public function __construct()
    {
        $this->viewBase = __DIR__ . "/../Views/";
        $this->layoutBase = $this->viewBase . "layouts/";
    }

    /**
     * RENDER: Public-facing site using main layout
     */
    protected function renderPublic(string $view, array $data = []): void
    {
        $this->renderWithLayout($view, "main", $data);
    }

    /**
     * RENDER: Auth pages (login/register)
     */
    protected function renderAuth(string $view, array $data = []): void
    {
        if (!str_starts_with($view, "Auth/")) {
            $view = "Auth/" . $view;
        }
        $this->renderWithLayout($view, "auth", $data);
    }

    /**
     * RENDER: Customer pages (/Views/Customer/)
     * Automatically adds folder prefix "Customer/"
     */
    protected function renderCustomer(string $view, array $data = []): void
    {
        if (!str_starts_with($view, "Customer/")) {
            $view = "Customer/" . $view;
        }

        $this->renderWithLayout($view, "customer_layout", $data);
    }

    /**
     * BASE RENDERING ENGINE
     * Injects $content inside layout
     */
    protected function renderWithLayout(string $view, string $layout, array $data = []): void
    {
        extract($data);

        $viewFile = $this->viewBase . $view . ".php";
        $layoutFile = $this->layoutBase . $layout . ".php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<b>View not found:</b> {$viewFile}";
            return;
        }

        if (!file_exists($layoutFile)) {
            http_response_code(500);
            echo "<b>Layout not found:</b> {$layoutFile}";
            return;
        }

        // Capture view output
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render layout with injected $content
        require $layoutFile;
    }
}