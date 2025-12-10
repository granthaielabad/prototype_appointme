<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reset Password – 8th Avenue Salon</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Icons + Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/auth.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@300;400;500;600;700&family=Italiana&family=WindSong:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body class="auth-body">

    <!-- Logo -->
    <img src="/assets/img/logo.svg" class="auth-logo">

    <div class="auth-container">

        <!-- LEFT: Form -->
        <div class="auth-card auth-left">
            <h2 class="auth-title">Reset Password</h2>

            <?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

            <form method="POST" action="/reset-password" class="auth-form">
                <input type="hidden" name="_csrf"
                    value="<?= \App\Core\CSRF::getToken() ?? \App\Core\CSRF::generate() ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <p class="forgot-subtext">
                    Please enter your new password below.
                </p>

                <!-- Password -->
                <div class="auth-field auth-password">
                    <input type="password" id="newPass" name="password" placeholder="New Password" required>
                    <img src="/assets/img/view-off.svg" class="toggle-eye" id="toggleNewPass">
                </div>

                <!-- Confirm Password -->
                <div class="auth-field auth-password">
                    <input type="password" id="confirmPass" name="confirm_password" placeholder="Confirm New Password"
                        required>
                    <img src="/assets/img/view-off.svg" class="toggle-eye" id="toggleConfirmPass">
                </div>

                <button class="auth-btn mt-3">Update Password</button>

                <a href="/login" class="auth-link-small mt-3 d-block">← Back to Login</a>
            </form>
        </div>

        <!-- RIGHT: Design Panel -->
        <div class="auth-card auth-right">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>

            <h1 class="right-title">
                Reset<br><span class="script">Securely</span>
            </h1>

            <p class="right-text">
                Create a new password to regain access to your account.  
                Keep your information safe and protected.
            </p>

            <p class="right-small">Having trouble? Contact support for help.</p>
        </div>

    </div>

    <!-- Toggle Logic -->
    <script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isHidden = input.type === "password";

        input.type = isHidden ? "text" : "password";
        icon.src = isHidden ? "/assets/img/view-on.svg" : "/assets/img/view-off.svg";
    }

    document.getElementById("toggleNewPass").onclick = () => togglePassword("newPass", "toggleNewPass");
    document.getElementById("toggleConfirmPass").onclick = () => togglePassword("confirmPass", "toggleConfirmPass");
    </script>

</body>

</html>
