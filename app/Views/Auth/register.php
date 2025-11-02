<?php
use App\Core\Session;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - AppointMe</title>
  <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-page">
  <div class="auth-container">
    <h2>Create Account</h2>

    <?php if ($f = Session::getFlash('success')): ?>
      <div class="alert alert-success"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endif; ?>
    <?php if ($f = Session::getFlash('error')): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/register">
      <input name="first_name" placeholder="First name" required>
      <input name="last_name" placeholder="Last name" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="contact_number" placeholder="Contact number">
      <input name="password" type="password" placeholder="Password" required>
      <input name="confirm_password" type="password" placeholder="Confirm password" required>
      <button type="submit">Register</button>
    </form>

    <p style="margin-top:12px;">Already have an account? <a href="/login">Login</a></p>
  </div>
</body>
</html>
