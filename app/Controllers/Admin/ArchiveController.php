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

        /*
        |--------------------------------------------------------------------------
        | Fetch from tbl_archives directly (x10hosting compatible)
        |--------------------------------------------------------------------------
        */
        $archiveQuery = "SELECT * FROM tbl_archives WHERE is_archived = 1";
        
        if ($filter !== 'all') {
            $filterType = $filter === 'service' ? 'service' : ($filter === 'appointment' ? 'appointment' : 'inquiry');
            $archiveQuery .= " AND item_type = '" . $filterType . "'";
        }
        
        $archiveQuery .= " ORDER BY archived_at DESC";
        
        $archiveRows = $serviceModel->getDb()->query($archiveQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        // Process each archive record
        foreach ($archiveRows as $archive) {
            $decoded = json_decode($archive['item_data'], true);
            $details = $decoded ?: [];
            
            // Get additional details from the details JSON column
            $detailsObj = json_decode($archive['details'], true);
            $itemId = $detailsObj['item_id'] ?? null;
            
            $items[] = [
                'item_type' => $archive['item_type'],
                'item_id' => $itemId,
                'item_name' => $archive['item_name'],
                'archived_at' => $archive['archived_at'],
                'details' => $details
            ];
        }

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
