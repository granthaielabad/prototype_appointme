// Store chart instances for animation updates
let monthlySalesChart = null;
let appointmentDonutChart = null;

// Variables to store chart data
let monthlyLabels = [];
let monthlyValues = [];
let donutAccepted = 0;
let donutRejected = 0;

// ===== DATE FILTER DROPDOWN WITH CALENDAR =====
// Modal calendar variables
let modalCurrentMonth = new Date();
let modalSelectedStartDate = null;
let modalSelectedEndDate = null;
const dateFilterDropdown = document.getElementById("dateFilterDropdown");
const dateFilterStart = document.getElementById("dateFilterStart");
const dateFilterEnd = document.getElementById("dateFilterEnd");
const startDateDisplay = document.getElementById("startDateDisplay");
const endDateDisplay = document.getElementById("endDateDisplay");
const resetDateFilter = document.getElementById("resetDateFilter");

// Prevent dropdown from closing when clicking inside calendar
const dateFilterMenu = dateFilterDropdown ? dateFilterDropdown.nextElementSibling : null;
if (dateFilterMenu) {
    dateFilterMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

let selectedStartDate = null;
let selectedEndDate = null;
let currentMonth = new Date();

// Check URL params for existing dates
const params = new URLSearchParams(window.location.search);
if (params.has('start')) {
    selectedStartDate = params.get('start');
    startDateDisplay.textContent = selectedStartDate;
    dateFilterStart.value = selectedStartDate;
}
if (params.has('end')) {
    selectedEndDate = params.get('end');
    endDateDisplay.textContent = selectedEndDate;
    dateFilterEnd.value = selectedEndDate;
}

// Render calendar when dropdown opens
if (dateFilterDropdown) {
    dateFilterDropdown.addEventListener('click', () => {
        setTimeout(() => renderCalendar(), 100);
    });
}

// Calendar Rendering
function renderCalendar() {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthYearEl = document.getElementById("monthYear");
    if (monthYearEl) {
        monthYearEl.textContent = `${monthNames[month]} ${year}`;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const calendarContainer = document.getElementById("calendarDays");
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
            const dayEl = createDayElement(dayData.day, dayData.dateStr, dayData.isMuted);
            weekRow.appendChild(dayEl);
        }

        calendarContainer.appendChild(weekRow);
    }
}

function createDayElement(day, dateStr, isMuted = false) {
    const dayEl = document.createElement('div');
    dayEl.className = 'col text-center p-2 small d-flex align-items-center justify-content-center';
    dayEl.style.borderRadius = '4px';
    dayEl.style.minHeight = '32px'; // Ensure consistent height for all days
    dayEl.textContent = day;

    // Determine styling
    let isStart = selectedStartDate === dateStr;
    let isEnd = selectedEndDate === dateStr;
    let isBetween = false;

    if (selectedStartDate && selectedEndDate) {
        isBetween = dateStr > selectedStartDate && dateStr < selectedEndDate;
    }

    if (isMuted) {
        // Muted days from other months
        dayEl.classList.add('text-muted');
        dayEl.style.cursor = 'default';
        dayEl.style.opacity = '0.4';
    } else {
        // Active days from current month
        dayEl.style.cursor = 'pointer';

        if (isStart || isEnd) {
            dayEl.classList.add('bg-primary', 'text-white', 'fw-bold');
        } else if (isBetween) {
            dayEl.classList.add('bg-light');
        }

        // Click handler only for non-muted days
        dayEl.onclick = () => selectDate(dateStr);
    }

    return dayEl;
}

