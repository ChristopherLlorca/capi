
<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. This action requires Admin privileges.");
}
include 'db.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        header("Location: User.php?msg=Error: You cannot delete your own account.");
        exit();
    }

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: User.php?msg=User deleted permanently.");
    } else {
        header("Location: User.php?msg=Error deleting user: " . $conn->error);
    }
    $stmt->close();
}
$conn->close();
?>