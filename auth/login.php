<?php
session_start();
require_once "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $message = "Invalid email or password.";

    if ($email === "" || $password === "") {
        $message = "Invalid email or password.";
    } else {

        $query = "SELECT id, full_name, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["user_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

           
                if ($user["role"] == "customer") {
                    header("Location: ../customer/dashboard.php");
                } elseif ($user["role"] == "agency") {
                    header("Location: ../agency/dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            }
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="register-section d-flex align-items-center">
    <div class="container">

        <h2 class="text-center mb-4">Login</h2>

        <?php if ($message != ""): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 bg-transparent border-0 shadow-lg">

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
