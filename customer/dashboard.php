<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

// Access control
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION["user_id"];
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">My Bookings</h2>

    <?php
    $query = "SELECT b.*, c.vehicle_model, c.vehicle_number
              FROM bookings b
              JOIN cars c ON b.car_id = c.id
              WHERE b.customer_id = ?
              ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
    ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td>
                            <?php echo $row["vehicle_model"]; ?>
                            <br>
                            <small><?php echo $row["vehicle_number"]; ?></small>
                        </td>

                        <td><?php echo $row["start_date"]; ?></td>
                        <td><?php echo $row["end_date"]; ?></td>
                        <td><?php echo $row["number_of_days"]; ?></td>
                        <td>₹ <?php echo number_format($row["total_amount"], 2); ?></td>

                        <td>
                            <?php
                            if ($row["status"] == "pending") {
                                echo "<span class='badge bg-warning'>Pending</span>";
                            } elseif ($row["status"] == "approved") {
                                echo "<span class='badge bg-success'>Approved</span>";
                            } elseif ($row["status"] == "rejected") {
                                echo "<span class='badge bg-danger'>Rejected</span>";
                            }
                            ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

    <?php else: ?>

        <p>No bookings yet.</p>

    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>
