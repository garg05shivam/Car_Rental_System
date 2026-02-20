<?php
session_start();
require_once "../config/db.php";

// Access control
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "agency") {
    header("Location: ../auth/login.php");
    exit();
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
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

    <h2 class="mb-4 text-center">My Cars</h2>

    <div class="text-end mb-3">
        <a href="add_car.php" class="btn btn-success">
            + Add New Car
        </a>
    </div>

    <?php if ($result->num_rows > 0): ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Model</th>
                        <th>Number</th>
                        <th>Seats</th>
                        <th>Rent/Day</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php
                        // Image path
                        $imagePath = "../assets/uploads/cars/" . $row["image"];

                        // Status badge color
                        $status = $row["status"];
                        $badgeClass = "bg-secondary";

                        if ($status === "available") {
                            $badgeClass = "bg-success";
                        } elseif ($status === "booked") {
                            $badgeClass = "bg-danger";
                        } elseif ($status === "maintenance") {
                            $badgeClass = "bg-warning text-dark";
                        }
                    ?>

                    <tr>
                        <td>
                            <?php if (!empty($row["image"]) && file_exists($imagePath)): ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     width="90" height="65"
                                     style="object-fit:cover; border-radius:6px;">
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>

                        <td><?php echo htmlspecialchars($row["vehicle_model"]); ?></td>
                        <td><?php echo htmlspecialchars($row["vehicle_number"]); ?></td>
                        <td><?php echo (int)$row["seating_capacity"]; ?></td>
                        <td>₹ <?php echo number_format($row["rent_per_day"], 2); ?></td>

                        <td>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>

                        <td>
                            <a href="edit_car.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-primary btn-sm me-1">
                               Edit
                            </a>

                            <form method="POST" action="delete_car.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this car?');">
                                <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="alert alert-info text-center">
            No cars added yet.
        </div>

    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>
