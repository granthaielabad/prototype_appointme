<?php
namespace App\Controllers\Admin;

use App\Models\Inquiry;
use App\Core\Session;

/**
 * Admin inquiry management.
 */
class InquiryController extends AdminController
{
    public function index(): void
    {
        $inquiries = (new Inquiry())->findAll();
        $this->render('inquiries/index', ['inquiries' => $inquiries]);
    }

    public function show(): void
    {
        $inquiry = (new Inquiry())->find($_GET['id'] ?? 0);
        if (!$inquiry) {
            Session::flash('error', 'Inquiry not found.', 'danger');
            header('Location: /admin/inquiries');
            return;
        }

        $this->render('inquiries/show', ['inquiry' => $inquiry]);
    }

    public function updateStatus(): void
    {
        if (empty($_POST['id']) || empty($_POST['status'])) {
            Session::flash('error', 'Invalid request.', 'danger');
            header('Location: /admin/inquiries');
            return;
        }

        (new Inquiry())->update($_POST['id'], ['status' => $_POST['status']]);
        Session::flash('success', 'Inquiry status updated successfully.', 'success');
        header('Location: /admin/inquiries');
        exit;
    }
}
