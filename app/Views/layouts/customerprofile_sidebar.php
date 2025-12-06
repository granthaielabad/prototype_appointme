<link href="/assets/css/customer.css" rel="stylesheet">

<aside id="customerSidebar" class="customer-sidebar">
    <!-- SIDEBAR TOGGLE BUTTON (LEFT ARROW) -->
<button id="sidebarToggle" class="sidebar-toggle-btn">
    <img src="/assets/img/arrow.png" class="arrow" alt="Open" />
</button>

<!-- SIDEBAR -->

    <div class="sidebar-header">
        <img src="/assets/img/logo.svg" class="logo" />
        <h4>Customer Panel</h4>
    </div>

    <nav class="sidebar-links">
        <a href="http://localhost:8000/Customer/invoices.php">
            <i class="bi bi-receipt"></i> Invoice History
        </a>

        <a href="./../../booking_history.php">
            <i class="bi bi-arrow-counterclockwise"></i> Booking History
        </a>
    </nav>
</aside>

<script>
function updateClock() {
    let now = new Date();

    let phTime = new Date(
        now.toLocaleString("en-US", { timeZone: "Asia/Manila" })
    );

    const dig = phTime.toLocaleTimeString("en-US", {
        hour12: false,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });
    document.getElementById("digTime").textContent = dig;

    let sec = phTime.getSeconds();
    let min = phTime.getMinutes();
    let hr  = phTime.getHours();

    let secDeg = sec * 6;           
    let minDeg = min * 6 + sec * 0.1; 
    let hrDeg  = hr * 30 + min * 0.5;

    document.getElementById("secondHand").style.transform =
    `translate(-50%, -100%) rotate(${secDeg}deg)`;

document.getElementById("minuteHand").style.transform =
    `translate(-50%, -100%) rotate(${minDeg}deg)`;

document.getElementById("hourHand").style.transform   =
    `translate(-50%, -100%) rotate(${hrDeg}deg)`;

}

setInterval(updateClock, 1000);
updateClock();
</script>

<script>
const sidebar = document.getElementById("customerSidebar");
const toggleBtn = document.getElementById("sidebarToggle");

// Toggle sidebar open/close when button is clicked
toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
});

// Close sidebar if clicking outside sidebar and toggle button
document.addEventListener("click", (event) => {
    const isClickInsideSidebar = sidebar.contains(event.target);
    const isClickToggleBtn = toggleBtn.contains(event.target);

    if (!isClickInsideSidebar && !isClickToggleBtn) {
        sidebar.classList.remove("open");
    }
});
</script>
