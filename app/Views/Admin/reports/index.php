<?php
$pageTitle = "Reports";
$activePage = "reports";
?>

<div class="admin-section">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="section-title">Reports & Analytics</h6>
            <small class="section-subtitle text-muted">
                View system insights, performance, and generate downloadable reports
            </small>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border" onclick="exportReport('csv')">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
            </button>
            <button type="button" class="btn btn-primary" onclick="exportReport('pdf')" style="background:#CD9FFE;border:none;">
                <i class="bi bi-filetype-pdf me-1"></i> Download PDF
            </button>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="card content-card p-3 mb-4">

        <h6 class="fw-semibold mb-3">Filter Reports</h6>

        <form method="GET" class="row g-3" id="filterForm">

            <!-- Date Range Picker -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Date Range</label>
                <div class="d-flex gap-2">
                    <!-- Date From -->
                    <div class="dropdown flex-grow-1">
                        <button class="btn btn-light border d-flex align-items-center justify-content-between dropdown-toggle w-100" type="button" id="reportsFromDateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="reportsFromDateText">
                                <?php
                                $fromDate = $_GET['from'] ?? date('Y-m-01');
                                echo date('M d, Y', strtotime($fromDate));
                                ?>
                            </span>
                            <i class="bi bi-calendar2-date"></i>
                        </button>

                        <div class="dropdown-menu p-3" aria-labelledby="reportsFromDateDropdown" style="width: 320px;">
                            <h6 class="dropdown-header px-0 py-0 mb-3">Pick From Date</h6>

                            <!-- Calendar Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button id="reportsFromPrevMonth" class="btn btn-sm btn-light">&lt;</button>
                                <h6 id="reportsFromMonthYear" class="mb-0 fw-semibold"></h6>
                                <button id="reportsFromNextMonth" class="btn btn-sm btn-light">&gt;</button>
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
                                <div id="reportsFromCalendarDays" class="row g-1"></div>
                            </div>

                            <!-- Hidden input to store selected date -->
                            <input type="hidden" id="reportsFromDate" name="from" value="<?= $fromDate ?>">

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm flex-grow-1" id="reportsFromResetDate">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="dropdown flex-grow-1">
                        <button class="btn btn-light border d-flex align-items-center justify-content-between dropdown-toggle w-100" type="button" id="reportsToDateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="reportsToDateText">
                                <?php
                                $toDate = $_GET['to'] ?? date('Y-m-d');
                                echo date('M d, Y', strtotime($toDate));
                                ?>
                            </span>
                            <i class="bi bi-calendar2-date"></i>
                        </button>

                        <div class="dropdown-menu p-3" aria-labelledby="reportsToDateDropdown" style="width: 320px;">
                            <h6 class="dropdown-header px-0 py-0 mb-3">Pick To Date</h6>

                            <!-- Calendar Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button id="reportsToPrevMonth" class="btn btn-sm btn-light">&lt;</button>
                                <h6 id="reportsToMonthYear" class="mb-0 fw-semibold"></h6>
                                <button id="reportsToNextMonth" class="btn btn-sm btn-light">&gt;</button>
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
                                <div id="reportsToCalendarDays" class="row g-1"></div>
                            </div>

                            <!-- Hidden input to store selected date -->
                            <input type="hidden" id="reportsToDate" name="to" value="<?= $toDate ?>">

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm flex-grow-1" id="reportsToResetDate">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Type -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Report Type</label>
                <select name="type" class="form-select">
                    <option value="appointments">Appointments</option>
                    <option value="sales">Sales</option>
                    <option value="customer_list">Customer Count</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100" style="background:#CD9FFE;border:none;">
                    <i class="bi bi-filter-circle me-1"></i> Apply Filter
                </button>
            </div>

        </form>

    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Appointments</h6>
                <h2><?= htmlspecialchars($summary['appointments'] ?? 0) ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Customers</h6>
                <h2><?= htmlspecialchars($summary['customers'] ?? 0) ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Revenue</h6>
                <h2>₱<?= number_format($summary['revenue'] ?? 0) ?></h2>
            </div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="card content-card p-4">
        <h6 class="fw-semibold mb-3">Monthly Trend Overview</h6>

        <canvas id="reportsChart" style="min-height:300px;"></canvas>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ===== REPORTS DATE FILTER CALENDAR =====
// Separate calendar implementation for reports page to avoid conflicts with appointments page

// From Date Calendar Variables
let reportsFromCurrentMonth = new Date('<?= date('Y-m-d', strtotime($fromDate)) ?>');
let reportsFromSelectedDate = '<?= $fromDate ?>';

