<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SESSION["role"] !== "agency") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2>Welcome Agency, <?php echo $_SESSION["user_name"]; ?> 👋</h2>
    <p>This is your agency dashboard.</p>

    <a href="add_car.php" class="btn btn-success mt-3">
        + Add New Car
    </a>

    <a href="my_cars.php" class="btn btn-primary mt-3 ms-2">
        View My Cars
    </a>
</div>

<?php include "../includes/footer.php"; ?>
