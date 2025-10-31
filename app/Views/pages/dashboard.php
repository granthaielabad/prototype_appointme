<div class="container my-5">
    <h1>Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'Guest') ?>!</p>
</div>
