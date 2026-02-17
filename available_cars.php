<?php
session_start();
require_once "config/db.php";
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Available Cars</h2>

    <div class="row">

        <?php
        $query = "SELECT * FROM cars WHERE status = 'available'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0):

            while ($row = $result->fetch_assoc()):
        ?>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">

                <?php
                $imagePath = "assets/uploads/cars/" . $row["image"];

                if (!empty($row["image"]) && file_exists($imagePath)):
                ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>"
                         class="card-img-top"
                         style="height:200px; object-fit:cover;">
                <?php else: ?>
                    <div style="height:200px; display:flex; align-items:center; justify-content:center; background:#f2f2f2;">
                        No Image
                    </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">

                    <h5 class="card-title">
                        <?php echo htmlspecialchars($row["vehicle_model"]); ?>
                    </h5>

                    <p class="card-text">
                        <strong>Number:</strong> <?php echo htmlspecialchars($row["vehicle_number"]); ?><br>
                        <strong>Seats:</strong> <?php echo htmlspecialchars($row["seating_capacity"]); ?><br>
                        <strong>Rent/Day:</strong> ₹ <?php echo number_format($row["rent_per_day"], 2); ?>
                    </p>

                    <span class="badge bg-success mb-3">
                        Available
                    </span>

                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] == "customer"): ?>

                        <form method="POST" action="book_car.php" class="mt-auto">

                            <input type="hidden" name="car_id" value="<?php echo $row['id']; ?>">

                            <div class="mb-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" 
                                       name="start_date" 
                                       class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Number of Days</label>
                                <select name="number_of_days" class="form-select" required>
                                    <option value="">Select Days</option>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>">
                                            <?php echo $i; ?> Day<?php echo $i > 1 ? 's' : ''; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Rent Car
                            </button>

                        </form>

                    <?php elseif (!isset($_SESSION["user_id"])): ?>

                        <a href="auth/login.php" class="btn btn-outline-primary mt-auto w-100">
                            Login to Book
                        </a>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <?php
            endwhile;

        else:
        ?>

        <div class="col-12">
            <div class="alert alert-info text-center">
                No cars available right now.
            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>
