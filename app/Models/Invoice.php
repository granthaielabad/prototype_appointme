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

    public function findByUserWithDetails(int $userId): array
{
    $sql = "
        SELECT 
            i.invoice_id,
            i.invoice_number,
            i.subtotal,
            i.tax,
            i.total,
            i.issued_at,
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            s.service_name,
            s.price,
            u.first_name,
            u.last_name
        FROM {$this->table} i
        JOIN tbl_appointments a ON a.appointment_id = i.appointment_id
        JOIN tbl_users u ON u.user_id = a.user_id
        JOIN tbl_services s ON s.service_id = a.service_id
        WHERE a.user_id = :uid
        ORDER BY i.issued_at DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}



}


