console.log('Inquiry modals script loaded');

document.addEventListener("DOMContentLoaded", () => {
    console.log('DOMContentLoaded fired for inquiry modals');

    const modal = document.getElementById("inquiryDetailsModal");
    const closeBtns = document.querySelectorAll(".close-modal");
    let currentInquiryData = null; // Store current inquiry data for reply button

    // Open modal
    document.querySelectorAll(".openInquiryModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const row = btn.closest("tr");
            const data = JSON.parse(row.dataset.inquiry);
            currentInquiryData = data; // Store for reply button
            console.log('Modal opened with inquiry data:', data);
            console.log('Inquiry ID:', data.inquiry_id, 'Email:', data.email);

            document.getElementById("inq_name").textContent = data.full_name || "Unknown";
            document.getElementById("inq_phone").textContent = data.phone || "N/A";
            document.getElementById("inq_email").textContent = data.email || "N/A";

            // Set status badge
            const statusElement = document.getElementById("inq_status");
            const status = data.status || 'pending';
            let statusBadge = '';

            switch (status) {
                case 'pending':
                    statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                    break;
                case 'read':
                    statusBadge = '<span class="badge bg-info text-white">Read</span>';
                    break;
                case 'replied':
                    statusBadge = '<span class="badge bg-success">Replied</span>';
                    break;
                case 'deleted':
                    statusBadge = '<span class="badge bg-danger">Deleted</span>';
                    break;
                default:
                    statusBadge = '<span class="badge bg-secondary">Unknown</span>';
            }
            statusElement.innerHTML = statusBadge;
            
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
                        // Update data object
                        data.is_read = 1;
                        data.status = 'read';
                        row.setAttribute('data-inquiry', JSON.stringify(data));

                        // Remove NEW badge if present (look for badge in name cell that contains "NEW")
                        const nameCell = row.querySelector('td:nth-child(2)'); // Name column
                        const badges = nameCell ? nameCell.querySelectorAll('.badge') : [];
                        badges.forEach(badge => {
                            if (badge.textContent.includes('NEW')) {
                                badge.remove();
                            }
                        });

                        // Update status badge to show "Read"
                        const statusCell = row.querySelector('td:nth-child(4)'); // Status column
                        if (statusCell) {
                            statusCell.innerHTML = '<span class="badge bg-info text-white">Read</span>';
                        }

                        // Update modal status if it's currently open
                        const modalStatusElement = document.getElementById("inq_status");
                        if (modalStatusElement) {
                            modalStatusElement.innerHTML = '<span class="badge bg-info text-white">Read</span>';
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
            currentInquiryData = null; // Clear current inquiry data
            console.log('Modal closed, cleared inquiry data');
        });
    });

    // Reply button functionality - using event delegation
    document.addEventListener("click", (e) => {
        if (e.target && e.target.id === "replyInquiryBtn") {
            console.log('Reply button clicked via delegation');
            console.log('Current inquiry data:', currentInquiryData);

            if (!currentInquiryData) {
                console.error('No inquiry data available for reply');
                alert('No inquiry data available. Please try opening the inquiry modal again.');
                return;
            }

            const inquiryId = currentInquiryData.inquiry_id;

            const email = document.getElementById("inq_email").textContent;
            const name = document.getElementById("inq_name").textContent;
            const subject = encodeURIComponent("Re: Your Inquiry - Simple Salon");

            console.log('Email:', email, 'Name:', name, 'Inquiry ID:', inquiryId);

            // Salon-themed email template
            const body = encodeURIComponent(`Dear ${name},

Thank you for reaching out to AppointMe Salon!

We appreciate your interest in our services. We're here to help make your beauty experience exceptional.

[Your personalized response here]

If you have any questions or need to reschedule, please don't hesitate to contact us.

Best regards,
AppointMe Salon
📞 Phone: 0912 345 6789
📧 Email: info@appointme.x10.network
🌐 Website: www.appointme.x10.network

---
This email is in response to your inquiry sent on ${document.getElementById("inq_date").textContent}

✨ AppontMe - Your Beauty Destination ✨`);

            console.log('Opening Gmail with URL:', `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}&su=${subject}&body=${body.substring(0, 50)}...`);

            // Open Gmail compose with pre-filled details
            const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}&su=${subject}&body=${body}`;
            window.open(gmailUrl, '_blank');

            // Archive the inquiry after opening Gmail
            setTimeout(() => {
                console.log('Archiving inquiry:', inquiryId);
                window.location.href = `/admin/inquiries/delete?id=${inquiryId}`;
            }, 1000); // Small delay to ensure Gmail opens first
        }
    });

});
