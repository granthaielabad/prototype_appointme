<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Notification;

class CronController extends Controller
{
    // GET /cron/send-reminders?token=YOUR_SECRET
    public function sendReminders(): void
    {
        $token = $_GET['token'] ?? '';
        if ($token !== getenv('CRON_TOKEN')) {
            http_response_code(401);
            echo 'Unauthorized';
            return;
        }

        $apptModel  = new Appointment();
        $notifModel = new Notification();

        $appointments = $apptModel->findPendingDueInNext24h();
        $created = 0;

        foreach ($appointments as $appt) {
            $notifModel->createReminder(
                (int)$appt['user_id'],
                (int)$appt['appointment_id'],
                $appt['service_name'],
                $appt['appointment_date'],
                $appt['formatted_time']
            );

            $apptModel->update((int)$appt['appointment_id'], [
                'reminder_sent' => 1,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            $created++;
        }

        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'created' => $created]);
    }
}
