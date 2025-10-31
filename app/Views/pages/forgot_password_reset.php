<div class="container my-5">
    <h3>Reset Password</h3>
    <form method="POST" action="/password-reset">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
        <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="New password" required></div>
        <div class="mb-3"><input name="confirm_password" type="password" class="form-control" placeholder="Confirm password" required></div>
        <button class="btn btn-primary">Reset Password</button>
    </form>
</div>
