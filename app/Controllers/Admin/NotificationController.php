<?php
namespace App\Controllers\Admin;

use App\Models\AdminNotification;

class NotificationController extends AdminController
{
    public function index(): void
    {
        $n = new AdminNotification();
        $items = $n->latest(50);
        $unread = $n->countUnread();

        $this->render('notifications/index', [
            'notifications' => $items,
            'unread' => $unread,
        ]);
    }

    public function markAll(): void
    {
        (new AdminNotification())->markAllRead();
        header('Location: /admin/notifications');
        exit();
    }

    public function markOne(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            (new AdminNotification())->markRead($id);
        }
        header('Location: /admin/notifications');
        exit();
    }
}
