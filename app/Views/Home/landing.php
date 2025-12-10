<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>8th Avenue Salon | Book Appointments Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Landing Styles -->
    <link rel="stylesheet" href="/assets/css/landing.css">
</head>

<body>

    <?php include __DIR__ . "/../layouts/navbar.php"; ?>


    <!-- ======================================================= -->
    <!-- HERO SECTION (full width, no container) -->
    <!-- ======================================================= -->
    <section id="home" class="hero-section">

        <div class="hero-inner">
            <!-- LEFT -->
            <div class="hero-left">
                <h1 class="hero-title">
                    Pamper Yourself,<br>
                    The Smart & Easy Way.
                </h1>

                <p class="hero-desc">
                    Book appointments, explore services, and enjoy a seamless salon experience
                    designed just for you at 8th Avenue Salon.
                </p>

                <a href="/login" class="btn btn-primary hero-btn" style="background:#AF62FF;border:none;">Get
                    Started</a>
            </div>

            <!-- RIGHT -->
            <div class="hero-right">
                <div class="hero-card price-card">
                    <h4>Haircut</h4>
                    <p class="price">₱150</p>
                    <small>Classic haircut service</small>
                </div>

                <div class="hero-card promo-card">
                    <h5>Holiday Promo 🎉</h5>
                    <p>15% OFF on hair coloring services</p>
                </div>

                <div class="hero-card stat-card">
                    <h2>24+</h2>
                    <small>Daily Appointments</small>
                </div>
            </div>
        </div>

    </section>


    <!-- ======================================================= -->
    <!-- SERVICES SECTION -->
    <!-- ======================================================= -->
    <section id="services" class="services-section">

        <h2 class="section-title italiana">Featured Services</h2>

        <div class="featured-services-wrapper">

            <!-- SERVICE CARD -->
            <div class="fs-card">
                <h3 class="fs-title">Rebond</h3>
                <p class="fs-desc">Achieve sleek, straight, and shiny hair with our expert rebonding service.</p>
                <p class="fs-price">Starts at ₱1500.00</p>
            </div>

            <div class="fs-card">
                <h3 class="fs-title">Hair Color</h3>
                <p class="fs-desc">Transform your look with vibrant, long-lasting hair color tailored just for you.</p>
                <p class="fs-price">Starts at ₱400.00</p>
            </div>

            <div class="fs-card">
                <h3 class="fs-title">Hair Treatment</h3>
                <p class="fs-desc">Revitalize and nourish your hair with our intensive treatment solutions.</p>
                <p class="fs-price">Starts at ₱700.00</p>
            </div>

            <div class="fs-card">
                <h3 class="fs-title">Perming</h3>
                <p class="fs-desc">Get beautiful, bouncy curls or waves that last with our professional perming.</p>
                <p class="fs-price">Starts at ₱800.00</p>
            </div>

            <div class="fs-card">
                <h3 class="fs-title">Make Up</h3>
                <p class="fs-desc">Look your best with our flawless professional makeup services.</p>
                <p class="fs-price">Starts at ₱600.00</p>
            </div>

        </div>

        <div class="featured-btn-wrap">
            <a href="/login" class="featured-btn">Book an Appointment</a>
        </div>
    </section>



    <!-- ======================================================= -->
    <!-- ABOUT SECTION (NEW DESIGN) -->
    <!-- ======================================================= -->
    <section id="about" class="aboutus-section">
        <div class="aboutus-inner">
            <!-- LEFT IMAGES -->
            <div class="aboutus-images">
                <!-- MAIN BACKGROUND IMAGE -->
                <img src="/assets/img/about-bg-02.svg" class="about-img about-img-main">
                <!-- TWO STACKED IMAGES -->
                <img src="/assets/img/about-bg-01.svg" class="about-img about-img-small about-img-small-1">
                <img src="/assets/img/about-bg-03.svg" class="about-img about-img-small about-img-small-2">
            </div>

            <!-- RIGHT CONTENT -->
            <div class="aboutus-content">
                <h2 class="aboutus-title italiana">About Us</h2>
                <p class="aboutus-text">
                    Best Beauty expert at your home and provides beauty salon at home.
                    Home Salon provide well trained beauty professionals for beauty services at home
                    including Facial, Clean Up, Bleach, Waxing, Pedicure, Manicure, etc.
                </p>
                <a href="/about" class="aboutus-btn">Learn More</a>
            </div>

        </div>

    </section>




    <!-- ======================================================= -->
    <!-- CONTACT SECTION — SVG STYLE (NO CARDS) -->
    <!-- ======================================================= -->
    <section id="contact" class="contact-section">

        <h2 class="section-title italiana">Contact Us</h2>
        <p class="section-subtitle">Do you want to ask something? Send us a message.</p>

        <div class="contact-layout">

            <!-- LEFT SIDE — PLAIN TEXT INFO -->
            <div class="contact-info-list">

                <div class="info-block">
                    <h4>Contact Us</h4>
                    <p>+639123456789</p>
                </div>


                <div class="info-block">
                    <h4>Email</h4>
                    <p>8thavenuesalon@gmail.com</p>
                </div>

                <div class="info-block">
                    <h4>Address</h4>
                    <p>33 Susana St. Gulod, Novaliches, Quezon City, Philippines</p>
                </div>

                <div class="info-block">
                    <h4>Business Hours</h4>
                    <p>Mon – Sun: 8:00 AM – 7:00 PM</p>
                </div>

            </div>

            <!-- RIGHT SIDE — SIMPLE FORM -->
            <div class="contact-form-wrapper">

                <form method="POST" action="/inquiry/storePublic" class="contact-form">
                    <div class="form-line">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-line">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-line">
                        <label>Message</label>
                        <textarea name="message" required></textarea>
                    </div>

                    <button class="contact-submit">Send Message</button>
                </form>

            </div>

        </div>

    </section>




    <script>
    // Smooth scroll for navbar links
    document.querySelectorAll('.scroll-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    </script>

</body>

</html>