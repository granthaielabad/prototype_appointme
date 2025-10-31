<h2>Add Service</h2>
<form method="POST" action="/admin/services/store">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="service_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label>Price (₱)</label>
        <input type="number" name="price" class="form-control" step="0.01" required>
    </div>
    <button class="btn btn-success">Save</button>
</form>
