<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Service;
use App\Models\Appointment;

class BookingController extends Controller
{
    public function index(): void
    {
        // only customers
        Auth::requireRole(3);

        $serviceModel = new Service();
        $services = $serviceModel->findAll();

        // recent bookings for the signed-in user (last 5)
        $recentBookings = [];
        $user = Auth::user();
        if ($user) {
            $apptModel = new Appointment();
            $recentBookings = $apptModel->findByUser((int) $user["user_id"], 5); // implement optional limit in model, or slice after
            if ($recentBookings === false) {
                $recentBookings = [];
            }
        }

        // use the PUBLIC LANDING LAYOUT
        $this->renderPublic("Customer/booking", [
            "pageTitle" => "Book Appointment",
            "services" => $services,
            "recentBookings" => $recentBookings,
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(3);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /book");
            exit();
        }

        $serviceId = $_POST["service_id"] ?? null;
        $date = $_POST["date"] ?? null;
        $time = $_POST["time"] ?? null;
        $note = trim($_POST["note"] ?? "");

        if (empty($serviceId) || empty($date) || empty($time)) {
            Session::flash("error", "Service, date and time are required.", "danger");
            header("Location: /book");
            exit();
        }

        $user = Auth::user();
        if (!$user) {
            Session::flash("error", "You must be logged in to book.", "danger");
            header("Location: /login");
            exit();
        }

        $apptModel = new Appointment();
        $apptModel->create([
            "user_id" => $user["user_id"],
            "service_id" => $serviceId,
            "appointment_date" => $date,
            "appointment_time" => $time,
            "note" => $note,
            "status" => "pending",
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        Session::flash(
            "success",
            "Appointment booked successfully. Wait for confirmation.",
            "success",
        );

        // after booking send user to their booking history
        header("Location: /book");
        exit();
    }

    public function myAppointments(): void
    {
        Auth::requireRole(3);

        $user = Auth::user();
        $apptModel = new Appointment();
        $appointments = $apptModel->findByUser($user["user_id"]);

        $this->renderWithLayout("Customer/booking_history", "customer_layout", [
            "pageTitle" => "My Appointments",
            "appointments" => $appointments,
        ]);
    }

    public function cancel(): void
    {
        Auth::requireRole(3);

        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            Session::flash("error", "Invalid appointment ID.", "danger");
            header("Location: /book");
            exit();
        }

        $apptModel = new Appointment();
        $apptModel->update($id, ["status" => "cancelled"]);

        Session::flash("success", "Appointment cancelled.", "success");
        header("Location: /book");
        exit();
    }
}