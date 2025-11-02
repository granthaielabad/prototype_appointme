<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Service;

/**
 * Basic reports for admin. Expand filter/exports as needed.
 */
class ReportController extends AdminController
{
    public function index(): void
    {
        // default view - show quick metrics and links
        $this->render('reports/index', []);
    }

    /**
     * Return appointment counts and revenue for a date range (GET: start, end)
     */
    public function summary(): void
    {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end   = $_GET['end'] ?? date('Y-m-d');

        $a = new Appointment();
        $stmt = $a->db->prepare("
            SELECT appointment_date, COUNT(*) as total
            FROM {$a->table}
            WHERE appointment_date BETWEEN :start AND :end
            GROUP BY appointment_date
            ORDER BY appointment_date
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('reports/summary', ['rows' => $rows, 'start' => $start, 'end' => $end]);
    }
}
