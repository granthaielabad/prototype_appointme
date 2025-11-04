<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verify OTP - AppointMe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { width:420px; }
    #resendBtn[disabled] { opacity:0.6; cursor:not-allowed; }
  </style>
</head>
<body>
<div class="d-flex align-items-center justify-content-center vh-100">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-3 text-center">Verify Your Account</h4>
    <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

    <!-- OTP Verification Form -->
    <form method="post" action="/verify-otp">
      <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">

      <div class="mb-3">
        <input class="form-control" name="email" type="email"
               placeholder="Email"
               value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
               required>
      </div>

      <div class="mb-3">
        <input class="form-control text-center fs-5" maxlength="6" name="code"
               placeholder="Enter 6-digit code" required>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-primary w-50">Verify</button>
        <button type="button" id="resendBtn" class="btn btn-outline-secondary w-50">
          Resend Code
        </button>
      </div>

      <div class="text-center mt-2">
        <small id="cooldownMsg" class="text-muted"></small>
      </div>
    </form>

    <hr>
    <div class="text-center">
      <a href="/" class="btn btn-link">← Back to Homepage</a>
    </div>
  </div>
</div>

<script>
const resendBtn = document.getElementById('resendBtn');
const cooldownMsg = document.getElementById('cooldownMsg');
let cooldown = 0;

/**
 * Send resend request via AJAX and expect JSON { success: bool, message: string }.
 */
async function resendCode() {
  const email = document.querySelector('input[name="email"]').value.trim();
  if (!email) {
    alert('Please enter your email to resend the code.');
    return;
  }

  resendBtn.disabled = true;
  resendBtn.textContent = 'Sending...';

  try {
    const params = new URLSearchParams();
    params.append('_csrf', '<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>');
    params.append('email', email);

    const response = await fetch('/otp/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: params.toString()
    });

    // parse JSON response
    const data = await response.json();

    if (response.ok && data.success) {
      alert(data.message || 'A new verification code has been sent to your email.');
      startCooldown(60);
    } else {
      // server returns success=false or non-2xx
      alert(data.message || 'Failed to resend code. Please try again later.');
      resendBtn.disabled = false;
      resendBtn.textContent = 'Resend Code';
    }
  } catch (err) {
    console.error(err);
    alert('An error occurred. Please try again.');
    resendBtn.disabled = false;
    resendBtn.textContent = 'Resend Code';
  }
}

function startCooldown(seconds) {
  cooldown = seconds;
  resendBtn.disabled = true;
  updateCooldownMsg();
  const interval = setInterval(() => {
    cooldown--;
    updateCooldownMsg();
    if (cooldown <= 0) {
      clearInterval(interval);
      resendBtn.disabled = false;
      resendBtn.textContent = 'Resend Code';
      cooldownMsg.textContent = '';
    }
  }, 1000);
}

function updateCooldownMsg() {
  cooldownMsg.textContent = cooldown > 0
    ? `You can resend a new code in ${cooldown}s`
    : '';
  resendBtn.textContent = cooldown > 0
    ? `Wait ${cooldown}s`
    : 'Resend Code';
}

resendBtn.addEventListener('click', resendCode);
</script>
</body>
</html>
