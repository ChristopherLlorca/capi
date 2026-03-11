<?php
session_start();
include 'db.php';

$message = '';
$error = '';

// Inside forgot_password.php, update the POST logic:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // SAVE REQUEST TO DATABASE
        $req = $conn->prepare("INSERT INTO password_requests (username) VALUES (?)");
        $req->bind_param("s", $username);
        $req->execute();
        
        $message = "Your request has been sent to the Admin. Please wait for them to reset your password.";
    } else {
        $error = "Username not found.";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - LHS DTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f7; font-family: 'Inter', sans-serif; }
        .reset-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        .btn-primary { background-color: #0056b3; border: none; padding: 0.75rem; border-radius: 0.75rem; font-weight: bold; }
        .btn-primary:hover { background-color: #004494; }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="text-center mb-4">
                <img src="lhs-reglogo.png" alt="Logo" style="height: 60px;">
                <h4 class="mt-3 fw-bold">Reset Password</h4>
                <p class="text-muted small">Enter your username to request a reset.</p>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success small"><?php echo $message; ?></div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-outline-secondary btn-sm w-100">Back to Login</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">USERNAME</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>
                    <?php if($error): ?>
                        <div class="text-danger small mb-3"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary w-100">SUBMIT REQUEST</button>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none small text-muted">Return to Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>