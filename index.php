<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<!-- HERO SECTION -->
<section class="hero-section text-white d-flex align-items-center">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Drive Your Dream Car</h1>
        <p class="lead mt-3">
            Premium vehicles. Transparent pricing. Instant booking.
        </p>

        <div class="mt-4">
            <a href="available_cars.php" class="btn btn-primary btn-lg me-3">
                Browse Fleet
            </a>

            <?php if (!isset($_SESSION["user_id"])): ?>
                <a href="auth/register.php" class="btn btn-outline-light btn-lg">
                    Get Started
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-4">Why Choose Us?</h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm bg-white rounded">
                    <h5>🚗 Premium Cars</h5>
                    <p class="text-muted">
                        Choose from a wide range of luxury and economy vehicles.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm bg-white rounded">
                    <h5>💰 Transparent Pricing</h5>
                    <p class="text-muted">
                        No hidden charges. Pay exactly what you see.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm bg-white rounded">
                    <h5>⚡ Instant Booking</h5>
                    <p class="text-muted">
                        Quick booking process with agency approval system.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-5 text-center bg-dark text-white">
    <div class="container">
        <h3>Ready to hit the road?</h3>
        <p class="mt-2">Explore our fleet and book your ride today.</p>
        <a href="available_cars.php" class="btn btn-success btn-lg mt-3">
            View Available Cars
        </a>
    </div>
</section>

<?php include "includes/footer.php"; ?>
