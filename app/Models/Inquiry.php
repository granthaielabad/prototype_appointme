<?php
namespace App\Models;

use App\Core\Model;
use PDO;


// Sql changes to mark-as-read and filtering by read status to work
// ALTER TABLE tbl_inquiries ADD COLUMN is_read TINYINT(1) DEFAULT 0;

class Inquiry extends Model
{
    protected string $table = 'tbl_inquiries';
    protected string $primaryKey = 'inquiry_id';

    protected array $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'created_at'
    ];

    /**
     * Get all inquiries with a full_name field.
     */
    public function getAll(): array
    {
        $sql = "
            SELECT 
                {$this->primaryKey},
                user_id,
                first_name,
                last_name,
                CONCAT(
                    COALESCE(first_name, ''), 
                    CASE 
                        WHEN first_name IS NOT NULL AND last_name IS NOT NULL THEN ' ' 
                        ELSE '' 
                    END, 
                    COALESCE(last_name, '')
                ) AS full_name,
                email,
                phone,
                message,
                is_read,
                created_at
            FROM {$this->table}
            WHERE is_deleted = 0
            ORDER BY is_read ASC, created_at DESC
        ";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get inquiries filtered by read status
     */
    public function getByReadStatus(?string $readStatus = null): array
    {
        $sql = "
            SELECT 
                {$this->primaryKey},
                user_id,
                first_name,
                last_name,
                CONCAT(
                    COALESCE(first_name, ''), 
                    CASE 
                        WHEN first_name IS NOT NULL AND last_name IS NOT NULL THEN ' ' 
                        ELSE '' 
                    END, 
                    COALESCE(last_name, '')
                ) AS full_name,
                email,
                phone,
                message,
                is_read,
                created_at
            FROM {$this->table}
            WHERE is_deleted = 0
        ";
        
        if ($readStatus === 'read') {
            $sql .= " WHERE is_read = 1";
        } elseif ($readStatus === 'unread') {
            $sql .= " WHERE is_read = 0";
        }
        
        $sql .= " ORDER BY is_read ASC, created_at DESC";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark inquiry as read
     */
    public function markAsRead(int|string $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Find a single inquiry by its ID.
     */
    public function find(int|string $id): ?array
    {
        $sql = "
            SELECT 
                {$this->primaryKey},
                user_id,
                first_name,
                last_name,
                CONCAT(
                    COALESCE(first_name, ''), 
                    CASE 
                        WHEN first_name IS NOT NULL AND last_name IS NOT NULL THEN ' ' 
                        ELSE '' 
                    END, 
                    COALESCE(last_name, '')
                ) AS full_name,
                email,
                phone,
                message,
                is_read,
                created_at
            FROM {$this->table}
            WHERE {$this->primaryKey} = :id
              AND is_deleted = 0
            LIMIT 1
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Get all inquiries belonging to a specific user.
     */
    public function getByUser(int|string $userId): array
    {
        $sql = "
            SELECT 
                {$this->primaryKey},
                user_id,
                first_name,
                last_name,
                CONCAT(
                    COALESCE(first_name, ''), 
                    CASE 
                        WHEN first_name IS NOT NULL AND last_name IS NOT NULL THEN ' ' 
                        ELSE '' 
                    END, 
                    COALESCE(last_name, '')
                ) AS full_name,
                email,
                phone,
                message,
                is_read,
                created_at
            FROM {$this->table}
            WHERE user_id = :user_id
              AND is_deleted = 0
            ORDER BY created_at DESC
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an inquiry by ID — hard delete.
     */
    public function delete(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Archive an inquiry (soft delete + snapshot to tbl_archives)
     */
    public function archive(int|string $id, ?int $adminId = null): bool
    {
        try {
            error_log("Inquiry::archive start - id={$id} admin={$adminId}");
            // Get the inquiry data
            $inquiry = $this->find($id);
            if (!$inquiry) {
                error_log("Inquiry::archive - inquiry not found id={$id}");
                return false;
            }

            // 1) Snapshot to tbl_archives
            $archive = new Archive();
            $snapOk = $archive->archive(
                'inquiry',
                (int)$id,
                $inquiry['full_name'] ?? 'Unknown',
                $inquiry,
                $adminId
            );

            if (!$snapOk) {
                error_log("Inquiry::archive - snapshot failed for id={$id}");
                return false;
            }

            // 2) Soft delete from main table
            $sql = "UPDATE {$this->table}
                    SET is_deleted = 1, deleted_at = NOW(), deleted_by = :admin_id
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->getDb()->prepare($sql);
            $ok = $stmt->execute(['id' => $id, 'admin_id' => $adminId]);
            if (!$ok) {
                error_log('Inquiry::archive - soft delete failed: ' . json_encode($stmt->errorInfo()));
            } else {
                error_log("Inquiry::archive - soft delete success id={$id}");
            }
            return $ok;
        } catch (\Throwable $e) {
            error_log('Error archiving inquiry: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore a soft-deleted inquiry.
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
     * Permanently delete an inquiry row.
     */
    public function hardDelete(int|string $id): bool
    {
        return $this->delete($id);
    }
}
