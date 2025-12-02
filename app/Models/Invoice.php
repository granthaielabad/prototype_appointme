<?php
namespace App\Models;

use App\Core\Model;

class Invoice extends Model
{
    protected string $table = 'tbl_invoices';
    protected string $primaryKey = 'invoice_id';



    public function findByAppointmentId(int $appointmentId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE appointment_id = :appointment_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['appointment_id' => $appointmentId]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }


}


