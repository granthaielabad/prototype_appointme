document.addEventListener("DOMContentLoaded", () => {

    const addModal = document.getElementById("addServiceModal");
    const editModal = document.getElementById("editServiceModal");
    const deleteModal = document.getElementById("deleteServiceModal");

    const closeButtons = document.querySelectorAll(".close-modal");

    // Open Add Modal
    document.getElementById("openAddModal").addEventListener("click", () => {
        addModal.style.display = "flex";
    });

    // Open Edit Modal
    document.querySelectorAll(".openEditModal").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const card = btn.closest(".service-card");
            const data = JSON.parse(card.dataset.service);

            document.getElementById("edit_id").value = data.service_id;
            document.getElementById("edit_name").value = data.service_name;
            document.getElementById("edit_price").value = data.price;
            document.getElementById("edit_category").value = data.category;
            document.getElementById("edit_description").value = data.description;

            editModal.style.display = "flex";
        });
    });

    // Open Delete Modal
    document.querySelectorAll(".openDeleteModal").forEach(btn => {
        btn.addEventListener("click", () => {
            const name = btn.dataset.name;
            const id = btn.dataset.id;

            document.getElementById("deleteServiceName").textContent = name;
            document.getElementById("confirmDeleteBtn").href =
                `/admin/services/delete?id=${id}`;

            deleteModal.style.display = "flex";
        });
    });

    // Close Modals
    closeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            addModal.style.display = "none";
            editModal.style.display = "none";
            deleteModal.style.display = "none";
        });
    });
});
