<?php
namespace App\Models;

use App\Core\Model;
use Exception;
use PDO;

class Appointment extends Model
{
    protected string $table = 'tbl_appointments';
    protected string $primaryKey = 'appointment_id';
    protected PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->db; // from Model
    }

    /*
     * Get all appointments of a specific user
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.*, 
                s.service_name, 
                s.category,
                s.duration_minutes
            FROM {$this->table} a
            JOIN tbl_services s ON a.service_id = s.service_id
            WHERE a.user_id = :user_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Admin view: all appointments with customer names
     */
    public function findAllWithUsers(): array
    {
        $stmt = $this->db->query("
            SELECT 
                a.*, 
                CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                IFNULL(u.contact_number, '') AS phone,
                s.service_name, 
                s.category
            FROM {$this->table} a
            JOIN tbl_users u ON a.user_id = u.user_id
            JOIN tbl_services s ON a.service_id = s.service_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Admin view: all appointments filtered by status
     */
    public function findAllWithUsersFiltered(?string $status = null): array
    {
        $query = "
            SELECT 
                a.*, 
                CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                IFNULL(u.contact_number, '') AS phone,
                s.service_name, 
                s.category
            FROM {$this->table} a
            JOIN tbl_users u ON a.user_id = u.user_id
            JOIN tbl_services s ON a.service_id = s.service_id
        ";
        
        // Add status filter if provided and not 'all'
        if (!empty($status) && $status !== 'all') {
            $query .= " WHERE a.status = :status";
        }
        
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        
        // Bind status parameter if filtering
        if (!empty($status) && $status !== 'all') {
            $stmt->execute(['status' => $status]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
    /*
     * Check if a specific time slot is already booked
     */
    public function isSlotTaken(string $date, string $time): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->table} 
            WHERE appointment_date = :date 
            AND appointment_time = :time 
            AND status NOT IN ('cancelled', 'completed')
        ");
        $stmt->execute(['date' => $date, 'time' => $time]);
        return $stmt->fetchColumn() > 0;
    }
        
    /**
     * Limit per user per day to prevent spammy bookings
     */
    public function countUserAppointmentsForDay(int $userId, string $date): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->table} 
            WHERE user_id = :user_id 
            AND appointment_date = :date
            AND status NOT IN ('cancelled')
        ");
        $stmt->execute(['user_id' => $userId, 'date' => $date]);
        return (int)$stmt->fetchColumn();
    }
        
    /**
     * Create appointment with validation for slot duplication and limit
     */
    public function createAppointment(array $data): int
    {
        // Prevent double booking of same slot
        if ($this->isSlotTaken($data['appointment_date'], $data['appointment_time'])) {
            throw new Exception("This time slot is already booked. Please choose another.");
        }

        // Prevent user from spamming bookings in one day
        $count = $this->countUserAppointmentsForDay($data['user_id'], $data['appointment_date']);
        if ($count >= 3) {
            throw new Exception("You have reached the maximum number of appointments for this day.");
        }

        // Proceed with insertion
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (user_id, service_id, appointment_date, appointment_time, note, status, created_at)
            VALUES (:user_id, :service_id, :appointment_date, :appointment_time, :note, 'pending', NOW())
        ");
        $stmt->execute([
            'user_id' => $data['user_id'],
            'service_id' => $data['service_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'note' => $data['note'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

}
