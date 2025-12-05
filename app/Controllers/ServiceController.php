<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();

        if ($user && (int) $user["role_id"] === 1) {
            header("Location: /admin/dashboard");
            exit();
        }

        $services = (new Service())->findAll();
        $this->renderPublic("pages/services", [
            "services" => $services,
            "pageTitle" => "Services",
        ]);
    }

    public function show($id): void
    {
        $service = (new Service())->find($id);
        $this->renderPublic("pages/service_show", [
            "service" => $service,
            "pageTitle" => $service["service_name"] ?? "Service",
        ]);
    }
}