function selectDate(dateStr) {
    if (!selectedStartDate) {
        // First click: set start date
        selectedStartDate = dateStr;
        selectedEndDate = null;
        startDateDisplay.textContent = dateStr;
        endDateDisplay.textContent = '-';
    } else if (!selectedEndDate) {
        // Second click
        if (dateStr > selectedStartDate) {
            // If after start date, set as end date and instantly apply
            selectedEndDate = dateStr;
            endDateDisplay.textContent = dateStr;
            applyFilter();
        } else if (dateStr < selectedStartDate) {
            // If before start date, swap them
            selectedEndDate = selectedStartDate;
            selectedStartDate = dateStr;
            startDateDisplay.textContent = dateStr;
            endDateDisplay.textContent = selectedEndDate;
            applyFilter();
        } else {
            // If same date, reset
            selectedStartDate = null;
            selectedEndDate = null;
            startDateDisplay.textContent = '-';
            endDateDisplay.textContent = '-';
        }
    } else {
        // Both dates selected, reset and start over
        selectedStartDate = dateStr;
        selectedEndDate = null;
        startDateDisplay.textContent = dateStr;
        endDateDisplay.textContent = '-';
    }
    
    dateFilterStart.value = selectedStartDate || '';
    dateFilterEnd.value = selectedEndDate || '';
    renderCalendar();
}

function applyFilter() {
    if (selectedStartDate && selectedEndDate) {
        const url = new URL(window.location);
        url.searchParams.set('start', selectedStartDate);
        url.searchParams.set('end', selectedEndDate);
        window.location.href = url.toString();
    }
}

// Month Navigation
const prevMonthBtn = document.getElementById("prevMonth");
const nextMonthBtn = document.getElementById("nextMonth");

if (prevMonthBtn) {
    prevMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1);
        renderCalendar();
    };
}

if (nextMonthBtn) {
    nextMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1);
        renderCalendar();
    };
}

// Reset Date Filter
if (resetDateFilter) {
    resetDateFilter.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent dropdown from closing

        // Clear date filter and reload page to reset all dashboard data
        const baseUrl = window.location.origin + window.location.pathname;
        const url = new URL(baseUrl);

        // Preserve other parameters if they exist (like period)
        const currentParams = new URLSearchParams(window.location.search);
        if (currentParams.has('period')) {
            url.searchParams.set('period', currentParams.get('period'));
        }

        window.location.href = url.toString();
    };
}

// ===== MODAL CALENDAR =====
const dateFilterModal = document.getElementById("dateFilterModal");
const modalPrevMonthBtn = document.getElementById("modalPrevMonth");
const modalNextMonthBtn = document.getElementById("modalNextMonth");
const modalMonthYearEl = document.getElementById("modalMonthYear");
const modalCalendarDays = document.getElementById("modalCalendarDays");
const modalStartDateDisplay = document.getElementById("modalStartDateDisplay");
const modalEndDateDisplay = document.getElementById("modalEndDateDisplay");
const modalDateFilterStart = document.getElementById("modalDateFilterStart");
const modalDateFilterEnd = document.getElementById("modalDateFilterEnd");
const modalResetDateFilter = document.getElementById("modalResetDateFilter");
const applyDateFilter = document.getElementById("applyDateFilter");

// Initialize modal calendar when modal opens
if (dateFilterModal) {
    // Copy current selections to modal
    modalSelectedStartDate = selectedStartDate;
    modalSelectedEndDate = selectedEndDate;
    modalCurrentMonth = new Date(currentMonth);

    // Update modal displays
    if (modalStartDateDisplay) modalStartDateDisplay.textContent = modalSelectedStartDate || '-';
    if (modalEndDateDisplay) modalEndDateDisplay.textContent = modalSelectedEndDate || '-';
    if (modalDateFilterStart) modalDateFilterStart.value = modalSelectedStartDate || '';
    if (modalDateFilterEnd) modalDateFilterEnd.value = modalSelectedEndDate || '';

    renderModalCalendar();
}

// Modal calendar rendering
function renderModalCalendar() {
    const year = modalCurrentMonth.getFullYear();
    const month = modalCurrentMonth.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    if (modalMonthYearEl) {
        modalMonthYearEl.textContent = `${monthNames[month]} ${year}`;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    if (!modalCalendarDays) return;

    modalCalendarDays.innerHTML = '';

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
            const dayEl = createModalDayElement(dayData.day, dayData.dateStr, dayData.isMuted);
            weekRow.appendChild(dayEl);
        }

        modalCalendarDays.appendChild(weekRow);
    }
}

