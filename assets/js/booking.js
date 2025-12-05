document.addEventListener("DOMContentLoaded", () => {

    const serviceSelect = document.getElementById("serviceSelect");
    const apptDate      = document.getElementById("apptDate");
    const apptTime      = document.getElementById("apptTime");
    const bookForm      = document.getElementById("bookForm");
    const bookNowBtn    = document.getElementById("bookNowBtn");

    // ===========================
    // TIME SLOTS (You can update this later)
    // ===========================
    const TIME_SLOTS = [
        "09:00 AM", "10:00 AM", "11:00 AM",
        "01:00 PM", "02:00 PM", "03:00 PM",
        "04:00 PM", "05:00 PM"
    ];

    // ===========================
    // Populate times once date is selected
    // ===========================
    apptDate.addEventListener("change", () => {
        apptTime.innerHTML = `<option value="">Appointment Time</option>`;

        if (!apptDate.value) return;

        TIME_SLOTS.forEach(t => {
            const opt = document.createElement("option");
            opt.value = t;
            opt.textContent = t;
            apptTime.appendChild(opt);
        });
    });


    // ===========================
    // VALIDATION HANDLING
    // ===========================
    function validateForm() {
        if (!serviceSelect.value.trim()) {
            alert("Please select a service.");
            return false;
        }
        if (!apptDate.value.trim()) {
            alert("Please select a date.");
            return false;
        }
        if (!apptTime.value.trim()) {
            alert("Please select a time.");
            return false;
        }
        return true;
    }


    // CINOMMENT KO TONG PART. INSTEAD KASI NA "BOOKINGCONTROLLER.PHP" YUNG GAMITIN ITO YUNG GINAGAMIT NG BOOKING FORM. KAYA HINDI GUMAGANA YUNG LOGIC SA SAVING DATABASE.

    
    // ===========================
    // SUBMIT BOOKING
    // ===========================
   /* bookForm.addEventListener("submit", e => {
        e.preventDefault(); // hindi umaabot sa php because of this

        if (!validateForm()) return;

        const formData = new FormData(bookForm);

        fetch("/book", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(() => {
            window.location.href = "/payment-qr"; // pinalitan ko ng location.
        })
        .catch(() => {
            alert("Something went wrong while booking your appointment.");
        });
    });*/

});
