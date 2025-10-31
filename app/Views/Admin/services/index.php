<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Services</h2>
    <a href="/admin/services/create" class="btn btn-primary">Add Service</a>
</div>
<table class="table table-striped">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Price</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($services as $s): ?>
        <tr>
            <td><?= $s['service_id'] ?></td>
            <td><?= htmlspecialchars($s['service_name']) ?></td>
            <td>₱<?= htmlspecialchars($s['price']) ?></td>
            <td>
                <a href="/admin/services/edit?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="/admin/services/delete?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
