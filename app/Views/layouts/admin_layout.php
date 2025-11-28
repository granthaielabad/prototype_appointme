<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? "Admin Panel" ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/site.webmanifest">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body>

    <div class="admin-wrapper d-flex">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <?php include __DIR__ . "/admin_sidebar.php"; ?>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main flex-grow-1">

            <!-- TOP HEADER -->
            <header class="admin-header d-flex justify-content-between align-items-center px-4 py-3 mb-3">

                <h2 class="fw-bold m-0"><?= $pageTitle ?? "" ?></h2>

                <div class="d-flex align-items-center gap-4">

                    <!-- NOTIFICATION ICON -->
                    <a href="/admin/notifications" class="header-icon">
                        <img src="/assets/img/admin/NotificationIcon.svg" width="24" height="24" alt="Notifications">
                    </a>

                    <!-- PROFILE DROPDOWN (ON CLICK) -->
                    <div class="profile-wrapper position-relative">

                        <!-- CLICKABLE PROFILE ICON -->
                        <button id="profileToggle" class="btn p-0 bg-transparent border-0">
                            <img src="/assets/img/admin/ProfileIcon.svg" width="28" height="28" alt="Profile">
                        </button>


                        <!-- DROPDOWN MENU -->
                        <div id="profileDropdown" class="profile-dropdown shadow-sm">
                            <a href="/admin/profile" class="dropdown-item">Profile</a>
                            <a href="/logout" class="dropdown-item text-danger">Logout</a>
                        </div>

                    </div>

                </div>
            </header>

            <!-- PAGE CONTENT -->
            <section class="admin-page px-4 pb-4">
                <?= $content ?>
            </section>

        </main>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/admin.js"></script>

</body>

</html>