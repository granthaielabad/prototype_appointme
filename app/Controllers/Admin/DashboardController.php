<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Inquiry;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $db = Database::getConnection();

        /* ===== FILTERS ===== */
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $period = $_GET['period'] ?? 'all';

        $dateFilter = "";
        if ($start && $end) {
            $dateFilter = " AND appointment_date BETWEEN '$start' AND '$end' ";
        } else {
            // Apply period-based filtering when no custom date range is selected
            switch ($period) {
                case 'daily':
                    $dateFilter = " AND DATE(appointment_date) = CURDATE() ";
                    break;
                case 'weekly':
                    $dateFilter = " AND YEARWEEK(appointment_date, 1) = YEARWEEK(CURDATE(), 1) ";
                    break;
                case 'monthly':
                    $dateFilter = " AND MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE()) ";
                    break;
                case 'all':
                    // No date filter for "all reports" - show all time data
                    $dateFilter = "";
                    break;
            }
        }

        /* ===== BASE TOTALS ===== */
        $totalUsers = (new User())->count();
        $totalServices = (new Service())->count();

        // Apply period-based filter to appointment counts
        $appointmentFilter = "WHERE status='confirmed' $dateFilter";
        $rejectedFilter = "WHERE status='cancelled' $dateFilter";

        $acceptedAppointments = $db->query("SELECT COUNT(*) FROM tbl_appointments $appointmentFilter")->fetchColumn();
        $rejectedAppointments = $db->query("SELECT COUNT(*) FROM tbl_appointments $rejectedFilter")->fetchColumn();

        /* ===== MONTHLY SALES (CHART 1) ===== */
        $sqlSales = "
            SELECT 
                MONTHNAME(a.appointment_date) AS month,
                SUM(s.price) AS total
            FROM tbl_appointments a
            JOIN tbl_services s 
                ON s.service_id = a.service_id
            WHERE a.status = 'completed'
            $dateFilter
            GROUP BY MONTH(a.appointment_date)
            ORDER BY a.appointment_date ASC
        ";
        $monthlySales = $db->query($sqlSales)->fetchAll(\PDO::FETCH_ASSOC);


        /* Format array for JS */
        $monthlyLabels = array_column($monthlySales, 'month');
        $monthlyValues = array_map('intval', array_column($monthlySales, 'total'));

        /* ===== APPOINTMENT DONUT (CHART 2) ===== */
        $donut = [
            'accepted' => $db->query("SELECT COUNT(*) FROM tbl_appointments WHERE status='confirmed' $dateFilter")->fetchColumn(),
            'rejected' => $db->query("SELECT COUNT(*) FROM tbl_appointments WHERE status='cancelled' $dateFilter")->fetchColumn()
        ];

        /* ===== PERIOD-BASED SALES CHART ===== */
        $periodSales = [];
        if ($period === 'daily') {
            // Show hourly breakdown for today
            $periodSales = $db->query("
                SELECT
                    CONCAT(LPAD(HOUR(a.appointment_date), 2, '0'), ':00') AS period_label,
                    HOUR(a.appointment_date) AS period_value,
                    COUNT(*) AS appointments,
                    SUM(s.price) AS amount
                FROM tbl_appointments a
                JOIN tbl_services s ON s.service_id = a.service_id
                WHERE DATE(a.appointment_date) = CURDATE()
                AND a.status = 'completed'
                GROUP BY HOUR(a.appointment_date)
                ORDER BY HOUR(a.appointment_date) ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } elseif ($period === 'weekly') {
            // Show daily breakdown for current week
            $periodSales = $db->query("
                SELECT
                    DAYNAME(a.appointment_date) AS period_label,
                    DATE(a.appointment_date) AS period_value,
                    COUNT(*) AS appointments,
                    SUM(s.price) AS amount
                FROM tbl_appointments a
                JOIN tbl_services s ON s.service_id = a.service_id
                WHERE YEARWEEK(a.appointment_date, 1) = YEARWEEK(CURDATE(), 1)
                AND a.status = 'completed'
                GROUP BY DATE(a.appointment_date)
                ORDER BY a.appointment_date ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } elseif ($period === 'monthly') {
            // Show weekly breakdown for current month
            $periodSales = $db->query("
                SELECT
                    CONCAT('Week ', WEEK(a.appointment_date) - WEEK(DATE_SUB(a.appointment_date, INTERVAL DAYOFMONTH(a.appointment_date)-1 DAY)) + 1) AS period_label,
                    WEEK(a.appointment_date, 1) AS period_value,
                    COUNT(*) AS appointments,
                    SUM(s.price) AS amount
                FROM tbl_appointments a
                JOIN tbl_services s ON s.service_id = a.service_id
                WHERE MONTH(a.appointment_date) = MONTH(CURDATE())
                AND YEAR(a.appointment_date) = YEAR(CURDATE())
                AND a.status = 'completed'
                GROUP BY WEEK(a.appointment_date, 1)
                ORDER BY WEEK(a.appointment_date, 1) ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } elseif ($period === 'all') {
            // Show monthly breakdown for all time
            $periodSales = $db->query("
                SELECT
                    DATE_FORMAT(a.appointment_date, '%M %Y') AS period_label,
                    DATE_FORMAT(a.appointment_date, '%Y-%m') AS period_value,
                    COUNT(*) AS appointments,
                    SUM(s.price) AS amount
                FROM tbl_appointments a
                JOIN tbl_services s ON s.service_id = a.service_id
                WHERE a.status = 'completed'
                GROUP BY DATE_FORMAT(a.appointment_date, '%Y-%m'), DATE_FORMAT(a.appointment_date, '%M %Y')
                ORDER BY a.appointment_date ASC
                LIMIT 12
            ")->fetchAll(\PDO::FETCH_ASSOC);
        }


        /* ===== TOP SERVICES ===== */
        $topServices = $db->query("
            SELECT
                s.service_name,
                SUM(s.price) AS total
            FROM tbl_appointments a
            JOIN tbl_services s
                ON s.service_id = a.service_id
            WHERE a.status = 'completed'
            $dateFilter
            GROUP BY a.service_id
            ORDER BY total DESC
            LIMIT 4
        ")->fetchAll(\PDO::FETCH_ASSOC);


        $this->render('dashboard', [
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'donut'         => $donut,
            'periodSales'   => $periodSales,
            'topServices'   => $topServices,
            'totalUsers'    => $totalUsers,
            'totalServices' => $totalServices,
            'acceptedAppointments' => $acceptedAppointments,
            'rejectedAppointments' => $rejectedAppointments,
            'currentPeriod' => $period,
        ]);
    }
}