const reportsFromDateDropdown = document.getElementById("reportsFromDateDropdown");
const reportsFromDateFilter = document.getElementById("reportsFromDate");
const reportsFromResetDateFilter = document.getElementById("reportsFromResetDate");

// To Date Calendar Variables
let reportsToCurrentMonth = new Date('<?= date('Y-m-d', strtotime($toDate)) ?>');
let reportsToSelectedDate = '<?= $toDate ?>';

const reportsToDateDropdown = document.getElementById("reportsToDateDropdown");
const reportsToDateFilter = document.getElementById("reportsToDate");
const reportsToResetDateFilter = document.getElementById("reportsToResetDate");

// Prevent dropdown from closing when clicking inside calendar
const reportsFromDateFilterMenu = reportsFromDateDropdown ? reportsFromDateDropdown.nextElementSibling : null;
if (reportsFromDateFilterMenu) {
    reportsFromDateFilterMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

const reportsToDateFilterMenu = reportsToDateDropdown ? reportsToDateDropdown.nextElementSibling : null;
if (reportsToDateFilterMenu) {
    reportsToDateFilterMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// Render calendar when dropdown opens - From Date
if (reportsFromDateDropdown) {
    reportsFromDateDropdown.addEventListener('click', () => {
        setTimeout(() => reportsFromRenderCalendar(), 100);
    });
}

// Render calendar when dropdown opens - To Date
if (reportsToDateDropdown) {
    reportsToDateDropdown.addEventListener('click', () => {
        setTimeout(() => reportsToRenderCalendar(), 100);
    });
}

// From Date Calendar Functions
function reportsFromRenderCalendar() {
    const year = reportsFromCurrentMonth.getFullYear();
    const month = reportsFromCurrentMonth.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthYearEl = document.getElementById("reportsFromMonthYear");
    if (monthYearEl) {
        monthYearEl.textContent = `${monthNames[month]} ${year}`;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const calendarContainer = document.getElementById("reportsFromCalendarDays");
    if (!calendarContainer) return;

    calendarContainer.innerHTML = '';

    // Calculate total cells needed (6 weeks * 7 days = 42 cells)
    const totalCells = 42;
    const daysPerWeek = 7;

    // Create all calendar days array
    const allDays = [];

    // Days from previous month to fill the beginning
    const prevMonthDays = new Date(year, month, 0).getDate();
    const prevMonthStart = prevMonthDays - firstDay + 1;

    // Add days from previous month (muted)
    for (let i = 0; i < firstDay; i++) {
        const prevMonth = month === 0 ? 11 : month - 1;
        const prevYear = month === 0 ? year - 1 : year;
        const day = prevMonthStart + i;
        const dateStr = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: true });
    }

    // Add days of current month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: false });
    }

    // Calculate remaining cells needed
    const usedCells = firstDay + daysInMonth;
    const remainingCells = totalCells - usedCells;

    // Add days from next month (muted)
    for (let i = 0; i < remainingCells; i++) {
        const nextMonth = month === 11 ? 0 : month + 1;
        const nextYear = month === 11 ? year + 1 : year;
        const day = i + 1;
        const dateStr = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: true });
    }

    // Create rows for each week
    for (let week = 0; week < 6; week++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'row g-1';

        // Add 7 days for this week
        for (let dayOfWeek = 0; dayOfWeek < daysPerWeek; dayOfWeek++) {
            const dayIndex = week * daysPerWeek + dayOfWeek;
            const dayData = allDays[dayIndex];
            const dayEl = reportsFromCreateDayElement(dayData.day, dayData.dateStr, dayData.isMuted);
            weekRow.appendChild(dayEl);
        }

        calendarContainer.appendChild(weekRow);
    }
}

function reportsFromCreateDayElement(day, dateStr, isMuted = false) {
    const dayEl = document.createElement('div');
    dayEl.className = 'col text-center p-2 small d-flex align-items-center justify-content-center';
    dayEl.style.borderRadius = '4px';
    dayEl.style.minHeight = '32px';
    dayEl.textContent = day;

    // Determine styling
    let isSelected = reportsFromSelectedDate === dateStr;

    if (isMuted) {
        dayEl.classList.add('text-muted');
        dayEl.style.cursor = 'default';
        dayEl.style.opacity = '0.4';
    } else {
        dayEl.style.cursor = 'pointer';

        if (isSelected) {
            dayEl.classList.add('bg-primary', 'text-white', 'fw-bold');
        }

        // Click handler only for non-muted days
        dayEl.onclick = () => reportsFromSelectDate(dateStr);
    }

    return dayEl;
}

