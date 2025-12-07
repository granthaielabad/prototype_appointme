<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Employee extends User
{
    protected string $table = 'tbl_users';
    protected string $primaryKey = 'user_id';

    /**
     * Get all employees (users with role_id = 2)
     */
    public function getAllEmployees(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE role_id = 2 ORDER BY date_created DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active employees only
     */
    public function getActiveEmployees(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE role_id = 2 AND is_active = 1 ORDER BY date_created DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new employee
     */
    public function createEmployee(array $data): int
    {
        // Ensure role is set to employee (2)
        $data['role_id'] = 2;
        $data['is_active'] = $data['is_active'] ?? 1;

        return $this->create($data);
    }

    /**
     * Update employee information
     */
    public function updateEmployee(int $id, array $data): bool
    {
        // Don't allow role changes through this method
        unset($data['role_id']);

        return $this->update($id, $data);
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['role_id'] == 2;
    }
}



