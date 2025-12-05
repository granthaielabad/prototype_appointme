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

        // Simple approach: read snapshots directly from tbl_archives and decode
        // This is more compatible across hosting environments than complex JSON subqueries
        // Query all active archives (is_archived = 1)
        $sql = "SELECT * FROM tbl_archives 
                WHERE is_archived = 1
                ORDER BY archived_at DESC";
        
        if ($filter !== 'all') {
            $sql = "SELECT * FROM tbl_archives 
                    WHERE item_type = :item_type 
                    AND is_archived = 1
                    ORDER BY archived_at DESC";
        }

        try {
            $db = (new Service())->getDb();
            
            if ($filter === 'all') {
                $stmt = $db->query($sql);
                $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            } else {
                $stmt = $db->prepare($sql);
                $success = $stmt->execute(['item_type' => $filter]);
                $rows = $success ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            }

            // Log for debugging
            error_log("ArchiveController::index - filter={$filter}, found " . count($rows) . " rows");

            foreach ($rows as $archive) {
                // Decode the snapshot JSON
                $itemData = !empty($archive['item_data']) 
                    ? json_decode($archive['item_data'], true) 
                    : [];

                // Extract the item_id from snapshot (service_id, appointment_id, or inquiry_id)
                $itemId = null;
                $itemType = $archive['item_type'] ?? 'unknown';
                if ($itemType === 'service' && isset($itemData['service_id'])) {
                    $itemId = $itemData['service_id'];
                } elseif ($itemType === 'appointment' && isset($itemData['appointment_id'])) {
                    $itemId = $itemData['appointment_id'];
                } elseif ($itemType === 'inquiry' && isset($itemData['inquiry_id'])) {
                    $itemId = $itemData['inquiry_id'];
                }

                // Build the item for display
                $item = [
                    'archive_id' => $archive['archive_id'] ?? null,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'item_name' => $archive['item_name'] ?? 'Unknown',
                    'archived_at' => $archive['archived_at'] ?? date('Y-m-d H:i:s'),
                    'archived_by' => $archive['archived_by'] ?? null,
                    'details' => $itemData ?: []
                ];

                // Normalize details for the modal
                if ($item['item_type'] === 'service') {
                    $item['details']['service_name'] = $item['details']['service_name'] ?? $item['details']['name'] ?? $item['item_name'] ?? 'Unknown Service';
                    $item['details']['price'] = $item['details']['price'] ?? 0;
                    $item['details']['description'] = $item['details']['description'] ?? $item['details']['desc'] ?? '';
                    $item['details']['category'] = $item['details']['category'] ?? 'Service';
                    // Include archived_at in details for modal
                    $item['details']['archived_at'] = $item['archived_at'];
                } elseif ($item['item_type'] === 'appointment') {
                    $item['details']['appointment_id'] = $item['details']['appointment_id'] ?? 'N/A';
                    $item['details']['appointment_date'] = $item['details']['appointment_date'] ?? null;
                    $item['details']['appointment_time'] = $item['details']['appointment_time'] ?? null;
                    $item['details']['status'] = $item['details']['status'] ?? 'pending';
                    $item['details']['archived_at'] = $item['archived_at'];
                } elseif ($item['item_type'] === 'inquiry') {
                    $item['details']['full_name'] = $item['details']['full_name'] ?? $item['item_name'] ?? 'Unknown';
                    $item['details']['phone'] = $item['details']['phone'] ?? 'N/A';
                    $item['details']['email'] = $item['details']['email'] ?? 'N/A';
                    $item['details']['message'] = $item['details']['message'] ?? '';
                    $item['details']['created_at'] = $item['details']['created_at'] ?? $item['archived_at'];
                    $item['details']['archived_at'] = $item['archived_at'];
                }

                $items[] = $item;
            }

        } catch (\Throwable $e) {
            error_log('ArchiveController::index error: ' . $e->getMessage());
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
        $filter = $_GET['filter'] ?? 'all';

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

        // Also update the archive record to mark it as not archived
        if ($ok) {
            try {
                $db = (new Service())->getDb();
                // Search in JSON details column for the item_id
                $stmt = $db->prepare("UPDATE tbl_archives SET is_archived = 0 WHERE item_type = :type AND details LIKE :details_search");
                $detailsSearch = '%"item_id":' . intval($id) . '%';
                $stmt->execute(['type' => $type, 'details_search' => $detailsSearch]);
                
                error_log("Archive restore: Updated archives for type=$type, id=$id");
            } catch (\Throwable $e) {
                error_log("Failed to update archive status: " . $e->getMessage());
                // Don't fail the restore just because archive update failed
            }
        }

        Session::flash(
            $ok ? 'success' : 'error',
            $ok ? 'Item restored successfully.' : 'Failed to restore item.',
            $ok ? 'success' : 'danger'
        );

        // Redirect back with the filter preserved
        header('Location: /admin/archives?filter=' . urlencode($filter));
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
