<div class="mb-2 booking-card booking-status pending">
  <div class="d-flex justify-content-between">
    <div>
      <strong><?= htmlspecialchars($b["service_name"] ?? "Service") ?></strong>
      <div class="text-muted small"><?= htmlspecialchars(
          $b["appointment_date"],
      ) ?> • <?= htmlspecialchars(substr($b["appointment_time"], 0, 5)) ?></div>
    </div>
    <div class="text-end">
      <span class="badge bg-warning text-dark">Pending</span>
      <div class="mt-2"><a href="/cancel?id=<?= $b[
          "appointment_id"
      ] ?>" class="text-danger small">Cancel</a></div>
    </div>
  </div>
</div>
