<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Inquiry Details</h3>
    <a href="/admin/inquiries" class="btn btn-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Back to List
    </a>
  </div>

  <?php if (!empty($inquiry)): ?>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-md-3 fw-semibold">Full Name</dt>
          <dd class="col-md-9"><?= htmlspecialchars($inquiry['full_name'] ?? 'Unknown') ?></dd>

          <dt class="col-md-3 fw-semibold">Email</dt>
          <dd class="col-md-9"><?= htmlspecialchars($inquiry['email']) ?></dd>

          <dt class="col-md-3 fw-semibold">Phone</dt>
          <dd class="col-md-9"><?= htmlspecialchars($inquiry['phone'] ?: '—') ?></dd>

          <dt class="col-md-3 fw-semibold">Message</dt>
          <dd class="col-md-9">
            <div class="p-3 bg-light rounded border"><?= nl2br(htmlspecialchars($inquiry['message'])) ?></div>
          </dd>

          <dt class="col-md-3 fw-semibold">Status</dt>
          <dd class="col-md-9">
            <span class="badge 
              <?= $inquiry['status'] === 'new' ? 'bg-primary' : 
                 ($inquiry['status'] === 'replied' ? 'bg-success' : 'bg-secondary') ?>">
              <?= ucfirst($inquiry['status']) ?>
            </span>
          </dd>

          <dt class="col-md-3 fw-semibold">Date Submitted</dt>
          <dd class="col-md-9">
            <?= date('F j, Y g:i A', strtotime($inquiry['created_at'])) ?>
          </dd>
        </dl>
      </div>
    </div>

    <form method="POST" action="/admin/inquiries/update-status" class="mt-4">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <input type="hidden" name="id" value="<?= htmlspecialchars($inquiry['inquiry_id']) ?>">

      <div class="d-flex gap-2">
        <select name="status" class="form-select w-auto">
          <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
          <option value="replied" <?= $inquiry['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
          <option value="archived" <?= $inquiry['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
        </select>

        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> Update Status
        </button>
      </div>
    </form>
  <?php else: ?>
    <div class="alert alert-warning">No inquiry data found.</div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>
