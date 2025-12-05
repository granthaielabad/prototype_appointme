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

    public function export(): void
    {
        $format = $_POST['format'] ?? 'pdf';
        $type = $_POST['type'] ?? 'appointments';
        $period = $_POST['period'] ?? 'all';

        // Determine date range based on period
        $start = null;
        $end = null;

        switch ($period) {
            case 'daily':
                $start = date('Y-m-d');
                $end = date('Y-m-d');
                break;
            case 'weekly':
                $start = date('Y-m-d', strtotime('monday this week'));
                $end = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'monthly':
                $start = date('Y-m-01');
                $end = date('Y-m-t');
                break;
            case 'all':
                // For "all" period, don't filter by date - show all historical data
                $start = null;
                $end = null;
                break;
            default:
                $start = date('Y-m-01', strtotime('first day of january this year'));
                $end = date('Y-m-d');
                break;
        }

        if ($format === 'csv') {
            $this->exportCsvByType($type, $start, $end);
        } else {
            $this->exportPdfByType($type, $start, $end);
        }
    }

    private function exportCsvByType(string $type, ?string $start, ?string $end): void
    {
        $db = \App\Core\Database::getConnection();

        $query = "";
        $filename = "";
        $headers = [];

        switch ($type) {
            case 'appointments':
                $query = "
                    SELECT a.appointment_date, a.appointment_time, s.service_name, a.status, u.first_name, u.last_name, u.email
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    WHERE a.appointment_date BETWEEN ? AND ?
                    ORDER BY a.appointment_date, a.appointment_time
                ";
                $filename = "appointments_{$start}_to_{$end}";
                $headers = ['Date', 'Time', 'Service', 'Status', 'Customer Name', 'Email'];
                break;

            case 'sales':
                $query = "
                    SELECT a.appointment_date, s.service_name, s.price, u.first_name, u.last_name
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    WHERE a.status = 'completed' AND a.appointment_date BETWEEN ? AND ?
                    ORDER BY a.appointment_date
                ";
                $filename = "sales_{$start}_to_{$end}";
                $headers = ['Date', 'Service', 'Amount', 'Customer Name'];
                break;


            case 'customer_list':
                $query = "
                    SELECT u.user_id, u.first_name, u.last_name, u.contact_number, u.email, u.date_created, u.is_active
                    FROM tbl_users u
                    WHERE u.role_id = 3
                    ORDER BY u.date_created DESC
                ";
                $filename = "customer_list_{$start}_to_{$end}";
                $headers = ['Customer Name', 'ID', 'Mobile Number', 'Email Address', 'Created Account Since', 'Status'];
                break;

            case 'all':
                $query = "
                    SELECT a.appointment_date, a.appointment_time, s.service_name, a.status, u.first_name, u.last_name, u.email, s.price
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    WHERE a.appointment_date BETWEEN ? AND ?
                    ORDER BY a.appointment_date, a.appointment_time
                ";
                $filename = "all_reports_{$start}_to_{$end}";
                $headers = ['Date', 'Time', 'Service', 'Status', 'Customer Name', 'Email', 'Amount'];
                break;
        }

        if (empty($query)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid report type';
            exit;
        }

        $stmt = $db->prepare($query);
        $stmt->execute([$start, $end]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                $key = strtolower(str_replace([' ', '-'], '_', $header));
                
                // Map header names to actual database column keys
                if ($key === 'date') {
                    $csvRow[] = $row['appointment_date'] ?? '';
                } elseif ($key === 'time') {
                    $csvRow[] = $row['appointment_time'] ?? '';
                } elseif ($key === 'service') {
                    $csvRow[] = $row['service_name'] ?? '';
                } elseif ($key === 'customer_name') {
                    $csvRow[] = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
                } elseif ($key === 'mobile_number') {
                    $csvRow[] = $row['contact_number'] ?? '';
                } elseif ($key === 'email_address') {
                    $csvRow[] = $row['email'] ?? '';
                } elseif ($key === 'created_account_since') {
                    $csvRow[] = $row['date_created'] ?? '';
                } elseif ($key === 'id') {
                    $csvRow[] = $row['user_id'] ?? '';
                } elseif ($key === 'status') {
                    // For appointment reports, show appointment status; for customer list, show user status
                    if (isset($row['status'])) {
                        // This is an appointment record
                        $csvRow[] = ucfirst($row['status']);
                    } elseif (isset($row['is_active'])) {
                        // This is a customer/user record
                        $csvRow[] = ($row['is_active'] == 1) ? 'Active' : 'Inactive';
                    } else {
                        $csvRow[] = '';
                    }
                } elseif ($key === 'amount') {
                    $csvRow[] = '₱ ' . number_format((float)($row['price'] ?? 0), 2);
                } elseif ($key === 'total_spent' || $key === 'total_revenue') {
                    $csvRow[] = '₱ ' . number_format((float)($row[$key] ?? 0), 2);
                } elseif ($key === 'total_appointments') {
                    $csvRow[] = $row[$key] ?? '';
                } else {
                    $csvRow[] = $row[$key] ?? '';
                }
            }
            fputcsv($out, $csvRow);
        }

        fclose($out);
        exit;
    }

    private function exportPdfByType(string $type, ?string $start, ?string $end): void
    {
        $db = \App\Core\Database::getConnection();

        $query = "";
        $title = "";
        $headers = [];

        switch ($type) {
            case 'appointments':
                $query = "
                    SELECT a.appointment_date, a.appointment_time, s.service_name, a.status, u.first_name, u.last_name, u.email
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    " . ($start && $end ? "WHERE a.appointment_date BETWEEN ? AND ?" : "") . "
                    ORDER BY a.appointment_date, a.appointment_time
                ";
                $title = "Appointments Report";
                $headers = ['Date', 'Time', 'Service', 'Status', 'Customer Name', 'Email'];
                break;

            case 'sales':
                $query = "
                    SELECT a.appointment_date, s.service_name, s.price, u.first_name, u.last_name
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    WHERE a.status = 'completed' " . ($start && $end ? "AND a.appointment_date BETWEEN ? AND ?" : "") . "
                    ORDER BY a.appointment_date
                ";
                $title = "Sales Report";
                $headers = ['Date', 'Service', 'Amount', 'Customer Name'];
                break;


            case 'customer_list':
                $query = "
                    SELECT u.user_id, u.first_name, u.last_name, u.contact_number, u.email, u.date_created, u.is_active
                    FROM tbl_users u
                    WHERE u.role_id = 3
                    ORDER BY u.date_created DESC
                ";
                $title = "Customer List";
                $headers = ['Customer Name', 'ID', 'Mobile Number', 'Email Address', 'Created Account Since', 'Status'];
                break;

            case 'all':
                $query = "
                    SELECT a.appointment_date, a.appointment_time, s.service_name, a.status, u.first_name, u.last_name, u.email, s.price
                    FROM tbl_appointments a
                    JOIN tbl_services s ON a.service_id = s.service_id
                    JOIN tbl_users u ON a.user_id = u.user_id
                    " . ($start && $end ? "WHERE a.appointment_date BETWEEN ? AND ?" : "") . "
                    ORDER BY a.appointment_date, a.appointment_time
                ";
                $title = "All Appointments Report";
                $headers = ['Date', 'Time', 'Service', 'Status', 'Customer Name', 'Email', 'Amount'];
                break;
        }

        if (empty($query)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid report type';
            exit;
        }

        $stmt = $db->prepare($query);
        if ($start && $end) {
            $stmt->execute([$start, $end]);
        } else {
            $stmt->execute();
        }
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; }
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 9px; 
            color: #333;
            margin: 15px 20px;
        }
        
        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            gap: 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
            min-width: 0;
        }

        .header-left img {
            width: 50px;
            height: auto;
            flex-shrink: 0;
        }

        .company-info {
            min-width: 0;
        }

        .company-info h3 {
            font-size: 13px;
            color: #333;
            margin: 0;
            font-weight: bold;
            white-space: nowrap;
            line-height: 1.2;
        }

        .company-info p {
            font-size: 8px;
            color: #999;
            margin: 2px 0 0 0;
            white-space: nowrap;
        }

        .header-right {
            text-align: right;
            flex: 1;
            min-width: 0;
        }

        .header-right h1 {
            font-size: 18px;
            color: #a855f7;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            word-wrap: break-word;
        }
        
        .header-right p {
            font-size: 9px;
            color: #666;
            margin: 3px 0 0 0;
        }
        
        /* Table Section */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 8px;
            margin-top: 15px;
        }
        
        th { 
            background: #f3f4f6; 
            color: #333;
            padding: 10px 8px; 
            text-align: left; 
            font-weight: bold; 
            font-size: 8px;
            border-bottom: 1px solid #d1d5db;
        }
        
        td { 
            padding: 6px; 
            border-bottom: 1px solid #e5e7eb; 
            font-size: 8px;
        }
        
        tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .currency { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>';

        // Header with company info
        $html .= '<div class="header">
            <div class="header-left">
                <div class="company-info">
                    <h3>8th Avenue Salon</h3>
                    <p>Professional Beauty Services</p>
                </div>
            </div>
            <div class="header-right">
                <h1>' . htmlspecialchars($title) . '</h1>
                <p>Record Count: <span style="color: #a855f7; font-weight: bold;">' . count($rows) . '</span></p>
            </div>
        </div>';

        $html .= '<table border="0" cellspacing="0">';
        $html .= '<thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($headers as $header) {
                $key = strtolower(str_replace([' ', '-'], '_', $header));
                
                // Map header names to actual database column keys
                if ($key === 'date') {
                    $html .= '<td>' . htmlspecialchars($row['appointment_date'] ?? '') . '</td>';
                } elseif ($key === 'time') {
                    $html .= '<td>' . htmlspecialchars($row['appointment_time'] ?? '') . '</td>';
                } elseif ($key === 'service') {
                    $html .= '<td>' . htmlspecialchars($row['service_name'] ?? '') . '</td>';
                } elseif ($key === 'customer_name') {
                    $html .= '<td>' . htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) . '</td>';
                } elseif ($key === 'mobile_number') {
                    $html .= '<td>' . htmlspecialchars($row['contact_number'] ?? '') . '</td>';
                } elseif ($key === 'email_address') {
                    $html .= '<td>' . htmlspecialchars($row['email'] ?? '') . '</td>';
                } elseif ($key === 'created_account_since') {
                    $html .= '<td>' . htmlspecialchars($row['date_created'] ?? '') . '</td>';
                } elseif ($key === 'id') {
                    $html .= '<td>' . htmlspecialchars($row['user_id'] ?? '') . '</td>';
                } elseif ($key === 'status') {
                    // For appointment reports, show appointment status; for customer list, show user status
                    if (isset($row['status'])) {
                        // This is an appointment record
                        $appointmentStatus = $row['status'] ?? '';
                        $html .= '<td>' . htmlspecialchars(ucfirst($appointmentStatus)) . '</td>';
                    } elseif (isset($row['is_active'])) {
                        // This is a customer/user record
                        $userStatus = ($row['is_active'] == 1) ? 'Active' : 'Inactive';
                        $html .= '<td>' . htmlspecialchars($userStatus) . '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                } elseif ($key === 'amount') {
                    $html .= '<td class="currency">₱ ' . number_format((float)($row['price'] ?? 0), 2) . '</td>';
                } elseif ($key === 'total_spent' || $key === 'total_revenue') {
                    $html .= '<td class="currency">₱ ' . number_format((float)($row[$key] ?? 0), 2) . '</td>';
                } elseif ($key === 'total_appointments') {
                    $html .= '<td class="center">' . htmlspecialchars($row[$key] ?? '') . '</td>';
                } else {
                    $html .= '<td>' . htmlspecialchars($row[$key] ?? '') . '</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</body></html>';

        try {
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');

            // Set options
            $options = $dompdf->getOptions();
            $options->set([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'dpi' => 96
            ]);
            $dompdf->setOptions($options);

            $dompdf->render();

            // Generate filename based on whether dates are provided
            $filename = $start && $end ? $title . '_' . $start . '_to_' . $end . '.pdf' : $title . '_all_time.pdf';

            // Set headers for PDF download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $dompdf->output();
            exit;
        } catch (Exception $e) {
            // If PDF generation fails, show error
            header('Content-Type: text/plain');
            echo 'PDF Generation Error: ' . $e->getMessage();
            exit;
        }
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
