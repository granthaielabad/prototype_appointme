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
        // only customers
        
        Auth::requireRole(2);
        
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
        Auth::requireRole(2);

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

        $serviceModel = new Service();
        $service = $serviceModel->find($serviceId);

        if (!$service) {
            Session::flash("error", "Service not found. ", "danger");
            header("Location: /book");
            exit();
        }

        // combine
        $apptModel = new Appointment();
        $appointmentId = $apptModel->createAppointment([
            "user_id"          => $user["user_id"],
            "service_id"       => $serviceId,
            "appointment_date" => $date,
            "appointment_time" => $time,
            "notes"             => $note,
            "status"           => "pending",
            "created_at"       => date("Y-m-d H:i:s"),
        ]);

         if (!$appointmentId) {
            Session::flash("error", "Failed to create appointment.", "danger");
            header("Location: /book");
            exit();

         }

          $referenceNumber = 'APT-' . $appointmentId . '-' . time();

          $paymentModel = new Payment();
            $paymentModel->create([
                'appointment_id' =>$appointmentId,
                'amount' => $service['price'],
                'status' => 'pending',

            ]);

              // billing part
        $billing = [
            'name' => $user['first_name']. ' ' . $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['contact_number']
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

        //temporary lipat sa env
        $token   = "super-secret-string";
        $nodeUrl = "http://localhost:4000/api/payments/checkout";

        $ch = curl_init($nodeUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                "Authorization: Bearer $token",
            ],
            CURLOPT_POSTFIELDS     => $payload,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            Session::flash('error', 'Unable to start payment. Please try again.', 'danger');
            header('Location: /my-appointments');
            exit();
        }

        $result = json_decode($response, true);

        if (empty($result['success']) || empty($result['checkout_url'])) {
            Session::flash('error', 'Payment session failed to initialize.', 'danger');
            header('Location: /my-appointments');
            exit();
        }

      
        $_SESSION['checkout_url'] = $result['checkout_url'];

        Session::flash(
            'success',
            'Appointment booked successfully! Please proceed to the payment.',
            'success'
        );

        header("Location: /payment-qr");
        exit();
    }



    public function paymentQr(): void
    {
        Auth::requireRole(2);

        if (empty($_SESSION['checkout_url'])) {
            Session::flash('error', 'No active paymentsession found. Please try booking again.', 'danger' );
            header('Location: /my-appointments' );
            exit;
        }

        $checkoutUrl = $_SESSION['checkout_url'];
        

         $this->renderCustomer('payment_qr', [
             'pageTitle' => 'Payment QR',
             'checkoutUrl' => $checkoutUrl,
    ]);

    }

    // GREY OUT THE TAKEN SLOTS 
    public function takenSlots(): void
{
    Auth::requireRole(2);

    $date = $_GET['date'] ?? null;
    if (!$date) {
        http_response_code(400);
        echo json_encode(['error' => 'date is required']);
        return;
    }

    header('Content-Type: application/json');

    $apptModel = new Appointment();
    $slots = $apptModel->getTakenSlotsForDate($date);

    echo json_encode(['slots' => $slots]);
}



    public function myAppointments(): void
    {
        Auth::requireRole(2);

        $user = Auth::user();
        $apptModel = new Appointment();
        $appointments = $apptModel->findByUser($user["user_id"]);

        $this->renderWithLayout("Customer/booking_history", "customer_layout", [
            "pageTitle" => "My Appointments",
            "appointments" => $appointments,
            "user" => $user,
        ]);
    }

    // nagagamit po ba ito? 
    public function cancel(): void
    {
        Auth::requireRole(2);

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
    
   public function cancelFromHistory(): void
                {
            Auth::requireRole(2);

            $id = (int) ($_GET["id"] ?? 0);
            if ($id <= 0) {
                Session::flash("error", "Invalid appointment ID.");
                header("Location: /my-appointments");
                exit();
            }

            $apptModel = new Appointment();
            $appointment = $apptModel->find($id);

            if (!$appointment) {
                Session::flash("error", "Appointment not found.");
                header("Location: /my-appointments");
                exit();
            }

            if ($appointment["user_id"] !== Auth::user()["user_id"]) {
                Session::flash("error", "Unauthorized action.");
                header("Location: /my-appointments");
                exit();
            }

            if ($appointment["status"] === "cancelled") {
                Session::flash("warning", "This appointment is already cancelled.");
                header("Location: /my-appointments");
                exit();
            }

            $apptModel->update($id, [
                "status" => "cancelled",
                "updated_at" => date("Y-m-d H:i:s"),
            ]);

            Session::flash("success", "Appointment cancelled successfully.");
            header("Location: /my-appointments");
        }







}