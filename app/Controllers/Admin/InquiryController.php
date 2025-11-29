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
            $filter = $_GET['filter'] ?? 'all';
            
            if ($filter === 'read') {
                $inquiries = $this->inquiryModel->getByReadStatus('read');
            } elseif ($filter === 'unread') {
                $inquiries = $this->inquiryModel->getByReadStatus('unread');
            } else {
                $inquiries = $this->inquiryModel->getAll();
            }
            
            $this->render('inquiries/index', [
                'inquiries' => $inquiries,
                'currentFilter' => $filter
            ]);
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to load inquiries: ' . $e->getMessage(), 'danger');
            $this->render('inquiries/index', ['inquiries' => [], 'currentFilter' => 'all']);
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

        // Mark as read
        $this->inquiryModel->markAsRead($id);

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

    /**
     * Mark inquiry as read (AJAX endpoint)
     */
    public function markAsRead(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid inquiry ID']);
            exit;
        }

        $success = $this->inquiryModel->markAsRead($id);
        
        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Inquiry marked as read']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
        exit;
    }

    /**
     * Fetch inquiries for real-time updates (AJAX endpoint)
     */
    public function fetch(): void
    {
        header('Content-Type: application/json');

        try {
            $filter = $_GET['filter'] ?? 'all';
            
            if ($filter === 'read') {
                $inquiries = $this->inquiryModel->getByReadStatus('read');
            } elseif ($filter === 'unread') {
                $inquiries = $this->inquiryModel->getByReadStatus('unread');
            } else {
                $inquiries = $this->inquiryModel->getAll();
            }
            
            echo json_encode([
                'success' => true,
                'inquiries' => $inquiries
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch inquiries'
            ]);
        }
        exit;
    }
}
