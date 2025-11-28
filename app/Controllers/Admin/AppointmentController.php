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
        
        // Get filter from query parameter (default to 'all')
        $filter = $_GET['filter'] ?? 'all';
        
        // Validate filter to prevent SQL injection
        $allowedFilters = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'all';
        }
        
        // Fetch appointments with filter
        $appointments = $appointmentModel->findAllWithUsersFiltered($filter);
        
        // Pass filter to view so dropdown can show current selection
        $this->render("appointments/index", [
            "appointments" => $appointments,
            "currentFilter" => $filter
        ]);
    }

    public function updateStatus(): void
    {
        if (empty($_POST["id"]) || empty($_POST["status"])) {
            Session::flash("error", "Invalid appointment update request.", "danger");
            header("Location: /admin/appointments");
            exit();
        }

        (new Appointment())->update($_POST["id"], ["status" => $_POST["status"]]);
        Session::flash("success", "Appointment status updated successfully.", "success");
        header("Location: /admin/appointments");
        exit();
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/appointments');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Invalid appointment ID.', 'danger');
            header('Location: /admin/appointments');
            exit();
        }

        $data = [];
        if (isset($_POST['appointment_date'])) {
            $data['appointment_date'] = $_POST['appointment_date'];
        }
        if (isset($_POST['appointment_time'])) {
            $data['appointment_time'] = $_POST['appointment_time'];
        }
        if (isset($_POST['status'])) {
            $data['status'] = $_POST['status'];
        }

        // Validate status if present to avoid DB enum truncation warnings
        $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (isset($data['status'])) {
            $data['status'] = trim((string)$data['status']);
            if (!in_array($data['status'], $allowedStatuses, true)) {
                Session::flash('error', 'Invalid status value.', 'danger');
                header('Location: /admin/appointments');
                exit();
            }
        }

        if (!empty($data)) {
            (new Appointment())->update($id, $data);
            Session::flash('success', 'Appointment updated successfully.', 'success');
        } else {
            Session::flash('info', 'No changes submitted.', 'info');
        }

        header('Location: /admin/appointments');
        exit();
    }
}
