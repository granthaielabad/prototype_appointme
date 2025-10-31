<h2>Manage Appointments</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Change</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['full_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                <td><?= htmlspecialchars(substr($a['appointment_time'], 0, 5)) ?></td>
                <td><?= htmlspecialchars($a['status']) ?></td>
                <td>
                    <form method="POST" action="/admin/appointments/updateStatus" class="d-flex">
                        <input type="hidden" name="id" value="<?= $a['appointment_id'] ?>">
                        <select name="status" class="form-select form-select-sm me-2">
                            <option value="pending" <?= $a['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $a['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="completed" <?= $a['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $a['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
