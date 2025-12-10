<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class AdminNotification extends Model
{
    protected string $table = 'tbl_admin_notifications';
    protected string $primaryKey = 'admin_notification_id';

    public function createForEvent(int $appointmentId, int $userId, string $type, array $context = []): ?int
    {
        $type = strtolower(trim($type));
        if ($appointmentId <= 0 || $userId <= 0) {
            return null;
        }

        $titleMap = [
            'booked'      => 'New Booking',
            'rescheduled' => 'Appointment Rescheduled',
            'cancelled'   => 'Appointment Cancelled',
        ];

        $title = $context['title'] ?? ($titleMap[$type] ?? 'Appointment Update');
        $message = $context['message'] ?? $this->buildMessage($type, $context);

        $sql = "
            INSERT INTO {$this->table}
                (appointment_id, user_id, type, title, message, is_read, created_at)
            VALUES
                (:appointment_id, :user_id, :type, :title, :message, 0, NOW())
        ";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'appointment_id' => $appointmentId,
            'user_id'        => $userId,
            'type'           => $type,
            'title'          => $title,
            'message'        => $message,
        ]);

        return $ok ? (int) $this->db->lastInsertId() : null;
    }

    private function buildMessage(string $type, array $context = []): string
    {
        $customerName = trim(($context['customer_name'] ?? '') ?: 'A customer');
        $serviceName  = trim($context['service_name'] ?? 'a service');
        $date         = $context['date'] ?? null;
        $time         = $context['time'] ?? null;

        $when = '';
        if ($date && $time) {
            $when = " on {$date} at {$time}";
        } elseif ($date) {
            $when = " on {$date}";
        }

        return match ($type) {
            'booked'      => "{$customerName} booked {$serviceName}{$when}.",
            'rescheduled' => "{$customerName} rescheduled {$serviceName} to{$when}.",
            'cancelled'   => "{$customerName} cancelled {$serviceName}{$when}.",
            default       => "{$customerName} updated an appointment{$when}.",
        };
    }

    public function latest(int $limit = 20): array
    {
        $limit = max(1, $limit);
        $sql = "
            SELECT * FROM {$this->table}
            ORDER BY created_at DESC
            LIMIT :limit
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnread(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0");
        return (int) $stmt->fetchColumn();
    }

    public function markAllRead(): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE is_read = 0");
        return $stmt->execute();
    }

    public function markRead(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
