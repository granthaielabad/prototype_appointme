<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Service extends Model
{
    protected string $table = 'tbl_services';
    protected string $primaryKey = 'service_id';

    /**
     * Get all non-deleted services for admin listing.
     */
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY service_name";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allGroupedByCategory(): array
    {
        $stmt = $this->db->query("
            SELECT category, service_id, service_name, price, duration_minutes, description
            FROM {$this->table}
            WHERE is_active = 1 AND is_deleted = 0
            ORDER BY category, service_name
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }
        return $grouped;
    }

    /**
     * Archive a service (soft delete + snapshot to tbl_archives)
     */
    public function archive(int|string $id, ?int $adminId = null): bool
    {
        try {
            error_log("Service::archive start - id={$id} admin={$adminId}");
            // Get the service data
            $service = $this->find($id);
            if (!$service) {
                error_log("Service::archive - service not found id={$id}");
                return false;
            }

            // 1) Snapshot into tbl_archives for history / archive view
            $archive = new Archive();
            $snapOk = $archive->archive(
                'service',
                (int)$id,
                $service['service_name'] ?? 'Unknown Service',
                $service,
                $adminId
            );

            if (!$snapOk) {
                error_log("Service::archive - snapshot failed for id={$id}");
                return false;
            }

            // 2) Soft delete in main table
            $sql = "UPDATE {$this->table}
                    SET is_deleted = 1, deleted_at = NOW(), deleted_by = :admin_id
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->getDb()->prepare($sql);
            $ok = $stmt->execute(['id' => $id, 'admin_id' => $adminId]);
            if (!$ok) {
                error_log('Service::archive - soft delete failed: ' . json_encode($stmt->errorInfo()));
            } else {
                error_log("Service::archive - soft delete success id={$id}");
            }
            return $ok;
        } catch (\Throwable $e) {
            error_log('Error archiving service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restore(int|string $id): bool
    {
        try {
            error_log("Service::restore start - id={$id}");
            
            // 1) Restore in main table
            $sql = "UPDATE {$this->table}
                    SET is_deleted = 0, deleted_at = NULL, deleted_by = NULL
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->getDb()->prepare($sql);
            $ok = $stmt->execute(['id' => $id]);
            
            if (!$ok) {
                error_log('Service::restore - main table restore failed: ' . json_encode($stmt->errorInfo()));
                return false;
            }
            
            // 2) Mark ALL archive snapshots for this service as inactive (is_archived = 0)
            // Use a simpler approach compatible with x10hosting: find archives then update by ID
            $findSql = "SELECT archive_id FROM tbl_archives 
                        WHERE item_type = 'service' AND is_archived = 1";
            $findStmt = $this->getDb()->query($findSql);
            $archives = $findStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($archives as $archive) {
                // Decode the JSON to check if it matches this service_id
                $archiveSql = "SELECT item_data FROM tbl_archives WHERE archive_id = :archive_id";
                $archiveStmt = $this->getDb()->prepare($archiveSql);
                $archiveStmt->execute(['archive_id' => $archive['archive_id']]);
                $archiveRow = $archiveStmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($archiveRow && !empty($archiveRow['item_data'])) {
                    $itemData = json_decode($archiveRow['item_data'], true);
                    // If this archive is for the service being restored, deactivate it
                    if (isset($itemData['service_id']) && $itemData['service_id'] == $id) {
                        $updateSql = "UPDATE tbl_archives SET is_archived = 0 WHERE archive_id = :archive_id";
                        $updateStmt = $this->getDb()->prepare($updateSql);
                        $updateStmt->execute(['archive_id' => $archive['archive_id']]);
                        error_log("Service::restore - deactivated archive " . $archive['archive_id']);
                    }
                }
            }
            
            error_log("Service::restore - completed id={$id}");
            return true;
        } catch (\Throwable $e) {
            error_log('Error restoring service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete a service row.
     */
    public function hardDelete(int|string $id): bool
    {
        return $this->delete($id);
    }
}
