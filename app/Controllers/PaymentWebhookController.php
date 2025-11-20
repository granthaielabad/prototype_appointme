<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;

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
        }

        http_response_code(200);
        echo "Webhook processed";



    
    }
}
