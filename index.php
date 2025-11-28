<?php
declare(strict_types=1);
date_default_timezone_set("Asia/Manila");

use App\Core\Session;
use App\Core\Router;
use App\Core\CSRF;
use Dotenv\Dotenv;

require_once __DIR__ . "/vendor/autoload.php";

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

/* CUSTOMER */
$router->get("/book", "BookingController@index");
$router->post("/book", "BookingController@store");



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

$router->get("/admin/services", "Admin\\ServiceController@index");
$router->get("/admin/services/create", "Admin\\ServiceController@create");
$router->post("/admin/services/store", "Admin\\ServiceController@store");
$router->get("/admin/services/edit", "Admin\\ServiceController@edit");
$router->post("/admin/services/update", "Admin\\ServiceController@update");
$router->get("/admin/services/delete", "Admin\\ServiceController@delete");

$router->get("/admin/inquiries", "Admin\\InquiryController@index");
$router->get("/admin/inquiries/show", "Admin\\InquiryController@show");
$router->post("/admin/inquiries/update-status", "Admin\\InquiryController@updateStatus");

$router->get("/admin/archives", "Admin\\ArchiveController@index");
$router->get("/admin/archives/restore", "Admin\\ArchiveController@restore");
$router->get("/admin/archives/delete", "Admin\\ArchiveController@delete");

/* RESOURCE-LIKE (misc) */
$router->get("/services", "ServiceController@index");
$router->get("/services/show", "ServiceController@show");

$router->dispatch($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);