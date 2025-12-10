<?php

$userFullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$createdAt = !empty($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : 'mm yyyy';
$avatar = $user['avatar'] ?? '/assets/img/apple-touch-icon.png';

$phone   = htmlspecialchars($user['contact_number'] ?? '');
$email   = htmlspecialchars($user['email'] ?? '');
$address = htmlspecialchars($user['address'] ?? '123 Luzon, Quezon City');
$bio     = htmlspecialchars($user['bio'] ?? 'Salon Customer');
?>

<div class="profile-root container-fluid py-4">

    <section class="profile-header mb-4">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'My Profile') ?></h1>
        <p class="page-sub">
            Hello, <strong><?= htmlspecialchars($user['first_name'] ?? 'Guest') ?>!</strong>
            <span class="d-block">This is your space — review your details and make sure everything looks good.</span>
        </p>
    </section>

    <section class="d-flex gap-4 align-items-start mb-4 flex-wrap">
        <div class="appt-status-box flex-grow-1">
            <div class="status-head">
                <h6>Appointment Status</h6>
            </div>

            <div class="status-body d-flex justify-content-between align-items-center">
                <!-- Left -->
                <div class="status-left">
                    <div class="appt-number">
                        #<?= htmlspecialchars($user['current_appointment_no'] ?? '00000') ?>
                    </div>
                </div>

                <!-- Center -->
                <div class="status-center text-center">
                    <div class="appt-state text-success">
                        <?= htmlspecialchars($user['current_appointment_status'] ?? 'On Going') ?>
                    </div>
                    <div class="appt-date">
                        <?= htmlspecialchars($user['current_appointment_date'] ?? date('F j, Y')) ?>
                    </div>
                    <div class="appt-time">
                        <?= htmlspecialchars($user['current_appointment_time'] ?? '3:00 P.M.') ?>
                    </div>
                </div>

                <!-- Clock -->
                <div class="status-clock text-center">
                    <div class="clock-visual">
                        <div class="hand hour" id="hourHand"></div>
                        <div class="hand minute" id="minuteHand"></div>
                        <div class="hand second" id="secondHand"></div>
                        <div class="center-dot"></div>
                    </div>
                    <div class="digital-time" id="digTime">--:--:--</div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN PROFILE ROW -->
    <section class="profile-main row g-4">

        <!-- LEFT: AVATAR CARD -->
        <div class="col-lg-4">
            <div class="card profile-avatar-card">
                <div class="card-body text-center">

                    <h4 class="mb-1"><?= htmlspecialchars($userFullName ?: 'Firstname Lastname') ?></h4>
                    <p class="text-muted mb-3">Created Since <?= $createdAt ?></p>

                    <div class="avatar-wrap mx-auto mb-3">
                        <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar-img">
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/profile/edit" class="btn btn-purple">Edit Profile</a>

                        <form action="/profile/delete" method="post"
                              onsubmit="return confirm('Delete your profile? This cannot be undone.')">
                            <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">
                            <button type="submit" class="btn btn-danger-outline">
                                Delete Profile
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT: DETAILS CARD -->
        <div class="col-lg-8">
            <div class="card profile-details-card">
                <div class="card-body d-flex flex-wrap gap-4">

                    <!-- Personal Info -->
                    <div class="flex-grow-1">
                        <h5>Personal Information</h5>

                        <div class="info-row">
                            <label>Full name</label>
                            <div class="info-value"><?= htmlspecialchars($userFullName ?: 'User') ?></div>
                        </div>

                        <div class="info-row">
                            <label>Email</label>
                            <div class="info-value"><?= $email ?></div>
                        </div>

                        <div class="info-row">
                            <label>Phone</label>
                            <div class="info-value"><?= $phone ?></div>
                        </div>

                        <div class="info-row">
                            <label>Bio</label>
                            <div class="info-value"><?= $bio ?></div>
                        </div>
                    </div>

                    <!-- Contact & Security -->
                    <div class="contact-col" style="max-width:360px;">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="me-3">Contact & Security</h5>

                            <div class="d-flex gap-2">

                                <!-- Edit Profile -->
                                <a href="/profile/edit" class="btn btn-sm btn-icon btn-edit" title="Edit Profile">
                                    <img src="/assets/img/EditIcon.svg" alt="Edit" />
                                </a>

                                <!-- Delete Profile -->
                                <form action="/profile/delete" method="post"
                                      onsubmit="return confirm('Delete your profile? This cannot be undone.')">
                                    <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">
                                    <button class="btn btn-sm btn-icon btn-delete" title="Delete Profile">
                                        <img src="/assets/img/DeleteIcon.svg" alt="Delete" />
                                    </button>
                                </form>

                            </div>
                        </div>

                        <div class="info-row">
                            <label>Address</label>
                            <div class="info-value"><?= $address ?></div>
                        </div>

                        <div class="info-row">
                            <label>Emergency Contact</label>
                            <div class="info-value"><?= htmlspecialchars($user['emergency_contact_name'] ?? '-') ?></div>
                            <div class="info-value small"><?= htmlspecialchars($user['emergency_contact_phone'] ?? '') ?></div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-outline-settings" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <img src="/assets/img/SettingsIcon.svg" class="icon" alt="Settings" />
                                Change Password
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </section>
</div>

<!-- ===================== CLOCK SCRIPT ======================= -->
<script>
(function(){
  function updateClock() {
    const now = new Date();
    const ph = new Date(now.toLocaleString("en-US", { timeZone: "Asia/Manila" }));

    const sec = ph.getSeconds();
    const min = ph.getMinutes();
    const hr  = ph.getHours();

    document.getElementById('secondHand').style.transform =
        `translate(-50%, -100%) rotate(${sec * 6}deg)`;

    document.getElementById('minuteHand').style.transform =
        `translate(-50%, -100%) rotate(${min * 6 + sec * 0.1}deg)`;

    document.getElementById('hourHand').style.transform =
        `translate(-50%, -100%) rotate(${hr * 30 + min * 0.5}deg)`;

    document.getElementById('digTime').textContent =
        ph.toLocaleTimeString('en-US', { hour12: false });
  }

  updateClock();
  setInterval(updateClock, 1000);
})();
</script>
