<?php
namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    protected string $table = 'tbl_services';
    protected string $primaryKey = 'service_id';   
    protected array $fillable = [
        'service_name',
        'category',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'date_created',
        'last_updated'
    ];
        
    public function allGroupedByCategory(): array {
        $stmt = $this->db->query("
            SELECT category, service_id, service_name, price, duration_minutes
            FROM {$this->table}
            WHERE is_active = 1
            ORDER BY category, service_name
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }
        return $grouped;
    }

}
