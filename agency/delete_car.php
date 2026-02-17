<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: my_cars.php");
    exit();
}

$car_id = intval($_GET["id"]);
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

$deleteQuery = "DELETE FROM cars WHERE id = ? AND agency_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();

header("Location: my_cars.php");
exit();
?>
