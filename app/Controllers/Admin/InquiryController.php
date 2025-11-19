<?php
namespace App\Controllers\Admin;

use App\Models\Inquiry;
use App\Core\Session;

/**
 * Admin Inquiry Management Controller
 */
class InquiryController extends AdminController
{
    protected Inquiry $inquiryModel;

    public function __construct()
    {
        parent::__construct();
        $this->inquiryModel = new Inquiry();
    }

    /**
     * Display all inquiries in descending order by creation date.
     */
    public function index(): void
    {
        try {
            $inquiries = $this->inquiryModel->getAll();
            $this->render('inquiries/index', ['inquiries' => $inquiries]);
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to load inquiries: ' . $e->getMessage(), 'danger');
            $this->render('inquiries/index', ['inquiries' => []]);
        }
    }

    /**
     * Display a single inquiry.
     */
    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            Session::flash('error', 'Invalid inquiry ID.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }

        $inquiry = $this->inquiryModel->find($id);
        if (!$inquiry) {
            Session::flash('error', 'Inquiry not found.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }

        $this->render('inquiries/show', ['inquiry' => $inquiry]);
    }

    /**
     * Update inquiry status (new, replied, archived).
     */
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::flash('error', 'Invalid request method.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = trim($_POST['status'] ?? '');

        if ($id <= 0 || $status === '') {
            Session::flash('error', 'Missing inquiry ID or status.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }

        $allowedStatuses = ['new', 'replied', 'archived'];
        if (!in_array($status, $allowedStatuses, true)) {
            Session::flash('error', 'Invalid status provided.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }

        $success = $this->inquiryModel->updateStatus($id, $status);

        if ($success) {
            Session::flash('success', 'Inquiry status updated successfully.', 'success');
        } else {
            Session::flash('error', 'Failed to update inquiry status.', 'danger');
        }

        header('Location: /admin/inquiries');
        exit;
    }
}
