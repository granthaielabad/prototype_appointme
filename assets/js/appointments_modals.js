document.addEventListener("DOMContentLoaded", () => {

    const closeBtns = document.querySelectorAll(".close-modal");

    const editModal = document.getElementById("editAppointmentModal");

    // Open edit modal when clicking edit button
    document.querySelectorAll(".openEditModal").forEach(btn => {
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

            // appointment_time may include seconds, ensure HH:MM:SS format for option matching
            if (data.appointment_time) {
                // Ensure it's in HH:MM:SS format (09:00:00)
                let timeStr = data.appointment_time;
                if (timeStr.length === 5) {
                    // Convert HH:MM to HH:MM:00
                    timeStr = timeStr + ':00';
                }
                document.getElementById('edit_appointment_time').value = timeStr;
            } else {
                document.getElementById('edit_appointment_time').value = '';
            }

            if (data.status) {
                document.getElementById('edit_status').value = data.status;
            }

            // show modal
            editModal.style.display = 'flex';
        });
    });

    // Close modal buttons
    closeBtns.forEach(b => {
        b.addEventListener('click', () => {
            if (editModal) editModal.style.display = 'none';
        });
    });

    // Close when clicking outside modal content
    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) {
                editModal.style.display = 'none';
            }
        });
    }

});
