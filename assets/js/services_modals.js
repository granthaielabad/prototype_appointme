document.addEventListener("DOMContentLoaded", () => {

    const addModal = document.getElementById("addServiceModal");
    const editModal = document.getElementById("editServiceModal");
    const deleteModal = document.getElementById("deleteServiceModal");

    const closeButtons = document.querySelectorAll(".close-modal");
    
    const validCategories = [
        "Straightening (By Length Level)",
        "Color Classic",
        "Hair Treatment",
        "Haircut",
        "Perming",
        "Other Services"
    ];

    /* -------------------------------------------
       HELPER FUNCTIONS
    ------------------------------------------- */

    const closeAllModals = () => {
        addModal.style.display = "none";
        editModal.style.display = "none";
        deleteModal.style.display = "none";
    };

    const openModal = (modal) => {
        closeAllModals();
        modal.style.display = "flex";
    };

    /* -------------------------------------------
       ADD SERVICE MODAL
    ------------------------------------------- */
    const openAdd = document.getElementById("openAddModal");

    if (openAdd) {
        openAdd.addEventListener("click", () => {
            // Reset form fields before showing modal
            addModal.querySelectorAll("input, textarea, select").forEach(field => field.value = "");
            openModal(addModal);
        });
    }

    /* -------------------------------------------
       EDIT SERVICE MODAL
    ------------------------------------------- */
    document.querySelectorAll(".openEditModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".service-card");
            if (!card) return;

            const data = JSON.parse(card.dataset.service);

            // Fill fields
            document.getElementById("edit_id").value = data.service_id;
            document.getElementById("edit_name").value = data.service_name;
            document.getElementById("edit_price").value = data.price;
            document.getElementById("edit_description").value = data.description ?? "";

            // Validate category (fallback to "Other Services")
            const categorySelect = document.getElementById("edit_category");
            categorySelect.value = validCategories.includes(data.category)
                ? data.category
                : "Other Services";

            openModal(editModal);
        });
    });

    /* -------------------------------------------
       DELETE SERVICE MODAL
    ------------------------------------------- */
    document.querySelectorAll(".openDeleteModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const name = btn.dataset.name;
            const id = btn.dataset.id;

            document.getElementById("deleteServiceName").textContent = name;
            document.getElementById("confirmDeleteBtn").setAttribute(
                "href",
                `/admin/services/delete?id=${id}`
            );

            openModal(deleteModal);
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
    [addModal, editModal, deleteModal].forEach(modal => {
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
