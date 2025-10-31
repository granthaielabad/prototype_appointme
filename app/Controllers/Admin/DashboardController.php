<?php
namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $data = [
            'totalUsers' => count((new User())->findAll()),
            'totalServices' => count((new Service())->findAll()),
            'totalAppointments' => count((new Appointment())->findAll()),
        ];
        $this->render('dashboard', $data);
    }
}
