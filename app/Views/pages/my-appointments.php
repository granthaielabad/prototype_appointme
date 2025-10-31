<div class="container my-5">
    <h2>My Appointments</h2>

    <?php if (empty($appointments)): ?>
        <p>You have no appointments yet.</p>
        <a href="/book" class="btn btn-primary">Book Now</a>
    <?php else: ?>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date (YYYY-MM-DD)</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['service_name']) ?></td>
                        <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                        <td><?= htmlspecialchars(substr($a['appointment_time'], 0, 5)) ?></td>
                        <td>
                            <?php if ($a['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif ($a['status'] == 'confirmed'): ?>
                                <span class="badge bg-success">Confirmed</span>
                            <?php elseif ($a['status'] == 'completed'): ?>
                                <span class="badge bg-secondary">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($a['status'] == 'pending'): ?>
                                <a href="/cancel-appointment?id=<?= $a['appointment_id'] ?>" class="btn btn-sm btn-outline-danger">
                                    Cancel
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
