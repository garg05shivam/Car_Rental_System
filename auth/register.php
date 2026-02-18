<?php
require_once "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // ================= VALIDATIONS =================

    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required.";
    }

    elseif (strlen($full_name) < 3) {
        $message = "Full name must be at least 3 characters.";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    }

    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
        $message = "Password must contain uppercase, lowercase, number and minimum 6 characters.";
    }

    elseif ($role !== "customer" && $role !== "agency") {
        $message = "Invalid role selected.";
    }

    else {

        // Check if email already exists
        $checkQuery = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email already registered!";
        } else {

            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {

                header("Location: login.php?msg=registered");
                exit();

            } else {
                $message = "Something went wrong. Try again.";
            }
        }
    }
}
?>


<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="register-section d-flex align-items-center">
    <div class="container">

        <h2 class="text-center mb-4">Create Account</h2>

        <?php if ($message != ""): ?>
            <div class="alert alert-info text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 bg-transparent border-0 shadow-lg">

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Register As</label>
                            <select name="role" class="form-select" required>
                                <option value="customer">Customer</option>
                                <option value="agency">Car Rental Agency</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Register
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
