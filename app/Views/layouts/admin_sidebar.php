<aside>
    <div class="p-3 border-bottom">
        <h4 class="text-white">AppointMe</h4>
        <small class="text-muted"><?= htmlspecialchars($_SESSION['user']['role'] ?? 'Admin') ?></small>
    </div>
    <a href="/admin/dashboard" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/admin/services"><i class="bi bi-scissors"></i> Services</a>
    <a href="/admin/appointments"><i class="bi bi-calendar-check"></i> Appointments</a>
    <a href="/admin/users"><i class="bi bi-people"></i> Users</a>
    <a href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
</aside>
<main>
