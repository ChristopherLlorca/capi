<?php
session_start();
include 'db.php';

// This line prevents the "Fatal error" crash and allows your custom error check to run
mysqli_report(MYSQLI_REPORT_OFF); 

$message = '';
$messageClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role']; // 'admin' or 'staff'
    
    // Hash the password using the same method as create_admin.php
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hash, $full_name, $role);

    if ($stmt->execute()) {
        $message = "Account created successfully! <a href='login.php'>Login here</a>";
        $messageClass = "alert-success";
    } else {
        // If the error code is 1062, it means the username already exists in the database
        if ($conn->errno == 1062) {
            $message = "<strong>Registration Error:</strong> The username '" . htmlspecialchars($username) . "' is already taken. Please choose another.";
        } else {
            $message = "Error: " . $conn->error;
        }
        $messageClass = "alert-danger";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - LHS DTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f7; font-family: 'Inter', sans-serif; }
        .register-container { max-width: 500px; margin: 80px auto; }
        .card { border-radius: 1rem; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #0d6efd; border: none; padding: 0.75rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container register-container">
        <div class="card p-4">
            <div class="text-center mb-4">
                <img src="lhs-reglogo.png" alt="Logo" style="height: 60px;">
                <h2 class="mt-3">Create Account</h2>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= $messageClass ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Role</label>
                    <select name="role" class="form-select" required>
                        <option value="staff">Staff (Standard User)</option>
                        <option value="admin">Admin (Full Access)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">REGISTER ACCOUNT</button>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none text-muted small">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>