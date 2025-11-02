<?php
// Views/Home/landing.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>AppointMe — Salon Appointments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* small custom styles */
    .hero { padding: 6rem 0; background: linear-gradient(135deg,#f8fafc,#fff); }
    .service-card { min-height: 160px; }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <header id="home" class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="display-5 fw-bold">Book salon appointments effortlessly</h1>
          <p class="lead text-muted">Find services, pick a time, and get confirmed — all in one place.</p>
          <p>
            <a href="/register" class="btn btn-primary btn-lg me-2">Get Started</a>
            <a href="/#services" class="btn btn-outline-secondary btn-lg">View Services</a>
          </p>
        </div>
        <div class="col-md-6 text-center">
          <img src="/assets/img/hero.png" alt="hero" class="img-fluid" style="max-height:320px">
        </div>
      </div>
    </div>
  </header>

  <section id="services" class="py-5">
    <div class="container">
      <h2 class="mb-4">Services</h2>
      <div class="row g-3">
        <?php foreach($services as $category => $list): ?>
          <div class="col-12">
            <h5 class="mt-3"><?=htmlspecialchars($category)?></h5>
            <div class="row">
              <?php foreach($list as $s): ?>
                <div class="col-md-4">
                  <div class="card p-3 service-card shadow-sm">
                    <h6><?=$s['service_name']?></h6>
                    <p class="mb-1 small text-muted"><?=number_format($s['price'],2)?> • <?=$s['duration_minutes']?> mins</p>
                    <p class="small text-muted"><?=htmlspecialchars($s['service_name'])?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="about" class="py-5 bg-white">
    <div class="container">
      <h2>About</h2>
      <p class="lead text-muted">AppointMe is a demo salon appointment system — made to be clean, fast, and mobile-friendly.</p>
    </div>
  </section>

  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <h2>Contact / Inquiry</h2>
      <form action="/inquiry/storePublic" method="post" class="row g-3">
        <div class="col-md-6"><input class="form-control" name="first_name" placeholder="First name" required></div>
        <div class="col-md-6"><input class="form-control" name="last_name" placeholder="Last name" required></div>
        <div class="col-md-6"><input class="form-control" name="email" placeholder="Email" type="email" required></div>
        <div class="col-md-6"><input class="form-control" name="phone" placeholder="Phone"></div>
        <div class="col-12"><textarea name="message" class="form-control" rows="4" placeholder="Message" required></textarea></div>
        <div class="col-12"><button class="btn btn-primary">Send Inquiry</button></div>
      </form>
    </div>
  </section>

  <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
