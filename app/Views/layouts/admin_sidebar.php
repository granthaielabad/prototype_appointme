<ul class="nav flex-column">
  <li class="nav-item mb-1">
    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/dashboard') ? 'active fw-bold text-primary' : 'text-dark' ?>" href="/admin/dashboard">
      <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
  </li>
  <li class="nav-item mb-1">
    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/appointments') ? 'active fw-bold text-primary' : 'text-dark' ?>" href="/admin/appointments">
      <i class="bi bi-calendar-check me-2"></i> Appointments
    </a>
  </li>
  <li class="nav-item mb-1">
    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/services') ? 'active fw-bold text-primary' : 'text-dark' ?>" href="/admin/services">
      <i class="bi bi-gear me-2"></i> Services
    </a>
  </li>
  <li class="nav-item mb-1">
    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/inquiries') ? 'active fw-bold text-primary' : 'text-dark' ?>" href="/admin/inquiries">
      <i class="bi bi-envelope me-2"></i> Inquiries
    </a>
  </li>
  <li class="nav-item mb-1">
    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/reports') ? 'active fw-bold text-primary' : 'text-dark' ?>" href="/admin/reports">
      <i class="bi bi-bar-chart me-2"></i> Reports
    </a>
  </li>
</ul>
</aside>

<main class="col-md-10 py-4">
