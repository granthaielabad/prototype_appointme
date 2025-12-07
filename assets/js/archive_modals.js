document.addEventListener("DOMContentLoaded", () => {

    const serviceModal = document.getElementById("archiveServiceViewModal");
    const appointmentModal = document.getElementById("archiveAppointmentViewModal");
    const inquiryModal = document.getElementById("archiveInquiryViewModal");
    const employeeModal = document.getElementById("archiveEmployeeViewModal");
    const closeBtns = document.querySelectorAll(".close-modal");

    /* -------------------------------------------
       HELPER: Format Full Date
    ------------------------------------------- */
    const formatFullDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        // Handle various date formats (ISO, timestamps, etc.)
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr; // fallback to original if parsing fails
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    };

    /* -------------------------------------------
       HELPER: Close All Modals
    ------------------------------------------- */
    const closeAllModals = () => {
        serviceModal.style.display = "none";
        appointmentModal.style.display = "none";
        inquiryModal.style.display = "none";
        employeeModal.style.display = "none";
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

            if (itemType === 'service') {
                // Defensive retrieval of service fields since archived JSON shape can vary.
                const getName = () => {
                    if (details && typeof details === 'object') {
                        return details.service_name || details.name || details.item_name || data.item_name || 'N/A';
                    }
                    return data.item_name || 'N/A';
                };

                const getDescription = () => {
                    if (details && typeof details === 'object') {
                        return details.description || details.desc || details.details || 'No description available';
                    }
                    return 'No description available';
                };

                const getPrice = () => {
                    let priceVal = null;
                    if (details && typeof details === 'object') {
                        if (details.price !== undefined && details.price !== null && details.price !== '') return details.price;
                        if (details.service && details.service.price !== undefined) return details.service.price;
                        if (details.amount !== undefined) return details.amount;
                        if (details.cost !== undefined) return details.cost;
                    }
                    if (data.price !== undefined) return data.price;
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
                const categoryVal = (details && details.category) || (details?.service?.category) || "Service";
                categoryBadge.textContent = categoryVal;

                // Format archived date as "Full Month Name, Day, Year"
                const archivedDateFormatted = formatFullDate(data.archived_at || details.archived_at);
                document.getElementById("archive_service_archived_date").textContent = archivedDateFormatted;

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

                // Format times
                const formatTime = (timeStr) => {
                    if (!timeStr) return '';
                    try {
                        return new Date(`1970-01-01T${timeStr}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    } catch {
                        return timeStr;
                    }
                };

                const archivedDateFormatted = formatFullDate(details.archived_at || new Date());
                
                // "Booked on: [Archived Date]" and "Booked Date: [Appointment Date Time]"
                document.getElementById("archive_appointment_booked_on").textContent = `Booked on: ${archivedDateFormatted}`;
                
                const appointmentDateTime = `Booked Date: ${formatFullDate(details.appointment_date || '')}${
                    details.appointment_time ? ' at ' + formatTime(details.appointment_time) : ''
                }`;
                document.getElementById("archive_appointment_date_time").textContent = appointmentDateTime;

                // Populate Services List as badges with categories
                const servicesContainer = document.getElementById("archive_appointment_services");
                servicesContainer.innerHTML = "";
                if (details.services && Array.isArray(details.services)) {
                    const servicesList = details.services.map(s => {
                        if (typeof s === 'string') {
                            // Legacy format - just service name
                            return `<span class="badge rounded-pill me-2" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                <i class="bi bi-scissors me-1"></i>${htmlEscape(s)}
                            </span>`;
                        } else if (s && typeof s === 'object' && s.name) {
                            // New format with name and category
                            const serviceBadge = `<span class="badge rounded-pill me-2" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                <i class="bi bi-scissors me-1"></i>${htmlEscape(s.name)}
                            </span>`;
                            const categoryBadge = `<span class="badge rounded-pill" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                <i class="bi bi-brush me-1"></i>${htmlEscape(s.category || 'Service')}
                            </span>`;
                            return serviceBadge + categoryBadge;
                        }
                        return '';
                    }).join("");
                    servicesContainer.innerHTML = servicesList;
                } else if (typeof details.services === 'string') {
                    servicesContainer.innerHTML = `<span class="badge rounded-pill me-2" style="background-color: #6c757d; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="bi bi-scissors me-1"></i>${htmlEscape(details.services)}
                    </span>`;
                } else {
                    servicesContainer.innerHTML = `<span style="color: #999; font-size: 0.9rem;">No services</span>`;
                }

                document.getElementById("archive_appointment_note").textContent = details.note || 'No notes provided';
                document.getElementById("archive_appointment_archived_date").textContent = archivedDateFormatted;

                closeAllModals();
                appointmentModal.style.display = "flex";

            } else if (itemType === 'inquiry') {
                // Populate Inquiry Modal
                document.getElementById("archive_inq_name").textContent = details.full_name || "Unknown";
                document.getElementById("archive_inq_phone").textContent = details.phone || "N/A";
                document.getElementById("archive_inq_email").textContent = details.email || "N/A";

                // Set status badge
                const statusElement = document.getElementById("archive_inq_status");
                const status = details.status || 'pending'; // Use stored status or default to pending
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

                // Populate message content
                document.getElementById("archive_inq_message").textContent = details.message || "No message provided";

                // Format inquiry date as "Full Month Name, Day, Year"
                const inquiryDateFormatted = formatFullDate(details.created_at || details.inquiry_date);
                document.getElementById("archive_inq_date").textContent = inquiryDateFormatted;

                const archivedDateFormatted = formatFullDate(details.archived_at);
                document.getElementById("archive_inq_archived_date").textContent = archivedDateFormatted;

                closeAllModals();
                inquiryModal.style.display = "flex";

            } else if (itemType === 'employee') {
                // Populate Employee Modal
                document.getElementById("archive_employee_name").textContent = details.full_name || "Unknown Employee";
                document.getElementById("archive_employee_email").textContent = details.email || "N/A";
                document.getElementById("archive_employee_contact").textContent = details.contact_number || "N/A";
                document.getElementById("archive_employee_position").textContent = details.position || "N/A";
                
                // Format hire date
                const hireDateFormatted = formatFullDate(details.hire_date);
                document.getElementById("archive_employee_hire_date").textContent = hireDateFormatted;
                
                document.getElementById("archive_employee_address").textContent = details.address || "N/A";

                // Status badge - always show as "Deactivated" for archived employees
                const statusBadge = document.getElementById("archive_employee_status_badge");
                statusBadge.textContent = "Status: Deactivated";
                statusBadge.className = "badge badge-inactive";
                statusBadge.style.backgroundColor = "#dc3545";
                statusBadge.style.color = "#fff";

                // Format archived date
                const archivedDateFormatted = formatFullDate(details.archived_at);
                document.getElementById("archive_employee_archived_date").textContent = archivedDateFormatted;

                closeAllModals();
                employeeModal.style.display = "flex";
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
    [serviceModal, appointmentModal, inquiryModal, employeeModal].forEach(modal => {
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
