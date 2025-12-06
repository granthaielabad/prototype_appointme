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
public function findByUser(int $userId, ?int $limit = null): array
{
    $query = "
        SELECT 
            a.*, 
            s.service_name, 
            s.category,
            s.duration_minutes
        FROM {$this->table} a
        JOIN tbl_services s ON a.service_id = s.service_id
        WHERE a.user_id = :user_id
          AND a.is_deleted = 0
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    if ($limit !== null) {
        $query .= " LIMIT :limit";
    }

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    if ($limit !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();

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
            WHERE a.is_deleted = 0
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
            WHERE a.is_deleted = 0
        ";
        
        // Add status filter if provided and not 'all'
        if (!empty($status) && $status !== 'all') {
            $query .= " AND a.status = :status";
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
            AND is_deleted = 0
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
            AND is_deleted = 0
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

            // Limit to one booking per user per day (exclude cancelled)
            $count = $this->countUserAppointmentsForDay($data['user_id'], $data['appointment_date']);
            if ($count >= 1) {
                throw new Exception("You already have an appointment for this day.");
            }

            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (user_id, service_id, appointment_date, appointment_time, notes, status, created_at)
                VALUES (:user_id, :service_id, :appointment_date, :appointment_time, :notes, 'pending', NOW())
            ");
            $stmt->execute([
                'user_id'          => $data['user_id'],
                'service_id'       => $data['service_id'],
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'notes'            => $data['notes'] ?? null
            ]);

            return (int)$this->db->lastInsertId();
        }

        // for greying out the already scheduled.
       public function getTakenSlotsForDate(string $date): array 
{
    $stmt = $this->db->prepare("
        SELECT DATE_FORMAT(appointment_time, '%h:%i %p') AS appointment_time
        FROM {$this->table}
        WHERE appointment_date = :date
          AND status NOT IN ('cancelled', 'failed')
    ");
    $stmt->execute(['date' => $date]);
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'appointment_time');
}





    /**
     * Archive an appointment (soft delete + snapshot to tbl_archives)
     */
    public function archive(int|string $id, ?int $adminId = null): bool
    {
        try {
            error_log("Appointment::archive start - id={$id} admin={$adminId}");
            // Get the appointment data with user name
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name
                FROM {$this->table} a
                JOIN tbl_users u ON a.user_id = u.user_id
                WHERE a.appointment_id = :id
            ");
            $stmt->execute(['id' => $id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                error_log("Appointment::archive - appointment not found id={$id}");
                return false;
            }

            // 1) Snapshot to tbl_archives using the user's full name
            $archive = new Archive();
            $snapOk = $archive->archive(
                'appointment',
                (int)$id,
                $appointment['full_name'],
                $appointment,
                $adminId
            );

            if (!$snapOk) {
                error_log("Appointment::archive - snapshot failed for id={$id}");
                return false;
            }

            // 2) Soft delete from main table
            $sql = "UPDATE {$this->table}
                    SET is_deleted = 1, deleted_at = NOW(), deleted_by = :admin_id
                    WHERE {$this->primaryKey} = :id";
            $update = $this->db->prepare($sql);
            $ok = $update->execute(['id' => $id, 'admin_id' => $adminId]);
            if (!$ok) {
                error_log('Appointment::archive - soft delete failed: ' . json_encode($update->errorInfo()));
            } else {
                error_log("Appointment::archive - soft delete success id={$id}");
            }
            return $ok;
        } catch (\Throwable $e) {
            error_log('Error archiving appointment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore a soft-deleted appointment.
     */
    public function restore(int|string $id): bool
    {
        try {
            error_log("Appointment::restore start - id={$id}");
            
            // 1) Restore in main table
            $sql = "UPDATE {$this->table}
                    SET is_deleted = 0, deleted_at = NULL, deleted_by = NULL
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->getDb()->prepare($sql);
            $ok = $stmt->execute(['id' => $id]);
            
            if (!$ok) {
                error_log('Appointment::restore - main table restore failed: ' . json_encode($stmt->errorInfo()));
                return false;
            }
            
            // 2) Mark ALL archive snapshots for this appointment as inactive (is_archived = 0)
            // Use a simpler approach compatible with x10hosting: find archives then update by ID
            $findSql = "SELECT archive_id FROM tbl_archives 
                        WHERE item_type = 'appointment' AND is_archived = 1";
            $findStmt = $this->getDb()->query($findSql);
            $archives = $findStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($archives as $archive) {
                // Decode the JSON to check if it matches this appointment_id
                $archiveSql = "SELECT item_data FROM tbl_archives WHERE archive_id = :archive_id";
                $archiveStmt = $this->getDb()->prepare($archiveSql);
                $archiveStmt->execute(['archive_id' => $archive['archive_id']]);
                $archiveRow = $archiveStmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($archiveRow && !empty($archiveRow['item_data'])) {
                    $itemData = json_decode($archiveRow['item_data'], true);
                    // If this archive is for the appointment being restored, deactivate it
                    if (isset($itemData['appointment_id']) && $itemData['appointment_id'] == $id) {
                        $updateSql = "UPDATE tbl_archives SET is_archived = 0 WHERE archive_id = :archive_id";
                        $updateStmt = $this->getDb()->prepare($updateSql);
                        $updateStmt->execute(['archive_id' => $archive['archive_id']]);
                        error_log("Appointment::restore - deactivated archive " . $archive['archive_id']);
                    }
                }
            }
            
            error_log("Appointment::restore - completed id={$id}");
            return true;
        } catch (\Throwable $e) {
            error_log('Error restoring appointment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete an appointment row.
     */
    public function hardDelete(int|string $id): bool
    {
        return $this->delete($id);
    }

}
