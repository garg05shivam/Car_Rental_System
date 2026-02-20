<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: my_cars.php");
    exit();
}

if (
    !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    header("Location: my_cars.php");
    exit();
}

$car_id = intval($_POST["id"]);
$agency_id = $_SESSION["user_id"];



$query = "SELECT status FROM cars WHERE id = ? AND agency_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: my_cars.php");
    exit();
}

$car = $result->fetch_assoc();

if ($car["status"] == "booked") {
    // Do not allow deleting booked car
    header("Location: my_cars.php");
    exit();
}

$activeBookingQuery = "SELECT id FROM bookings WHERE car_id = ? AND status IN ('pending','confirmed') LIMIT 1";
$stmt = $conn->prepare($activeBookingQuery);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$activeBookings = $stmt->get_result();

if ($activeBookings->num_rows > 0) {
    header("Location: my_cars.php");
    exit();
}

$deleteQuery = "DELETE FROM cars WHERE id = ? AND agency_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();

header("Location: my_cars.php");
exit();
?>
