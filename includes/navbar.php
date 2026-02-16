<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/car_rental/">CarRental</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <li class="nav-item">
          <a class="nav-link" href="/car_rental/available_cars.php">Available Cars</a>
        </li>

        <?php if (!isset($_SESSION["user_id"])): ?>

          
          <li class="nav-item">
            <a class="nav-link" href="/car_rental/auth/login.php">Login</a>
          </li>

          <li class="nav-item">
            <a class="nav-link ms-3" href="/car_rental/auth/register.php">Register</a>
          </li>

        <?php else: ?>

          
          <li class="nav-item">
            <span class="nav-link text-info">
              <?php echo $_SESSION["user_name"]; ?>
            </span>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/car_rental/auth/logout.php">Logout</a>
          </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
