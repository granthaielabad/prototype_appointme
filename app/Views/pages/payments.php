<div class="container my-5">
    <h2>Payments</h2>
    <?php if (!empty($payments)): ?>
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['payment_id']) ?></td>
                    <td>₱<?= htmlspecialchars($p['amount']) ?></td>
                    <td><?= htmlspecialchars($p['method']) ?></td>
                    <td><?= htmlspecialchars($p['date']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payments yet.</p>
    <?php endif; ?>
</div>
