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
        $reportType = $_GET['report'] ?? 'all';

        $dateFilter = "";
        if ($start && $end) {
            $dateFilter = " AND appointment_date BETWEEN '$start' AND '$end' ";
        }

        /* ===== BASE TOTALS ===== */
        $totalUsers = (new User())->count();
        $totalServices = (new Service())->count();

        $acceptedAppointments = $db->query("SELECT COUNT(*) FROM tbl_appointments WHERE status='confirmed'")->fetchColumn();
        $rejectedAppointments = $db->query("SELECT COUNT(*) FROM tbl_appointments WHERE status='cancelled'")->fetchColumn();

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
            'accepted' => $acceptedAppointments,
            'rejected' => $rejectedAppointments
        ];

        /* ===== WEEKLY SALES ===== */
        $weeklySales = $db->query("
            SELECT 
                DAYNAME(a.appointment_date) AS day,
                COUNT(*) AS appointments,
                SUM(s.price) AS amount
            FROM tbl_appointments a
            JOIN tbl_services s ON s.service_id = a.service_id
            WHERE YEARWEEK(a.appointment_date, 1) = YEARWEEK(CURDATE(), 1)
            AND a.status = 'completed'
            GROUP BY a.appointment_date
            ORDER BY a.appointment_date
        ")->fetchAll(\PDO::FETCH_ASSOC);


        /* ===== TOP SERVICES ===== */
        $topServices = $db->query("
            SELECT 
                s.service_name,
                SUM(s.price) AS total
            FROM tbl_appointments a
            JOIN tbl_services s
                ON s.service_id = a.service_id
            WHERE a.status = 'completed'
            GROUP BY a.service_id
            ORDER BY total DESC
            LIMIT 4
        ")->fetchAll(\PDO::FETCH_ASSOC);


        $this->render('dashboard', [
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'donut'         => $donut,
            'weeklySales'   => $weeklySales,
            'topServices'   => $topServices,
            'totalUsers'    => $totalUsers,
            'totalServices' => $totalServices,
            'acceptedAppointments' => $acceptedAppointments,
            'rejectedAppointments' => $rejectedAppointments,
        ]);
    }
}
