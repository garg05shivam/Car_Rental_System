<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "agency") {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $agency_id = $_SESSION["user_id"];
    $vehicle_model = trim($_POST["vehicle_model"]);
    $vehicle_number = trim($_POST["vehicle_number"]);
    $seating_capacity = intval($_POST["seating_capacity"]);
    $rent_per_day = floatval($_POST["rent_per_day"]);
    $description = trim($_POST["description"]);

    $image_name = null;
    $upload_ok = true;

if (isset($_FILES["car_image"]) && $_FILES["car_image"]["error"] == 0) {

  $target_dir = "../assets/uploads/cars/";


    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $maxSize = 2 * 1024 * 1024; // 2MB
    $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
    $allowedMimeTypes = ["image/jpeg", "image/png", "image/webp"];

    $originalName = basename($_FILES["car_image"]["name"]);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $mime = mime_content_type($_FILES["car_image"]["tmp_name"]);

    if (!in_array($ext, $allowedExtensions, true) || !in_array($mime, $allowedMimeTypes, true)) {
        $message = "Only JPG, JPEG, PNG, WEBP images are allowed.";
        $upload_ok = false;
    } elseif ($_FILES["car_image"]["size"] > $maxSize) {
        $message = "Image size must be 2MB or less.";
        $upload_ok = false;
    }

    if ($upload_ok) {
        $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $originalName);

        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            $message = "Image upload failed.";
            $upload_ok = false;
        }
    }
}
    if ($upload_ok && $image_name !== null) {
        $query = "INSERT INTO cars 
                  (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day, image, description) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "issidss",
            $agency_id,
            $vehicle_model,
            $vehicle_number,
            $seating_capacity,
            $rent_per_day,
            $image_name,
            $description
        );

        if ($stmt->execute()) {
            $message = "Car added successfully!";
        } else {
            $message = "Something went wrong!";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">Add New Car</h2>

    <?php if ($message != ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Vehicle Model</label>
            <input type="text" name="vehicle_model" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Seating Capacity</label>
            <input type="number" name="seating_capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rent Per Day</label>
            <input type="number" step="0.01" name="rent_per_day" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
    <label class="form-label">Car Image</label>
    <input type="file" name="car_image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
</div>


        <button type="submit" class="btn btn-success">
            Add Car
        </button>

    </form>
</div>

<?php include "../includes/footer.php"; ?>
