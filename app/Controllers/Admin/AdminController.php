<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

/**
 * Base controller for all admin pages.
 * Ensures only users with role_id = 1 (admin) can access.
 */
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

        $adminBase = __DIR__ . '/../../Views/Admin/';
        $layoutBase = __DIR__ . '/../../Views/layouts/';

        $viewFile = $adminBase . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Admin view not found: {$viewFile}";
            return;
        }

        require_once $layoutBase . 'admin_header.php';
        require_once $layoutBase . 'admin_sidebar.php';
        require $viewFile;
        require_once $layoutBase . 'admin_footer.php';
    }
}
