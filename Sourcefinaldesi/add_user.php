<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. This action requires Admin privileges.");
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // 1. Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 2. Prepare the SQL
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("ssss", $full_name, $username, $hashed_password, $role);

    // 3. Use Try-Catch to handle the Duplicate Entry Exception
    try {
        if ($stmt->execute()) {
            header("Location: User.php?msg=New user '$username' created successfully!");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Check if the error code is 1062 (Duplicate entry)
        if ($e->getCode() == 1062) {
            header("Location: User.php?msg=Error: Username '$username' already exists.");
        } else {
            header("Location: User.php?msg=Error: " . $e->getMessage());
        }
        exit();
    }

    $stmt->close();
}
$conn->close();
?>