<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Service extends Model
{
    protected string $table = 'tbl_services';
    protected string $primaryKey = 'service_id';

    public function allGroupedByCategory(): array
    {
        $stmt = $this->db->query("SELECT category, service_id, service_name, price, duration_minutes, description FROM {$this->table} WHERE is_active = 1 ORDER BY category, service_name");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }
        return $grouped;
    }
}