function createModalDayElement(day, dateStr, isMuted = false) {
    const dayEl = document.createElement('div');
    dayEl.className = 'col text-center p-2 small d-flex align-items-center justify-content-center';
    dayEl.style.borderRadius = '4px';
    dayEl.style.minHeight = '32px'; // Ensure consistent height for all days
    dayEl.textContent = day;

    // Determine styling
    let isStart = modalSelectedStartDate === dateStr;
    let isEnd = modalSelectedEndDate === dateStr;
    let isBetween = false;

    if (modalSelectedStartDate && modalSelectedEndDate) {
        isBetween = dateStr > modalSelectedStartDate && dateStr < modalSelectedEndDate;
    }

    if (isMuted) {
        // Muted days from other months
        dayEl.classList.add('text-muted');
        dayEl.style.cursor = 'default';
        dayEl.style.opacity = '0.4';
    } else {
        // Active days from current month
        dayEl.style.cursor = 'pointer';

        if (isStart || isEnd) {
            dayEl.classList.add('bg-primary', 'text-white', 'fw-bold');
        } else if (isBetween) {
            dayEl.classList.add('bg-light');
        }

        // Click handler only for non-muted days
        dayEl.onclick = () => selectModalDate(dateStr);
    }

    return dayEl;
}

function selectModalDate(dateStr) {
    if (!modalSelectedStartDate) {
        // First click: set start date
        modalSelectedStartDate = dateStr;
        modalSelectedEndDate = null;
        if (modalStartDateDisplay) modalStartDateDisplay.textContent = dateStr;
        if (modalEndDateDisplay) modalEndDateDisplay.textContent = '-';
    } else if (!modalSelectedEndDate) {
        // Second click
        if (dateStr > modalSelectedStartDate) {
            // If after start date, set as end date and instantly apply
            modalSelectedEndDate = dateStr;
            if (modalEndDateDisplay) modalEndDateDisplay.textContent = dateStr;
            applyModalFilter(); // Auto-apply when both dates are selected
        } else if (dateStr < modalSelectedStartDate) {
            // If before start date, swap them and apply
            modalSelectedEndDate = modalSelectedStartDate;
            modalSelectedStartDate = dateStr;
            if (modalStartDateDisplay) modalStartDateDisplay.textContent = dateStr;
            if (modalEndDateDisplay) modalEndDateDisplay.textContent = modalSelectedEndDate;
            applyModalFilter(); // Auto-apply when both dates are selected
        } else {
            // If same date, reset
            modalSelectedStartDate = null;
            modalSelectedEndDate = null;
            if (modalStartDateDisplay) modalStartDateDisplay.textContent = '-';
            if (modalEndDateDisplay) modalEndDateDisplay.textContent = '-';
        }
    } else {
        // Both dates selected, reset and start over
        modalSelectedStartDate = dateStr;
        modalSelectedEndDate = null;
        if (modalStartDateDisplay) modalStartDateDisplay.textContent = dateStr;
        if (modalEndDateDisplay) modalEndDateDisplay.textContent = '-';
    }

    if (modalDateFilterStart) modalDateFilterStart.value = modalSelectedStartDate || '';
    if (modalDateFilterEnd) modalDateFilterEnd.value = modalSelectedEndDate || '';
    renderModalCalendar();
}

