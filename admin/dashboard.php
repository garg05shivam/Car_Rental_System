<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2>Admin Panel 👑</h2>
    <p>Welcome, <?php echo $_SESSION["user_name"]; ?></p>
</div>

<?php include "../includes/footer.php"; ?>
