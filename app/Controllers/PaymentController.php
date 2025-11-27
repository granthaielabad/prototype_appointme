<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(3);
        $user = Auth::user();
        if ($user && (int) $user["role_id"] === 1) {
            header("Location: /admin/dashboard");
            exit();
        }

        $payments = (new Payment())->findAll();
        $this->renderPublic("pages/payments", ["payments" => $payments, "pageTitle" => "Payments"]);
    }

    public function show($id): void
    {
        $payment = (new Payment())->find($id);
        $this->renderPublic("pages/payment_show", [
            "payment" => $payment,
            "pageTitle" => "Payment",
        ]);
    }
}
