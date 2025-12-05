<?php
$pageTitle = "Dashboard";
$activePage = "dashboard";
?>

<!-- HEADER TEXT -->
<div class="mb-4">
    <h3 class="fw-bold">Welcome back, Admin!</h3>
    <small class="text-muted">Here’s what’s happening at 8th Avenue Salon today.</small>
</div>

<!-- FILTER PANEL -->
<div class="d-flex justify-content-end gap-2 mb-4">

    <!-- Date Filter -->
    <button class="btn filter-btn d-flex align-items-center" id="openDateFilter">
        <i class="bi bi-calendar2-date me-2"></i> Date Filter
    </button>

    <!-- Report Type -->
    <div class="dropdown">
        <button class="btn filter-btn d-flex align-items-center" data-bs-toggle="dropdown">
            <i class="bi bi-funnel me-2"></i> Reports
        </button>

        <ul class="dropdown-menu p-2">
            <li><a class="dropdown-item" href="?report=all">All Reports</a></li>
            <li><a class="dropdown-item" href="?report=appointments">Appointments</a></li>
            <li><a class="dropdown-item" href="?report=sales">Sales</a></li>
            <li><a class="dropdown-item" href="?report=services">Services</a></li>
        </ul>
    </div>

    <!-- Download -->
    <button class="btn download-btn d-flex align-items-center" id="openDownloadModal">
        <i class="bi bi-download me-2"></i> Download
    </button>

</div>



<!-- ===================== STATISTICS (TOP) ===================== -->
<div class="row mt-2">

    <!-- MONTHLY SALES -->
    <div class="col-lg-8 mb-3">
        <div class="card p-3">
            <h6 class="fw-semibold">Monthly Sales</h6>
            <canvas id="monthlySales"></canvas>
        </div>
    </div>

    <!-- TOTAL APPOINTMENTS -->
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="fw-semibold">Total Appointments</h6>
            <canvas id="appointmentDonut"></canvas>
        </div>
    </div>

</div>


<!-- ===================== SUMMARY CARDS ===================== -->
<div class="row g-3 mt-1">

    <div class="col-md-3">
        <div class="card stats-card text-center">
            <h6>Total Customers</h6>
            <h2><?= $totalUsers ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-center">
            <h6>Total Services</h6>
            <h2><?= $totalServices ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-center">
            <h6>Rejected Appointments</h6>
            <h2><?= $rejectedAppointments ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-center">
            <h6>Accepted Appointments</h6>
            <h2><?= $acceptedAppointments ?></h2>
        </div>
    </div>

</div>


<!-- ===================== WEEKLY + TOP SERVICES ===================== -->
<div class="row mt-3">

    <!-- WEEKLY SALES -->
    <div class="col-lg-8 mb-3">
        <div class="card p-3">
            <h6 class="fw-semibold">Weekly Sales Overview</h6>
            <small class="text-muted">Daily Sales Performance for the current week</small>

            <table class="table table-borderless align-middle weekly-table mt-2">
                <?php foreach ($weeklySales as $day): ?>
                    <tr>
                        <td><?= $day['day'] ?></td>
                        <td><?= $day['appointments'] ?> Appointments</td>
                        <td class="text-end fw-bold">₱<?= number_format($day['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- TOP SERVICES -->
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="fw-semibold">Top Services by Revenue</h6>
            <small class="text-muted">This week's best performing services</small>

            <ul class="list-group list-group-flush top-service-list mt-3">
                <?php foreach ($topServices as $svc): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($svc['service_name']) ?>
                        <span class="float-end">₱<?= number_format($svc['total']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div>


<!-- ===================== DOWNLOAD MODAL ===================== -->
<div class="custom-modal" id="downloadModal">
    <div class="custom-modal-content small-modal">

        <div class="modal-header mb-2">
            <h5>Download Report</h5>
            <button class="close-modal">&times;</button>
        </div>

        <form method="POST" action="/admin/reports/export">
            <label class="modal-label">File Format</label>
            <select class="form-select mb-3" name="format">
                <option value="pdf">PDF</option>
                <option value="csv">CSV</option>
            </select>

            <label class="modal-label">Report Type</label>
            <select class="form-select mb-3" name="type">
                <option value="appointments">Appointments</option>
                <option value="sales">Sales</option>
                <option value="services">Top Services</option>
                <option value="customers">Customers</option>
            </select>

            <button class="btn btn-primary w-100">Download</button>
        </form>

    </div>
</div>

<script src="/assets/js/dashboard.js"></script>
