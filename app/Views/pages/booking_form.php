<div class="container my-5">
    <h2>Book an Appointment</h2>
    <form method="POST" action="/book">
      
    <div class="mb-3">
        <label for="first name">Firstname</label>
        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="form-control" required placeholder="First Name">
    </div>
    
    <div class="mb-3">
        <label for="last_name">Lastname</label>
        <input type="text" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="form-control" required placeholder="Last Name">    
    </div>

    <input type="email" id="email" placeholder="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">


   

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

        <div class="mb-3">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-primary">Confirm Booking</button>
    </form>
</div>
