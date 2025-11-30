<?php
namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Core\Session;

/**
 * Admin appointment management.
 */
class AppointmentController extends AdminController
{
    public function index(): void
    {
        $appointmentModel = new Appointment();
        
        // Get filter from query parameter (default to 'all')
        $filter = $_GET['filter'] ?? 'all';
        
        // Validate filter to prevent SQL injection
        $allowedFilters = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = 'all';
        }
        
        // Fetch appointments with filter
        $appointments = $appointmentModel->findAllWithUsersFiltered($filter);
        
        // Pass filter to view so dropdown can show current selection
        $this->render("appointments/index", [
            "appointments" => $appointments,
            "currentFilter" => $filter
        ]);
    }

    public function updateStatus(): void
    {
        if (empty($_POST["id"]) || empty($_POST["status"])) {
            Session::flash("error", "Invalid appointment update request.", "danger");
            header("Location: /admin/appointments");
            exit();
        }

        (new Appointment())->update($_POST["id"], ["status" => $_POST["status"]]);
        Session::flash("success", "Appointment status updated successfully.", "success");
        header("Location: /admin/appointments");
        exit();
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/appointments');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Invalid appointment ID.', 'danger');
            header('Location: /admin/appointments');
            exit();
        }

        $data = [];
        if (isset($_POST['appointment_date'])) {
            $data['appointment_date'] = $_POST['appointment_date'];
        }
        if (isset($_POST['appointment_time'])) {
            $data['appointment_time'] = $_POST['appointment_time'];
        }
        if (isset($_POST['status'])) {
            $data['status'] = $_POST['status'];
        }

        // Validate status if present to avoid DB enum truncation warnings
        $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (isset($data['status'])) {
            $data['status'] = trim((string)$data['status']);
            if (!in_array($data['status'], $allowedStatuses, true)) {
                Session::flash('error', 'Invalid status value.', 'danger');
                header('Location: /admin/appointments');
                exit();
            }
        }

        if (!empty($data)) {
            (new Appointment())->update($id, $data);
            Session::flash('success', 'Appointment updated successfully.', 'success');
        } else {
            Session::flash('info', 'No changes submitted.', 'info');
        }

        header('Location: /admin/appointments');
        exit();
    }

    /**
     * Fetch appointments for real-time updates (AJAX endpoint)
     */
    public function fetch(): void
    {
        // Set JSON headers early
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            // Check authentication for API endpoint
            $user = \App\Core\Auth::user();
            if (!$user || (int)($user['role_id'] ?? 0) !== 1) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]);
                exit;
            }

            $filter = $_GET['filter'] ?? 'all';
            $allowedFilters = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
            if (!in_array($filter, $allowedFilters, true)) {
                $filter = 'all';
            }

            $model = new Appointment();
            $appointments = $model->findAllWithUsersFiltered($filter);

            $response = [
                'success' => true,
                'appointments' => $appointments,
                'count' => count($appointments),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch appointments',
                'message' => getenv('APP_DEBUG') ? $e->getMessage() : 'Server error'
            ]);
        }
        exit;
    }
}
