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
        Auth::requireRole(3); // customers only
        $services = (new Service())->findAll();
        $this->view('pages/booking_form', ['services' => $services, 'title' => 'Book Appointment']);
    }

    public function store(): void
    {
        Auth::requireRole(3);
        $data = $_POST;

        if (empty($data['service_id']) || empty($data['date']) || empty($data['time'])) {
            Session::flash('error', 'All fields are required.', 'danger');
            header('Location: /book');
            exit;
        }

        (new Appointment())->create([
            'user_id' => Auth::user()['user_id'],
            'service_id' => $data['service_id'],
            'appointment_date' => $data['date'],
            'appointment_time' => $data['time'],
            'status' => 'pending'
        ]);

        Session::flash('success', 'Appointment booked successfully! Please wait for confirmation.', 'success');
        header('Location: /my-appointments');
    }

    public function myAppointments(): void
    {
        Auth::requireRole(3);
        $userId = Auth::user()['user_id'];
        $appointments = (new Appointment())->findByUser($userId);
        $this->view('pages/my-appointments', ['appointments' => $appointments]);
    }

    public function cancel(): void
    {
        Auth::requireRole(3);
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new Appointment())->update($id, ['status' => 'cancelled']);
            Session::flash('success', 'Appointment cancelled successfully.', 'success');
        }
        header('Location: /my-appointments');
    }
}
