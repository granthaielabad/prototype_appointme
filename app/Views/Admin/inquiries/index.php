<div class="container-fluid px-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary">Inquiries</h4>
  </div>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">
      <table id="inquiriesTable" class="table table-hover table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($inquiries)): ?>
            <?php foreach ($inquiries as $i => $inq): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($inq['full_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($inq['email'] ?? 'N/A') ?></td>
                <td><?= substr(htmlspecialchars($inq['message'] ?? ''), 0, 60) ?>...</td>
                <td><?= htmlspecialchars(date('M d, Y', strtotime($inq['created_at'] ?? 'now'))) ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?= $inq['inquiry_id'] ?>">
                    <i class="bi bi-eye"></i>
                  </button>
                  <a href="/admin/inquiries/delete/<?= $inq['inquiry_id'] ?>" onclick="return confirm('Are you sure you want to delete this inquiry?')" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>

              <!-- View Modal -->
              <div class="modal fade" id="viewModal<?= $inq['inquiry_id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $inq['inquiry_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="viewModalLabel<?= $inq['inquiry_id'] ?>">Inquiry from <?= htmlspecialchars($inq['name'] ?? 'Unknown') ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Email:</strong> <?= htmlspecialchars($inq['email'] ?? 'N/A') ?></p>
                      <p><strong>Message:</strong></p>
                      <p class="border rounded p-2 bg-light"><?= nl2br(htmlspecialchars($inq['message'] ?? '')) ?></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center py-3 text-muted">No inquiries found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new DataTable('#inquiriesTable');
});
</script>
