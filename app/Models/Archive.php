<?php
namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Model for tbl_archives, which acts both as a snapshot storage
 * and a soft-deletable entity itself.
 */
class Archive extends Model
{
    protected string $table = 'tbl_archives';
    protected string $primaryKey = 'archive_id';

    /**
     * Create an archive snapshot of another item.
     * @param string $type The type of item (service, appointment, inquiry)
     * @param int $itemId The ID of the item being archived
     * @param string $itemName The name/title of the item
     * @param array $data The full data of the item
     * @param int|null $adminId The admin who archived it
     * @return bool
     */
    public function archive(string $type, int $itemId, string $itemName, array $data, ?int $adminId = null): bool
    {
        try {
            // The `tbl_archives` schema in this project stores the full snapshot JSON in
            // `item_data` and has a `details` field for a short description. The table
            // does not include an `item_id` column in the current schema, so insert
            // into the actual columns present.
            $sql = "INSERT INTO {$this->table} 
                    (item_type, item_name, item_data, details, is_archived, archived_at)
                    VALUES (:item_type, :item_name, :item_data, :details, 1, NOW())";

            $stmt = $this->getDb()->prepare($sql);
            $params = [
                'item_type' => $type,
                'item_name' => $itemName,
                // store a UTF-8 safe JSON representation
                'item_data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                // keep a small details summary (including original id) to help quick lookups
                'details' => json_encode(['item_id' => $itemId, 'name' => $itemName], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ];

            $ok = $stmt->execute($params);
            if (!$ok) {
                $err = $stmt->errorInfo();
                error_log('Archive insert failed: ' . json_encode($err));
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            error_log('Error creating archive snapshot: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete (mark archive as deleted by setting is_archived to 0).
     * @param int|string $id The primary key of the archive record to soft delete.
     * @param int|null $deletedBy The ID of the user performing the deletion (not used since no column for it).
     * @return bool
     */
    public function softDelete(int|string $id, ?int $deletedBy = null): bool
    {
        try {
            // tbl_archives only has is_archived column, set it to 0 to "soft delete"
            $sql = "UPDATE {$this->table}
                    SET is_archived = 0
                    WHERE {$this->primaryKey} = :id";

            $stmt = $this->getDb()->prepare($sql);
            $success = $stmt->execute(['id' => $id]);

            if (!$success) {
                error_log('DB Error: Soft delete execution failed for ID ' . $id . ': ' . json_encode($stmt->errorInfo()));
                return false;
            }

            $affectedRows = $stmt->rowCount();
            if ($affectedRows === 0) {
                error_log('Warning: Soft delete executed but 0 rows affected for ID ' . $id);
            }

            return $success;
        } catch (\Throwable $e) {
            error_log('Exception during soft delete for ID ' . $id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore soft-deleted archive row (set is_archived = 1).
     * @param int|string $id The primary key of the archive record to restore.
     * @return bool
     */
    public function restore(int|string $id): bool
    {
        try {
            $sql = "UPDATE {$this->table}
                    SET is_archived = 1
                    WHERE {$this->primaryKey} = :id";

            $stmt = $this->getDb()->prepare($sql);
            $success = $stmt->execute(['id' => $id]);

            if (!$success) {
                error_log('DB Error: Restore execution failed for ID ' . $id . ': ' . json_encode($stmt->errorInfo()));
                return false;
            }

            $affectedRows = $stmt->rowCount();
            if ($affectedRows === 0) {
                error_log('Warning: Restore executed but 0 rows affected for ID ' . $id);
            }

            return $success;
        } catch (\Throwable $e) {
            error_log('Exception during restore for ID ' . $id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hard delete (permanently remove row).
     * @param int|string $id The primary key of the archive record to hard delete.
     * @return bool
     */
    public function hardDelete(int|string $id): bool
    {
        // Assuming the base Model has a generic delete($id) method
        return $this->delete($id);
    }

    /**
     * Get all active (non-deleted) archives.
     * @return array
     */
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_archived = 1
                ORDER BY {$this->primaryKey} DESC";

        return $this->getDb()
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all soft-deleted (inactive) archives.
     * @return array
     */
    public function getAllDeleted(): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_archived = 0
                ORDER BY archived_at DESC";

        return $this->getDb()
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}