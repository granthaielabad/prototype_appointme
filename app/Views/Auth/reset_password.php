<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset Password - AppointMe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <div class="d-flex align-items-center justify-content-center vh-100" style="background:#f8fafc">
    <div class="card p-4 shadow-sm" style="width:380px">
      <h4 class="mb-3 text-center">Reset Password</h4>
      <p class="text-muted text-center mb-4">Enter your new password below.</p>

      <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

      <form method="post" action="/reset-password">
        <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

        <div class="mb-3">
          <input type="password" class="form-control" name="password" placeholder="New password" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Password</button>
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
