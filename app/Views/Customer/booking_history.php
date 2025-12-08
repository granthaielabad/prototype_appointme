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
    <?php 
        $status = strtolower($appt["status"]);
        $rescheduleCount = (int) ($appt["reschedule_count"] ?? 0);
        $canModify = $status !== "cancelled" && $status !== "completed";
        $canReschedule = $canModify && $rescheduleCount < 1;
    ?>

    <a href="/appointment/edit/<?= $appt["appointment_id"] ?>" class="action-edit">
        <i class="bx bx-edit"></i>
    </a>

    <?php if ($canReschedule): ?>
        <button
            type="button"
            class="resched-btn"
            data-appt-id="<?= $appt["appointment_id"] ?>"
            data-reschedule-count="<?= $rescheduleCount ?>">
            Reschedule
        </button>
    <?php elseif ($rescheduleCount >= 1): ?>
        <span class="resched-used" title="You can only reschedule once.">Rescheduled</span>
    <?php endif; ?>

    <?php if ($canModify): ?>
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

<div id="resched-modal" class="resched-modal" aria-hidden="true">
    <div class="resched-modal__dialog">
        <h4>Reschedule Appointment</h4>
        <p class="resched-note">You can only reschedule once. Pick a new date and time.</p>
        <form id="resched-form">
            <input type="hidden" name="appointment_id" id="resched-appt-id">
            <label class="resched-field">
                <span>New Date</span>
                <input type="date" name="date" id="resched-date" min="<?= date('Y-m-d') ?>" required>
            </label>
            <label class="resched-field">
                <span>New Time</span>
                <input type="time" name="time" id="resched-time" required>
            </label>
            <div class="resched-modal__actions">
                <button type="button" class="resched-cancel" data-close-modal>Keep Appointment</button>
                <button type="submit" class="resched-confirm">Update Appointment</button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('resched-modal');
    const form = document.getElementById('resched-form');
    const apptIdInput = document.getElementById('resched-appt-id');
    const dateInput = document.getElementById('resched-date');
    const timeInput = document.getElementById('resched-time');

    const openModal = (apptId) => {
        apptIdInput.value = apptId;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        form.reset();
    };

    document.querySelectorAll('.resched-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const count = Number(btn.dataset.rescheduleCount || 0);
            if (count >= 1) {
                alert('You can only reschedule once.');
                return;
            }
            openModal(btn.dataset.apptId);
        });
    });

    modal?.querySelectorAll('[data-close-modal]').forEach(el => {
        el.addEventListener('click', closeModal);
    });

    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        try {
            const res = await fetch('/appointment/reschedule', { method: 'POST', body: fd });
            const data = await res.json();
            if (res.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Unable to reschedule. Please try another slot.');
            }
        } catch (err) {
            alert('Network error. Please try again.');
        }
    });
})();
</script>



<link rel="stylesheet" href="/assets/css/booking_history.css">
