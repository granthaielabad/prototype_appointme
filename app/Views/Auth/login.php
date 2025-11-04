<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - AppointMe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex align-items-center justify-content-center vh-100" style="background:#f8fafc">
  <div class="card p-4" style="width:380px">
    <h4 class="mb-3">Sign in</h4>
    <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>
    <form method="post" action="/login">
      <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">
      <div class="mb-3"><input class="form-control" name="email" placeholder="Email" type="email" required></div>
      <div class="mb-3"><input class="form-control" name="password" placeholder="Password" type="password" required></div>
      <div class="d-flex justify-content-between align-items-center">
        <a href="/forgot-password">Forgot Password?</a>
        <button class="btn btn-primary">Login</button>
      </div>
    </form>
    <hr>
    <small>Don't have an account? <a href="/register">Register</a></small>

    <div class="text-center mt-3">
      <a href="/" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Go back to homepage
      </a>
    </div>
    
  </div>
</div>
</body>
</html>
