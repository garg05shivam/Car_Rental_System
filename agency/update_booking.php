<?php
session_start();
require_once "../config/db.php";


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"]) || !isset($_GET["action"])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = intval($_GET["id"]);
$action = $_GET["action"];


if ($action == "approve") {
    $new_status = "confirmed";
} elseif ($action == "reject") {
    $new_status = "cancelled";
} else {
    header("Location: bookings.php");
    exit();
}



$query = "
    SELECT b.car_id, c.agency_id
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: bookings.php");
    exit();
}

$data = $result->fetch_assoc();

$car_id = $data["car_id"];
$agency_id = $data["agency_id"];



if ($agency_id != $_SESSION["user_id"]) {
    header("Location: bookings.php");
    exit();
}


$conn->begin_transaction();
$status_updated = false;

try {

    
    $updateBooking = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateBooking);
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    $status_updated = true;

    if ($new_status == "confirmed") {

        $updateCar = "UPDATE cars SET status = 'booked' WHERE id = ?";
        $stmt2 = $conn->prepare($updateCar);
        $stmt2->bind_param("i", $car_id);
        $stmt2->execute();
    }

   
    if ($new_status == "cancelled") {

        $updateCar = "UPDATE cars SET status = 'available' WHERE id = ?";
        $stmt3 = $conn->prepare($updateCar);
        $stmt3->bind_param("i", $car_id);
        $stmt3->execute();
    }

    $conn->commit();

} catch (Exception $e) {

    $conn->rollback();
}

if ($status_updated) {
    header("Location: bookings.php?msg=updated");
} else {
    header("Location: bookings.php");
}
exit();
?>
