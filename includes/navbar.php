<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="/car_rental/">CarRental</a>

        
        <button class="navbar-toggler" type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

      
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'available_cars.php') ? 'active' : ''; ?>" 
                       href="/car_rental/available_cars.php">
                        Available Cars
                    </a>
                </li>

                <?php if (!isset($_SESSION["user_id"])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/car_rental/auth/login.php">
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/car_rental/auth/register.php">
                            Register
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "agency"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/car_rental/agency/dashboard.php">
                            Agency Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "customer"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/car_rental/customer/dashboard.php">
                            My Bookings
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/car_rental/auth/logout.php">
                            Logout
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>
