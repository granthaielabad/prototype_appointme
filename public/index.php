<?php
declare(strict_types=1);

use App\Core\Session;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

Session::start();

// Initialize Router
$router = new Router();

/*
    PUBLIC ROUTES (Single Page)
*/
$router->get('/', 'HomeController@landing');
$router->post('/inquiry/submit', 'InquiryController@storePublic');

/*
    AUTH ROUTES
*/
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@handleLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@handleRegister');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@sendResetLink');
$router->get('/password-reset', 'ForgotPasswordController@resetForm'); // ?token=...
$router->post('/password-reset', 'ForgotPasswordController@handleReset');
$router->get('/logout', 'AuthController@logout');

/*
    CUSTOMER ROUTES
*/
$router->get('/book', 'BookingController@index');
$router->post('/book', 'BookingController@store');
$router->get('/my-appointments', 'BookingController@myAppointments');
$router->get('/cancel-appointment', 'BookingController@cancel');

/*
    ADMIN ROUTES
*/
$router->get('/admin/dashboard', 'Admin\\DashboardController@index');
$router->get('/admin/appointments', 'Admin\\AppointmentController@index');
$router->post('/admin/appointments/updateStatus', 'Admin\\AppointmentController@updateStatus');
$router->get('/admin/services', 'Admin\\ServiceController@index');
$router->get('/admin/services/create', 'Admin\\ServiceController@create');
$router->post('/admin/services/store', 'Admin\\ServiceController@store');
$router->get('/admin/services/edit', 'Admin\\ServiceController@edit');
$router->post('/admin/services/update', 'Admin\\ServiceController@update');
$router->get('/admin/services/delete', 'Admin\\ServiceController@delete');
$router->get('/admin/inquiries', 'Admin\\InquiryController@index');

// Users
$router->get('/users', 'UserController@index');
$router->get('/users/show', 'UserController@show'); // can later accept /users/{id}

// Services
$router->get('/services', 'ServiceController@index');
$router->get('/services/show', 'ServiceController@show');

// Appointments
$router->get('/appointments', 'AppointmentController@index');
$router->get('/appointments/show', 'AppointmentController@show');

// Payments
$router->get('/payments', 'PaymentController@index');
$router->get('/payments/show', 'PaymentController@show');

// Invoices
$router->get('/invoices', 'InvoiceController@index');
$router->get('/invoices/show', 'InvoiceController@show');

// Inquiries
$router->get('/inquiries', 'InquiryController@index');
$router->get('/inquiries/show', 'InquiryController@show');






// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

