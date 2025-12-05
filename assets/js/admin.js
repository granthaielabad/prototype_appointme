// Bar Chart (only initialize when element exists)
const bar = document.getElementById('barChart');
if (bar) {
    new Chart(bar, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun'],
            datasets: [{
                label: 'Sales',
                data: [3500, 2900, 4100, 3800, 4500, 5200],
                backgroundColor: '#ff99c2'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}

// Donut Chart (only initialize when element exists)
const donut = document.getElementById('donutChart');
if (donut) {
    new Chart(donut, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [68, 22, 10],
                backgroundColor: ['#d6336c', '#ff99c2', '#ffe0ea']
            }]
        },
        options: {
            cutout: '68%',
            plugins: { legend: { display: true } }
        }
    });
}


document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("profileToggle");
    const dropdown = document.getElementById("profileDropdown");

    if (!toggleBtn || !dropdown) return;

    toggleBtn.addEventListener("click", (e) => {
        e.stopPropagation(); 
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });

    // Close when clicking outside
    document.addEventListener("click", () => {
        dropdown.style.display = "none";
    });
});

/* ============================================================
   ARCHIVE - View Details Modal
   ============================================================ */
function viewArchiveDetails(archiveId) {
    // Placeholder for viewing full archived item details
    // Could expand to show full JSON data from the 'details' column
    alert('Viewing details for archive ID: ' + archiveId);
    // TODO: Implement modal to show full details from tbl_archives.details (JSON)
}