function applyModalFilter() {
    // Copy modal selections to main calendar and apply filter
    selectedStartDate = modalSelectedStartDate;
    selectedEndDate = modalSelectedEndDate;
    currentMonth = new Date(modalCurrentMonth);

    // Update main displays
    if (startDateDisplay) startDateDisplay.textContent = selectedStartDate || '-';
    if (endDateDisplay) endDateDisplay.textContent = selectedEndDate || '-';
    if (dateFilterStart) dateFilterStart.value = selectedStartDate || '';
    if (dateFilterEnd) dateFilterEnd.value = selectedEndDate || '';

    // Apply filter if both dates are selected
    if (selectedStartDate && selectedEndDate) {
        applyFilter();
    } else {
        renderCalendar();
    }

    // Close modal after auto-applying
    if (dateFilterModal) dateFilterModal.style.display = 'none';
}

// Modal month navigation
if (modalPrevMonthBtn) {
    modalPrevMonthBtn.onclick = (e) => {
        e.preventDefault();
        modalCurrentMonth = new Date(modalCurrentMonth.getFullYear(), modalCurrentMonth.getMonth() - 1);
        renderModalCalendar();
    };
}

if (modalNextMonthBtn) {
    modalNextMonthBtn.onclick = (e) => {
        e.preventDefault();
        modalCurrentMonth = new Date(modalCurrentMonth.getFullYear(), modalCurrentMonth.getMonth() + 1);
        renderModalCalendar();
    };
}

// Modal reset
if (modalResetDateFilter) {
    modalResetDateFilter.onclick = (e) => {
        e.preventDefault();
        modalSelectedStartDate = null;
        modalSelectedEndDate = null;
        if (modalStartDateDisplay) modalStartDateDisplay.textContent = '-';
        if (modalEndDateDisplay) modalEndDateDisplay.textContent = '-';
        if (modalDateFilterStart) modalDateFilterStart.value = '';
        if (modalDateFilterEnd) modalDateFilterEnd.value = '';
        modalCurrentMonth = new Date();
        renderModalCalendar();

        // Also reset main calendar and close modal
        selectedStartDate = null;
        selectedEndDate = null;
        if (startDateDisplay) startDateDisplay.textContent = '-';
        if (endDateDisplay) endDateDisplay.textContent = '-';
        if (dateFilterStart) dateFilterStart.value = '';
        if (dateFilterEnd) dateFilterEnd.value = '';

        // Close modal after resetting
        if (dateFilterModal) dateFilterModal.style.display = 'none';

        // Reload page to reset dashboard data
        const baseUrl = window.location.origin + window.location.pathname;
        const url = new URL(baseUrl);
        const currentParams = new URLSearchParams(window.location.search);
        if (currentParams.has('period')) {
            url.searchParams.set('period', currentParams.get('period'));
        }
        window.location.href = url.toString();
    };
}

// Modal now auto-applies when both dates are selected
// The applyDateFilter button is no longer needed

// ===== PERIOD DROPDOWN LABEL UPDATE =====
function updatePeriodDropdownLabel() {
    // Get current period from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentPeriod = urlParams.get('period') || 'all';

    const periodLabels = {
        'daily': 'Daily',
        'weekly': 'Weekly',
        'monthly': 'Monthly',
        'all': 'All Reports'
    };

    const labelElement = document.getElementById('currentPeriodLabel');
    if (labelElement) {
        labelElement.textContent = periodLabels[currentPeriod] || 'All Reports';
    }
}

// Update label on page load
updatePeriodDropdownLabel();

// ===== DOWNLOAD MODAL =====
const modal = document.getElementById("downloadModal");
document.getElementById("openDownloadModal").onclick = () => {
    // Populate export date inputs with current calendar selections
    const exportStartDate = document.getElementById("exportStartDate");
    const exportEndDate = document.getElementById("exportEndDate");

    if (exportStartDate) exportStartDate.value = selectedStartDate || '';
    if (exportEndDate) exportEndDate.value = selectedEndDate || '';

    modal.style.display = "flex";
};
modal.querySelector(".close-modal").onclick = () => modal.style.display = "none";

