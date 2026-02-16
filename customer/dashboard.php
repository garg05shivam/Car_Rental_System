<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] != "customer") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION["user_name"]; ?> 👋</h2>
    <p>This is your customer dashboard.</p>
</div>

<?php include "../includes/footer.php"; ?>
