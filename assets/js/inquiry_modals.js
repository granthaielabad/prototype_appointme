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
            
            // Format date as "Full Month Name, Day, Year"
            const formatFullDate = (dateStr) => {
                if (!dateStr) return 'N/A';
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            };
            
            document.getElementById("inq_date").textContent = formatFullDate(data.created_at);
            document.getElementById("inq_message").textContent = data.message || "";

            modal.style.display = "flex";

            // Mark as read via AJAX
            if (data.inquiry_id && !parseInt(data.is_read)) {
                console.log('Marking inquiry as read:', data.inquiry_id, 'current is_read value:', data.is_read, 'type:', typeof data.is_read);
                
                fetch(window.location.origin + '/admin/inquiries/mark-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + encodeURIComponent(data.inquiry_id),
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Mark as read response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('Mark as read result:', result);
                    if (result.success) {
                        console.log('Inquiry marked as read successfully:', data.inquiry_id);
                        // Update row styling to remove unread appearance
                        row.classList.remove('table-light', 'fw-bold');
                        // Update is_read in data
                        data.is_read = 1;
                        row.setAttribute('data-inquiry', JSON.stringify(data));
                        // Remove NEW badge if present
                        const badge = row.querySelector('.badge');
                        if (badge) {
                            badge.remove();
                        }
                    } else {
                        console.error('Failed to mark as read:', result.error || result.message);
                    }
                })
                .catch(err => {
                    console.error('Error marking inquiry as read:', err);
                });
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
