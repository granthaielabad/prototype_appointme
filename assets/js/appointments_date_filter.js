// ===== APPOINTMENT DATE FILTER CALENDAR =====
// Modal calendar variables
let apptCurrentMonth = new Date();
let apptSelectedDate = null;

const apptDateFilterDropdown = document.getElementById("apptDateFilterDropdown");
const apptDateFilter = document.getElementById("apptDateFilter");
const apptResetDateFilter = document.getElementById("apptResetDateFilter");

// Prevent dropdown from closing when clicking inside calendar
const apptDateFilterMenu = apptDateFilterDropdown ? apptDateFilterDropdown.nextElementSibling : null;
if (apptDateFilterMenu) {
    apptDateFilterMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// Check URL params for existing date
const apptParams = new URLSearchParams(window.location.search);
if (apptParams.has('date')) {
    apptSelectedDate = apptParams.get('date');
    apptDateFilter.value = apptSelectedDate;
}

// Render calendar when dropdown opens
if (apptDateFilterDropdown) {
    apptDateFilterDropdown.addEventListener('click', () => {
        setTimeout(() => apptRenderCalendar(), 100);
    });
}

// Calendar Rendering
function apptRenderCalendar() {
    const year = apptCurrentMonth.getFullYear();
    const month = apptCurrentMonth.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const monthYearEl = document.getElementById("apptMonthYear");
    if (monthYearEl) {
        monthYearEl.textContent = `${monthNames[month]} ${year}`;
    }

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const calendarContainer = document.getElementById("apptCalendarDays");
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
            const dayEl = apptCreateDayElement(dayData.day, dayData.dateStr, dayData.isMuted);
            weekRow.appendChild(dayEl);
        }

        calendarContainer.appendChild(weekRow);
    }
}

function apptCreateDayElement(day, dateStr, isMuted = false) {
    const dayEl = document.createElement('div');
    dayEl.className = 'col text-center p-2 small d-flex align-items-center justify-content-center';
    dayEl.style.borderRadius = '4px';
    dayEl.style.minHeight = '32px'; // Ensure consistent height for all days
    dayEl.textContent = day;

    // Determine styling
    let isSelected = apptSelectedDate === dateStr;

    if (isMuted) {
        // Muted days from other months
        dayEl.classList.add('text-muted');
        dayEl.style.cursor = 'default';
        dayEl.style.opacity = '0.4';
    } else {
        // Active days from current month
        dayEl.style.cursor = 'pointer';

        if (isSelected) {
            dayEl.classList.add('bg-primary', 'text-white', 'fw-bold');
        }

        // Click handler only for non-muted days
        dayEl.onclick = () => apptSelectDate(dateStr);
    }

    return dayEl;
}

function apptSelectDate(dateStr) {
    if (apptSelectedDate === dateStr) {
        // If same date clicked, deselect it
        apptSelectedDate = null;
    } else {
        // Select the new date and apply filter
        apptSelectedDate = dateStr;
        apptApplyFilter();
    }

    apptDateFilter.value = apptSelectedDate || '';
    apptRenderCalendar();
}

function apptApplyFilter() {
    if (apptSelectedDate) {
        const url = new URL(window.location);
        url.searchParams.set('date', apptSelectedDate);
        window.location.href = url.toString();
    }
}

// Month Navigation
const apptPrevMonthBtn = document.getElementById("apptPrevMonth");
const apptNextMonthBtn = document.getElementById("apptNextMonth");

if (apptPrevMonthBtn) {
    apptPrevMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        apptCurrentMonth = new Date(apptCurrentMonth.getFullYear(), apptCurrentMonth.getMonth() - 1);
        apptRenderCalendar();
    };
}

if (apptNextMonthBtn) {
    apptNextMonthBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        apptCurrentMonth = new Date(apptCurrentMonth.getFullYear(), apptCurrentMonth.getMonth() + 1);
        apptRenderCalendar();
    };
}

// Reset Date Filter
if (apptResetDateFilter) {
    apptResetDateFilter.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent dropdown from closing
        apptSelectedDate = null;
        apptDateFilter.value = '';
        apptCurrentMonth = new Date();
        apptRenderCalendar();

        // Update URL without page reload using history API
        const url = new URL(window.location);
        url.searchParams.delete('date');
        window.history.replaceState(null, '', url.toString());
    };
}
