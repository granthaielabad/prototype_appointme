<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function list(): void
    {
        Auth::requireRole(2);
        $user = Auth::user();
        $n = new Notification();
        $rows = $n->findByUser($user["user_id"]);
        header("Content-Type: application/json");
        echo json_encode($rows);
        exit();
    }

    public function markAllRead(): void
    {
        Auth::requireRole(2);
        $user = Auth::user();
        (new Notification())->markAllRead($user["user_id"]);
        Session::flash("success", "All notifications marked read", "success");
        header("Location: /book");
        exit();
    }
}
