<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index(): void
    {
        $user = \App\Core\Auth::user();
        if ($user && $user['role_id'] == 1) {
            header('Location: /admin/dashboard');
            exit;
        }

        $invoices = (new Invoice())->findAll();
        $this->view('pages/invoices', ['invoices' => $invoices]);
    }

    public function show($id): void
    {
        $invoice = (new Invoice())->find($id);
        $this->view('pages/invoice_show', ['invoice' => $invoice]);
    }
}
