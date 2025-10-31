<?php
namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Core\Session;

class AppointmentController extends AdminController
{
    public function index(): void
    {
        $appointments = (new Appointment())->findAllWithUsers();
        $this->render('appointments/index', ['appointments' => $appointments]);
    }

    public function updateStatus(): void
    {
        (new Appointment())->update($_POST['id'], ['status' => $_POST['status']]);
        Session::flash('success', 'Appointment status updated.');
        header('Location: /admin/appointments');
    }
}