// Function to load chart data from DOM
function loadChartData() {
    try {
        const monthlyLabelsEl = document.getElementById("monthlyLabels");
        const monthlyValuesEl = document.getElementById("monthlyValues");
        const donutAcceptedEl = document.getElementById("donutAccepted");
        const donutRejectedEl = document.getElementById("donutRejected");

        if (monthlyLabelsEl) monthlyLabels = JSON.parse(monthlyLabelsEl.textContent);
        if (monthlyValuesEl) monthlyValues = JSON.parse(monthlyValuesEl.textContent);
        if (donutAcceptedEl) donutAccepted = parseInt(donutAcceptedEl.textContent);
        if (donutRejectedEl) donutRejected = parseInt(donutRejectedEl.textContent);
    } catch (error) {
        console.error('Error loading chart data:', error);
    }
}

// CHART: MONTHLY SALES (Bar + Line overlay)
function createMonthlySalesChart() {
    const canvas = document.getElementById("monthlySales");
    if (!canvas) return null;

    // Create initial empty data arrays for animation
    const initialData = monthlyValues.map(() => 0);

    const chart = new Chart(canvas, {
    type: "bar",
    data: {
        labels: monthlyLabels,
        datasets: [
            // Bar dataset
            {
                label: "Sales",
                type: "bar",
                    data: initialData,
                backgroundColor: "rgba(205, 159, 254, 0.4)",
                borderColor: "rgba(205, 159, 254, 0.8)",
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
                order: 2
            },
            // Line overlay dataset
            {
                label: "Trend",
                type: "line",
                    data: initialData,
                borderColor: "#CD9FFE",
                backgroundColor: "transparent",
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: "#CD9FFE",
                pointBorderColor: "#FFF",
                pointBorderWidth: 2,
                order: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        animation: {
                duration: 1500,
                easing: 'easeOutQuart',
                onComplete: function() {
                    // Animation completed
                },
            delay: (context) => {
                let delay = 0;
                if (context.type === 'data') {
                        // Stagger bars for sequential animation
                        delay = context.dataIndex * 80;
                }
                return delay;
            }
        },
            animations: {
                y: {
                    from: 0,
                    duration: 1500,
                    easing: 'easeOutQuart'
                },
                x: {
                    duration: 0
                },
                colors: {
                    from: 'rgba(205, 159, 254, 0)',
                    duration: 1500
                }
            },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 12 },
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        if (context.dataset.type === 'line') {
                            return '₱' + new Intl.NumberFormat('en-PH').format(context.parsed.y);
                        }
                        return '';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                    min: 0,
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) {
                            return '₱' + (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            return '₱' + (value / 1000).toFixed(0) + 'K';
                        }
                        return '₱' + value;
                    },
                    font: { size: 11, color: '#999' }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: { size: 11, color: '#999' }
                }
            }
        }
    }
});

    // Animate from zero to actual values - bars will grow from bottom to top
    setTimeout(() => {
        chart.data.datasets[0].data = monthlyValues;
        chart.data.datasets[1].data = monthlyValues;
        // Use 'active' mode to trigger smooth animation from bottom
        chart.update('active');
    }, 200);

    return chart;
}

// CHART: APPOINTMENT DONUT
function createAppointmentDonutChart() {
    const canvas = document.getElementById("appointmentDonut");
    if (!canvas) return null;

    // Calculate total for initial animation
    const total = donutAccepted + donutRejected;
    const initialData = total > 0 ? [0, 0] : [donutAccepted, donutRejected];

    const chart = new Chart(canvas, {
    type: "doughnut",
    data: {
        labels: ["Accepted", "Rejected"],
        datasets: [{
                data: initialData,
            backgroundColor: ["#2563EB", "#67E8F9"],
            borderColor: "#FFF",
            borderWidth: 3
        }]
    },
    options: {
        cutout: "50%",
        responsive: true,
        maintainAspectRatio: true,
        animation: {
                duration: 2000,
                easing: 'easeOutCubic',
                animateRotate: true,
                animateScale: true,
                onComplete: function() {
                    // Animation completed
                }
            },
            animations: {
                radius: {
                    from: 0,
                    duration: 2000,
                    easing: 'easeOutCubic'
                },
                angle: {
                    from: 0,
                    duration: 2000,
                    easing: 'easeOutCubic'
                },
                colors: {
                    from: 'transparent',
                    duration: 2000
                }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: { size: 12, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 8
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 12 },
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(0);
                        return percentage + '%';
                    }
                }
            },
            datalabels: {
                color: '#000',
                font: { size: 13, weight: 'bold' },
                formatter: function(value, context) {
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(0);
                    return percentage + '%';
                }
            }
        }
    }
});

    // Animate from zero to actual values
    if (total > 0) {
        setTimeout(() => {
            chart.data.datasets[0].data = [donutAccepted, donutRejected];
            chart.update('active');
        }, 100);
    }

    return chart;
}

