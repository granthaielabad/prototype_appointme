<?php
namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Core\Session;

/**
 * Admin appointment management.
 */
class AppointmentController extends AdminController
{
    public function index(): void
    {
        $appointmentModel = new Appointment();
        $appointments = method_exists($appointmentModel, 'findAllWithUsers')
            ? $appointmentModel->findAllWithUsers()
            : $appointmentModel->findAll();

        $this->render('appointments/index', ['appointments' => $appointments]);
    }

    public function updateStatus(): void
    {
        if (empty($_POST['id']) || empty($_POST['status'])) {
            Session::flash('error', 'Invalid appointment update request.', 'danger');
            header('Location: /admin/appointments');
            exit;
        }

        (new Appointment())->update($_POST['id'], ['status' => $_POST['status']]);
        Session::flash('success', 'Appointment status updated successfully.', 'success');
        header('Location: /admin/appointments');
        exit;
    }
}
