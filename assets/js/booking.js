document.addEventListener("DOMContentLoaded", () => {
    const serviceSelect = document.getElementById("serviceSelect");
    const apptDate      = document.getElementById("apptDate");
    const apptTime      = document.getElementById("apptTime");
    const bookForm      = document.getElementById("bookForm");
    const bookNowBtn    = document.getElementById("bookNowBtn");
    const MINUTES_AHEAD = 30; // lead time required

    const todayISO = new Date().toISOString().split("T")[0];
    if (apptDate) apptDate.min = todayISO; // block past days

    const TIME_SLOTS = [
        "09:00 AM", "10:00 AM", "11:00 AM",
        "01:00 PM", "02:00 PM", "03:00 PM",
        "04:00 PM", "05:00 PM"
    ];

    const slotToDate = (dateStr, slotStr) => {
        if (!dateStr || !slotStr) return null;
        const [time, meridiem] = slotStr.split(" ");
        if (!time || !meridiem) return null;
        let [hours, minutes] = time.split(":").map(Number);
        if (meridiem.toUpperCase() === "PM" && hours !== 12) hours += 12;
        if (meridiem.toUpperCase() === "AM" && hours === 12) hours = 0;
        const hh = String(hours).padStart(2, "0");
        const mm = String(minutes || 0).padStart(2, "0");
        return new Date(`${dateStr}T${hh}:${mm}:00`);
    };

    const getCutoff = () => new Date(Date.now() + MINUTES_AHEAD * 60000);

    const renderSlots = (selectedDate, taken = []) => {
        apptTime.innerHTML = `<option value="">Appointment Time</option>`;
        const cutoff = getCutoff();
        TIME_SLOTS.forEach(t => {
            const opt = document.createElement("option");
            opt.value = t;
            const isTaken = taken.includes(t);
            const isPast = selectedDate ? (slotToDate(selectedDate, t) < cutoff) : false;
            const disabled = isTaken || isPast;
            opt.textContent = isTaken ? `${t} Booked` : (isPast ? `${t} Unavailable` : t);
            if (disabled) opt.disabled = true;
            apptTime.appendChild(opt);
        });
    };

    const fetchTakenSlots = async (date) => {
        if (!date) {
            renderSlots(null);
            return;
        }
        try {
            const res = await fetch(`/api/appointments/taken?date=${encodeURIComponent(date)}`);
            const data = await res.json();
            renderSlots(date, data.slots || []);
        } catch (err) {
            renderSlots(date); // fallback
        }
    };

    apptDate.addEventListener("change", () => {
        if (apptDate.value && apptDate.value < todayISO) {
            apptDate.value = todayISO; // correct manual back-dating
        }
        fetchTakenSlots(apptDate.value);
    });

    renderSlots(apptDate.value || null);

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

        const slotDate = slotToDate(apptDate.value, apptTime.value);
        if (slotDate && slotDate < getCutoff()) {
            alert(`Please choose a time at least ${MINUTES_AHEAD} minutes from now.`);
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
