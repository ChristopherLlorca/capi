<?php
include 'db.php';
session_start();

if ($_SESSION['role'] === 'admin' && isset($_GET['user'])) {
    $username = $_GET['user'];
    $req_id = $_GET['id'];
    
    $default_password = password_hash("LHS12345", PASSWORD_DEFAULT);
    
    // Reset password AND clear the 12-hour lockout/trials
    $stmt = $conn->prepare("UPDATE users SET password = ?, login_attempts = 0, lockout_until = NULL WHERE username = ?");
    $stmt->bind_param("ss", $default_password, $username);
    
    if ($stmt->execute()) {
        $conn->query("DELETE FROM password_requests WHERE id = $req_id");
        header("Location: User.php?msg=Account Unlocked. Password for $username reset to default 'LHS12345'.");
    }
}
?>