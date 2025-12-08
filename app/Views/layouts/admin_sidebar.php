<aside class="sidebar">

    <!-- LOGO -->
    <div class="logo mb-4">
        <img src="/assets/img/admin/adminLogo.svg" alt="Admin Logo">
    </div>

    <!-- NAVIGATION -->
    <nav>
        <ul class="p-0 m-0" style="list-style: none;">

            <!-- Dashboard -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/dashboard'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/dashboard">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_DashboardIcon.svg' : 'DashboardIcon.svg' ?>">
                    Dashboard
                </a>
            </li>

            <!-- Appointments -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/appointments'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/appointments">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_AppointmentsIcon.svg' : 'AppointmentsIcon.svg' ?>">
                    Appointments
                </a>
            </li>

            <!-- Services -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/services'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/services">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_ServicesIcon.svg' : 'ServicesIcon.svg' ?>">
                    Services
                </a>
            </li>


            <!-- Inquiries -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/inquiries'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/inquiries">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_InquiryIcon.svg' : 'InquiryIcon.svg' ?>">
                    Inquiries
                </a>
            </li>

                        <!-- Employees -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/employees'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/employees">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_UserListIcon.svg' : 'UserListIcon.svg' ?>">
                    Employees
                </a>
            </li>

            <!-- Archives -->
            <?php $isActive = str_starts_with($_SERVER['REQUEST_URI'], '/admin/archives'); ?>
            <li>
                <a class="nav-link <?= $isActive ? 'active-nav' : '' ?>" href="/admin/archives">
                    <img src="/assets/img/admin/<?= $isActive ? 'active_ArchiveIcon.svg' : 'ArchiveIcon.svg' ?>">
                    Archives
                </a>
            </li>

        </ul>
    </nav>
</aside>
