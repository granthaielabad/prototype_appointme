<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(3);
        $appointments = (new Appointment())->findByUser(Auth::user()["user_id"]);
        $this->renderPublic("pages/appointments", [
            "appointments" => $appointments,
            "pageTitle" => "Appointments",
        ]);
    }

    public function show(): void
    {
        Auth::requireRole(3);
        $id = $_GET["id"] ?? 0;
        $appointment = (new Appointment())->find($id);
        if (!$appointment) {
            Session::flash("error", "Appointment not found.", "danger");
            header("Location: /my-appointments");
            exit();
        }
        $this->renderPublic("pages/appointment_show", [
            "appointment" => $appointment,
            "pageTitle" => "Appointment Details",
        ]);
    }
}