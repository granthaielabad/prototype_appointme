<?php
$first = $user['first_name'] ?? '';
$last  = $user['last_name'] ?? '';
$fullName = trim("$first $last");
$created = !empty($user['date_created']) ? date('F Y', strtotime($user['date_created'])) : '';
$phone = $user['contact_number'] ?? '';
$email = $user['email'] ?? '';
$bio = $user['bio'] ?? '';
$todayAppt = $todayAppointment ?? null;
?>
<div class="body-body">
    <div class="body-body-1">
        <div class="container-inside1-body1">
            <h3>Hello, <?= htmlspecialchars($first ?: 'User') ?>!</h3>
            <p>This is your space. Review your details and make sure everything looks good</p>

            <div class="appointment-container">
    <div class="appt-card">
        <div class="appt-card-header">
            <h5>Appointment Status</h5>
        </div>
        <?php if ($todayAppt): ?>
            <div class="appt-card-body">
                <div class="appt-id">#<?= htmlspecialchars($todayAppt['appointment_id']) ?></div>
                <div class="appt-meta">
                    <div class="appt-status <?= htmlspecialchars(strtolower($todayAppt['status'])) ?>">
                        <?= htmlspecialchars(ucwords($todayAppt['status'])) ?>
                    </div>
                    <div class="appt-date"><?= date('F j, Y', strtotime($todayAppt['appointment_date'])) ?></div>
                    <div class="appt-time"><?= date('g:i A', strtotime($todayAppt['appointment_time'])) ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="appt-card-body empty">No appointment today.</div>
        <?php endif; ?>
    </div>
</div>


        </div>

        <div class="container-inside2-body1">
            <div class="clock">
                <div class="hand hour" id="hourHand"></div>
                <div class="hand minute" id="minuteHand"></div>
                <div class="hand second" id="secondHand"></div>
                <div class="numbers">
                    <span style="--i:1">1</span>
                    <span style="--i:2">2</span>
                    <span style="--i:3">3</span>
                    <span style="--i:4">4</span>
                    <span style="--i:5">5</span>
                    <span style="--i:6">6</span>
                    <span style="--i:7">7</span>
                    <span style="--i:8">8</span>
                    <span style="--i:9">9</span>
                    <span style="--i:10">10</span>
                    <span style="--i:11">11</span>
                    <span style="--i:12">12</span>
                </div>
                <div class="center"></div>
            </div>
            <div class="digital">
                <div id="digTime">--:--:--</div>
                <div class="label">Philippine Time (UTC +8)</div>
            </div>
        </div>
    </div>

    <div class="body-body-2">
        <div class="container-inside1-body2">
            <h5><?= htmlspecialchars($fullName ?: 'Your Name') ?></h5>
            <p><?= $created ? 'Created since ' . htmlspecialchars($created) : '' ?></p>
            <img src="/assets/img/apple-touch-icon.png" height="300" width="300" alt="Profile photo">
        </div>

        <div class="container-inside2-body2">
            <div class="container-inside2-body2-1">
                <h4>Personal Information</h4>
                <h6>Full Name</h6>
                <p><?= htmlspecialchars($fullName) ?></p>
                <h6>Email</h6>
                <p><?= htmlspecialchars($email) ?></p>
                <h6>Phone</h6>
                <p><?= htmlspecialchars($phone) ?></p>
                <h6>Bio</h6>
                <p><?= htmlspecialchars($bio) ?></p>
            </div>

            <div class="container-inside2-body2-2">
                <button class="deleteprofile" aria-label="Delete profile">D</button>
                <button class="editprofile">Edit Profile</button>

                <h4>Contact &amp; Security</h4>
                <h6>Address</h6>
                <p><!-- TODO: add address when available --></p>
                <h6>Emergency Contact</h6>
                <p><!-- TODO: add name/relation/phone when available --></p>

                <button class="changepass" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <img src="/assets/img/settings.png" class="icon" alt=""> Change password
                </button>
            </div>
        </div>
    </div>
</div>
