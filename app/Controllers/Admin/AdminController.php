<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        \App\Core\Session::start();
        Auth::requireLogin();

        $user = Auth::user();
        if (($user['role_id'] ?? 0) != 1) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>Admins only.</p>";
            exit;
        }
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $base = __DIR__ . '/../../Views/Admin/';
        require $base . '../layouts/admin_header.php';
        require $base . '../layouts/admin_sidebar.php';
        require $base . $view . '.php';
        require $base . '../layouts/admin_footer.php';
    }
}
