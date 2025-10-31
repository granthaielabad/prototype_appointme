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
            if ($user['role_id'] == 1) {
                header('Location: /admin/dashboard');
                exit;
            } elseif ($user['role_id'] == 2) {
                header('Location: /staff/dashboard');
                exit;
            } else {
                header('Location: /my-appointments');
                exit;
            }
        }
                
        // Load all services grouped by category for the Services section
        $serviceModel = new Service();
        $services = $serviceModel->allGroupedByCategory();

        // Render the single public landing page for unregistered users
        $this->view('Home/landing', ['services' => $services]);
    }

}
