<div class="container my-5">
    <h2>Book an Appointment</h2>
    <form method="POST" action="/book">
        <div class="mb-3">
            <label for="service">Select Service</label>
            <select name="service_id" id="service" class="form-select" required>
                <option value="">-- Choose a Service --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['service_id'] ?>">
                        <?= htmlspecialchars($service['service_name']) ?> (₱<?= htmlspecialchars($service['price']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label>Time</label>
            <input type="time" name="time" class="form-control" required>
        </div>

        <button class="btn btn-primary">Confirm Booking</button>
    </form>
</div>
