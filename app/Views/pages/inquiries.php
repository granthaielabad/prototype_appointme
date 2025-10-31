<div class="container my-5">
    <h2>Customer Inquiries</h2>
    <?php if (!empty($inquiries)): ?>
        <ul class="list-group">
            <?php foreach ($inquiries as $inq): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($inq['name'] ?? '') ?>:</strong>
                    <?= htmlspecialchars($inq['message'] ?? '') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No inquiries available.</p>
    <?php endif; ?>
</div>
