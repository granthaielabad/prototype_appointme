document.addEventListener("DOMContentLoaded", () => {

    const serviceModal = document.getElementById("archiveServiceViewModal");
    const appointmentModal = document.getElementById("archiveAppointmentViewModal");
    const inquiryModal = document.getElementById("archiveInquiryViewModal");
    const closeBtns = document.querySelectorAll(".close-modal");

    /* -------------------------------------------
       HELPER: Close All Modals
    ------------------------------------------- */
    const closeAllModals = () => {
        serviceModal.style.display = "none";
        appointmentModal.style.display = "none";
        inquiryModal.style.display = "none";
    };

    /* -------------------------------------------
       OPEN ARCHIVE DETAILS MODAL
    ------------------------------------------- */
    document.querySelectorAll(".openArchiveDetailsModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".archive-item-card");
            if (!card) return;

            const data = JSON.parse(card.dataset.archive);
            const details = typeof data.details === 'string' ? JSON.parse(data.details) : data.details;
            const itemType = data.item_type.toLowerCase();
            const archivedDate = new Date(data.archived_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            if (itemType === 'service') {
                // Populate Service Modal
                const getName = () => {
                    return details.service_name || details.name || data.item_name || 'N/A';
                };

                const getDescription = () => {
                    return details.description || details.desc || 'No description available';
                };

                const getPrice = () => {
                    // Try multiple locations for price
                    if (details.price !== undefined && details.price !== null && details.price !== '') return details.price;
                    if (details.amount !== undefined && details.amount !== null) return details.amount;
                    if (details.cost !== undefined && details.cost !== null) return details.cost;
                    if (data.price !== undefined && data.price !== null) return data.price;
                    return 0;
                };

                const nameVal = getName();
                const descVal = getDescription();
                const priceRaw = getPrice();
                const priceNum = Number(String(priceRaw).replace(/[^0-9.-]+/g, '')) || 0;

                document.getElementById("archive_service_name").textContent = nameVal;
                document.getElementById("archive_service_description").textContent = descVal;
                document.getElementById("archive_service_price").textContent = `PHP ${priceNum.toLocaleString()}`;
                
                // Category badge
                const categoryBadge = document.getElementById("archive_service_category_badge");
                categoryBadge.textContent = details.category || "Service";

                document.getElementById("archive_service_archived_date").textContent = archivedDate;

                closeAllModals();
                serviceModal.style.display = "flex";

            } else if (itemType === 'appointment') {
                // Populate Appointment Modal
                document.getElementById("archive_appointment_id").textContent = `#${details.appointment_id || 'N/A'}`;
                
                // Status badge with color coding
                const statusBadge = document.getElementById("archive_appointment_status_badge");
                const status = (details.status || 'pending').toLowerCase();
                const statusColors = {
                    'pending': '#FFC107',
                    'confirmed': '#0d6efd',
                    'completed': '#28a745',
                    'cancelled': '#dc3545'
                };
                statusBadge.textContent = `Status: ${status.charAt(0).toUpperCase() + status.slice(1)}`;
                statusBadge.style.backgroundColor = statusColors[status] || '#6c757d';

                // Format dates to "Full Month Name, Day, Year" format
                const formatFullDate = (dateStr) => {
                    const date = new Date(dateStr + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                };

                // Format times
                const formatTime = (timeStr) => {
                    if (!timeStr) return '';
                    try {
                        return new Date(`1970-01-01T${timeStr}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    } catch {
                        return timeStr;
                    }
                };

                const archivedDate = new Date(details.archived_at || new Date()).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                
                // "Booked on: [Archived Date]" and "Booked Date: [Appointment Date Time]"
                document.getElementById("archive_appointment_booked_on").textContent = `Booked on: ${archivedDate}`;
                
                const appointmentDateTime = `Booked Date: ${formatFullDate(details.appointment_date || '')}${
                    details.appointment_time ? ' at ' + formatTime(details.appointment_time) : ''
                }`;
                document.getElementById("archive_appointment_date_time").textContent = appointmentDateTime;

                // Populate Services List as badges
                const servicesContainer = document.getElementById("archive_appointment_services");
                servicesContainer.innerHTML = "";
                if (details.services && Array.isArray(details.services)) {
                    const servicesList = details.services.map(s => {
                        const serviceName = typeof s === 'string' ? s : (s.service_name || s);
                        return `<span class="badge rounded-pill" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                            <i class="bi bi-scissors me-1"></i>${htmlEscape(serviceName)}
                        </span>`;
                    }).join("");
                    servicesContainer.innerHTML = servicesList;
                } else if (typeof details.services === 'string') {
                    servicesContainer.innerHTML = `<span class="badge rounded-pill" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="bi bi-scissors me-1"></i>${htmlEscape(details.services)}
                    </span>`;
                } else {
                    servicesContainer.innerHTML = `<span style="color: #999; font-size: 0.9rem;">No services</span>`;
                }

                document.getElementById("archive_appointment_note").textContent = details.note || 'No notes provided';
                document.getElementById("archive_appointment_archived_date").textContent = archivedDate;

                closeAllModals();
                appointmentModal.style.display = "flex";

            } else if (itemType === 'inquiry') {
                // Populate Inquiry Modal
                document.getElementById("archive_inq_name").textContent = details.full_name || "Unknown";
                document.getElementById("archive_inq_phone").textContent = details.phone || "N/A";
                document.getElementById("archive_inq_email").textContent = details.email || "N/A";
                document.getElementById("archive_inq_date").textContent = new Date(details.created_at || details.inquiry_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById("archive_inq_message").textContent = details.message || "";
                document.getElementById("archive_inq_archived_date").textContent = archivedDate;

                closeAllModals();
                inquiryModal.style.display = "flex";
            }
        });
    });

    /* -------------------------------------------
       CLOSE BUTTONS
    ------------------------------------------- */
    closeBtns.forEach(btn => {
        btn.addEventListener("click", closeAllModals);
    });

    /* -------------------------------------------
       CLICK OUTSIDE TO CLOSE
    ------------------------------------------- */
    [serviceModal, appointmentModal, inquiryModal].forEach(modal => {
        modal.addEventListener("click", (e) => {
            if (e.target === modal) closeAllModals();
        });
    });

    /* -------------------------------------------
       ESC KEY TO CLOSE MODAL
    ------------------------------------------- */
    document.addEventListener("keyup", (e) => {
        if (e.key === "Escape") closeAllModals();
    });

    /* -------------------------------------------
       HELPER: Escape HTML Special Characters
    ------------------------------------------- */
    function htmlEscape(str) {
        if (!str) return "";
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

});
