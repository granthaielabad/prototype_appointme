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

        <!-- FILTER DROPDOWN -->
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
        <script src="/assets/js/appointments_modals.js"></script>

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
                        <tr>
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
                                    <a href="/admin/appointments/delete?id=<?= $a['appointment_id'] ?>" 
                                       onclick="return confirm('Delete appointment?')" 
                                       class="text-purple">
                                        <i class="bi bi-trash"></i>
                                    </a>
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

    </div>
</div>
