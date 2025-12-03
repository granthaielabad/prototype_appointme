<?php
$userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-1">Hello, <?= htmlspecialchars($userName ?: 'Guest') ?>!</h2>
      <p class="text-muted mb-0">Here are your invoices.</p>
    </div>
   
  </div>

  <?php if (empty($invoices)): ?>
    <div class="alert alert-info">No invoices yet.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($invoices as $inv): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card shadow-sm h-100" style="border-radius:14px;">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <div class="fw-bold fs-5">Invoice</div>
                  <div class="text-muted small">
                    <?= date('F j, Y', strtotime($inv['issued_at'])) ?>
                  </div>
                </div>


                <!--  LOGO  -->
                <img src="../../../assets/img/apple-touch-icon.png" alt="Logo" style="height:40px;object-fit:contain;">
              </div>

              <div class="mb-3 small">
                <div class="fw-semibold">Billing To:</div>
                <div><?= htmlspecialchars($userName) ?></div>
                <div>Appointment #: <?= htmlspecialchars($inv['appointment_id']) ?></div>
                <div>Invoice #: <?= htmlspecialchars($inv['invoice_number']) ?></div>
              </div>

              <div class="table-responsive mb-3">
                <table class="table table-sm mb-0">
                  <thead>
                    <tr>
                      <th>Service/s</th>
                      <th class="text-end">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?= htmlspecialchars($inv['service_name']) ?></td>
                      <td class="text-end"><?= number_format((float)$inv['price'], 2) ?></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Total:</th>
                      <th class="text-end"><?= number_format((float)$inv['total'], 2) ?></th>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="d-flex gap-2 mt-auto">
                <a href="/invoices/show?id=<?= (int)$inv['invoice_id'] ?>" class="btn btn-outline-secondary w-50">View</a>
                <a href="/invoices/show?id=<?= (int)$inv['invoice_id'] ?>&print=1" class="btn btn-primary w-50" target="_blank" rel="noopener">Print</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
