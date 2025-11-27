<div class="mb-2 booking-card">
  <div class="d-flex justify-content-between">
    <div>
      <strong>Invoice #<?= htmlspecialchars($inv["invoice_id"]) ?></strong>
      <div class="text-muted small"><?= htmlspecialchars($inv["date"]) ?></div>
    </div>
    <div class="text-end">
      <span class="badge badge-accent">₱<?= number_format((float) $inv["amount"], 2) ?></span>
      <div class="mt-2"><a href="/invoices/view?id=<?= $inv[
          "invoice_id"
      ] ?>" class="small">View</a></div>
    </div>
  </div>
</div>
