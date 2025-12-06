<?php
$userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$formatMoney = static function ($value) {
    return number_format((float) $value, 0, '.', ','); // match screenshot (no decimals)
};
?>
<style>

</style>

<div class="container py-4">
  <?php if (empty($invoices)): ?>
    <div class="alert alert-info">No invoices yet.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($invoices as $inv): ?>
        <?php
          $appointmentNo = str_pad((string) ($inv['appointment_id'] ?? ''), 5, '0', STR_PAD_LEFT);
          $invoiceNo = str_pad((string) ($inv['invoice_number'] ?? ''), 5, '0', STR_PAD_LEFT);
        ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card invoice-card h-100">
            <div class="invoice-header d-flex justify-content-between align-items-center">
              <div class="invoice-logo">
                <img src="../../../assets/img/apple-touch-icon.png" alt="Logo">
              </div>
              <div class="text-end">
                <div class="invoice-title">Invoice</div>
                <div class="invoice-date"><?= date('F j, Y', strtotime($inv['issued_at'])) ?></div>
              </div>
            </div>

            <div class="card-body">
              <div class="invoice-meta mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="label">Billing To:</span>
                  <span class="value"><?= htmlspecialchars($userName ?: 'Guest') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="label">Appointment #:</span>
                  <span class="value"><?= htmlspecialchars($appointmentNo) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="label">Invoice #:</span>
                  <span class="value"><?= htmlspecialchars($invoiceNo) ?></span>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-sm mb-0 invoice-table">
                  <thead class="fw-bold">
                    <tr>
                      <th>Service/s</th>
                      <th class="text-end">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?= htmlspecialchars($inv['service_name']) ?></td>
                      <td class="text-end"><?= $formatMoney($inv['price'] ?? 0) ?></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Total:</th>
                      <th class="text-end"><?= $formatMoney($inv['total'] ?? ($inv['price'] ?? 0)) ?></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
              <div class="d-flex gap-2">
                <a href="/invoices/show?id=<?= (int) $inv['invoice_id'] ?>" class="btn btn-light border invoice-btn w-50">View</a>
                <a href="/invoices/show?id=<?= (int)$inv['invoice_id'] ?>" class="btn btn-outline-secondary invoice-btn w-50 js-download-invoice" data-invoice-id="<?= (int)$inv['invoice_id'] ?>">Download</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
