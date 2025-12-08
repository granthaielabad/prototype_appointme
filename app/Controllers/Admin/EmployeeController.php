<?php
namespace App\Controllers\Admin;

use App\Core\Session;
use PDO;

/**
 * Admin employee CRUD management.
 */
class EmployeeController extends AdminController
{
    public function index(): void
    {
        $filter = $_GET['filter'] ?? 'all';

        // Validate filter parameter
        if (!in_array($filter, ['all', 'active', 'inactive'])) {
            $filter = 'all';
        }

        $db = \App\Core\Database::getConnection();

        $sql = "SELECT * FROM tbl_employees WHERE is_deleted = 0";
        if ($filter === 'active') {
            $sql .= " AND is_active = 1";
        } elseif ($filter === 'inactive') {
            $sql .= " AND is_active = 0";
        }
        $sql .= " ORDER BY hire_date DESC";

        $stmt = $db->query($sql);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('employees/index', ['employees' => $employees, 'currentFilter' => $filter]);
    }

    public function create(): void
    {
        $this->render('employees/create');
    }

    public function store(): void
    {
        if (empty($_POST['full_name'])) {
            Session::flash('error', 'Please fill in required fields.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        // Create employee record in tbl_employees
        $this->createEmployeeRecord($_POST);

        Session::flash('success', 'Employee created successfully.', 'success');
        header('Location: /admin/employees');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Invalid employee ID.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tbl_employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            Session::flash('error', 'Employee not found.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $this->render('employees/edit', ['employee' => $employee]);
    }

    public function update(): void
    {
        if (empty($_POST['id'])) {
            Session::flash('error', 'Invalid update request.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tbl_employees WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            Session::flash('error', 'Employee not found.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $updates = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'] ?? null,
            'contact_number' => $_POST['phone_number'] ?? null,
            'position' => $_POST['position'] ?? null,
            'address' => $_POST['address'] ?? null,
            'work_schedule' => $_POST['work_schedule'] ?? null,
            'remarks' => $_POST['remarks'] ?? null,
        ];

        $setClause = [];
        $params = [];
        foreach ($updates as $field => $value) {
            $setClause[] = "$field = ?";
            $params[] = $value;
        }
        $params[] = $_POST['id'];

        $sql = "UPDATE tbl_employees SET " . implode(', ', $setClause) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        Session::flash('success', 'Employee updated successfully.', 'success');
        header('Location: /admin/employees');
        exit;
    }

    public function toggleStatus(): void
    {
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            Session::flash('error', 'Invalid request.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $db = \App\Core\Database::getConnection();

        // Check if employee exists
        $stmt = $db->prepare("SELECT * FROM tbl_employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            Session::flash('error', 'Employee not found.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        // Update status
        $newStatus = ($status === 'active') ? 0 : 1;
        $stmt = $db->prepare("UPDATE tbl_employees SET is_active = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        $statusText = $newStatus ? 'activated' : 'deactivated';
        Session::flash('success', "Employee {$statusText} successfully.", 'success');
        header('Location: /admin/employees');
        exit;
    }

    public function archive(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Invalid archive request.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $db = \App\Core\Database::getConnection();

        // Get the employee data
        $stmt = $db->prepare("SELECT * FROM tbl_employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            Session::flash('error', 'Employee not found.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $adminId = \App\Core\Auth::user()['user_id'] ?? null;

        // 1) Create archive snapshot
        $archive = new \App\Models\Archive();
        $snapOk = $archive->archive(
            'employee',
            (int)$id,
            $employee['full_name'],
            $employee,
            $adminId
        );

        if (!$snapOk) {
            Session::flash('error', 'Failed to create archive snapshot.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        // 2) Soft delete from main table (mark as deleted)
        $stmt = $db->prepare("UPDATE tbl_employees SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ?");
        $ok = $stmt->execute([$adminId, $id]);

        if (!$ok) {
            Session::flash('error', 'Failed to archive employee.', 'danger');
        } else {
            Session::flash('success', 'Employee archived successfully.', 'success');
        }

        header('Location: /admin/employees');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Invalid delete request.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tbl_employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            Session::flash('error', 'Employee not found.', 'danger');
            header('Location: /admin/employees');
            return;
        }

        // Hard delete the employee record
        $stmt = $db->prepare("DELETE FROM tbl_employees WHERE id = ?");
        $stmt->execute([$id]);

        Session::flash('success', 'Employee deleted successfully.', 'success');
        header('Location: /admin/employees');
        exit;
    }


    /**
     * Create employee record in tbl_employees
     */
    private function createEmployeeRecord(array $data): void
    {
        $db = \App\Core\Database::getConnection();
        $hireDate = date('Y-m-d'); // Set hire date to today

        // Generate employee number (EMP001, EMP002, etc.)
        $employeeNumber = $this->generateEmployeeNumber($db);

        $stmt = $db->prepare("INSERT INTO tbl_employees (full_name, email, contact_number, employee_number, hire_date, position, address, work_schedule, remarks, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['full_name'],
            $data['email'] ?? null,
            $data['phone_number'] ?? null,
            $employeeNumber,
            $hireDate,
            $data['position'] ?? null,
            $data['address'] ?? null,
            $data['work_schedule'] ?? null,
            $data['remarks'] ?? null,
            1 // is_active defaults to 1 (active)
        ]);
    }

    /**
     * Generate unique employee number
     */
    private function generateEmployeeNumber(PDO $db): string
    {
        // Get the highest existing employee number
        $stmt = $db->query("SELECT employee_number FROM tbl_employees WHERE employee_number IS NOT NULL ORDER BY CAST(SUBSTRING(employee_number, 4) AS UNSIGNED) DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['employee_number']) {
            // Extract number from EMP001 format and increment
            $number = (int) substr($result['employee_number'], 3);
            $nextNumber = $number + 1;
        } else {
            // Start with 1 if no employees exist
            $nextNumber = 1;
        }

        // Format as EMP001, EMP002, etc.
        return 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }


}

