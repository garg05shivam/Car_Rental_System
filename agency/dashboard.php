<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

$agency_id = $_SESSION["user_id"];
$agency_name = $_SESSION["full_name"] ?? "Agency";


$totalCarsQ = $conn->prepare("SELECT COUNT(*) as total FROM cars WHERE agency_id = ?");
$totalCarsQ->bind_param("i", $agency_id);
$totalCarsQ->execute();
$totalCars = $totalCarsQ->get_result()->fetch_assoc()['total'];

$availableCarsQ = $conn->prepare("SELECT COUNT(*) as total FROM cars WHERE agency_id = ? AND status='available'");
$availableCarsQ->bind_param("i", $agency_id);
$availableCarsQ->execute();
$availableCars = $availableCarsQ->get_result()->fetch_assoc()['total'];

$bookingsQ = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE c.agency_id = ?
");
$bookingsQ->bind_param("i", $agency_id);
$bookingsQ->execute();
$totalBookings = $bookingsQ->get_result()->fetch_assoc()['total'];
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">

    <div class="text-center mb-5">
        <h2>Welcome <?php echo htmlspecialchars($agency_name); ?> 👋</h2>
        <p class="text-muted">Manage your fleet and bookings efficiently.</p>
    </div>

    
    <div class="row text-center mb-5">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h5>Total Cars</h5>
                <h2 class="text-primary"><?php echo $totalCars; ?></h2>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h5>Available Cars</h5>
                <h2 class="text-success"><?php echo $availableCars; ?></h2>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h5>Total Bookings</h5>
                <h2 class="text-warning"><?php echo $totalBookings; ?></h2>
            </div>
        </div>

    </div>

    
    <div class="text-center">
        <a href="add_car.php" class="btn btn-success btn-lg me-3">
            + Add New Car
        </a>

        <a href="my_cars.php" class="btn btn-primary btn-lg me-3">
            View My Cars
        </a>

        <a href="bookings.php" class="btn btn-warning btn-lg">
            View Bookings
        </a>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
