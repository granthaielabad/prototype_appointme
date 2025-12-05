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

    <!-- Date Filter Dropdown -->
    <div class="dropdown">
        <button class="btn filter-btn d-flex align-items-center dropdown-toggle" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-calendar2-date me-2"></i> Date Filter
        </button>

        <div class="dropdown-menu p-3" aria-labelledby="dateFilterDropdown" style="width: 320px;">
            <h6 class="dropdown-header px-0 py-0 mb-3">Pick a Date Range</h6>
            
            <!-- Calendar Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button id="prevMonth" class="btn btn-sm btn-light">&lt;</button>
                <h6 id="monthYear" class="mb-0 fw-semibold"></h6>
                <button id="nextMonth" class="btn btn-sm btn-light">&gt;</button>
            </div>

            <!-- Calendar Grid -->
            <div class="mb-3">
                <div class="row g-1 text-center mb-2">
                    <div class="col text-muted small fw-semibold">Su</div>
                    <div class="col text-muted small fw-semibold">Mo</div>
                    <div class="col text-muted small fw-semibold">Tu</div>
                    <div class="col text-muted small fw-semibold">We</div>
                    <div class="col text-muted small fw-semibold">Th</div>
                    <div class="col text-muted small fw-semibold">Fr</div>
                    <div class="col text-muted small fw-semibold">Sa</div>
                </div>
                <div id="calendarDays" class="row g-1"></div>
            </div>

            <!-- Date Range Display -->
            <p class="text-muted small mb-3" id="filterDateRange">
                <span id="startDateDisplay">-</span> to <span id="endDateDisplay">-</span>
            </p>

            <!-- Hidden inputs to store selected dates -->
            <input type="hidden" id="dateFilterStart">
            <input type="hidden" id="dateFilterEnd">

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm flex-grow-1" id="resetDateFilter">Reset</button>
            </div>
        </div>
    </div>
    
    <!-- Report Type Dropdown -->
    <div class="dropdown">
        <button class="btn filter-btn d-flex align-items-center dropdown-toggle" type="button" id="reportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-funnel me-2"></i>
            <span id="currentPeriodLabel">
                <?php
                $currentPeriod = $_GET['period'] ?? 'all';
                $periodLabels = [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'all' => 'All Reports'
                ];
                echo $periodLabels[$currentPeriod] ?? 'All Reports';
                ?>
            </span>
        </button>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="reportDropdown">
            <li>
                <a class="dropdown-item <?php echo (!isset($_GET['period']) || $_GET['period'] === 'all') ? 'active' : ''; ?>" href="?period=all">
                    <i class="bi bi-check2 me-2"></i> All Reports
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item <?php echo (isset($_GET['period']) && $_GET['period'] === 'daily') ? 'active' : ''; ?>" href="?period=daily">
                    <i class="bi bi-calendar-day me-2"></i> Daily
                </a>
            </li>
            <li>
                <a class="dropdown-item <?php echo (isset($_GET['period']) && $_GET['period'] === 'weekly') ? 'active' : ''; ?>" href="?period=weekly">
                    <i class="bi bi-calendar-week me-2"></i> Weekly
                </a>
            </li>
            <li>
                <a class="dropdown-item <?php echo (isset($_GET['period']) && $_GET['period'] === 'monthly') ? 'active' : ''; ?>" href="?period=monthly">
                    <i class="bi bi-calendar-month me-2"></i> Monthly
                </a>
            </li>
        </ul>
    </div>

    <!-- Download -->
    <button class="btn download-btn d-flex align-items-center" id="openDownloadModal">
        <i class="bi bi-download me-2"></i> Download
    </button>

</div>

