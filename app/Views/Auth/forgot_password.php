<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Forgot Password - AppointMe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <div class="d-flex align-items-center justify-content-center vh-100" style="background:#f8fafc">
    <div class="card p-4 shadow-sm" style="width:380px">
      <h4 class="mb-3 text-center">Forgot Password</h4>
      <p class="text-muted text-center mb-4">Enter your registered email and we'll send a link to reset your password.</p>

      <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

      <form method="post" action="/forgot-password">
        <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">
        <div class="mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email address" autocomplete="email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
      </form>

      <hr>

      <div class="text-center">
        <a href="/login" class="text-decoration-none d-block mb-2">Back to Login</a>
        <a href="/" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left"></i> Go back to homepage
        </a>
      </div>
    </div>
  </div>
</body>
</html>
