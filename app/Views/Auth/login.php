<?php
use App\Core\Session;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - AppointMe</title>
  <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-page">
  <div class="auth-container">
    <h2>Login</h2>

    <?php if ($f = Session::getFlash('success')): ?>
      <div class="alert alert-success"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endif; ?>
    <?php if ($f = Session::getFlash('error')): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <p style="margin-top:12px;">
      <a href="/forgot-password">Forgot password?</a> · <a href="/register">Create account</a>
    </p>
  </div>
</body>
</html>
