<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Payment;


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

        $user = Auth::user();
        $serviceModel = new Service();
        $service = $serviceModel->find($data['service_id']);

        if (!$service) {
            Session::flash('error' , 'Service not found.', 'danger');
            header('Location: /book');
        }

        // kinuha ko yung function sa model appointment
        $appointmentModel = new Appointment();
        $appointmentId = $appointmentModel->createAppointment([

            'user_id' => Auth::user()['user_id'],
            'service_id' => $data['service_id'],
            'appointment_date' => $data['date'],
            'appointment_time' => $data['time'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ]);

        if (!$appointmentId){
            Session::flash('error', 'Failed to create appointment', 'danger');
            header('Location: /book');
            exit;
        }

            $referenceNumber = 'APT-' . $appointmentId . '-' . time();

            $paymentModel = new Payment();
            $paymentModel->create([
                'appointment_id' =>$appointmentId,
                'amount' => $service['price'],
                'status' => 'pending',

            ]);





        // billing parth
        $billing = [
            'name' => $user['first_name']. ' ' . $user['last_name'],
            'email' => $user['email'],
        ];

       
      $lineItems = [
    [
        'name'     => $service['service_name'],
        'amount'   => (int) round($service['price'] * 100),
        'currency' => 'PHP',
        'quantity' => 1,
    ]
];




     $payload = json_encode([
        'billing'          => $billing,
        'line_items'       => $lineItems,
        'reference_number' => $referenceNumber,
    ]);

    // temporary for testing puposes. Hindi ko ma retrieve sa .env
    $token = "super-secret-string";
    
    $nodeUrl = "http://localhost:4000/api/payments/checkout";

        $ch = curl_init($nodeUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $token"
            ],
             CURLOPT_POSTFIELDS     => $payload,
        ]);

        // checkpoint

 $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        Session::flash('error', 'Unable to start payment. Please try again.', 'danger');
        header('Location: /my-appointments');
        exit;
    }

    $result = json_decode($response, true);

    if (empty($result['success']) || empty($result['checkout_url'])) {
        Session::flash('error', 'Payment session failed to initialize.', 'danger');
        header('Location: /my-appointments');
        exit;
    }

 
    Session::flash('success', 'Appointment booked successfully! Redirecting to payment...', 'success');


    header("Location: " . $result['checkout_url']);
    exit;
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
