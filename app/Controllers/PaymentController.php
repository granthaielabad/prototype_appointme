<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment; 

class PaymentController extends Controller
{
    public function index(): void
    {
        // previously used requireLogin - use requireRole for consistent behaviour
        \App\Core\Auth::requireRole(3);
        $user = \App\Core\Auth::user();
        if ($user && $user['role_id'] == 1) {
            header('Location: /admin/dashboard');
            exit;
        }

        $payments = (new Payment())->findAll();
        $this->view('pages/payments', ['payments' => $payments]);
    }


    public function show($id): void
    {
        $payment = (new Payment())->find($id);
        $this->view('pages/payment_show', ['payment' => $payment]);
    }
}
