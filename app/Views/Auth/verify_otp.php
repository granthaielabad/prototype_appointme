<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Verify Account – 8th Avenue Salon</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Auth Styles -->
    <link rel="stylesheet" href="/assets/css/auth.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700&family=WindSong:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body class="auth-body">
    <img src="/assets/img/logo.svg" class="auth-logo">

    <div class="auth-container">

        <!-- LEFT PANEL (Blur Card) -->
        <div class="auth-card auth-left">
            <h2 class="auth-title">Verify Your Account</h2>

            <?php require __DIR__ . '/../layouts/alerts.php'; ?>

            <form id="otpForm" method="post" action="/verify-otp" class="auth-form">
                <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">

                <!-- Email -->
                <div class="auth-field">
                    <input type="email" name="email" placeholder="Email"
                        value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
                </div>

                <!-- OTP FIELD -->
                <div class="auth-field">
                    <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" required>
                </div>

                <!-- Buttons -->
                <div class="otp-btn-row">
                    <button type="submit" class="auth-btn w-50 me-2">VERIFY</button>

                    <button type="button" id="resendBtn" class="resend-btn w-50">
                        Resend Code
                    </button>
                </div>

                <small id="cooldownMsg" class="cooldown-text"></small>

                <a href="/login" class="auth-link-small mt-3 d-block">← Back to Login</a>
            </form>
        </div>

        <!-- RIGHT PANEL -->
        <div class="auth-card auth-right">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>

            <h1 class="right-title">Email<br><span class="script">Verification</span></h1>

            <p class="right-text">
                We've sent a verification code to your email.  
                Enter the 6-digit code to activate your account.
            </p>

            <p class="right-small">
                Didn't receive your code?<br>
                <span class="acc-sub-text">Use the resend button to get another one.</span>
            </p>
        </div>

    </div>

    <!-- RESEND JS -->
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

            const text = await res.text();
            let data;
            try { data = JSON.parse(text); } catch { data = { message: text }; }

            cooldownMsg.textContent = data.message || "A new verification code has been sent.";
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
