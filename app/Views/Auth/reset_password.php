<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | AppointMe</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2>Reset Password</h2>
        <p>Enter your new password below.</p>

        <?php if ($flash = \App\Core\Session::getFlash('reset')): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/password-reset">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="btn-primary">Update Password</button>
        </form>

        <p class="mt-2"><a href="/login">Back to login</a></p>
    </div>
</div>
</body>
</html>
