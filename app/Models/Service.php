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
        $sql = "UPDATE {$this->table}
                SET is_deleted = 0, deleted_at = NULL, deleted_by = NULL
                WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Permanently delete a service row.
     */
    public function hardDelete(int|string $id): bool
    {
        return $this->delete($id);
    }
}
