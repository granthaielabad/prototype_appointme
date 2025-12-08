<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Notification extends Model
{
    protected string $table = 'tbl_notifications'; // keep if other code references it
    protected string $primaryKey = 'notification_id';

    public function findByUser(int $userId, int $limit = 20): array
    {
        $sql = "
            SELECT
                a.appointment_id AS notification_id,
                a.user_id,
                a.appointment_id,
                'Appointment Reminder' AS title,
                CONCAT(
                    'You have an upcoming ',
                    COALESCE(s.service_name, 'service'),
                    ' appointment on ',
                    a.appointment_date,
                    ' at ',
                    DATE_FORMAT(a.appointment_time, '%h:%i %p')
                ) AS message,
                'appointment_reminder' AS type,
                0 AS is_read,
                CONCAT(a.appointment_date, ' ', a.appointment_time) AS created_at
            FROM tbl_appointments a
            LEFT JOIN tbl_services s ON s.service_id = a.service_id
            WHERE a.user_id = :user_id
              AND a.status = 'Pending'
              AND TIMESTAMP(a.appointment_date, a.appointment_time)
                    BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAllRead(int $userId): bool
    {
        // no-op: notifications are computed dynamically, not stored
        return true;
    }

    // leave createReminder/remove other methods if still used elsewhere
}
