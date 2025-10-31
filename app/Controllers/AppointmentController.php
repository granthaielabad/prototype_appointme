<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index(): void
    {
        \App\Core\Auth::requireRole(3); // only customers
        $user = \App\Core\Auth::user();
        
        $appointments = (new Appointment())->findAll();
        $this->view('pages/appointments', ['appointments' => $appointments]);
    }

    public function show($id): void
    {
        $appointment = (new Appointment())->find($id);
        $this->view('pages/appointment_show', ['appointment' => $appointment]);
    }
}
