<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Invoice;

class InvoiceController extends Controller
{

    // exisiting ? "        $this->renderPublic("pages/invoices", ["invoices" => $invoices, "pageTitle" => "Invoices"]);

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


    // invoice shower 
    public function customerList(): void
{
    Auth::requireRole(3); // customers only
    $user = Auth::user();

    $invoices = (new Invoice())->findByUserWithDetails((int)$user['user_id']); // add this method in the Invoice model if not present

    $this->renderCustomer("Customer/invoices", [
        "pageTitle" => "My Invoices",
        "user"      => $user,
        "invoices"  => $invoices,
    ]);
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