<!-- ===================== DATE FILTER MODAL ===================== -->
<div class="custom-modal" id="dateFilterModal">
    <div class="custom-modal-content" style="width: 350px;">

        <div class="modal-header mb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Date Filter</h5>
            <button class="close-modal">&times;</button>
        </div>

        <div class="modal-body">
            <label class="modal-label fw-semibold mb-3">Pick a Date</label>
            
            <!-- Calendar Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button id="modalPrevMonth" class="btn btn-sm btn-light">&lt;</button>
                <h6 id="modalMonthYear" class="mb-0 fw-semibold"></h6>
                <button id="modalNextMonth" class="btn btn-sm btn-light">&gt;</button>
            </div>

            <!-- Calendar Grid -->
            <div class="mb-3">
                <div class="row g-1 text-center mb-2">
                    <div class="col text-muted small fw-semibold">Su</div>
                    <div class="col text-muted small fw-semibold">Mo</div>
                    <div class="col text-muted small fw-semibold">Tu</div>
                    <div class="col text-muted small fw-semibold">We</div>
                    <div class="col text-muted small fw-semibold">Th</div>
                    <div class="col text-muted small fw-semibold">Fr</div>
                    <div class="col text-muted small fw-semibold">Sa</div>
                </div>
                <div id="modalCalendarDays" class="row g-1"></div>
            </div>

            <!-- Date Range Display -->
            <p class="text-muted small mb-3" id="modalFilterDateRange">
                <span id="modalStartDateDisplay">-</span> to <span id="modalEndDateDisplay">-</span>
            </p>

            <!-- Hidden inputs to store selected dates -->
            <input type="hidden" id="modalDateFilterStart">
            <input type="hidden" id="modalDateFilterEnd">

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary flex-grow-1" id="modalResetDateFilter">Reset Section</button>
                <button class="btn btn-primary flex-grow-1" id="applyDateFilter">Apply</button>
            </div>
        </div>

    </div>
</div>



<!-- ===================== STATISTICS (TOP) ===================== -->
<div class="row mt-2">

    <!-- MONTHLY SALES -->
    <div class="col-lg-8 mb-3">
        <div class="card chart-card p-5">
            <h6 class="fw-semibold fs-3">Total Sales</h6>
            <div style="height: 300px; position: relative;">
                <canvas id="monthlySales"></canvas>
            </div>
        </div>
    </div>

<div class="col-lg-4">
    <div class="card chart-card p-5">
        <h6 class="fw-semibold fs-3 text-center">Total Appointments</h6>
        <div class="chart-container">
            <canvas id="appointmentDonut"></canvas>
        </div>
    </div>
</div>


<!-- ===================== SUMMARY CARDS ===================== -->
<div class="row g-3">

    <div class="col-md-3">
        <div class="card stats-card square-card">
            <h6>Total Customers</h6>
            <h2><?= $totalUsers ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card square-card">
            <h6>Total Services</h6>
            <h2><?= $totalServices ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card square-card">
            <h6>Rejected Appointments</h6>
            <h2><?= $rejectedAppointments ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card square-card">
            <h6>Accepted Appointments</h6>
            <h2><?= $acceptedAppointments ?></h2>
        </div>
    </div>

</div>


