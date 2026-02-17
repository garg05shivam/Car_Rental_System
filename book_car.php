<?php
session_start();
require_once "config/db.php";


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "customer") {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST["car_id"])) {
    header("Location: available_cars.php");
    exit();
}

$car_id = intval($_POST["car_id"]);
$customer_id = $_SESSION["user_id"];
$start_date = $_POST["start_date"];
$number_of_days = intval($_POST["number_of_days"]);


if (empty($start_date) || $number_of_days <= 0) {
    header("Location: available_cars.php");
    exit();
}

if ($start_date < date("Y-m-d")) {
    header("Location: available_cars.php");
    exit();
}


$end_date = date("Y-m-d", strtotime($start_date . " +$number_of_days days"));

$query = "SELECT rent_per_day FROM cars WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    header("Location: available_cars.php");
    exit();
}

$checkBooking = "SELECT id FROM bookings 
                 WHERE car_id = ? 
                 AND status IN ('pending','confirmed')";

$stmt = $conn->prepare($checkBooking);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: available_cars.php");
    exit();
}

$total_amount = $car["rent_per_day"] * $number_of_days;

$conn->begin_transaction();

try {

    $insert = "INSERT INTO bookings 
               (car_id, customer_id, start_date, end_date, number_of_days, total_amount, status)
               VALUES (?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($insert);
    $stmt->bind_param(
        "iissid",
        $car_id,
        $customer_id,
        $start_date,
        $end_date,
        $number_of_days,
        $total_amount
    );
    $stmt->execute();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
}

header("Location: customer/dashboard.php?msg=booked");
exit();
?>
