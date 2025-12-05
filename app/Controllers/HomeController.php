<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Service;

class HomeController extends Controller
{
    public function landing(): void
    {
        $user = Auth::user();

        // Redirect logged-in users to their dashboards
        if ($user) {
            if ((int) $user["role_id"] === 1) {
                header("Location: /admin/dashboard");
                exit();
            } elseif ((int) $user["role_id"] === 2) {
                header("Location: /staff/dashboard");
                exit();
            } else {
                header("Location: /my-appointments");
                exit();
            }
        }

        // Load services grouped by category
        $serviceModel = new Service();
        $services = $serviceModel->allGroupedByCategory();

        $this->renderPublic("Home/landing", [
            "services" => $services,
            "pageTitle" => "AppointMe - 8th Avenue",
        ]);
    }
}
