/**
 * Restore Warning Modal Handler
 * Handles the restore confirmation modal for archived items
 */

document.addEventListener("DOMContentLoaded", () => {
    const restoreWarningModal = document.getElementById("restoreWarningModal");
    
    if (restoreWarningModal) {
        const confirmBtn = document.getElementById("confirmRestoreBtn");
        const cancelBtn = document.getElementById("cancelRestoreBtn");
        let currentRestoreData = null;

        // Open modal on restore button click
        document.querySelectorAll(".openRestoreWarningModal").forEach(btn => {
            btn.addEventListener("click", () => {
                currentRestoreData = {
                    id: btn.dataset.id,
                    type: btn.dataset.type,
                    name: btn.dataset.name,
                    filter: btn.dataset.filter || 'all'
                };
                
                const displayName = currentRestoreData.name || "this item";
                document.getElementById("restoreItemName").textContent = displayName;
                restoreWarningModal.style.display = "flex";
            });
        });

        // Confirm restore
        if (confirmBtn) {
            confirmBtn.addEventListener("click", () => {
                if (currentRestoreData) {
                    window.location.href = `/admin/archives/restore?type=${encodeURIComponent(currentRestoreData.type)}&id=${encodeURIComponent(currentRestoreData.id)}&filter=${encodeURIComponent(currentRestoreData.filter)}`;
                }
            });
        }

        // Cancel restore
        if (cancelBtn) {
            cancelBtn.addEventListener("click", () => {
                restoreWarningModal.style.display = "none";
                currentRestoreData = null;
            });
        }

        // Close on background click
        restoreWarningModal.addEventListener("click", (e) => {
            if (e.target === restoreWarningModal) {
                restoreWarningModal.style.display = "none";
                currentRestoreData = null;
            }
        });

        // Close on ESC
        document.addEventListener("keyup", (e) => {
            if (e.key === "Escape" && restoreWarningModal.style.display === "flex") {
                restoreWarningModal.style.display = "none";
                currentRestoreData = null;
            }
        });
    }
});