function reportsFromSelectDate(dateStr) {
    if (reportsFromSelectedDate === dateStr) {
        reportsFromSelectedDate = null;
    } else {
        reportsFromSelectedDate = dateStr;
        reportsFromApplyFilter();
    }

    reportsFromDateFilter.value = reportsFromSelectedDate || '';
    reportsFromRenderCalendar();
}

function reportsFromApplyFilter() {
    if (reportsFromSelectedDate) {
        // Update the display text
        const dateTextEl = document.getElementById('reportsFromDateText');
        if (dateTextEl) {
            const date = new Date(reportsFromSelectedDate);
            dateTextEl.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    }
}

// To Date Calendar Functions
function reportsToRenderCalendar() {
    const year = reportsToCurrentMonth.getFullYear();
    const month = reportsToCurrentMonth.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthYearEl = document.getElementById("reportsToMonthYear");
    if (monthYearEl) {
        monthYearEl.textContent = `${monthNames[month]} ${year}`;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const calendarContainer = document.getElementById("reportsToCalendarDays");
    if (!calendarContainer) return;

    calendarContainer.innerHTML = '';

    // Calculate total cells needed (6 weeks * 7 days = 42 cells)
    const totalCells = 42;
    const daysPerWeek = 7;

    // Create all calendar days array
    const allDays = [];

    // Days from previous month to fill the beginning
    const prevMonthDays = new Date(year, month, 0).getDate();
    const prevMonthStart = prevMonthDays - firstDay + 1;

    // Add days from previous month (muted)
    for (let i = 0; i < firstDay; i++) {
        const prevMonth = month === 0 ? 11 : month - 1;
        const prevYear = month === 0 ? year - 1 : year;
        const day = prevMonthStart + i;
        const dateStr = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: true });
    }

    // Add days of current month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: false });
    }

    // Calculate remaining cells needed
    const usedCells = firstDay + daysInMonth;
    const remainingCells = totalCells - usedCells;

    // Add days from next month (muted)
    for (let i = 0; i < remainingCells; i++) {
        const nextMonth = month === 11 ? 0 : month + 1;
        const nextYear = month === 11 ? year + 1 : year;
        const day = i + 1;
        const dateStr = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        allDays.push({ day, dateStr, isMuted: true });
    }

    // Create rows for each week
    for (let week = 0; week < 6; week++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'row g-1';

        // Add 7 days for this week
        for (let dayOfWeek = 0; dayOfWeek < daysPerWeek; dayOfWeek++) {
            const dayIndex = week * daysPerWeek + dayOfWeek;
            const dayData = allDays[dayIndex];
            const dayEl = reportsToCreateDayElement(dayData.day, dayData.dateStr, dayData.isMuted);
            weekRow.appendChild(dayEl);
        }

        calendarContainer.appendChild(weekRow);
    }
}

function reportsToCreateDayElement(day, dateStr, isMuted = false) {
    const dayEl = document.createElement('div');
    dayEl.className = 'col text-center p-2 small d-flex align-items-center justify-content-center';
    dayEl.style.borderRadius = '4px';
    dayEl.style.minHeight = '32px';
    dayEl.textContent = day;

    // Determine styling
    let isSelected = reportsToSelectedDate === dateStr;

    if (isMuted) {
        dayEl.classList.add('text-muted');
        dayEl.style.cursor = 'default';
        dayEl.style.opacity = '0.4';
    } else {
        dayEl.style.cursor = 'pointer';

        if (isSelected) {
            dayEl.classList.add('bg-primary', 'text-white', 'fw-bold');
        }

        // Click handler only for non-muted days
        dayEl.onclick = () => reportsToSelectDate(dateStr);
    }

    return dayEl;
}

function reportsToSelectDate(dateStr) {
    if (reportsToSelectedDate === dateStr) {
        reportsToSelectedDate = null;
    } else {
        reportsToSelectedDate = dateStr;
        reportsToApplyFilter();
    }

    reportsToDateFilter.value = reportsToSelectedDate || '';
    reportsToRenderCalendar();
}

function reportsToApplyFilter() {
    if (reportsToSelectedDate) {
        // Update the display text
        const dateTextEl = document.getElementById('reportsToDateText');
        if (dateTextEl) {
            const date = new Date(reportsToSelectedDate);
            dateTextEl.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    }
}

// Month Navigation - From Date
const reportsFromPrevMonthBtn = document.getElementById("reportsFromPrevMonth");
const reportsFromNextMonthBtn = document.getElementById("reportsFromNextMonth");

if (reportsFromPrevMonthBtn) {
    reportsFromPrevMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        reportsFromCurrentMonth = new Date(reportsFromCurrentMonth.getFullYear(), reportsFromCurrentMonth.getMonth() - 1);
        reportsFromRenderCalendar();
    };
}

