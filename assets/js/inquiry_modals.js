document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("inquiryDetailsModal");
    const closeBtns = document.querySelectorAll(".close-modal");

    // Open modal
    document.querySelectorAll(".openInquiryModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const row = btn.closest("tr");
            const data = JSON.parse(row.dataset.inquiry);

            document.getElementById("inq_name").textContent = data.full_name || "Unknown";
            document.getElementById("inq_phone").textContent = data.phone || "N/A";
            document.getElementById("inq_email").textContent = data.email || "N/A";
            document.getElementById("inq_date").textContent = new Date(data.created_at).toDateString();
            document.getElementById("inq_message").textContent = data.message || "";

            modal.style.display = "flex";

            // Mark as read via AJAX
            if (data.inquiry_id && !data.is_read) {
                const formData = new FormData();
                formData.append('id', data.inquiry_id);

                fetch('/admin/inquiries/mark-as-read', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Update row styling to remove unread appearance
                        row.classList.remove('table-light', 'fw-bold');
                        // Update is_read in data
                        data.is_read = 1;
                        // Remove NEW badge if present
                        const badge = row.querySelector('.badge');
                        if (badge) {
                            badge.remove();
                        }
                    }
                })
                .catch(err => console.error('Error marking as read:', err));
            }
        });
    });

    // Close modal
    closeBtns.forEach(b => {
        b.addEventListener("click", () => {
            modal.style.display = "none";
        });
    });

});
