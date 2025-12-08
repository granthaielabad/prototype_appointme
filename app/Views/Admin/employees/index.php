<?php
$pageTitle = "Employees";
$activePage = "employees";
?>

<div class="admin-section">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="section-title">Employee Management</h6>
            <small class="section-subtitle text-muted">Manage your salon staff and employees</small>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- FILTER DROPDOWN -->
            <div class="dropdown" id="employeeFilterDropdownWrapper">
                <button class="btn dropdown-toggle filter-btn" type="button" id="employeeFilterDropdown" aria-expanded="false">
                    <?php
                        $filterLabels = [
                            'all' => 'All Employees',
                            'active' => 'Active Employees',
                            'inactive' => 'Inactive Employees'
                        ];
                        echo $filterLabels[$currentFilter] ?? 'All Employees';
                    ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="employeeFilterDropdown">
                    <li><a class="dropdown-item" href="?filter=all">All Employees</a></li>
                    <li><a class="dropdown-item" href="?filter=active">Active Employees</a></li>
                    <li><a class="dropdown-item" href="?filter=inactive">Inactive Employees</a></li>
                </ul>
            </div>

            <button class="btn add-service-btn d-flex align-items-center" id="openAddModal">
                <i class="bi bi-person-plus me-2"></i> Add Employee
            </button>
        </div>
    </div>

    <!-- EMPLOYEE LIST -->
    <div class="archive-items-container">
    <?php if (empty($employees)): ?>
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">No employees found</h5>
            <p class="text-muted">Start by adding your first employee.</p>
        </div>
    <?php else: ?>
        <?php foreach ($employees as $employee): ?>
            <div class="employee-card"
                 data-employee='<?= json_encode(
                         $employee,
                         JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT,
                     ) ?>'>
                <div class="d-flex justify-content-between align-items-start">
                    <!-- LEFT SIDE: Employee Info -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Profile Icon -->
                        <div class="employee-avatar">
                            <i class="bi bi-person-fill text-secondary" style="font-size: 24px;"></i>
                        </div>

                        <!-- Employee Details -->
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="fw-semibold" style="font-size: 15px;">
                                    <?= htmlspecialchars($employee["full_name"]) ?>
                                </span>
                                <?php if (isset($employee["is_active"]) && $employee["is_active"]): ?>
                                <span class="badge badge-active">
                                    Active
                                </span>
                                <?php else: ?>
                                <span class="badge badge-inactive">
                                    Inactive
                                </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block" style="font-size: 13px;">
                                <a href="mailto:<?= htmlspecialchars($employee["email"]) ?>" class="email-link" style="color: inherit; text-decoration: none;">
                                    <?= htmlspecialchars($employee["email"]) ?>
                                </a>
                            </small>
                            <small class="text-muted d-block" style="font-size: 13px;">
                                <?= htmlspecialchars($employee["contact_number"] ?: '—') ?>
                            </small>
                            <small class="text-muted d-block" style="font-size: 12px; color: #6b7280;">
                                Hired: <?= htmlspecialchars($employee["hire_date"]) ?>
                            </small>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: Actions -->
                    <div class="d-flex align-items-center gap-2 position-relative">
                        <!-- Edit Button -->
                        <button type="button" class="action-btn openEditModal" aria-label="Edit" title="Edit">
                            <i class="bi bi-pencil" style="font-size: 18px;"></i>
                        </button>
                        <!-- Actions Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="action-btn dropdown-toggle" aria-label="More actions">
                                <i class="bi bi-three-dots-vertical" style="font-size: 18px;"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item toggle-status" href="#" data-id="<?= $employee['id'] ?>" data-status="<?= $employee['is_active'] ? 'active' : 'inactive' ?>">
                                        <i class="bi bi-<?= $employee['is_active'] ? 'pause-circle' : 'play-circle' ?> me-2"></i>
                                        Mark as <?= $employee['is_active'] ? 'Inactive' : 'Active' ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item archive-employee" href="#" data-id="<?= $employee['id'] ?>">
                                        <i class="bi bi-archive me-2"></i>
                                        Archive Employee
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<!-- ADD EMPLOYEE MODAL -->
<div class="custom-modal" id="addEmployeeModal">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h5>Add New Employee</h5>
            <button class="close-modal">&times;</button>
        </div>

        <p class="text-muted mb-3">Create a new employee account for your salon.</p>

        <form action="/admin/employees/store" method="POST">

            <label class="modal-label">Full Name</label>
            <input type="text" name="full_name" class="modal-input" required>

            <label class="modal-label mt-2">Phone Number</label>
            <input type="text" name="phone_number" class="modal-input" required>

            <label class="modal-label mt-2">Email</label>
            <input type="email" name="email" class="modal-input" required>

            <label class="modal-label mt-2">Position</label>
            <select name="position" class="modal-input" required>
                <option value="">Select Position</option>
                <option value="Stylist">Stylist</option>
                <option value="Nail Tech">Nail Tech</option>
                <option value="Assistant">Assistant</option>
                <option value="Manager">Manager</option>
            </select>

            <label class="modal-label mt-2">Address</label>
            <input type="text" name="address" class="modal-input" placeholder="e.g., 123 Main St, City">

            <label class="modal-label mt-2">Work Schedule</label>
            <input type="text" name="work_schedule" class="modal-input" placeholder="e.g., Mon-Fri">

            <label class="modal-label mt-2">Remarks</label>
            <textarea name="remarks" class="modal-input" rows="2" placeholder="Additional notes or remarks"></textarea>

            <button class="btn btn-primary w-100 mt-3" style="background:#CD9FFE;border:none;">
                Add Employee
            </button>
        </form>
    </div>
