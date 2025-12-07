<?php
declare(strict_types=1);
date_default_timezone_set("Asia/Manila");

use App\Core\Session;
use App\Core\Router;
use App\Core\CSRF;
use App\Core\Debug;
use Dotenv\Dotenv;

require_once __DIR__ . "/vendor/autoload.php";

// Initialize debugging
Debug::init(__DIR__ . '/logs');
Debug::logRequest();

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Start session
Session::start();

// Ensure CSRF token exists (generate once per session)
if (!CSRF::getToken()) {
    CSRF::generate();
}

// Router
$router = new Router();

/* PUBLIC */
$router->get("/", "HomeController@landing");
$router->post("/inquiry/storePublic", "InquiryController@storePublic");

/* AUTH */
$router->get("/login", "AuthController@loginForm");
$router->post("/login", "AuthController@login");

$router->get("/register", "AuthController@registerForm");
$router->post("/register", "AuthController@register");

$router->get("/verify-otp", "AuthController@verifyOtpForm");
$router->post("/verify-otp", "AuthController@verifyOtp");

$router->get("/forgot-password", "AuthController@forgotPasswordForm");
$router->post("/forgot-password", "AuthController@sendResetLink");

$router->get("/reset-password", "AuthController@resetPasswordForm");
$router->post("/reset-password", "AuthController@resetPassword");

$router->get("/logout", "AuthController@logout");

/* EMPLOYEE INVITATION */
$router->get("/employee/accept-invitation", "EmployeeInvitationController@acceptInvitation");
$router->post("/employee/complete-setup", "EmployeeInvitationController@completeSetup");

/* CUSTOMER */
$router->get("/book", "BookingController@index");
$router->post("/book", "BookingController@store");
$router->get("/api/appointments/taken", "BookingController@takenSlots");
$router->get("/appointment/cancel", "BookingController@cancelFromHistory");



$router->get("/invoices", "InvoiceController@customerList");


/* added testing carl */
$router->get("/my-appointments", "BookingController@myAppointments");
$router->get("/cancel-appointment", "BookingController@cancel");
$router->get("/payment-qr", "BookingController@paymentQr"); 
$router->post("/webhook/paymongo", "PaymentWebhookController@handle");



$router->get("/profile", "UserController@profile");
$router->post("/profile/update", "UserController@updateProfile");

$router->get("/notifications/get", "NotificationController@getAll");

/* OTP */
$router->post("/otp/send", "OTPController@send");
$router->post("/otp/verify", "OTPController@verify");

/* ANALYTICS */
$router->get("/analytics/daily-appointments", "AnalyticsController@dailyAppointments");
$router->get("/analytics/service-popularity", "AnalyticsController@servicePopularity");

/* ADMIN (namespaced) */
$router->get("/admin/dashboard", "Admin\\DashboardController@index");
$router->get("/admin/appointments", "Admin\\AppointmentController@index");
$router->post("/admin/appointments/update-status", "Admin\\AppointmentController@updateStatus");
$router->post("/admin/appointments/update", "Admin\\AppointmentController@update");
$router->get("/admin/appointments/fetch", "Admin\\AppointmentController@fetch");
$router->get("/admin/appointments/delete", "Admin\\AppointmentController@archive");
// also accept the `/archive` path for backwards-compatibility
$router->get("/admin/appointments/archive", "Admin\\AppointmentController@archive");

$router->get("/admin/services", "Admin\\ServiceController@index");
$router->get("/admin/services/create", "Admin\\ServiceController@create");
$router->post("/admin/services/store", "Admin\\ServiceController@store");
$router->get("/admin/services/edit", "Admin\\ServiceController@edit");
$router->post("/admin/services/update", "Admin\\ServiceController@update");
$router->get("/admin/services/delete", "Admin\\ServiceController@delete");

$router->get("/admin/employees", "Admin\\EmployeeController@index");
$router->get("/admin/employees/create", "Admin\\EmployeeController@create");
$router->post("/admin/employees/store", "Admin\\EmployeeController@store");
$router->get("/admin/employees/edit", "Admin\\EmployeeController@edit");
$router->post("/admin/employees/update", "Admin\\EmployeeController@update");
$router->post("/admin/employees/toggle-status", "Admin\\EmployeeController@toggleStatus");
$router->get("/admin/employees/archive", "Admin\\EmployeeController@archive");
$router->get("/admin/employees/delete", "Admin\\EmployeeController@delete");
$router->get("/admin/employees/activate", "Admin\\EmployeeController@activate");

$router->get("/admin/inquiries", "Admin\\InquiryController@index");
$router->get("/admin/inquiries/show", "Admin\\InquiryController@show");
$router->post("/admin/inquiries/mark-as-read", "Admin\\InquiryController@markAsRead");
$router->get("/admin/inquiries/fetch", "Admin\\InquiryController@fetch");
$router->get("/admin/inquiries/delete", "Admin\\InquiryController@archive");;

$router->get("/admin/archives", "Admin\\ArchiveController@index");
$router->get("/admin/archives/restore", "Admin\\ArchiveController@restore");
$router->get("/admin/archives/delete", "Admin\\ArchiveController@delete");

$router->get("/admin/reports", "Admin\\ReportController@index");
$router->post("/admin/reports/export", "Admin\\ReportController@export");
$router->get("/admin/reports/export-csv", "Admin\\ReportController@exportCsv");
$router->get("/admin/reports/export-pdf", "Admin\\ReportController@exportPdf");
$router->get("/admin/reports/summary", "Admin\\ReportController@summary");

/* RESOURCE-LIKE (misc) */
$router->get("/services", "ServiceController@index");
$router->get("/services/show", "ServiceController@show");

$router->dispatch($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);