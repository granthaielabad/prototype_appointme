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
                All appointment
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
                            <tr>
                                <td><?= $a['appointment_id'] ?></td>
                                <td><?= htmlspecialchars($a['full_name']) ?></td>
                                <td><?= htmlspecialchars($a['phone']) ?></td>
                                <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                                <td><?= htmlspecialchars(substr($a['appointment_time'], 0, 5)) ?></td>
                                <td><?= htmlspecialchars($a['status']) ?></td>
                                <td>
                                    <a href="/admin/appointments/edit?id=<?= $a['appointment_id'] ?>" class="text-purple me-2">
                                        <i class="bi bi-pencil"></i>
                                    </a>
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

    </div>
</div>