</div>


<!-- EDIT EMPLOYEE MODAL -->
<div class="custom-modal" id="editEmployeeModal">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h5>Edit Employee</h5>
            <button class="close-modal">&times;</button>
        </div>

        <p class="text-muted mb-3">Update this employee's information.</p>

        <form action="/admin/employees/update" method="POST">

            <input type="hidden" name="id" id="edit_id">

            <label class="modal-label">Full Name</label>
            <input type="text" name="full_name" id="edit_full_name" class="modal-input" required>

            <label class="modal-label mt-2">Phone Number</label>
            <input type="text" name="phone_number" id="edit_phone_number" class="modal-input" required>

            <label class="modal-label mt-2">Email</label>
            <input type="email" name="email" id="edit_email" class="modal-input" required>

            <label class="modal-label mt-2">Position</label>
            <select name="position" id="edit_position" class="modal-input" required>
                <option value="">Select Position</option>
                <option value="Stylist">Stylist</option>
                <option value="Nail Tech">Nail Tech</option>
                <option value="Assistant">Assistant</option>
                <option value="Manager">Manager</option>
            </select>

            <label class="modal-label mt-2">Address</label>
            <input type="text" name="address" id="edit_address" class="modal-input" placeholder="e.g., 123 Main St, City">

            <label class="modal-label mt-2">Work Schedule</label>
            <input type="text" name="work_schedule" id="edit_work_schedule" class="modal-input" placeholder="e.g., Mon-Fri">

            <label class="modal-label mt-2">Remarks</label>
            <textarea name="remarks" id="edit_remarks" class="modal-input" rows="2" placeholder="Additional notes or remarks"></textarea>


            <button class="btn btn-primary w-100 mt-3" style="background:#CD9FFE;border:none;">
                Save Changes
            </button>
        </form>
    </div>
</div>


<!-- DEACTIVATE EMPLOYEE MODAL -->
<div class="custom-modal" id="deleteEmployeeModal">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">

        <!-- Warning Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-exclamation-triangle" style="font-size: 60px; color: #8b0000;"></i>
        </div>

        <!-- Warning Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #8b0000; font-weight: bold;">delete</span> this employee?
        </p>

        <div class="d-flex gap-2 mt-3">
            <a id="confirmDeleteBtn" class="btn btn-outline-danger w-50" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>Delete</strong></a>
            <button class="btn btn-outline-secondary w-50 close-modal" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>No</strong></button>
        </div>
    </div>
</div>


<!-- ARCHIVE EMPLOYEE MODAL -->
<div class="custom-modal" id="archiveEmployeeModal">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">

        <!-- Archive Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-archive" style="font-size: 60px; color: #6b7280;"></i>
        </div>

        <!-- Confirmation Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #6b7280; font-weight: bold;">archive</span> this employee?
        </p>

        <div class="d-flex gap-2 mt-3">
            <a id="confirmArchiveBtn" class="btn btn-outline-secondary w-50" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>Archive</strong></a>
            <button class="btn btn-outline-secondary w-50 close-modal" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>No</strong></button>
        </div>
    </div>
</div>

<!-- ACTIVATE EMPLOYEE MODAL -->
<div class="custom-modal" id="activateEmployeeModal">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">

        <!-- Success Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-check-circle" style="font-size: 60px; color: #28a745;"></i>
        </div>

        <!-- Confirmation Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #28a745; font-weight: bold;">activate</span> this employee?
        </p>

        <div class="d-flex gap-2 mt-3">
            <a id="confirmActivateBtn" class="btn btn-outline-success w-50" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>Activate</strong></a>
            <button class="btn btn-outline-secondary w-50 close-modal" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>No</strong></button>
        </div>
    </div>
</div>

<script src="/assets/js/employee_modals.js"></script>