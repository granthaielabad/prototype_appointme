<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(3);
        $user = Auth::user();
        if ($user && (int) $user["role_id"] === 1) {
            header("Location: /admin/dashboard");
            exit();
        }

        $invoices = (new Invoice())->findAll();
        $this->renderPublic("pages/invoices", ["invoices" => $invoices, "pageTitle" => "Invoices"]);
    }

    public function show($id): void
    {
        $invoice = (new Invoice())->find($id);
        $this->renderPublic("pages/invoice_show", [
            "invoice" => $invoice,
            "pageTitle" => "Invoice",
        ]);
    }
}
