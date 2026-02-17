<?php
session_start();
require_once "config/db.php";
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">Available Cars</h2>

    <div class="row">

        <?php
        $query = "SELECT * FROM cars WHERE status = 'available'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">

                        <?php
                        $imagePath = "assets/uploads/cars/" . $row["image"];

                        if (!empty($row["image"]) && file_exists($imagePath)) {
                        ?>
                            <img src="<?php echo $imagePath; ?>"
                                class="card-img-top"
                                style="height:200px; object-fit:cover;">
                        <?php
                        } else {
                        ?>
                            <div style="height:200px; display:flex; align-items:center; justify-content:center; background:#f2f2f2;">
                                No Image
                            </div>
                        <?php } ?>

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($row["vehicle_model"]); ?>
                            </h5>

                            <p class="card-text">
                                <strong>Number:</strong> <?php echo htmlspecialchars($row["vehicle_number"]); ?><br>
                                <strong>Seats:</strong> <?php echo $row["seating_capacity"]; ?><br>
                                <strong>Rent/Day:</strong> ₹ <?php echo number_format($row["rent_per_day"], 2); ?>
                            </p>

                            <span class="badge bg-success mb-2">
                                <?php echo ucfirst($row["status"]); ?>
                            </span>

                            
                            <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] == "customer"): ?>
                                <a href="book_car.php?car_id=<?php echo $row["id"]; ?>" 
                                   class="btn btn-primary mt-auto">
                                   Book Now
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

        <?php
            }
        } else {
            echo "<p>No cars available right now.</p>";
        }
        ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>
