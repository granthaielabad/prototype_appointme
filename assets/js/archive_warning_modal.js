/**
 * Archive Warning Modal Handler
 * Handles the warning modal for archiving appointments, services, and inquiries
 */

document.addEventListener("DOMContentLoaded", () => {
    // ===== APPOINTMENTS ARCHIVE WARNING MODAL =====
    const appointmentWarningModal = document.getElementById("archiveWarningModal");
    
    if (appointmentWarningModal) {
        const confirmBtn = document.getElementById("confirmArchiveBtn");
        const cancelBtn = document.getElementById("cancelArchiveBtn");
        let currentArchiveId = null;

        // Open modal on delete button click
        document.querySelectorAll(".openArchiveWarningModal").forEach(btn => {
            btn.addEventListener("click", () => {
                currentArchiveId = btn.dataset.id;
                const itemName = btn.dataset.name || "this item";
                document.getElementById("warningItemName").textContent = itemName;
                appointmentWarningModal.style.display = "flex";
            });
        });

        // Confirm archive
        if (confirmBtn) {
            confirmBtn.addEventListener("click", () => {
                if (currentArchiveId) {
                    window.location.href = `/admin/appointments/delete?id=${currentArchiveId}`;
                }
            });
        }

        // Cancel archive
        if (cancelBtn) {
            cancelBtn.addEventListener("click", () => {
                appointmentWarningModal.style.display = "none";
                currentArchiveId = null;
            });
        }

        // Close on background click
        appointmentWarningModal.addEventListener("click", (e) => {
            if (e.target === appointmentWarningModal) {
                appointmentWarningModal.style.display = "none";
                currentArchiveId = null;
            }
        });

        // Close on ESC
        document.addEventListener("keyup", (e) => {
            if (e.key === "Escape" && appointmentWarningModal.style.display === "flex") {
                appointmentWarningModal.style.display = "none";
                currentArchiveId = null;
            }
        });
    }

    // ===== INQUIRIES ARCHIVE WARNING MODAL =====
    const inquiryWarningModal = document.getElementById("inquiryArchiveWarningModal");
    
    if (inquiryWarningModal) {
        const confirmBtn = document.getElementById("confirmInquiryArchiveBtn");
        const cancelBtn = document.getElementById("cancelInquiryArchiveBtn");
        let currentArchiveId = null;

        // Open modal on delete button click
        document.querySelectorAll(".openInquiryArchiveWarningModal").forEach(btn => {
            btn.addEventListener("click", () => {
                currentArchiveId = btn.dataset.id;
                inquiryWarningModal.style.display = "flex";
            });
        });

        // Confirm archive
        if (confirmBtn) {
            confirmBtn.addEventListener("click", () => {
                if (currentArchiveId) {
                    window.location.href = `/admin/inquiries/delete?id=${currentArchiveId}&action=delete`;
                }
            });
        }

        // Cancel archive
        if (cancelBtn) {
            cancelBtn.addEventListener("click", () => {
                inquiryWarningModal.style.display = "none";
                currentArchiveId = null;
            });
        }

        // Close on background click
        inquiryWarningModal.addEventListener("click", (e) => {
            if (e.target === inquiryWarningModal) {
                inquiryWarningModal.style.display = "none";
                currentArchiveId = null;
            }
        });

        // Close on ESC
        document.addEventListener("keyup", (e) => {
            if (e.key === "Escape" && inquiryWarningModal.style.display === "flex") {
                inquiryWarningModal.style.display = "none";
                currentArchiveId = null;
            }
        });
    }
});
