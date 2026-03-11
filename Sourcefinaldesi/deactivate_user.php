<?php
session_start();

// 1. Check if the session exists AND if the user is an admin
// This prevents the "Undefined array key 'role'" warning
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access. This action requires Admin privileges.");
}

include 'db.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // 2. Fetch current status using a prepared statement for security
    $stmt_fetch = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt_fetch->bind_param("i", $user_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        // 3. Toggle logic: if active -> inactive; if inactive -> active
        $new_status = ($user['status'] == 'active') ? 'inactive' : 'active';

        // 4. Update the database with the new status
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $user_id);

        if ($stmt->execute()) {
            // Redirect back to the User Management page with a success message
            header("Location: User.php?msg=User status updated to " . $new_status);
            exit();
        } else {
            header("Location: User.php?msg=Error updating status.");
            exit();
        }
        $stmt->close();
    }
    $stmt_fetch->close();
}
$conn->close();
?>