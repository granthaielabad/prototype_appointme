<?php
$pageTitle = "Complete Account Setup";
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h4 class="auth-title">Complete Your Account Setup</h4>
            <p class="auth-subtitle">Welcome to our salon team! Please set up your password to get started.</p>
        </div>

        <form action="/employee/complete-setup" method="POST" class="auth-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Create Password</label>
                <input type="password" name="password" class="form-input" placeholder="Enter your password" required minlength="8">
                <small class="form-hint">Password must be at least 8 characters long</small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required minlength="8">
            </div>

            <button type="submit" class="auth-btn">
                Complete Setup
            </button>
        </form>

        <div class="auth-footer">
            <p>Need help? Contact your administrator.</p>
        </div>
    </div>
</div>

<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.auth-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 450px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-title {
    color: #333;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 8px;
}

.auth-subtitle {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

.auth-form {
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    color: #333;
    font-weight: 500;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #CD9FFE;
    box-shadow: 0 0 0 3px rgba(205, 159, 254, 0.1);
}

.form-input[readonly] {
    background-color: #f9fafb;
    cursor: not-allowed;
}

.form-hint {
    display: block;
    color: #6b7280;
    font-size: 12px;
    margin-top: 4px;
}

.auth-btn {
    width: 100%;
    background: linear-gradient(135deg, #CD9FFE 0%, #a855f7 100%);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(205, 159, 254, 0.3);
}

.auth-footer {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.auth-footer p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
}
</style>
