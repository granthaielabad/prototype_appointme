<?php
namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Inquiry;

/**
 * Admin dashboard displaying system statistics.
 */
class DashboardController extends AdminController
{
    public function index(): void
    {
        $userModel = new User();
        $appointmentModel = new Appointment();
        $serviceModel = new Service();
        $inquiryModel = new Inquiry();

        $data = [
            'pageTitle' => 'Admin Dashboard',
            'totalUsers' => count($userModel->findAll()),
            'totalAppointments' => count($appointmentModel->findAll()),
            'totalServices' => count($serviceModel->findAll()),
            'newInquiries' => count(array_filter(
                $inquiryModel->findAll(),
                fn($inq) => ($inq['status'] ?? '') === 'new'
            )),
        ];

        $this->render('dashboard', $data);
    }
}
