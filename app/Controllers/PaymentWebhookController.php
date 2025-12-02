<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Service;


class PaymentWebhookController extends Controller
{
    public function handle(): void
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data) {
            http_response_code(400);
            echo "Invalid JSON";
            return;
        }

        $referenceNumber = $data['data']['attributes']['reference_number'] ?? null;

        if (!$referenceNumber) {
            http_response_code(400);
            echo "Missing reference_number";
            return;
        }

        $parts = explode('-', $referenceNumber);

        if (count($parts) < 3 || $parts[0] !== 'APT') {
            http_response_code(400);
            echo "Invalid reference format";
            return;
        }

        $appointmentId = $parts[1];

        $paymongoStatus = $data['data']['attributes']['status'] ?? null;

        $finalStatus = match ($paymongoStatus) {
            'paid'   => 'completed',
            'failed' => 'failed',
            default  => 'failed'
        };

        $paymentModel = new Payment();
        $paymentRow = $paymentModel->findByAppointmentId((int)$appointmentId);


        if ($paymentRow) {
            $paymentModel->update($paymentRow['payment_id'], [
                'status' => $finalStatus
            ]);
        };

        if ($finalStatus === 'completed') {

            $appointmentModel = new Appointment();
            $appointment = $appointmentModel->find((int)$appointmentId);

         if ($appointment) {

              
                // INVOICE
                
                $invoiceModel = new Invoice();

                
                $existingInvoice = $invoiceModel->findByAppointmentId((int)$appointmentId);

                if (!$existingInvoice) {

                   
                    $serviceModel = new Service();
                    $service = $serviceModel->find((int)$appointment["service_id"]);

                    $subtotal = (float)$service["price"];
                    $tax = 0;
                    $total = $subtotal;

                   
                    $invoiceNumber = "INV-" . date("Y") . "-" . str_pad($appointmentId, 6, "0", STR_PAD_LEFT);

                    $invoiceModel->create([
                        "appointment_id" => $appointmentId,
                        "invoice_number" => $invoiceNumber,
                        "subtotal"       => $subtotal,
                        "tax"            => $tax,
                        "total"          => $total
                    ]);
                }

               // SMS

                $userModel = new User();
                $user = $userModel->find((int)$appointment['user_id']);

                if ($user && !empty($user['contact_number'])) {

                    $recipient = $user['contact_number'];

                    $message = "Good Day! {$user['first_name']}, your payment and booking reservation (Ref: {$referenceNumber}) has been confirmed. Thank you !";

                    $smsPayload = json_encode([
                        'recipient' => $recipient,
                        'message'   => $message,
                    ]);

                    $token = "super-secret-string";

                    $ch = curl_init("http://localhost:4000/api/sms");
                    curl_setopt_array($ch, [
                        CURLOPT_POST           => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER     => [
                            'Content-Type: application/json',
                            "Authorization: Bearer $token",
                        ],
                        CURLOPT_POSTFIELDS     => $smsPayload,
                    ]);

                    $smsResponse = curl_exec($ch);
                    $smsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                }
            }
        }

        http_response_code(200);
        echo "Webhook processed";
    }
}
