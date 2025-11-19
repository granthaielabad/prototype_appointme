<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function storePublic(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo "Method Not Allowed"; exit;
        }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::verify($token)) {
            Session::flash('error','Invalid form submission','danger');
            header('Location: /#contact'); exit;
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'message' => trim($_POST['message'] ?? '')
        ];

        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['message'])) {
            Session::flash('error', 'Please fill in all required fields.', 'danger');
            header('Location: /#contact'); exit;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.', 'danger');
            header('Location: /#contact'); exit;
        }

        foreach ($data as $k => $v) $data[$k] = htmlspecialchars($v);

        $inquiry = new Inquiry();
        $inquiry->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'status' => 'new',
            'user_id' => null
        ]);

        Session::flash('success', 'Your inquiry has been sent successfully! We’ll get back to you soon.');
        header('Location: /#contact'); exit;
    }
}
