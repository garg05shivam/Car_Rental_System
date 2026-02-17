<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "customer") {
    header("Location: auth/login.php");
    exit();
}

if (!isset($_GET["car_id"])) {
    header("Location: available_cars.php");
    exit();
}

$car_id = intval($_GET["car_id"]);
$customer_id = $_SESSION["user_id"];

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $start_date = $_POST["start_date"];
    $number_of_days = intval($_POST["number_of_days"]);

    // Get car price
    $carQuery = "SELECT rent_per_day FROM cars WHERE id = ?";
    $stmt = $conn->prepare($carQuery);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    $total_amount = $car["rent_per_day"] * $number_of_days;

    // Insert booking
    $insertQuery = "INSERT INTO bookings 
                    (car_id, customer_id, start_date, number_of_days, total_amount) 
                    VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iisid", 
        $car_id, 
        $customer_id, 
        $start_date, 
        $number_of_days, 
        $total_amount
    );

    if ($stmt->execute()) {

        // Update car status
        $updateQuery = "UPDATE cars SET status = 'booked' WHERE id = ?";
        $stmt2 = $conn->prepare($updateQuery);
        $stmt2->bind_param("i", $car_id);
        $stmt2->execute();

        $message = "Car booked successfully!";
    } else {
        $message = "Booking failed!";
    }
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container mt-5">
    <h2>Book Car</h2>

    <?php if ($message != ""): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Number of Days</label>
            <input type="number" name="number_of_days" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">
            Confirm Booking
        </button>

    </form>
</div>

<?php include "includes/footer.php"; ?>
