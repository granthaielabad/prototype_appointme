<div class="container my-5">
    <h2>Invoices</h2>
    <?php if (!empty($invoices)): ?>
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($invoices as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['invoice_id']) ?></td>
                    <td>₱<?= htmlspecialchars($i['amount']) ?></td>
                    <td><?= htmlspecialchars($i['date']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No invoices found.</p>
    <?php endif; ?>
</div>
