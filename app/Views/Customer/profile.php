<?php
$first = $user['first_name'] ?? '';
$last  = $user['last_name'] ?? '';
$fullName = trim("$first $last");
$created = !empty($user['date_created']) ? date('F Y', strtotime($user['date_created'])) : '';
$phone = $user['contact_number'] ?? '';
$email = $user['email'] ?? '';
$bio = $user['bio'] ?? '';
$todayAppt = $todayAppointment ?? null;
// additional db 
$photo = $user['profile_photo'] ?? '/assets/img/apple-touch-icon.png';
$address = $user['address'] ?? '';
$emgName = $user['emergency_name'] ?? '';
$emgRel  = $user['emergency_relation'] ?? '';
$emgPhone = $user['emergency_phone'] ?? '';





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
              <!--photo form   --> 
           <form id="profilePhotoForm" method="POST" action="/profile/update-photo" enctype="multipart/form-data" class="d-inline-block">
            <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">
            <div class="profile-photo-wrapper">
              <img id="profilePhoto" src="<?= htmlspecialchars($photo) ?>" class="profile-photo" alt="Profile photo">
              <button type="button" id="profilePhotoTrigger" class="photo-edit-btn" aria-label="Change profile photo">
                <i class="bi bi-pencil-fill"></i>
              </button>
              <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="d-none">
            </div>
          </form>

          </div>



         <!--TRANFSFER DETAILS TO A FORM 
        --> 
        <form id="profileEditForm" class="profile-inline-form" method="POST" action="/profile/update" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">

  <div class="container-inside2-body2">
    <div class="container-inside2-body2-1">
      <h4>Personal Information</h4>

      <h6>Full Name</h6>
      <div class="view-mode"><?= htmlspecialchars($fullName) ?></div>
      <div class="edit-mode d-none">
        <input class="form-control mb-2" name="first_name" value="<?= htmlspecialchars($first) ?>" placeholder="First name">
        <input class="form-control" name="last_name" value="<?= htmlspecialchars($last) ?>" placeholder="Last name">
      </div>

      <h6>Email</h6>
      <div class="view-mode"><?= htmlspecialchars($email) ?></div>
      <div class="edit-mode d-none">
        <input class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" disabled>
        <small class="text-muted">Email not editable here</small>
      </div>

      <h6>Phone</h6>
      <div class="view-mode"><?= htmlspecialchars($phone) ?></div>
      <div class="edit-mode d-none">
        <input class="form-control" name="contact_number" value="<?= htmlspecialchars($phone) ?>">
      </div>

      <h6>Bio</h6>
      <div class="view-mode"><?= htmlspecialchars($bio) ?></div>
      <div class="edit-mode d-none">
        <textarea class="form-control" name="bio" rows="2"><?= htmlspecialchars($bio) ?></textarea>
      </div>
    </div>

    <div class="container-inside2-body2-2">
      <div class="d-flex justify-content-end gap-2 mb-2">
        <button type="button" id="editProfileBtn" class="editprofile">Edit Profile</button>
<button type="button" class="deleteprofile" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" aria-label="Delete account">🗑</button>
      </div>

      <h4>Contact &amp; Security</h4>

      <h6>Address</h6>
      <div class="view-mode"><?= htmlspecialchars($address ?? '') ?></div>
      <div class="edit-mode d-none">
        <input class="form-control" name="address" value="<?= htmlspecialchars($address ?? '') ?>">
      </div>

      <h6>Emergency Contact</h6>
      <div class="view-mode">
        <?= htmlspecialchars($emgName ?? '') ?><?= !empty($emgRel) ? " (" . htmlspecialchars($emgRel) . ")" : "" ?><br>
        <?= htmlspecialchars($emgPhone ?? '') ?>
      </div>
      <div class="edit-mode d-none">
        <input class="form-control mb-2" name="emergency_name" value="<?= htmlspecialchars($emgName ?? '') ?>" placeholder="Name">
        <input class="form-control mb-2" name="emergency_relation" value="<?= htmlspecialchars($emgRel ?? '') ?>" placeholder="Relation">
        <input class="form-control" name="emergency_phone" value="<?= htmlspecialchars($emgPhone ?? '') ?>" placeholder="Phone">
      </div>

      <div class="mt-3 view-mode">
        <button class="changepass" type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
          <img src="/assets/img/settings.png" class="icon" alt=""> Change password
        </button>
      </div>

      <div class="edit-mode d-none mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" id="cancelEditBtn" class="btn btn-outline-secondary">Cancel</button>
      </div>
    </div>
  </div>
</form>

</div>
