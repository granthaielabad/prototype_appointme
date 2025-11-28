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
        });
    });

    // Close modal
    closeBtns.forEach(b => {
        b.addEventListener("click", () => {
            modal.style.display = "none";
        });
    });

});
