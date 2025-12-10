/**
 * Appointment real-time updates
 * Polls `/admin/appointments/fetch` every 5 seconds and updates the appointments table
 */

let appointmentPollInterval;

function initAppointmentRealtime() {
    // start polling
    appointmentPollInterval = setInterval(fetchAndUpdateAppointments, 5000);
}

function fetchAndUpdateAppointments() {
    const currentFilter = new URLSearchParams(window.location.search).get('filter') || 'all';
    const date = new URLSearchParams(window.location.search).get('date') || '';

    // Use absolute path with query parameters
    let apiUrl = window.location.origin + '/admin/appointments/fetch?filter=' + encodeURIComponent(currentFilter);
    if (date) apiUrl += '&date=' + encodeURIComponent(date);

    fetch(apiUrl)
        .then(res => {
            // Check if response is JSON
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.warn('Expected JSON but got:', contentType);
            }
            return res.json();
        })
        .then(payload => {
            if (!payload.success || !payload.appointments) return;

            const appointments = payload.appointments;
            const tbody = document.querySelector('table.table.align-middle tbody');
            if (!tbody) return;

            const existingRows = Array.from(tbody.querySelectorAll('tr[data-appointment]'));
            const existingIds = existingRows.map(r => {
                try { return JSON.parse(r.dataset.appointment).appointment_id; } catch { return null; }
            }).filter(id => id !== null);

            const serverIds = appointments.map(a => a.appointment_id);

            // Add new appointments (server has, DOM doesn't)
            appointments.forEach(app => {
                if (!existingIds.includes(app.appointment_id)) {
                    addAppointmentRow(app, tbody);
                } else {
                    // Update row if any key fields changed (status/date/time)
                    const row = existingRows.find(r => {
                        try { return JSON.parse(r.dataset.appointment).appointment_id === app.appointment_id; } catch { return false; }
                    });
                    if (row) {
                        try {
                            const rowData = JSON.parse(row.dataset.appointment);
                            if (rowData.status !== app.status || rowData.appointment_date !== app.appointment_date || rowData.appointment_time !== app.appointment_time) {
                                updateAppointmentRow(app, row);
                            }
                        } catch (e) { console.error(e); }
                    }
                }
            });

            // Remove DOM rows not present on server (deleted or filtered out)
            existingRows.forEach(row => {
                try {
                    const id = JSON.parse(row.dataset.appointment).appointment_id;
                    if (!serverIds.includes(id)) row.remove();
                } catch (e) { /* ignore parse errors */ }
            });

            // Update empty state
            updateAppointmentEmptyState(tbody);
        })
        .catch(err => console.error('Error fetching appointments:', err));
}

function addAppointmentRow(app, tbody) {
    // Prevent duplicates
    const exists = Array.from(tbody.querySelectorAll('tr[data-appointment]')).some(r => {
        try { return JSON.parse(r.dataset.appointment).appointment_id === app.appointment_id; } catch { return false; }
    });
    if (exists) return;

    const row = document.createElement('tr');
    row.setAttribute('data-appointment', JSON.stringify(app));

    const timeDisplay = app.appointment_time ? new Date('1970-01-01T' + app.appointment_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

    row.innerHTML = `
        <td>${escapeHtml(String(app.appointment_id))}</td>
        <td>${escapeHtml(app.full_name || '')}</td>
        <td>${escapeHtml(app.phone || 'N/A')}</td>
        <td>${escapeHtml(app.appointment_date || '')}</td>
        <td>${escapeHtml(timeDisplay)}</td>
        <td>${escapeHtml(app.status || '')}</td>
        <td>
            <button type="button" class="text-purple me-2 btn btn-link p-0 openEditModal" aria-label="Edit">
                <i class="bi bi-pencil"></i>
            </button>
            <a href="/admin/appointments/delete?id=${app.appointment_id}" onclick="return confirm('Delete appointment?')" class="text-purple">
                <i class="bi bi-trash"></i>
            </a>
        </td>
    `;

    // Insert at top
    if (tbody.firstElementChild && !tbody.firstElementChild.classList.contains('text-center')) {
        tbody.insertBefore(row, tbody.firstElementChild);
    } else {
        tbody.appendChild(row);
    }

    // Reattach edit modal listener using the shared helper from appointments_modals.js
    const btn = row.querySelector('.openEditModal');
    if (btn) {
        if (window.attachAppointmentEditListener && typeof window.attachAppointmentEditListener === 'function') {
            window.attachAppointmentEditListener(btn);
        } else {
            // Fallback: add a basic handler that opens the modal by triggering the same click behavior
            btn.addEventListener('click', () => {
                // Attempt to reuse existing DOM handler by dispatching a click event (if any)
                btn.dispatchEvent(new Event('click'));
            });
        }
    }
}

function updateAppointmentRow(app, row) {
    if (!row) return;
    row.setAttribute('data-appointment', JSON.stringify(app));

    const timeDisplay = app.appointment_time ? new Date('1970-01-01T' + app.appointment_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

    const cells = row.querySelectorAll('td');
    if (cells.length >= 6) {
        cells[0].textContent = app.appointment_id;
        cells[1].textContent = app.full_name || '';
        cells[2].textContent = app.phone || 'N/A';
        cells[3].textContent = app.appointment_date || '';
        cells[4].textContent = timeDisplay;
        cells[5].textContent = app.status || '';
    }
}

function updateAppointmentEmptyState(tbody) {
    const rows = tbody.querySelectorAll('tr[data-appointment]');
    const emptyRow = tbody.querySelector('tr[data-empty-state]');
    if (rows.length === 0) {
        if (!emptyRow) {
            const el = document.createElement('tr');
            el.setAttribute('data-empty-state', 'true');
            el.innerHTML = '<td colspan="7" class="text-center text-muted py-4">No appointments available.</td>';
            tbody.appendChild(el);
        }
    } else {
        if (emptyRow) emptyRow.remove();
    }
}

function escapeHtml(text) {
    if (typeof text !== 'string') return text;
    return text.replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"})[m]);
}

window.addEventListener('beforeunload', () => {
    if (appointmentPollInterval) clearInterval(appointmentPollInterval);
});

document.addEventListener('DOMContentLoaded', initAppointmentRealtime);
