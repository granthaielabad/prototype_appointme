<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'tbl_payments';
    protected string $primaryKey = 'payment_id';

    public function findByAppointmentId(int $appointmentId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE appointment_id = :appointment_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['appointment_id' => $appointmentId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }





}


