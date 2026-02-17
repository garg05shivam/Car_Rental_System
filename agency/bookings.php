<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "agency") {
    header("Location: ../auth/login.php");
    exit();
}

$agency_id = $_SESSION["user_id"];

$query = "
    SELECT 
        b.*, 
        c.vehicle_model, 
        u.full_name AS customer_name
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    JOIN users u ON b.customer_id = u.id
    WHERE c.agency_id = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">

    <h2 class="mb-4 text-center">Car Bookings</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success">
            Booking status updated successfully!
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Car</th>
                    <th>Customer</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php
                        $status = $row["status"];
                        $badgeClass = "bg-secondary";

                        if ($status == "pending") {
                            $badgeClass = "bg-warning text-dark";
                        } elseif ($status == "confirmed") {
                            $badgeClass = "bg-success";
                        } elseif ($status == "cancelled") {
                            $badgeClass = "bg-danger";
                        }
                    ?>

                    <tr>
                        <td><?php echo htmlspecialchars($row["vehicle_model"]); ?></td>
                        <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["start_date"]); ?></td>
                        <td>
                            <?php 
                                echo $row["end_date"] 
                                ? htmlspecialchars($row["end_date"]) 
                                : "-"; 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row["number_of_days"]); ?></td>
                        <td>₹ <?php echo number_format($row["total_amount"], 2); ?></td>

                        <td>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($status == "pending"): ?>
                                <a href="update_booking.php?id=<?php echo $row["id"]; ?>&action=approve" 
                                   class="btn btn-success btn-sm me-1">
                                   Approve
                                </a>

                                <a href="update_booking.php?id=<?php echo $row["id"]; ?>&action=reject" 
                                   class="btn btn-danger btn-sm">
                                   Reject
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="text-center">
                        No bookings found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