<!-- ===================== WEEKLY + TOP SERVICES ===================== -->
<div class="row mt-3">

    <!-- PERIOD SALES -->
    <div class="col-lg-8 mb-3">
        <div class="card weekly-sales-card p-4">
            <?php
            $periodTitles = [
                'daily' => ['title' => 'Daily Sales Overview', 'subtitle' => 'Hourly Sales Performance for today'],
                'weekly' => ['title' => 'Weekly Sales Overview', 'subtitle' => 'Daily Sales Performance for the current week'],
                'monthly' => ['title' => 'Monthly Sales Overview', 'subtitle' => 'Weekly Sales Performance for the current month'],
                'all' => ['title' => 'All Time Sales Overview', 'subtitle' => 'Monthly Sales Performance for all time']
            ];
            $currentTitle = $periodTitles[$currentPeriod] ?? $periodTitles['all'];
            ?>
            <h6 class="fw-semibold mb-1"><?= $currentTitle['title'] ?></h6>
            <small class="text-muted"><?= $currentTitle['subtitle'] ?></small>

            <div class="mt-3 d-flex flex-column gap-2">
                <?php if (empty($periodSales)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No data available for the selected period</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($periodSales as $period): ?>
                        <div class="card p-3 border" style="border-radius: 12px; border: 1px solid #e9ecef;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mt-3"><?= $period['period_label'] ?></h6>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <h6 class="mb-1 fw-bold">₱<?= number_format((int)$period['amount']) ?></h6>
                                        <small class="text-muted mb-2"><?= $period['appointments'] ?> Appointments</small>
                                    </div>
                                </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TOP SERVICES -->
    <div class="col-lg-4">
        <div class="card top-services-card p-3">
            <h6 class="fw-semibold">Top Services by Revenue</h6>
            <?php
            $periodDescriptions = [
                'daily' => 'Today\'s best performing services',
                'weekly' => 'This week\'s best performing services',
                'monthly' => 'This month\'s best performing services',
                'all' => 'All time best performing services'
            ];
            ?>
            <small class="text-muted"><?= $periodDescriptions[$currentPeriod] ?? $periodDescriptions['all'] ?></small>

            <ul class="list-group list-group-flush top-service-list mt-3">
                <?php foreach ($topServices as $svc): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($svc['service_name']) ?>
                        <span class="float-end">₱<?= number_format((int)$svc['total']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div>


<!-- ===================== DOWNLOAD MODAL ===================== -->
<div class="custom-modal" id="downloadModal">
    <div class="custom-modal-content download-modal-content">

        <div class="modal-header">
            <h5 class="modal-title">File Format</h5>
            <button class="close-modal">&times;</button>
        </div>

        <form method="POST" action="/admin/reports/export">
            <input type="hidden" name="period" value="<?= $currentPeriod ?? 'all' ?>">
            <!-- Include selected date range from calendar -->
            <input type="hidden" id="exportStartDate" name="from" value="">
            <input type="hidden" id="exportEndDate" name="to" value="">

            <!-- File Format Options -->
            <div class="format-options mb-4">
                <div class="format-option">
                    <input type="radio" id="formatPdf" name="format" value="pdf" checked>
                    <label for="formatPdf" class="option-label">PDF</label>
                </div>
                <div class="format-option">
                    <input type="radio" id="formatCsv" name="format" value="csv">
                    <label for="formatCsv" class="option-label">CSV</label>
                </div>
            </div>

            <!-- Report Type Label -->
            <h6 class="modal-subtitle mb-3">Report for :</h6>

            <!-- Report Type Options -->
            <div class="report-options mb-4">
                <div class="report-option">
                    <input type="radio" id="typeAppointments" name="type" value="appointments" checked>
                    <label for="typeAppointments" class="option-label">Appointments</label>
                </div>
                <div class="report-option">
                    <input type="radio" id="typeSales" name="type" value="sales">
                    <label for="typeSales" class="option-label">Sales</label>
                </div>
                <div class="report-option">
                    <input type="radio" id="typeCustomerList" name="type" value="customer_list">
                    <label for="typeCustomerList" class="option-label">Customer List</label>
                </div>
                <div class="report-option">
                    <input type="radio" id="typeAll" name="type" value="all">
                    <label for="typeAll" class="option-label">All</label>
                </div>
            </div>

            <button type="submit" class="btn btn-success download-btn-modal w-100">Download</button>
        </form>

    </div>
</div>

<!-- Data for Charts (hidden) -->
<script type="application/json" id="monthlyLabels"><?= json_encode($monthlyLabels) ?></script>
<script type="application/json" id="monthlyValues"><?= json_encode($monthlyValues) ?></script>
<script type="application/json" id="donutAccepted"><?= $donut['accepted'] ?></script>
<script type="application/json" id="donutRejected"><?= $donut['rejected'] ?></script>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="/assets/js/dashboard.js"></script>
