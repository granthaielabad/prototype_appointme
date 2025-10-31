<div class="container my-5">
    <h2>Salon Services</h2>
    <p class="text-muted">Here are some of our available services:</p>
    <div class="row">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($service['name'] ?? '') ?></h5>
                            <p><?= htmlspecialchars($service['description'] ?? '') ?></p>
                            <strong>₱<?= htmlspecialchars($service['price'] ?? '0') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No services available yet.</p>
        <?php endif; ?>
    </div>
</div>
