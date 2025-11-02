<div class="admin-content">
    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Total Users</h3>
            <p><?= htmlspecialchars($totalUsers) ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Total Appointments</h3>
            <p><?= htmlspecialchars($totalAppointments) ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Services</h3>
            <p><?= htmlspecialchars($totalServices) ?></p>
        </div>
        <div class="dashboard-card">
            <h3>New Inquiries</h3>
            <p><?= htmlspecialchars($newInquiries) ?></p>
        </div>
    </div>
</div>
