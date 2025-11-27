<?php
$pageTitle = "Archive";
$activePage = "archives";
?>

<div class="admin-section">

    <!-- HEADER -->
    <div class="mb-4">
        <h6 class="section-title">Archive</h6>
        <small class="section-subtitle text-muted">
            View, restore, or permanently remove archived records.
        </small>
    </div>

    <!-- CONTENT CARD -->
    <div class="card content-card p-3">

        <h6 class="fw-semibold mb-3">Archived Items</h6>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Archived At</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No archived items found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($item['item_type']) ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($item['archived_at'])) ?></td>
                                <td>
                                    <a href="/admin/archives/restore?id=<?= $item['archive_id'] ?>"
                                       class="text-purple me-3">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                    <a href="/admin/archives/delete?id=<?= $item['archive_id'] ?>"
                                       onclick="return confirm('Permanently delete item?')"
                                       class="text-purple">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>
