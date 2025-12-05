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

    <div class="customer-wrapper d-flex">
        <nav class="customer-sidebar d-none d-md-block">
            <div class="p-3">
                <a href="/"><img src="/assets/img/logo.svg" alt="logo" style="max-width:160px;"></a>
            </div>
        </nav>

        <main class="flex-grow-1 min-vh-100">
            <header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
                <div class="d-flex align-items-center gap-3">
                    <a class="me-3" href="/"><img src="/assets/img/logo.svg" height="36" alt=""></a>
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
                            <img src="/assets/img/avatar_placeholder.png" class="rounded-circle" width="36" height="36"
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
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="/assets/js/customer.js"></script>

</body>

</html>