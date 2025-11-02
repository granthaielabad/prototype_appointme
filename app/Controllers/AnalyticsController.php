<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Service;

/**
 * Small analytics scaffold. Use to power admin charts.
 */
class AnalyticsController extends Controller
{
    public function dailyAppointments(): void
    {
        $a = new Appointment();
        $stmt = $a->db->query("
            SELECT appointment_date, COUNT(*) as total
            FROM {$a->table}
            GROUP BY appointment_date
            ORDER BY appointment_date DESC
            LIMIT 30
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($rows);
    }
}
