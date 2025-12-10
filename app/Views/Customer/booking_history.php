<div class="appointments-page-root">

  
    <section class="appointments-header">
        <h2>Hello, <strong><?= htmlspecialchars($user["first_name"] ?? "User") ?>!</strong></h2>
        <p>This is your space—review your details and make sure everything looks good</p>
    </section>

  
    <section class="appointments-container">
        <div class="appointments-card">
            <h3 class="appointments-title">All Appointments:</h3>

            <div class="table-responsive">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Appointment #</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $appt): ?>

                            <tr>
                                <td><?= htmlspecialchars($appt["appointment_id"]) ?></td>
                                <td><?= htmlspecialchars($appt["service_name"]) ?></td>
                                <td><?= htmlspecialchars($appt["appointment_date"]) ?></td>
                                <td><?= date("h:i A", strtotime($appt["appointment_time"])) ?></td>

                                <!-- STATUS COLORS -->
                                <td>
                                    <?php 
                                        $status = strtolower($appt["status"]);
                                        $color = match ($status) {
                                            "pending"   => "color: orange;",
                                            "cancelled" => "color: red;",
                                            "completed" => "color: green;",
                                            "on going"  => "color: blue;",
                                            default     => "color: gray;"
                                        };
                                    ?>
                                    <span style="<?= $color ?>; font-weight:600;">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($appt["category"]) ?></td>
                                <td><?= htmlspecialchars($appt["notes"]) ?></td>

                                <td class="action-col">
                                    <a href="/appointment/edit/<?= $appt["appointment_id"] ?>" class="action-edit">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                        <?php 
                                          
                                            $status = strtolower($appt["status"]);
                                        ?>

                                     <?php if ($status !== "cancelled" && $status !== "completed"): ?>
                                        <a href="/appointment/cancel?id=<?= $appt['appointment_id'] ?>" 
                                            onclick="return confirm('Are you sure you want to cancel this appointment?')" 
                                            class="cancel-btn">
                                            Cancel
                                        </a>
                                    <?php endif; ?>

                                </td>
                            </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-appointments">No appointments yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</div>

<link rel="stylesheet" href="/assets/css/booking_history.css">
