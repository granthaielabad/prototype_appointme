// Inject PHP → JS variables
const monthlyLabels = JSON.parse(document.getElementById("monthlyLabels").textContent);
const monthlyValues = JSON.parse(document.getElementById("monthlyValues").textContent);
const donutAccepted = parseInt(document.getElementById("donutAccepted").textContent);
const donutRejected = parseInt(document.getElementById("donutRejected").textContent);

// DATE FILTER MODAL
const dateFilterBtn = document.getElementById("openDateFilter");
const dateModal = document.getElementById("dateFilterModal");


// OPEN / CLOSE DOWNLOAD MODAL
const modal = document.getElementById("downloadModal");
document.getElementById("openDownloadModal").onclick = () => modal.style.display = "flex";
modal.querySelector(".close-modal").onclick = () => modal.style.display = "none";

if (dateFilterBtn && dateModal) {
    dateFilterBtn.onclick = () => dateModal.style.display = "flex";
    dateModal.querySelector(".close-modal").onclick = () => dateModal.style.display = "none";
}

// CHART: MONTHLY SALES
new Chart(document.getElementById("monthlySales"), {
    type: "bar",
    data: {
        labels: monthlyLabels,
        datasets: [{
            data: monthlyValues,
            backgroundColor: "#CD9FFE"
        }]
    },
    options: {
        plugins: { legend: { display: false }},
        responsive: true
    }
});


// CHART: APPOINTMENT DONUT
new Chart(document.getElementById("appointmentDonut"), {
    type: "doughnut",
    data: {
        labels: ["Accepted", "Rejected"],
        datasets: [{
            data: [donutAccepted, donutRejected],
            backgroundColor: ["#4ac4e1", "#a3cbe1"]
        }]
    },
    options: {
        cutout: "68%",
        plugins: { legend: { display: true }}
    }
});
