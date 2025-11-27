<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Archive extends Model
{
    protected string $table = 'tbl_archives';
    protected string $primaryKey = 'archive_id';

    /**
     * Return all archived items (is_archived = 1 by default).
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT archive_id, item_type, item_name, details, is_archived, archived_at
                FROM {$this->table}
                WHERE is_archived = 1
                ORDER BY archived_at DESC";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Restore an item (set is_archived = 0)
     */
    public function restore(int|string $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_archived = 0 WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Permanently delete archive row
     */
    public function remove(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