if (reportsFromNextMonthBtn) {
    reportsFromNextMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        reportsFromCurrentMonth = new Date(reportsFromCurrentMonth.getFullYear(), reportsFromCurrentMonth.getMonth() + 1);
        reportsFromRenderCalendar();
    };
}

// Month Navigation - To Date
const reportsToPrevMonthBtn = document.getElementById("reportsToPrevMonth");
const reportsToNextMonthBtn = document.getElementById("reportsToNextMonth");

if (reportsToPrevMonthBtn) {
    reportsToPrevMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        reportsToCurrentMonth = new Date(reportsToCurrentMonth.getFullYear(), reportsToCurrentMonth.getMonth() - 1);
        reportsToRenderCalendar();
    };
}

if (reportsToNextMonthBtn) {
    reportsToNextMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        reportsToCurrentMonth = new Date(reportsToCurrentMonth.getFullYear(), reportsToCurrentMonth.getMonth() + 1);
        reportsToRenderCalendar();
    };
}

// Reset Date Filter - From Date
if (reportsFromResetDateFilter) {
    reportsFromResetDateFilter.onclick = (e) => {
        e.preventDefault();
        reportsFromSelectedDate = null;
        reportsFromDateFilter.value = '';
        reportsFromCurrentMonth = new Date();
        reportsFromRenderCalendar();
        // Reset display text to current month start
        const dateTextEl = document.getElementById('reportsFromDateText');
        if (dateTextEl) {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            dateTextEl.textContent = firstDay.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            reportsFromDateFilter.value = firstDay.toISOString().split('T')[0];
        }
    };
}

// Reset Date Filter - To Date
if (reportsToResetDateFilter) {
    reportsToResetDateFilter.onclick = (e) => {
        e.preventDefault();
        reportsToSelectedDate = null;
        reportsToDateFilter.value = '';
        reportsToCurrentMonth = new Date();
        reportsToRenderCalendar();
        // Reset display text to today
        const dateTextEl = document.getElementById('reportsToDateText');
        if (dateTextEl) {
            const today = new Date();
            dateTextEl.textContent = today.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            reportsToDateFilter.value = today.toISOString().split('T')[0];
        }
    };
}

// Handle form submission to update summary data
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fromDate = this.querySelector('input[name="from"]').value;
    const toDate = this.querySelector('input[name="to"]').value;

    if (!fromDate || !toDate) {
        alert('Please select both "From Date" and "To Date".');
        return;
    }

    if (new Date(fromDate) > new Date(toDate)) {
        alert('From Date cannot be later than To Date.');
        return;
    }

    // Update URL with filter parameters
    const url = new URL(window.location);
    url.searchParams.set('from', fromDate);
    url.searchParams.set('to', toDate);
    window.location.href = url.toString();
});

// Export report with selected dates
function exportReport(format) {
    const filterForm = document.querySelector('.card.content-card form');
    const fromInput = filterForm.querySelector('input[name="from"]');
    const toInput = filterForm.querySelector('input[name="to"]');

    if (!fromInput || !toInput) {
        alert('Error: Date inputs not found. Please refresh the page.');
        return;
    }

    const fromDate = fromInput.value.trim();
    const toDate = toInput.value.trim();

    if (!fromDate || !toDate) {
        alert('Please select both "From Date" and "To Date" before exporting.');
        return;
    }

    // Validate date range
    if (new Date(fromDate) > new Date(toDate)) {
        alert('From Date cannot be later than To Date.');
        return;
    }

    // Build the export URL with parameters
    const reportType = filterForm.querySelector('select[name="type"]').value;
    const url = `/admin/reports/export?format=${format}&type=${reportType}&from=${encodeURIComponent(fromDate)}&to=${encodeURIComponent(toDate)}`;

    console.log('Exporting to:', url);

    // Open as download
    window.location.href = url;
}

fetch('/analytics/daily-appointments')
  .then(res => res.json())
  .then(data => {
      const labels = data.map(x => x.appointment_date).reverse();
      const totals = data.map(x => Number(x.total)).reverse();

      const ctx = document.getElementById('reportsChart').getContext('2d');

      new Chart(ctx, {
          type: 'line',
          data: {
              labels: labels,
              datasets: [{
                  label: "Daily Appointments",
                  data: totals,
                  borderColor: "#CD9FFE",
                  backgroundColor: "rgba(205,159,254,0.2)",
                  borderWidth: 2,
                  tension: 0.4,
                  fill: true
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: { display: true }
              },
              scales: {
                  y: { beginAtZero: true }
              }
          }
      });
  });
</script>
