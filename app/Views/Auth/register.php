<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register - AppointMe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="d-flex align-items-center justify-content-center vh-100" style="background:#f8fafc">
  <div class="card p-4" style="width:420px">
    <h4>Create account</h4>
    <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>
    <form method="post" action="/register">
      <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">
      <div class="row">
        <div class="col"><input class="form-control mb-2" name="first_name" placeholder="First name" required></div>
        <div class="col"><input class="form-control mb-2" name="last_name" placeholder="Last name" required></div>
      </div>
      <div class="mb-2"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
      <div class="mb-2"><input class="form-control" name="contact_number" placeholder="Contact number"></div>
      <div class="mb-2"><input class="form-control" name="password" type="password" placeholder="Password" required></div>
      <div class="mb-2"><input class="form-control" name="confirm_password" type="password" placeholder="Confirm password" required></div>
      <button class="btn btn-primary w-100">Register</button>
    </form>
    <hr>
    <small>Already have an account? <a href="/login">Login</a></small>

    <div class="text-center mt-3">
      <a href="/" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Go back to homepage
      </a>
    </div>
    
  </div>
</div>
</body>
</html>
