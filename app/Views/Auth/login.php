
<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <title>Login – 8th Avenue Salon</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/auth.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=WindSong:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body class="auth-body">
    <img src="/assets/img/logo.svg" class="auth-logo">

    <div class="auth-container">

        <!-- LEFT SIDE (FORM CARD) -->
        <div class="auth-card auth-left">
            <h2 class="auth-title">Log In</h2>

            <?php require __DIR__ . '/../layouts/alerts.php'; ?>

            <form method="POST" action="/login" class="auth-form">
                <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">

                <div class="auth-field">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="auth-field auth-password">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <img src="/assets/img/view-off.svg" class="toggle-eye" id="toggleLoginPassword">
                </div>

                <a href="/forgot-password" class="auth-link-small">Forgot Password?</a>

                <button class="auth-btn">LOG IN</button>
            </form>
        </div>

        <!-- RIGHT SIDE -->
        <div class="auth-card auth-right">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>

            <h1 class="right-title">Hello,<br><span class="script">Welcome</span> Back!</h1>

            <p class="right-text">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua.
            </p>

            <p class="right-small">Don’t Have an Account?<br><span class="acc-sub-text">Unlock easy booking, exclusive
                    offers, and a
                    personalized
                    salon experience.</span></p>

            <a href="/register" class="right-btn-link">Register</a>
        </div>

    </div>

    <script>
    document.getElementById("toggleLoginPassword").onclick = function() {
        const input = document.getElementById("loginPassword");
        const isHidden = input.type === "password";

        input.type = isHidden ? "text" : "password";
        this.src = isHidden ?
            "/assets/img/view-on.svg" :
            "/assets/img/view-off.svg";
    };
    </script>


</body>

</html>