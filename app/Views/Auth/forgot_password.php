<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Forgot Password – 8th Avenue Salon</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/auth.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600&family=WindSong:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body class="auth-body">
    <!-- Logo -->
    <img src="/assets/img/logo.svg" class="auth-logo">

    <div class="auth-container">

        <!-- LEFT SIDE (FORM) -->
        <div class="auth-card auth-left">
            <h2 class="auth-title">Forgot Password</h2>

            <p class="forgot-subtext">
                Enter your registered email address and we will send you a link to reset your password.
            </p>

            <?php require __DIR__ . '/../layouts/alerts.php'; ?>

            <form method="POST" action="/forgot-password" class="auth-form">
                <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">

                <div class="auth-field">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>

                <button class="auth-btn">SEND RESET LINK</button>
            </form>

            <a href="/login" class="auth-link-small mt-3">Back to Login</a>
        </div>

        <!-- RIGHT SIDE (Simple decorative panel) -->
        <div class="auth-card auth-right">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>

            <h1 class="right-title">Forgot<br><span class="script">Password</span> ?</h1>

            <p class="right-text">
                We’ll help you get back into your account safely and quickly.
            </p>

            <p class="right-small">
                Didn’t receive the link?<br><span class="acc-sub-text">
                    Check your spam folder or try again later.</span>
            </p>
        </div>

    </div>

</body>

</html>