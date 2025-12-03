<?php
namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Inquiry;
use PDO;

class ArchiveController extends AdminController
{
    public function index(): void
    {
        $filter = $_GET['filter'] ?? 'all';

        $items = [];

        $serviceModel = new Service();
        $appointmentModel = new Appointment();
        $inquiryModel = new Inquiry();

        /*
        |--------------------------------------------------------------------------
        | SERVICES
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'service') {
            // find latest snapshot where the JSON item_data contains the matching service id
            $rows = $serviceModel->getDb()->query("                SELECT 
                    'service' AS item_type,
                    service_id AS item_id,
                    service_name AS item_name,
                    is_deleted,
                    deleted_at AS archived_at,
                    deleted_by AS archived_by,
                    (SELECT item_data FROM tbl_archives WHERE item_type = 'service' AND item_data LIKE CONCAT('%\"service_id\":', tbl_services.service_id, '%') ORDER BY archived_at DESC LIMIT 1) AS item_data
                FROM tbl_services
                WHERE is_deleted = 1
            ")->fetchAll(PDO::FETCH_ASSOC);

            $items = array_merge($items, $rows);
        }

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENTS
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'appointment') {
            $rows = $appointmentModel->getDb()->query("                SELECT 
                    'appointment' AS item_type,
                    a.appointment_id AS item_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS item_name,
                    a.is_deleted,
                    a.deleted_at AS archived_at,
                    a.deleted_by AS archived_by,
                    (SELECT item_data FROM tbl_archives WHERE item_type = 'appointment' AND item_data LIKE CONCAT('%\"appointment_id\":', a.appointment_id, '%') ORDER BY archived_at DESC LIMIT 1) AS item_data
                FROM tbl_appointments a
                LEFT JOIN tbl_users u ON a.user_id = u.user_id
                WHERE a.is_deleted = 1
            ")->fetchAll(PDO::FETCH_ASSOC);

            $items = array_merge($items, $rows);
        }

        /*
        |--------------------------------------------------------------------------
        | INQUIRIES
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'inquiry') {
            $rows = $inquiryModel->getDb()->query("                SELECT 
                    'inquiry' AS item_type,
                    inquiry_id AS item_id,
                    CONCAT(first_name, ' ', last_name) AS item_name,
                    is_deleted,
                    deleted_at AS archived_at,
                    deleted_by AS archived_by,
                    (SELECT item_data FROM tbl_archives WHERE item_type = 'inquiry' AND item_data LIKE CONCAT('%\"inquiry_id\":', tbl_inquiries.inquiry_id, '%') ORDER BY archived_at DESC LIMIT 1) AS item_data
                FROM tbl_inquiries
                WHERE is_deleted = 1
            ")->fetchAll(PDO::FETCH_ASSOC);

            $items = array_merge($items, $rows);
        }

        /*
        |--------------------------------------------------------------------------
        | Sort items by deleted date (newest first)
        |--------------------------------------------------------------------------
        */
        // Attach decoded details (from item_data snapshot) so the frontend modal can consume it
        foreach ($items as &$it) {
            if (!empty($it['item_data'])) {
                $decoded = json_decode($it['item_data'], true);
                $it['details'] = $decoded ?: (array) $it;
            } else {
                $it['details'] = (array) $it; // fallback to the available fields
            }
            unset($it['item_data']);
        }
        unset($it);

        usort($items, fn($a, $b) => strcmp($b['archived_at'], $a['archived_at']));

        $this->render('archives/index', [
            'items' => $items,
            'currentFilter' => $filter,
        ]);
    }

    /**
     * Restore soft-deleted item
     */
    public function restore(): void
    {
        $type = $_GET['type'] ?? null;
        $id   = $_GET['id'] ?? null;

        if (!$type || !$id) {
            Session::flash('error', 'Invalid restore request.', 'danger');
            header('Location: /admin/archives');
            exit;
        }

        switch ($type) {
            case 'service':
                $ok = (new Service())->restore($id);
                break;

            case 'appointment':
                $ok = (new Appointment())->restore($id);
                break;

            case 'inquiry':
                $ok = (new Inquiry())->restore($id);
                break;

            default:
                $ok = false;
        }

        Session::flash(
            $ok ? 'success' : 'error',
            $ok ? 'Item restored successfully.' : 'Failed to restore item.',
            $ok ? 'success' : 'danger'
        );

        header('Location: /admin/archives');
        exit;
    }

    /**
     * Soft delete (archive)
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? null;

        if (!$id || !$type) {
            Session::flash('error', 'Invalid delete request.', 'danger');
            header('Location: /admin/services');
            exit;
        }

        $adminId = \App\Core\Auth::user()['user_id'] ?? null;

        switch ($type) {
            case 'service':
                $ok = (new Service())->archive($id, $adminId);
                $redirect = '/admin/services';
                break;

            case 'appointment':
                $ok = (new Appointment())->archive($id, $adminId);
                $redirect = '/admin/appointments';
                break;

            case 'inquiry':
                $ok = (new Inquiry())->archive($id, $adminId);
                $redirect = '/admin/inquiries';
                break;

            default:
                $ok = false;
                $redirect = '/admin/services';
        }

        Session::flash(
            $ok ? 'success' : 'error',
            $ok ? 'Item moved to archive.' : 'Failed to archive item.',
            $ok ? 'success' : 'danger'
        );

        header("Location: {$redirect}");
        exit;
    }
}
