<?php
session_start();
include 'db.php';

// 1. Restriction: Only admins can perform this update
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    // 2. Prepare the update statement
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $username, $role, $user_id);

    // 3. Execute and redirect
    try {
        if ($stmt->execute()) {
            header("Location: User.php?msg=User details updated successfully!");
        } else {
            header("Location: User.php?msg=Error updating user.");
        }
    } catch (mysqli_sql_exception $e) {
        // Handle duplicate username error if the admin changes it to one that already exists
        if ($e->getCode() == 1062) {
            header("Location: User.php?msg=Error: Username '$username' is already taken.");
        } else {
            header("Location: User.php?msg=Error: " . $e->getMessage());
        }
    }

    $stmt->close();
}
$conn->close();
?>