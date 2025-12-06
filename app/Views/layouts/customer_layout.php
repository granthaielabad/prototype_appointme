<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? "Customer") ?> | AppointMe</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link href="/assets/css/customer.css" rel="stylesheet">
</head>

<body class="customer-layout-body">
<?php include __DIR__ . "/customerprofile_sidebar.php"; ?>

    <div class="customer-wrapper d-flex">

        <main class="flex-grow-1 min-vh-100">
            <header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
                <div class="d-flex align-items-center gap-3">
                    <a class="me-3" href="#home"><img src="/assets/img/apple-touch-icon.png" height="70" alt=""></a>
                    <h5 class="m-0"><?= htmlspecialchars($pageTitle ?? "") ?></h5>
                </div>

                

                <div class="d-flex align-items-center gap-3">
                    <button id="notifBtn" class="btn btn-ghost position-relative" data-bs-toggle="modal"
                        data-bs-target="#notificationsModal">
                        <i class="bi bi-bell fs-4"></i>
                        <span id="notifCount"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size:10px;display:none">0</span>
                    </button>

                    <div class="dropdown">
                        <a class="btn" href="/profile" id="profileDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="/assets/img/ProfileIcon.svg" class="rounded-circle" width="36" height="36"
                                alt="Profile">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">Change password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="/logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="container-fluid p-4">
                <?php // $content is injected by Controller::renderWithLayout

echo $content; ?>
            </div>

            <div class="body-body">
                <div class="body-body-1">
                  <div class="container-inside1-body1">
                     <h3>Hello, *Name!*</h3>
                     <p>This is your space. Review your details and make sure everything looks good</p>

                   <div class="appointment-container">
                        <h5>Appointment Status</h5>
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
                        <h5>Juan Dela Cruz</h5>
                        <p>Created Since mm yyyy</p>
                        <img src="/assets/img/apple-touch-icon.png" height="300" width="300">
                    </div>
                    <div class="container-inside2-body2">
                        <div class="container-inside2-body2-1">
                          <h4>Personal Information</h4>
                          <h6>Full Name</h6>
                          <p>Admin User</p>
                          <h6>Email</h6>
                          <p>admin@gmail.com</p>
                          <h6>Phone</h6>
                          <p>0912312312</p>
                          <h6>Bio</h6>
                          <p>Administrator</p>

                        </div>
                        <div class="container-inside2-body2-2">
                          <button class="deleteprofile">D</button>
                          <button class="editprofile">Edit Profile</button>
                          
                          <h4>Contact & Security</h4>
                          <h6>Address</h6>
                          <p>123 Luzon, Quezon City</p>
                          <h6>Emergency Contact</h6>
                          <p>Admin User</p>
                          <p>Admin User</p>
                          <p>Admin User</p>
                          <button class="changepass" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><img src="/assets/img/settings.png" class="icon" > Change password</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    

    <?php
  // optional modals: make sure these files exist or remove these includes
  if (file_exists(__DIR__ . "/../Customer/components/notifications_modal.php")) {
      include __DIR__ . "/../Customer/components/notifications_modal.php";
  }
  if (file_exists(__DIR__ . "/../Customer/components/change_password_modal.php")) {
      include __DIR__ . "/../Customer/components/change_password_modal.php";
  }
  ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/customer.js"></script>
 


</body>

</html>