// Function to animate charts when data changes
function animateCharts() {
    try {
        if (monthlySalesChart && typeof monthlySalesChart.update === 'function') {
            // Use 'active' mode to trigger animations
            monthlySalesChart.update('active');
        }
        if (appointmentDonutChart && typeof appointmentDonutChart.update === 'function') {
            // Use 'active' mode to trigger animations
            appointmentDonutChart.update('active');
        }
    } catch (error) {
        console.log('Chart animation error:', error);
        // Fallback: recreate charts if they failed
        setTimeout(() => {
            initializeCharts();
        }, 500);
    }
}

// Function to initialize/reinitialize charts
function initializeCharts() {
    // Load chart data first
    loadChartData();

    // Check if charts already exist and destroy them first
    if (monthlySalesChart) {
        monthlySalesChart.destroy();
        monthlySalesChart = null;
    }
    if (appointmentDonutChart) {
        appointmentDonutChart.destroy();
        appointmentDonutChart = null;
    }

    // Recreate the charts
    try {
        monthlySalesChart = createMonthlySalesChart();
        appointmentDonutChart = createAppointmentDonutChart();
        console.log('Charts initialized successfully');
    } catch (error) {
        console.error('Chart initialization error:', error);
    }
}

// Add CSS for smooth transitions on chart container
const style = document.createElement('style');
style.textContent = `
    .chart-container {
        position: relative;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0.8;
        }
        to {
            opacity: 1;
        }
    }
    
    canvas {
        transition: opacity 0.3s ease-in-out;
    }
`;
document.head.appendChild(style);

// ===== INITIALIZE CHARTS ON PAGE LOAD =====
function initCharts() {
    // Wait for Chart.js to be available
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded yet, retrying...');
        setTimeout(initCharts, 100);
        return;
    }

    // Configure Chart.js defaults to ensure animations are enabled
    Chart.defaults.animation = Chart.defaults.animation || {};
    Chart.defaults.animation.duration = 2000;
    Chart.defaults.animation.easing = 'easeOutCubic';

    // Load chart data and initialize charts
    loadChartData();
    
    // Check if canvas elements exist
    const monthlySalesCanvas = document.getElementById("monthlySales");
    const appointmentDonutCanvas = document.getElementById("appointmentDonut");
    
    if (!monthlySalesCanvas || !appointmentDonutCanvas) {
        console.warn('Chart canvas elements not found, retrying...');
        setTimeout(initCharts, 100);
        return;
    }
    
    // Initialize charts - they will animate automatically on creation
    try {
        // Destroy existing charts if they exist
        if (monthlySalesChart) {
            monthlySalesChart.destroy();
        }
        if (appointmentDonutChart) {
            appointmentDonutChart.destroy();
        }
        
        // Create new charts with animations
        monthlySalesChart = createMonthlySalesChart();
        appointmentDonutChart = createAppointmentDonutChart();
        
        console.log('Charts initialized successfully with animations');
        console.log('Monthly Sales Chart:', monthlySalesChart);
        console.log('Appointment Donut Chart:', appointmentDonutChart);
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    // DOM is already ready
    initCharts();
}
