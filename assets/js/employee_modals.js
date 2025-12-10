document.addEventListener("DOMContentLoaded", () => {

    const addModal = document.getElementById("addEmployeeModal");
    const editModal = document.getElementById("editEmployeeModal");
    const deleteModal = document.getElementById("deleteEmployeeModal");
    const archiveModal = document.getElementById("archiveEmployeeModal");
    const activateModal = document.getElementById("activateEmployeeModal");

    const closeButtons = document.querySelectorAll(".close-modal");

    /* -------------------------------------------
       HELPER FUNCTIONS
    ------------------------------------------- */

    const closeAllModals = () => {
        addModal.style.display = "none";
        editModal.style.display = "none";
        deleteModal.style.display = "none";
        archiveModal.style.display = "none";
        activateModal.style.display = "none";
    };

    const openModal = (modal) => {
        closeAllModals();
        modal.style.display = "flex";
    };

    /* -------------------------------------------
       ADD EMPLOYEE MODAL
    ------------------------------------------- */
    const openAdd = document.getElementById("openAddModal");

    if (openAdd) {
        openAdd.addEventListener("click", () => {
            // Reset form fields before showing modal
            addModal.querySelectorAll("input, textarea, select").forEach(field => {
                if (field.type === "checkbox") {
                    field.checked = true; // Default to active
                } else {
                    field.value = "";
                }
            });
            openModal(addModal);
        });
    }

    /* -------------------------------------------
       EDIT EMPLOYEE MODAL
    ------------------------------------------- */
    document.querySelectorAll(".openEditModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".employee-card");
            if (!card) return;

            const data = JSON.parse(card.dataset.employee);

            // Fill fields
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_full_name").value = data.full_name || "";
            document.getElementById("edit_phone_number").value = data.contact_number || "";
            document.getElementById("edit_position").value = data.position || "";
            document.getElementById("edit_address").value = data.address || "";
            document.getElementById("edit_work_schedule").value = data.work_schedule || "";
            document.getElementById("edit_remarks").value = data.remarks || "";
            document.getElementById("edit_email").value = data.email;

            openModal(editModal);
        });
    });

    /* -------------------------------------------
       TOGGLE EMPLOYEE STATUS
    ------------------------------------------- */
    document.querySelectorAll(".toggle-status").forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent dropdown from staying open

            const employeeId = link.dataset.id;
            const currentStatus = link.dataset.status;

            // Create form to submit status change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/employees/toggle-status';

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = employeeId;

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = currentStatus;

            form.appendChild(idInput);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        });
    });

    /* -------------------------------------------
       ARCHIVE EMPLOYEE
    ------------------------------------------- */
    document.querySelectorAll(".archive-employee").forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent dropdown from staying open

            const employeeId = link.dataset.id;

            // Show archive confirmation modal
            document.getElementById("confirmArchiveBtn").setAttribute(
                "href",
                `/admin/employees/archive?id=${employeeId}`
            );

            openModal(archiveModal);
        });
    });

    /* -------------------------------------------
       DELETE EMPLOYEE MODAL (DEACTIVATE)
    ------------------------------------------- */
    document.querySelectorAll(".openDeleteModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            document.getElementById("confirmDeleteBtn").setAttribute(
                "href",
                `/admin/employees/delete?id=${id}`
            );

            openModal(deleteModal);
        });
    });

    /* -------------------------------------------
       ACTIVATE EMPLOYEE MODAL
    ------------------------------------------- */
    document.querySelectorAll(".openActivateModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            document.getElementById("confirmActivateBtn").setAttribute(
                "href",
                `/admin/employees/activate?id=${id}`
            );

            openModal(activateModal);
        });
    });

    /* -------------------------------------------
       CLOSE BUTTONS
    ------------------------------------------- */
    closeButtons.forEach(btn => {
        btn.addEventListener("click", closeAllModals);
    });

    /* -------------------------------------------
       CLICK OUTSIDE TO CLOSE
    ------------------------------------------- */
    [addModal, editModal, deleteModal, archiveModal, activateModal].forEach(modal => {
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

});

// Simple dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown toggles (including filter dropdown)
    const toggles = document.querySelectorAll('.dropdown-toggle');

    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const dropdown = this.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (!menu) return;

            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(function(otherMenu) {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('show');
                }
            });

            // Toggle current dropdown
            menu.classList.toggle('show');
            
            // Update aria-expanded attribute
            this.setAttribute('aria-expanded', menu.classList.contains('show') ? 'true' : 'false');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
            });
            // Reset aria-expanded
            document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(function(toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
