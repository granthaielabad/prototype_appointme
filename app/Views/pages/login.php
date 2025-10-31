<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AppointMe</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h3 class="text-center mb-3">Login to AppointMe</h3>
                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-dark w-100">Login</button>
                    <p class="text-center mt-3">
                        No account? <a href="/register">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>


