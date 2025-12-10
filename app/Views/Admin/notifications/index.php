<?php
$pageTitle = 'Notifications';
$activePage = 'notifications';
?>
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Notifications (<?= (int) $unread ?> unread)</h5>
        <a href="/admin/notifications/mark-all" class="btn btn-sm btn-outline-secondary">Mark all as read</a>
    </div>

    <?php if (empty($notifications)): ?>
        <p class="text-muted mb-0">No notifications yet.</p>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notifications as $n): ?>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
                   href="/admin/notifications/mark-one?id=<?= $n['admin_notification_id'] ?>">
                    <div>
                        <div class="fw-semibold"><?= htmlspecialchars($n['title']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($n['message']) ?></div>
                    </div>
                    <?php if ((int)$n['is_read'] === 0): ?>
                        <span class="badge bg-danger rounded-pill">new</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
