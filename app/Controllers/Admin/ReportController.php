<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Appointment;
use Dompdf\Dompdf;

class ReportController extends AdminController
{
    public function index(): void
    {
        $this->render('reports/index', ['pageTitle' => 'Reports']);
    }

    public function summary(): void
    {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end   = $_GET['end'] ?? date('Y-m-d');

        $a = new Appointment();
        $db = $a->getDb();
        $table = $a->getTable();

        $stmt = $db->prepare("
            SELECT appointment_date, COUNT(*) AS total
            FROM {$table}
            WHERE appointment_date BETWEEN :start AND :end
            GROUP BY appointment_date
            ORDER BY appointment_date
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('reports/summary', [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
            'pageTitle' => 'Reports Summary'
        ]);
    }

    public function exportCsv(): void
    {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end   = $_GET['end'] ?? date('Y-m-d');

        $a = new Appointment();
        $db = $a->getDb();
        $table = $a->getTable();

        $stmt = $db->prepare("
            SELECT appointment_date, COUNT(*) AS total
            FROM {$table}
            WHERE appointment_date BETWEEN :start AND :end
            GROUP BY appointment_date
            ORDER BY appointment_date
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=appointments_summary_'.$start.'_to_'.$end.'.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date', 'Total Appointments']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['appointment_date'], $r['total']]);
        }
        fclose($out);
        exit;
    }

    public function exportPdf(): void
    {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end   = $_GET['end'] ?? date('Y-m-d');

        $a = new Appointment();
        $db = $a->getDb();
        $table = $a->getTable();

        $stmt = $db->prepare("
            SELECT appointment_date, COUNT(*) AS total
            FROM {$table}
            WHERE appointment_date BETWEEN :start AND :end
            GROUP BY appointment_date
            ORDER BY appointment_date
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $html = '<h1 style="text-align:center;">Appointments Summary</h1>';
        $html .= '<p style="text-align:center;">From ' . htmlspecialchars($start) . ' to ' . htmlspecialchars($end) . '</p>';
        $html .= '<table width="100%" border="1" cellspacing="0" cellpadding="6">';
        $html .= '<thead><tr style="background:#eee;"><th>Date</th><th>Total Appointments</th></tr></thead><tbody>';

        foreach ($rows as $r) {
            $html .= '<tr><td>' . htmlspecialchars($r['appointment_date']) . '</td><td style="text-align:center;">' . htmlspecialchars($r['total']) . '</td></tr>';
        }

        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('appointments_summary_'.$start.'_to_'.$end.'.pdf', ['Attachment' => true]);
        exit;
    }
}
