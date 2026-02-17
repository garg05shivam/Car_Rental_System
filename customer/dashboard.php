<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION["user_id"];
$customer_name = $_SESSION["full_name"] ?? "Customer";



$totalQ = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE customer_id = ?");
$totalQ->bind_param("i", $customer_id);
$totalQ->execute();
$totalBookings = $totalQ->get_result()->fetch_assoc()['total'];

$pendingQ = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE customer_id = ? AND status='pending'");
$pendingQ->bind_param("i", $customer_id);
$pendingQ->execute();
$pendingBookings = $pendingQ->get_result()->fetch_assoc()['total'];

$confirmedQ = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE customer_id = ? AND status='confirmed'");
$confirmedQ->bind_param("i", $customer_id);
$confirmedQ->execute();
$confirmedBookings = $confirmedQ->get_result()->fetch_assoc()['total'];
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">

    
    <div class="text-center mb-5">
        <h2>Welcome <?php echo htmlspecialchars($customer_name); ?> 👋</h2>
        <p class="text-muted">Here are your booking details.</p>
    </div>

    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'booked'): ?>
        <div class="alert alert-success text-center">
            Booking request sent successfully!
        </div>
    <?php endif; ?>

    <div class="row text-center mb-5">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h6>Total Bookings</h6>
                <h2 class="text-primary"><?php echo $totalBookings; ?></h2>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h6>Pending</h6>
                <h2 class="text-warning"><?php echo $pendingBookings; ?></h2>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 p-4">
                <h6>Confirmed</h6>
                <h2 class="text-success"><?php echo $confirmedBookings; ?></h2>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="mb-4 text-center">My Bookings</h4>

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

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Car</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Days</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

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
                                <td>
                                    <?php echo htmlspecialchars($row["vehicle_model"]); ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($row["vehicle_number"]); ?>
                                    </small>
                                </td>

                                <td><?php echo htmlspecialchars($row["start_date"]); ?></td>
                                <td><?php echo $row["end_date"] ? htmlspecialchars($row["end_date"]) : "-"; ?></td>
                                <td><?php echo htmlspecialchars($row["number_of_days"]); ?></td>
                                <td>₹ <?php echo number_format($row["total_amount"], 2); ?></td>

                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="alert alert-info text-center">
                    No bookings yet.
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
