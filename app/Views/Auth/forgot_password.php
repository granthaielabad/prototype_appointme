<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | AppointMe</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2>Forgot Password</h2>
        <p>Enter your email and we’ll send you a link to reset your password.</p>

        <?php if ($flash = \App\Core\Session::getFlash('forgot')): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/forgot-password">
            <input type="email" name="email" placeholder="Email Address" required>
            <button type="submit" class="btn-primary">Send Reset Link</button>
        </form>

        <p class="mt-2"><a href="/login">Back to login</a></p>
    </div>
</div>
</body>
</html>
