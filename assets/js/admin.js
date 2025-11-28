// Bar Chart
const bar = document.getElementById('barChart');

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

// Donut Chart
const donut = document.getElementById('donutChart');

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
