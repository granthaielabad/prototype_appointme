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
}
