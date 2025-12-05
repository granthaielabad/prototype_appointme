<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Service;

class AnalyticsController extends Controller
{
    public function dailyAppointments(): void
    {
        $a = new Appointment();
        $db = $a->getDb();
        $table = $a->getTable();

        $stmt = $db->query("
            SELECT appointment_date, COUNT(*) as total
            FROM {$table}
            GROUP BY appointment_date
            ORDER BY appointment_date DESC
            LIMIT 30
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($rows);
    }

    public function servicePopularity(): void
    {
        $s = new Service();
        $db = $s->getDb();

        $stmt = $db->query("
            SELECT s.service_name, COUNT(a.appointment_id) as total
            FROM tbl_services s
            LEFT JOIN tbl_appointments a ON s.service_id = a.service_id
            GROUP BY s.service_id
            ORDER BY total DESC
            LIMIT 10
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($rows);
    }
}
