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
$message = "";


$query = "SELECT * FROM cars WHERE id = ? AND agency_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: my_cars.php");
    exit();
}

$car = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vehicle_model = trim($_POST["vehicle_model"]);
    $vehicle_number = trim($_POST["vehicle_number"]);
    $seating_capacity = intval($_POST["seating_capacity"]);
    $rent_per_day = floatval($_POST["rent_per_day"]);
    $description = trim($_POST["description"]);

    // Validation
    if (
        empty($vehicle_model) ||
        empty($vehicle_number) ||
        $seating_capacity <= 0 ||
        $rent_per_day <= 0
    ) {
        $message = "All fields are required and must be valid.";
    } else {

        $updateQuery = "
            UPDATE cars 
            SET vehicle_model = ?, 
                vehicle_number = ?, 
                seating_capacity = ?, 
                rent_per_day = ?, 
                description = ?
            WHERE id = ? AND agency_id = ?
        ";

        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "ssidssi",
            $vehicle_model,
            $vehicle_number,
            $seating_capacity,
            $rent_per_day,
            $description,
            $car_id,
            $agency_id
        );

        if ($stmt->execute()) {
            $message = "Car updated successfully!";

            // Refresh updated data
            $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND agency_id = ?");
            $stmt->bind_param("ii", $car_id, $agency_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $car = $result->fetch_assoc();

        } else {
            $message = "Update failed!";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Edit Car</h2>

    <div class="card shadow p-4">

        <?php if ($message): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Vehicle Model</label>
                <input type="text" name="vehicle_model" class="form-control"
                       value="<?php echo htmlspecialchars($car["vehicle_model"]); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Vehicle Number</label>
                <input type="text" name="vehicle_number" class="form-control"
                       value="<?php echo htmlspecialchars($car["vehicle_number"]); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Seating Capacity</label>
                <input type="number" name="seating_capacity" class="form-control"
                       value="<?php echo htmlspecialchars($car["seating_capacity"]); ?>" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rent Per Day</label>
                <input type="number" step="0.01" name="rent_per_day" class="form-control"
                       value="<?php echo htmlspecialchars($car["rent_per_day"]); ?>" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?php echo htmlspecialchars($car["description"]); ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="my_cars.php" class="btn btn-secondary">
                    Back
                </a>

                <button type="submit" class="btn btn-primary">
                    Update Car
                </button>
            </div>

        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
