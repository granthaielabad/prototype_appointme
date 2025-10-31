<?php // header+navbar included by Controller::view ?>
<div class="container my-5">
    <h1>Welcome to AppointMe</h1>
    <p class="lead">Read-only landing for unregistered customers.</p>

    <h3 class="mt-4">Our Services</h3>
    <?php if (!empty($servicesByCat)): ?>
        <?php foreach ($servicesByCat as $cat=>$services): ?>
            <h5 class="mt-3"><?= htmlspecialchars($cat) ?></h5>
            <div class="row">
                <?php foreach ($services as $s): ?>
                    <div class="col-md-4 mb-2">
                        <div class="card p-2">
                            <strong><?= htmlspecialchars($s['service_name']) ?></strong>
                            <div>₱<?= htmlspecialchars($s['price']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No services yet.</p>
    <?php endif; ?>
</div>
