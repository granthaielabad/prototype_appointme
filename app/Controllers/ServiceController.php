<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(): void
    {
        $user = \App\Core\Auth::user();
        if ($user && $user['role_id'] == 1) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        $services = (new Service())->findAll();
        $this->view('pages/services', ['services' => $services]);
    }

    public function show($id): void
    {
        $service = (new Service())->find($id);
        $this->view('pages/service_show', ['service' => $service]);
    }
}
