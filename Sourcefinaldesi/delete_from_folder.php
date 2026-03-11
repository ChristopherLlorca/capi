<?php
include 'db.php';

// Check if the necessary ID parameters are present in the URL
if (isset($_GET['id']) && isset($_GET['student_id'])) {
    
    // Sanitize inputs to prevent basic SQL injection
    $id = $conn->real_escape_string($_GET['id']);
    $student_id = $conn->real_escape_string($_GET['student_id']);

    // 1. Fetch the filename before deleting the record so we can remove the physical file
    $file_query = $conn->query("SELECT document_file FROM documents WHERE id = '$id'");
    
    if ($file_query && $file_query->num_rows > 0) {
        $row = $file_query->fetch_assoc();
        $file_name = $row['document_file'];
        $file_path = "uploads/" . $file_name;

        // 2. Delete the physical file from the server folder
        if (!empty($file_name) && file_exists($file_path)) {
            unlink($file_path);
        }

        // 3. Delete the record from the 'documents' table
        $delete_sql = "DELETE FROM documents WHERE id = '$id'";
        
        if ($conn->query($delete_sql)) {
            // Success: Redirect back to the profile with the 'deleted' message
            header("Location: StudentProfile.php?id=$student_id&msg=deleted");
            exit();
        } else {
            // Database error
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Document record not found.";
    }
} else {
    // If accessed without IDs, send back to the main students list
    header("Location: Students.php");
    exit();
}
?>