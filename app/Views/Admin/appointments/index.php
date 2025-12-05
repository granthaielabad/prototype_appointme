<?php
$pageTitle = "Appointments";
$activePage = "appointments";
?>

<div class="admin-section">

    <!-- SECTION TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="section-title">Appointments</h6>
            <small class="section-subtitle text-muted">Manage customer appointments and bookings</small>
        </div>

        <!-- FILTERS -->
        <div class="d-flex gap-2">
            <!-- Date Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light border d-flex align-items-center dropdown-toggle" type="button" id="apptDateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar2-date me-2"></i> Date Filter
                </button>

                <div class="dropdown-menu p-3" aria-labelledby="apptDateFilterDropdown" style="width: 320px;">
                    <h6 class="dropdown-header px-0 py-0 mb-3">Pick a Date Range</h6>
                    
                    <!-- Calendar Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button id="apptPrevMonth" class="btn btn-sm btn-light">&lt;</button>
                        <h6 id="apptMonthYear" class="mb-0 fw-semibold"></h6>
                        <button id="apptNextMonth" class="btn btn-sm btn-light">&gt;</button>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="mb-3">
                        <div class="row g-1 text-center mb-2">
                            <div class="col text-muted small fw-semibold">Su</div>
                            <div class="col text-muted small fw-semibold">Mo</div>
                            <div class="col text-muted small fw-semibold">Tu</div>
                            <div class="col text-muted small fw-semibold">We</div>
                            <div class="col text-muted small fw-semibold">Th</div>
                            <div class="col text-muted small fw-semibold">Fr</div>
                            <div class="col text-muted small fw-semibold">Sa</div>
                        </div>
                        <div id="apptCalendarDays" class="row g-1"></div>
                    </div>

                    <!-- Date Range Display -->
                    <p class="text-muted small mb-3" id="apptFilterDateRange">
                        <span id="apptStartDateDisplay">-</span> to <span id="apptEndDateDisplay">-</span>
                    </p>

                    <!-- Hidden inputs to store selected dates -->
                    <input type="hidden" id="apptDateFilterStart">
                    <input type="hidden" id="apptDateFilterEnd">

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm flex-grow-1" id="apptResetDateFilter">Reset</button>
                    </div>
                </div>
            </div>

            <!-- Status Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <?php
                        $filterLabels = [
                            'all' => 'All appointments',
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled'
                        ];
                        echo $filterLabels[$currentFilter] ?? 'All appointments';
                    ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?filter=all">All</a></li>
                    <li><a class="dropdown-item" href="?filter=pending">Pending</a></li>
                    <li><a class="dropdown-item" href="?filter=confirmed">Confirmed</a></li>
                    <li><a class="dropdown-item" href="?filter=completed">Completed</a></li>
                    <li><a class="dropdown-item" href="?filter=cancelled">Cancelled</a></li>
                </ul>
            </div>
        </div>
    </div>
        <script src="/assets/js/appointments_modals.js"></script>
        <script src="/assets/js/appointment_realtime.js"></script>
        <script src="/assets/js/appointments_date_filter.js"></script>
        <script src="/assets/js/archive_warning_modal.js"></script>
    

    <!-- CONTENT CARD -->
    <div class="card content-card p-3">

        <h6 class="mb-3 fw-semibold">All Appointment:</h6>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Appointment Number</th>
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                        <th style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($appointments)): ?>
                        <tr data-empty-state="true">
                            <td colspan="7" class="text-center text-muted py-4">No appointments available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $a): ?>
                            <tr data-appointment='<?= json_encode($a) ?>'>
                                <td><?= $a['appointment_id'] ?></td>
                                <td><?= htmlspecialchars($a['full_name']) ?></td>
                                <td><?= htmlspecialchars($a['phone'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                                <td><?= date("h:i A", strtotime($a['appointment_time'])) ?></td>
                                <td><?= htmlspecialchars($a['status']) ?></td>
                                <td>
                                    <button type="button" class="text-purple me-2 btn btn-link p-0 openEditModal" aria-label="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="text-purple btn btn-link p-0 openArchiveWarningModal" 
                                            data-id="<?= $a['appointment_id'] ?>" 
                                            data-name="Appointment #<?= $a['appointment_id'] ?>"
                                            aria-label="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
<!-- Edit Modals -->

<div class="custom-modal" id="editAppointmentModal" style="display:none;">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h5>Edit Appointment</h5>
            <button class="close-modal">&times;</button>
        </div>

        <p class="text-muted mb-3">Update appointment details.</p>

        <form action="/admin/appointments/update" method="POST">

            <input type="hidden" name="id" id="edit_id">

            <label class="modal-label">Appointment Date</label>
            <input type="date" name="appointment_date" id="edit_appointment_date" class="modal-input" required>

            <label class="modal-label mt-2">Appointment Time</label>
            <select name="appointment_time" id="edit_appointment_time" class="modal-input" required>
                <option value="">-- Select Time --</option>
                <option value="09:00:00">09:00 AM</option>
                <option value="10:00:00">10:00 AM</option>
                <option value="11:00:00">11:00 AM</option>
                <option value="12:00:00">12:00 PM</option>
                <option value="13:00:00">01:00 PM</option>
                <option value="14:00:00">02:00 PM</option>
                <option value="15:00:00">03:00 PM</option>
                <option value="16:00:00">04:00 PM</option>
                <option value="17:00:00">05:00 PM</option>
            </select>

            <label class="modal-label mt-2">Status</label>
            <select name="status" id="edit_status" class="modal-input" required>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <button class="btn btn-primary w-100 mt-3" style="background:#CD9FFE;border:none;">
                Save Changes
            </button>
        </form>
    </div>
</div>

<!-- Archive Warning Modal -->
<div class="custom-modal" id="archiveWarningModal" style="display: none;">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">
        <!-- Warning Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-exclamation-triangle" style="font-size: 60px; color: #8b0000;"></i>
        </div>

        <!-- Warning Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #8b0000; font-weight: bold;">archive</span> <span id="warningItemName">this item</span>?
        </p>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button class="btn btn-danger w-50" id="confirmArchiveBtn" style="background:#8b0000; border:none;">
                Delete Service
            </button>
            <button class="btn btn-outline-secondary w-50" id="cancelArchiveBtn" style="border: 1px solid #ccc;">
                No
            </button>
        </div>
    </div>
</div>

    </div>
</div>
