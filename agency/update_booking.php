<?php
session_start();
require_once "../config/db.php";


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"]) || !isset($_POST["action"])) {
    header("Location: bookings.php");
    exit();
}

if (
    !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    header("Location: bookings.php");
    exit();
}

$booking_id = intval($_POST["id"]);
$action = $_POST["action"];


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

    
    $updateBooking = "UPDATE bookings SET status = ? WHERE id = ? AND status = 'pending'";
    $stmt = $conn->prepare($updateBooking);
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    if ($stmt->affected_rows !== 1) {
        throw new Exception("Booking is not pending");
    }
    $status_updated = true;

    if ($new_status == "confirmed") {
        $existingConfirmed = "SELECT id FROM bookings WHERE car_id = ? AND status = 'confirmed' AND id <> ? LIMIT 1";
        $stmt2 = $conn->prepare($existingConfirmed);
        $stmt2->bind_param("ii", $car_id, $booking_id);
        $stmt2->execute();
        $confirmedResult = $stmt2->get_result();

        if ($confirmedResult->num_rows > 0) {
            throw new Exception("Car already has a confirmed booking");
        }

        $updateCar = "UPDATE cars SET status = 'booked' WHERE id = ?";
        $stmt3 = $conn->prepare($updateCar);
        $stmt3->bind_param("i", $car_id);
        $stmt3->execute();
    }

   
    if ($new_status == "cancelled") {
        $activeBookingCheck = "SELECT id FROM bookings WHERE car_id = ? AND status = 'confirmed' LIMIT 1";
        $stmt4 = $conn->prepare($activeBookingCheck);
        $stmt4->bind_param("i", $car_id);
        $stmt4->execute();
        $activeResult = $stmt4->get_result();

        if ($activeResult->num_rows === 0) {
            $updateCar = "UPDATE cars SET status = 'available' WHERE id = ?";
            $stmt5 = $conn->prepare($updateCar);
            $stmt5->bind_param("i", $car_id);
            $stmt5->execute();
        }
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
