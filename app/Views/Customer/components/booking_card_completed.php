<div class="mb-2 booking-card booking-status completed">
  <div class="d-flex justify-content-between">
    <div>
      <strong><?= htmlspecialchars($b["service_name"] ?? "Service") ?></strong>
      <div class="text-muted small"><?= htmlspecialchars(
          $b["appointment_date"],
      ) ?> • <?= htmlspecialchars(substr($b["appointment_time"], 0, 5)) ?></div>
    </div>
    <div class="text-end">
      <span class="badge bg-success">Completed</span>
      <div class="mt-2"><a href="/invoices/view?id=<?= $b[
          "appointment_id"
      ] ?>" class="small">View invoice</a></div>
    </div>
  </div>
</div>
