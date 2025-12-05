document.addEventListener("DOMContentLoaded", () => {

    const closeBtns = document.querySelectorAll(".close-modal");

    const editModal = document.getElementById("editAppointmentModal");
    // Function to attach edit listener to a button (reusable for dynamically added rows)
    function formatTimeLabel(hms) {
        try {
            const d = new Date('1970-01-01T' + hms);
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return hms;
        }
    }

    function attachEditListener(btn) {
        if (!btn) return;
        btn.addEventListener("click", () => {
            const row = btn.closest("tr");
            if (!row) return;
            const dataRaw = row.getAttribute('data-appointment');
            if (!dataRaw) return;
            let data;
            try {
                data = JSON.parse(dataRaw);
            } catch (e) {
                console.error('Invalid appointment data', e);
                return;
            }

            // Fill modal fields
            document.getElementById('edit_id').value = data.appointment_id || '';

            // appointment_date should be in YYYY-MM-DD
            if (data.appointment_date) {
                document.getElementById('edit_appointment_date').value = data.appointment_date;
            } else {
                document.getElementById('edit_appointment_date').value = '';
            }

            // appointment_time may include seconds or be missing leading zero.
            // Normalize to HH:MM:SS so it matches the <select> option values.
            if (data.appointment_time) {
                let timeStr = String(data.appointment_time).trim();
                const m = timeStr.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
                if (m) {
                    let hh = m[1].padStart(2, '0');
                    const mm = m[2];
                    const ss = m[3] || '00';
                    timeStr = `${hh}:${mm}:${ss}`;
                } else {
                    if (timeStr.length === 5) timeStr = timeStr + ':00';
                }

                const select = document.getElementById('edit_appointment_time');
                if (select) {
                    // remove any previous temporary options
                    Array.from(select.querySelectorAll('option[data-temp]')).forEach(o => o.remove());

                    const found = Array.from(select.options).some(opt => opt.value === timeStr);
                    if (found) {
                        select.value = timeStr;
                    } else {
                        // Insert a temporary option so the current appointment time is visible and selectable
                        const opt = document.createElement('option');
                        opt.value = timeStr;
                        opt.text = formatTimeLabel(timeStr) + ' (current)';
                        opt.setAttribute('data-temp', '1');
                        // prepend so it's visible at top
                        select.prepend(opt);
                        select.value = timeStr;
                    }
                }
            } else {
                const select = document.getElementById('edit_appointment_time');
                if (select) select.value = '';
            }

            if (data.status) {
                document.getElementById('edit_status').value = data.status;
            }

            // show modal
            editModal.style.display = 'flex';
        });
    }

    // Expose attachment function globally so realtime script can reuse it
    window.attachAppointmentEditListener = attachEditListener;

    // Attach to initially present buttons
    document.querySelectorAll(".openEditModal").forEach(btn => attachEditListener(btn));

    // Close modal buttons
    closeBtns.forEach(b => {
        b.addEventListener('click', () => {
            if (editModal) editModal.style.display = 'none';
            // cleanup any temporary time option
            const select = document.getElementById('edit_appointment_time');
            if (select) Array.from(select.querySelectorAll('option[data-temp]')).forEach(o => o.remove());
        });
    });

    // Close when clicking outside modal content
    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) {
                editModal.style.display = 'none';
                const select = document.getElementById('edit_appointment_time');
                if (select) Array.from(select.querySelectorAll('option[data-temp]')).forEach(o => o.remove());
            }
        });
    }

});
