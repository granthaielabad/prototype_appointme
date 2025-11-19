<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verify Account - AppointMe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { width:420px; }
    .otp-input { letter-spacing:4px; text-align:center; font-size:1.2rem; }
    #cooldownMsg { min-height:1.5em; font-size:0.9rem; }
  </style>
</head>
<body>
<div class="d-flex align-items-center justify-content-center vh-100">
  <div class="card p-4 shadow-sm">
    <h4 class="text-center mb-3 fw-semibold">Verify Your Account</h4>

    <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

    <form id="otpForm" method="post" action="/verify-otp">
      <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">

      <div class="mb-3">
        <input class="form-control" name="email" type="email"
               placeholder="Email"
               value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
               required>
      </div>

      <div class="mb-3">
        <input class="form-control otp-input" maxlength="6" name="code"
               placeholder="Enter 6-digit code" required>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary w-50 me-1">Verify</button>
        <button type="button" id="resendBtn" class="btn btn-outline-secondary w-50">
          Resend Code
        </button>
      </div>

      <div class="text-center mt-2">
        <small id="cooldownMsg" class="text-muted d-block"></small>
      </div>

      <hr>
      <div class="text-center">
        <a href="/" class="btn btn-link text-decoration-none">← Back to Homepage</a>
      </div>
    </form>
  </div>
</div>

<script>
const resendBtn = document.getElementById('resendBtn');
const cooldownMsg = document.getElementById('cooldownMsg');
let cooldown = 0;

async function resendCode() {
  const email = document.querySelector('input[name="email"]').value.trim();
  const csrf = document.querySelector('input[name="_csrf"]').value;

  if (!email) {
    cooldownMsg.textContent = "Please enter your email address first.";
    return;
  }

  resendBtn.disabled = true;
  resendBtn.textContent = "Sending...";
  cooldownMsg.textContent = "";

  try {
    const res = await fetch('/otp/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ email, _csrf: csrf })
    });

    // Try to parse JSON; fallback to plain text
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch { data = { message: text }; }

    const msg = data.message || "A new verification code has been sent to your email.";
    cooldownMsg.textContent = msg;

    startCooldown(60);
  } catch (err) {
    cooldownMsg.textContent = "Error sending code. Please try again.";
    resendBtn.disabled = false;
    resendBtn.textContent = "Resend Code";
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
      resendBtn.textContent = "Resend Code";
      cooldownMsg.textContent = "";
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
