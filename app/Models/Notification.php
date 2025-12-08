<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Notification extends Model
{
    protected string $table = 'tbl_notifications';
    protected string $primaryKey = 'notification_id';

    public function findByUser(int $userId, int $limit = 20): array
    {
        $sql = "
            SELECT notification_id, user_id, title, message, type, appointment_id, is_read, created_at
            FROM {$this->table}
            WHERE user_id = :user_id
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
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function createReminder(int $userId, int $appointmentId, string $serviceName, string $date, string $time): int
    {
        return $this->create([
            'user_id'        => $userId,
            'appointment_id' => $appointmentId,
            'title'          => 'Appointment Reminder',
            'message'        => "You have an upcoming {$serviceName} appointment on {$date} at {$time}.",
            'type'           => 'appointment_reminder',
            'is_read'        => 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
