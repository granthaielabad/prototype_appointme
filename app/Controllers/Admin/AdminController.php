<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(1);
    }

    /**
     * Render an admin view wrapped in admin layout.
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $adminBase = __DIR__ . "/../../Views/Admin/";
        $layoutFile = __DIR__ . "/../../Views/layouts/admin_layout.php";
        $viewFile = $adminBase . $view . ".php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Admin view not found: {$viewFile}";
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
