<?php

use App\Core\Session;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AppointMe Salon</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        section { padding: 80px 0; }
        nav { position: fixed; top: 0; left: 0; width: 100%; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,.1); z-index: 999; }
        nav ul { display: flex; justify-content: center; list-style: none; margin: 0; padding: 1em; }
        nav ul li { margin: 0 20px; }
        nav a { text-decoration: none; color: #333; font-weight: 600; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="/login" class="btn btn-primary">Login</a></li>
    </ul>
</nav>

<section id="home">
    <div class="container text-center">
        <h1>Welcome to AppointMe Salon</h1>
        <p>Book your next appointment with ease.</p>
        <a href="/register" class="btn btn-success mt-3">Get Started</a>
    </div>
</section>

<section id="services">
    <div class="container">
        <h2>Our Services</h2>
        

        <?php foreach ($services as $category => $items): ?>
            <h4 class="mt-4"><?= htmlspecialchars($category) ?></h4>
            <ul>
                <?php foreach ($items as $s): ?>
                    <li><?= htmlspecialchars($s['service_name']) ?> — ₱<?= htmlspecialchars($s['price']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
</section>

<section id="about" style="background:#f9f9f9;">
    <div class="container">
        <h2>About Us</h2>
        <p>We’re a modern salon providing high-quality hair and beauty services.</p>
    </div>
</section>

<section id="contact">
    <div class="container">
        <h2>Contact Us</h2>
                
        <?php if ($f = Session::getFlash('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($f['msg']) ?></div>
        <?php endif; ?>

        <?php if ($f = Session::getFlash('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($f['msg']) ?></div>
        <?php endif; ?>

        <form method="POST" action="/inquiry/submit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>
            </div>
            <input type="text" name="phone" class="form-control mb-3" placeholder="Contact Number">
            <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
            <textarea name="message" class="form-control mb-3" placeholder="Your Message" rows="4" required></textarea>
            <button class="btn btn-primary">Send Message</button>
        </form>
    </div>
</section>

</body>
</html>
