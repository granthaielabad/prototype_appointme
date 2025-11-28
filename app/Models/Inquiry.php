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
        'status',
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
                status,
                is_read,
                created_at
            FROM {$this->table}
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
                status,
                is_read,
                created_at
            FROM {$this->table}
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
                status,
                is_read,
                created_at
            FROM {$this->table}
            WHERE {$this->primaryKey} = :id
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
                status,
                is_read,
                created_at
            FROM {$this->table}
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update the status of an inquiry.
     */
    public function updateStatus(int|string $id, string $status): bool
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Delete an inquiry by ID — override base method with same signature.
     */
    public function delete(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
