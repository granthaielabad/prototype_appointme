<?php
use App\Core\Auth;

$user = Auth::user();
$loggedIn = $user !== null;
$unreadCount = $notificationsCount ?? 0;
?>

<nav class="main-navbar">
    <div class="nav-left">
        <img src="/assets/img/logo.svg" class="nav-logo" alt="8th Avenue Logo">

        <div class="nav-divider"></div>

        <ul class="nav-links">
            <li><a href="#home" class="scroll-link">Home</a></li>
            <li><a href="#services" class="scroll-link">Services</a></li>
            <li><a href="#about" class="scroll-link">About Us</a></li>
            <li><a href="#contact" class="scroll-link">Contact Us</a></li>
        </ul>
    </div>

    <div class="nav-right">
        <?php if ($loggedIn): ?>
        <div class="notification-wrapper">
            <img src="/assets/img/NotificationIcon.svg" id="notifToggle" class="nav-icon" width="28" height="28"
                alt="Notifications">

            <?php if ($unreadCount > 0): ?>
            <span class="notif-count"><?= $unreadCount ?></span>
            <?php endif; ?>

            <div class="notif-dropdown" id="notifDropdown">
                <h6 class="notif-title">Notifications</h6>
                <div id="notifList">
                    <!-- JS will load notifications here -->
                    <p class="text-muted small">Loading...</p>
                </div>
            </div>
        </div>

        <div class="profile-wrapper">
            <!-- PROFILE ICON (redirects to /profile) -->
            <img src="/assets/img/ProfileIcon.svg" id="profileIcon" class="profile-icon" width="28" height="28"
                alt="Profile">

            <!-- DROPDOWN TOGGLE BUTTON -->
            <i class="bi bi-caret-down-fill profile-toggle" id="profileToggle"></i>

            <!-- PROFILE DROPDOWN -->
            <div class="profile-dropdown" id="profileDropdown">
                <a href="/profile" class="dropdown-item">My Profile</a>
                <a href="/logout" class="dropdown-item text-danger">Logout</a>
            </div>
        </div>

        <?php endif; ?>
    </div>
</nav>

<script>
document.querySelectorAll('.scroll-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault(); // prevent redirect
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            window.scrollTo({
                top: target.offsetTop - 80, // adjust for navbar height
                behavior: 'smooth'
            });
        }
    });
});

// ====== NOTIFICATION DROPDOWN ======
const notifBtn = document.getElementById("notifToggle");
const notifDropdown = document.getElementById("notifDropdown");

if (notifBtn) {
    notifBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle("show");

        // Optional AJAX loader:
        fetch('/notifications/get')
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.length === 0) {
                    html = '<p class="text-muted small">No new notifications.</p>';
                } else {
                    data.forEach(n => {
                        html += `
                            <div class="notif-item">
                                <strong>${n.title}</strong>
                                <p>${n.message}</p>
                                <small>${n.date}</small>
                            </div>
                        `;
                    });
                }
                document.getElementById("notifList").innerHTML = html;
            });
    });
}

// ===== PROFILE DROPDOWN TOGGLE =====
const profileToggle = document.getElementById("profileToggle");
const profileDropdown = document.getElementById("profileDropdown");

if (profileToggle) {
    profileToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle("show");
    });
}

// Close when clicking outside
document.addEventListener("click", () => {
    if (profileDropdown) profileDropdown.classList.remove("show");
});


// Close dropdowns when clicking outside
document.addEventListener("click", () => {
    if (notifDropdown) notifDropdown.classList.remove("show");
    if (profileDropdown) profileDropdown.classList.remove("show");
});
</script>