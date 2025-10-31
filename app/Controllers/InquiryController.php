<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    /*
        Admin View - list all inquiries 
     */
    public function index(): void
    {
        Auth::requireRole(1); // Admin only
        $inquiryModel = new Inquiry();
        $inquiries = $inquiryModel->findAll();

        $this->view('Admin/inquiries/index', ['inquiries' => $inquiries]);
    }

    /*
        Public Inquiry Submission - submitted via Contact Us form
    */
    public function storePublic(): void
    {     
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            exit;
        }
                
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'message' => trim($_POST['message'] ?? '')
        ];
        
        // Validate
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['message'])) {
            Session::flash('error', 'Please fill in all required fields.', 'danger');
            header('Location: /#contact');
            exit;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.', 'danger');
            header('Location: /#contact');
            exit;
        }

        // Sanitize
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
                
        // Save inquiry
        $inquiry = new Inquiry();
        $inquiry->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'status' => 'new',
            'user_id' => null // public visitor
        ]);

        Session::flash('success', 'Your inquiry has been sent successfully! We’ll get back to you soon.');
        header('Location: /#contact');
        exit;

    }

    /*
        Admin - Mark inquiry as read/viewed/replied 
     */
    public function markReplied(): void
    {
        Auth::requireRole(1);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Invalid inquiry ID.', 'danger');
            header('Location: /admin/inquiries');
            exit;
        }
                
        $inquiryModel = new Inquiry();
        $inquiryModel->update($id, ['status' => 'replied']);
        Session::flash('success', 'Inquiry marked as replied.');
        header('Location: /admin/inquiries');
    }

    /*
        Admin - Archive inquiry
    */
    public function archive(): void
    {
        Auth::requireRole(1);
        $id = $_POST['id'] ?? null;
        if ($id) {
            $inquiryModel = new Inquiry();
            $inquiryModel->update($id, ['status' => 'archived']);
            Session::flash('success', 'Inquiry archived successfully.');
        }
        header('Location: /admin/inquiries');
    }
}
