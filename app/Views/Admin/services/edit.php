<h2>Edit Service</h2>
<form method="POST" action="/admin/services/update">
    <input type="hidden" name="id" value="<?= $service['service_id'] ?>">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="service_name" class="form-control" value="<?= htmlspecialchars($service['service_name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($service['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Price (₱)</label>
        <input type="number" name="price" class="form-control" step="0.01" value="<?= htmlspecialchars($service['price']) ?>" required>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
