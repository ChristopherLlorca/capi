<?php
// Start or resume the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// OPTIONAL: Role restriction example (you can comment this out for now)
if (isset($requiredRole) && $_SESSION['role'] !== $requiredRole) {
    header("Location: Dashboard.php");
    exit();
}
?>
