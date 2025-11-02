<?php
// Views/layouts/navbar.php
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="/">AppointMe</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navCollapse" aria-controls="navCollapse" aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navCollapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/#services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="/#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/#contact">Contact</a></li>
        <?php if (!\App\Core\Auth::check()): ?>
          <li class="nav-item"><a class="nav-link btn btn-outline-primary ms-2" href="/login">Login</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary ms-2 text-white" href="/register">Sign Up</a></li>
        <?php else: $user = \App\Core\Auth::user(); ?>
          <?php if ($user['role_id'] == 1): ?>
            <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Admin</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/my-appointments">My Appointments</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
