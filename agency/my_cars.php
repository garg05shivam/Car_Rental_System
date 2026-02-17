<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

// Access control
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "agency") {
    header("Location: ../auth/login.php");
    exit();
}

$agency_id = $_SESSION["user_id"];

// Fetch only this agency cars
$query = "SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">My Cars</h2>

    <a href="add_car.php" class="btn btn-success mb-3">
        + Add New Car
    </a>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Model</th>
                        <th>Number</th>
                        <th>Seats</th>
                        <th>Rent/Day</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row["image"])): ?>
                                    <img src="../assets/uploads/cars/<?php echo htmlspecialchars($row["image"]); ?>" 
                                         width="80" height="60" style="object-fit:cover;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row["vehicle_model"]); ?></td>
                            <td><?php echo htmlspecialchars($row["vehicle_number"]); ?></td>
                            <td><?php echo $row["seating_capacity"]; ?></td>
                            <td>₹ <?php echo number_format($row["rent_per_day"], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row["status"] === "available" ? "success" : 
                                         ($row["status"] === "booked" ? "danger" : "warning"); ?>">
                                    <?php echo ucfirst($row["status"]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No cars added yet.
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
